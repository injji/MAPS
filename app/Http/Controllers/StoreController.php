<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Client\Inquiry, Client\MyPick, Agent\Service, Agent\ServiceCategory, Client\Review, Payment};
use App\Models\{Store\Store};
use App\Models\{Cms\Terms, Cms\Faq, Users};
use App\Models\Cms\Content;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

//CCC 20220615
class StoreController extends Controller
{
    /**
     * home view
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function __invoke(Request $request)
    {

        $position    = $request->pos;
        $prev_monday = date("Y-m-d", strtotime("last week monday"));
        $prev_sunday = date("Y-m-d", strtotime("last week sunday"));

        $viewdata = [
            'topBanner' => Store::getBanner(1),
            'midBanner' => Store::getBanner(2),
            'botBanner' => Store::getBanner(3) ? Store::getBanner(3)[0] : null,
            'funcKind'  => Store::getFuncKind($position == 'function' ? 20 : 4),
            'funclists' => Store::getFuncKind(),
            'best'      => Store::getBestService($prev_monday, $prev_sunday),//베스트
            'categorys' => ServiceCategory::getCate(),
            'review'    => Store::getSatisfy200($prev_monday, $prev_sunday, $position == 'satisfy' ? 20 : 4),//만족율 200%
            'adminSort' => Store::getMatech($position == 'matech' ? 20 : 4),//필수 마테크
            'position' => $position,
        ];
        $newCatagorys = Store::getNewSvcCatagory();

        $viewdata['newSVCcatagorys'] = $newCatagorys;
        $newsvcs = [];
        $newsvcs_all = Store::getNewServicesAll();//최근 한달간 새로운 서비스

        $newsvcs[0] = $newsvcs_all;
        foreach ($newCatagorys as $catagory) {
            $newsvcs[$catagory->id] = Store::getNewServices($catagory->id);
        }

        $viewdata['newsvcs'] = $newsvcs;
        $viewdata['newsvcs_all'] = $newsvcs_all;

        return view('store.home', $viewdata);
    }

    /**
     * 서비스 상세
     *
     * @param Illuminate\Http\Request $request
     * @param App\Models\Agent\Service $service
     * @param string $lang
     * @return Illuminate\View\View
     */
    public function service(Request $request, $lang = null)
    {
        if ($lang == null) {
            $lang = \App::getLocale();
        }

        $service = Service::find($request->service);
        $service->update([
            'view_cnt' => $service->view_cnt+1,
        ]);

        //바이앱스에서 넘어올경우 app_id 값이 있다
        if($request->app_id){
            $key = 'xFAGzkn9PW8l21WSlovqlCBxmf6xWMOf';
            $app_id = $this->aes_decrypt($key,$request->app_id);
            $byapps_user = \App\Models\Byapps\Apps::where('app_id', $app_id)->first();

            $byapps_id = $byapps_user->mem_id;
            $byapps_app_id = $request->app_id;

            if($byapps_id){
                //맵스에 가입했는지 체크
                $map = Users::where('byapps_id', $byapps_id)->first();

                // 맵스 회원이면 자동 로그인처리
                if($map){
                    Auth::guard('user')->loginUsingId($map->id);
                }else{
                    //맵스 회원이 아니라면  신청하기 버튼 클릭시 회원가입 페이지로 가야함
                    $request->session()->put('byapps_id', $byapps_id);
                    $request->session()->put('byapps_app_id', $byapps_app_id);

                    $current = $request->url();
                    $request->session()->put('service_url', $current);

                }

            }
        }


        if(\Auth::guard('user')->check())
        {
            $myid = \Auth::guard('user')->user()->id;
            $pickid = \App\Models\Client\MyPick::where('client_id', $myid)->where('service_id', $request->service)->count();
            $myreview = Review::where('client_id', $myid)->where('service_id', $request->service)->first();
            $reqablesvcs = Store::reqableMyService($request->service, $myid);
        }else{
            $pickid = 0;
            $myreview = null;
            $reqablesvcs = [];
        }
        return view('store.service.detail', [
            'categorys' => ServiceCategory::getCate(),
            'funclists' => Store::getFuncKind(),
            'service' => $service,
            'reviewCount' => $service->review()->where('visible', 1)->count(),
            'rating' => Store::getSvcRating($request->service),
            'category1' => Store::getCate($service->category1),
            'category2' => Store::getCate($service->category2),
            'pickid' => $pickid,
            'myreview' => $myreview,
            'faqs' => $service->faq,//getFaqWithDefault($lang),
            'agent' => $service->user,
            'inquiries' => $service->inquiry()->orderBy('id', 'desc')->paginate(5),
            'reviews' => $service->review()->where('visible', 1)->paginate(5),
            'reqablesvcs' => $reqablesvcs,
            'byapps_id' => $byapps_id ?? '',
            'app_id' => $byapps_app_id ?? '',
        ]);
    }

    public function inquiry(Request $request, Service $service)
    {
        return view('partials.store.detail_inquiry', [
            'inquiries' => $service->inquiry()->orderBy('id', 'desc')->paginate(5),
        ]);
    }

    public function review(Request $request, Service $service)
    {
        return view('partials.store.detail_review', [
            'reviews' => $service->review()->where('visible', 1)->paginate(5),
            'user' => $service->user,
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
        ]);

        $sort = 1;//:1 = 최신순, :2 = 판매량순, :3 = 낮은가격순, :4 = 별점순
        if($request->sort)
        {
            $sort = $request->sort;
        }

        $limit = 8;
        if($request->limit)
        {
            $limit = $request->limit;
        }
        $category = 0;
        if($request->category)
        {
            $category = $request->category;
        }

        $cateindex = 0;
        if($request->cateindex)
        {
            $cateindex = $request->cateindex;
        }

        $freecost=0;
        if($request->freecost)
        {
            $freecost = $request->freecost;
        }
        $filtercatory="";
        if($request->filtercatory)
        {
            $filtercatory = $request->filtercatory;
        }

        $search_categorys = [];
        $categorys = ServiceCategory::getCate();
        $lang = \App::getLocale();
        $services = Store::getSearch($lang, $request->search_type, $request->keyword, $request->search_type == 'amount' ? $request->min_price : null, $request->search_type == 'amount' ? $request->max_price : null, $limit, $sort, $category, $freecost, $filtercatory);
        $services_categorys = Store::getSearchCategoryAll($lang, $request->search_type, $request->keyword, $request->search_type == 'amount' ? $request->min_price : null, $request->search_type == 'amount' ? $request->max_price : null, $sort, $freecost, $filtercatory);
        foreach($services_categorys as $service){
            array_push($search_categorys, $service->cat1);
        }

        $search_categorys = array_unique($search_categorys);
        $filtercates = Store::childCate($category);
        $funclists = Store::getFuncKind();
        return view('front.auth.search_keyword', compact('services', 'filtercates', 'categorys', 'sort', 'limit', 'category', 'cateindex', 'freecost', 'filtercatory', 'funclists', 'search_categorys'));
    }

    public function categorylist(Request $request)
    {
        $request->validate([
        ]);

        $sort = 1;//:1 = 최신순, :2 = 판매량순, :3 = 낮은가격순, :4 = 별점순
        if($request->sort)
        {
            $sort = $request->sort;
        }

        $limit = 8;
        if($request->limit)
        {
            $limit = $request->limit;
        }
        $category = 0;
        if($request->category)
        {
            $category = $request->category;
        }

        $freecost=0;
        if($request->freecost)
        {
            $freecost = $request->freecost;
        }
        $filtercatory="";
        if($request->filtercatory)
        {
            $filtercatory = $request->filtercatory;
        }


        $categorys = ServiceCategory::getCate();
        if($category != 0)
        {
            $cateinf = Store::getCate($category)->text;
        }else{
            $cateinf = __('process.all');
        }
        $lang = \App::getLocale();
        $services = Store::getSearch($lang, "", "", null, null, $limit, $sort, $category, $freecost, $filtercatory);
        $filtercates = Store::childCate($category);
        $funclists = Store::getFuncKind();
        return view('front.auth.item_list', compact('services', 'filtercates', 'categorys', 'cateinf', 'sort', 'limit', 'category', 'freecost', 'filtercatory', 'funclists'));
    }

    public function allfuntion(Request $request)
    {
        $request->validate([
        ]);

        $categorys = ServiceCategory::getCate();
        $lang = \App::getLocale();
        $funclists = Store::getFuncKind();
        return view('front.auth.item_list2', compact('funclists', 'categorys' ));
    }

    public function funtioninf(Request $request)
    {
        $request->validate([
        ]);

        $categorys = ServiceCategory::getCate();
        $lang = \App::getLocale();
        $funclists = Store::getFuncKind();
        $functionid=0;
        $funcioninf = Store::getFuncInfo($request->id);
        $services = Store::getFuncSearch($lang, $funcioninf->service);
        return view('front.auth.item_list3', compact('funclists', 'categorys', 'funcioninf', 'services' ));
    }

    public function pick(Request $request)
    {

        if(\Auth::guard('user')->check())
        {
            $myid = \Auth::guard('user')->user()->id;

            $pickid = \App\Models\Client\MyPick::where('client_id', $myid)->where('service_id', $request->svcid)->count();
            if($pickid == 0)
            {
                $review = new MyPick();

                $review->client_id = $myid;
                $review->service_id = $request->svcid;
                $review->save();
                return response()->json([ 'code' => 200, 'message' => 1]);
            }else{
                \App\Models\Client\MyPick::where('client_id', $myid)->where('service_id', $request->svcid)->delete();
                return response()->json([ 'code' => 200, 'message' => 0]);
            }
            return response()->json([ 'code' => 200, 'message' => 1]);
        }else{
            return response()->json(['code' => 201, 'message' => 0]);
        }
    }


    /**
     * 문의 등록
     *
     * @param Illuminate\Http\Request $request
     */
    public function add_inquiry(Request $request)
    {
        $inquiry = new Inquiry();

        $client_id = \Auth::guard('user')->user()->getKey();
        $inquiry->lang = \Lang::getLocale();
        $inquiry->client_id = $client_id;
        $inquiry->service_id = $request->service_id;
        $inquiry->type = $request->type;
        $inquiry->title = $request->title;
        $inquiry->content = $request->content;

        if ($request->hasFile('question_file'))
            $inquiry->question_file = $request->question_file->store('client/question', 'public');

        $inquiry->visible = $request->visible;
        $inquiry->save();

        $alim = new \App\Models\Agent\Alim();

        $alim->agent_id = \App\Models\Agent\Service::find($request->service_id)->agent_id;
        $alim->content = '사용자문의가 접수되었습니다.';
        $alim->type = 4;
        $alim->save();

        // 메시지발송
        $phone = \App\Models\Agent\Service::find($request->service_id)->contact_phone;
        $response = Http::asForm()
        ->withHeaders([
            'Authorization' => config('services.phone_api.authorization')
        ])
        ->post(config('services.phone_api.url'), [
            'phone' => str_replace("-", "", $phone),
            'msg' => '[MAPSTREND] 고객님의 서비스에 대한 문의가 접수 되었습니다. '.PHP_EOL.config('app.domain.agent'),
        ]);

        if($response->json()['code'] != 200)
        {
            return response()->json(['code' => 402, 'error' => __('messages.send_fail')]);
        }

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);
    }

    /**
     * 서비스 신청 등록
     *
     * @param Illuminate\Http\Request $request
     */
    public function add_servicereq(Request $request)
    {
        $newsvc = new \App\Models\Client\Service();

        $pay_options = explode("_", $request->pay_option);

        if(count($pay_options) <= 3){
            $service_option = '인앱';
        }else{
            $service_option = $pay_options[5];
        }

        $service = Service::find($request->service_id);
        $service->request_cnt = $service->request_cnt+1;
        $service->save();

        $client_id = \Auth::guard('user')->user()->getKey();
        $newsvc->lang = \Lang::getLocale();
        $newsvc->client_id = $client_id;
        $newsvc->service_id = $request->service_id;
        $newsvc->site_id = $request->site_id;
        $newsvc->service_option = $service_option;
        $newsvc->period_type = $pay_options[2];
        $newsvc->period = $pay_options[1];
        $newsvc->order_no = $request->order_no;
        $newsvc->save();

        $reapply = \App\Models\Client\Service::where('client_id', $client_id)
                                                ->where('service_id', $request->service_id)
                                                ->where('site_id', $request->site_id)
                                                ->where('process', 5)
                                                ->first();

        if($reapply){
            $reapply->reapply = 1;
            $reapply->save();
        }

        \App\Models\Client\Service::where('client_id', $newsvc->client_id)
                                        ->where('service_id', $newsvc->service_id)
                                        ->where('site_id', $newsvc->site_id)
                                        ->update(['review_flag' => 1]);

        //인앱서비스 신청시 client_id , client_sid , hmac 값을 같이 넘긴다
        $client_site = \App\Models\Client\Site::find($request->site_id);

        $hmac_client_id = \Auth::guard('user')->user()->account;
        $hmac_client_sid = $client_site->client_sid;
        $hmac_timestamp = time();
        $hmac_query = "client_id=".$hmac_client_id."&client_sid=".$hmac_client_sid."&timestamp=".$hmac_timestamp;
        $hmac_secret = "MAPSTREND";
        $hmac = base64_encode(hash_hmac('sha256',$hmac_query,$hmac_secret,true));

        return response()->json([
            'code' => 200,
            'message' => __('messages.save'),
            'hmac_query' => $hmac_query,
            'hmac' => $hmac
        ]);
    }

    /**
     * 서비스 신청 등록
     *
     * @param Illuminate\Http\Request $request
     */
    public function reqsvcpay(Request $request)
    {
        header("Content-type: text/html; charset=utf-8");
        $target_URL = config('services.kcp.target_url');

        $pay_options = explode("_", $request->pay_option);

        //==========================================================================
        //    요청정보
        //--------------------------------------------------------------------------
        $tran_cd            = $_POST["tran_cd"]; // 요청코드
        $site_cd            = $_POST["site_cd"]; // 사이트코드
        $kcp_cert_info      = config('services.kcp.kcp_cert_info');
        $enc_data           = $_POST["enc_data"]; // 암호화 인증데이터
        $enc_info           = $_POST["enc_info"]; // 암호화 인증데이터
        $ordr_mony          = $pay_options[3]; // 결제요청금액   ** 1 원은 실제로 업체에서 결제하셔야 될 원 금액을 넣어주셔야 합니다. 결제금액 유효성 검증 **
        if($pay_options[4] == 1)
        {
            $ordr_mony .= "00";
        }
        //= -------------------------------------------------------------------------- =
        $use_pay_method     = $_POST["use_pay_method"]; // 결제 방법
        $ordr_idxx          = $_POST["ordr_idxx"]; // 주문번호

        $data = array( "tran_cd"        => $tran_cd,
                    "site_cd"        => $site_cd,
                    "kcp_cert_info"  => $kcp_cert_info,
                    "enc_data"       => $enc_data,
                    "enc_info"       => $enc_info,
                    "ordr_mony"      => $ordr_mony
                    );

        $req_data = json_encode($data);

        $header_data = array( "Content-Type: application/json", "charset=utf-8" );

        // API REQ
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $target_URL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_data);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // API RES
        $res_data  = curl_exec($ch);
        // RES JSON DATA Parsing
        $json_res = json_decode($res_data, true);

        $res_cd = $json_res["res_cd"];
        $res_msg = $json_res["res_msg"];

        // $res_data = {"order_no":"2_1_5","mall_taxno":"1138521083","res_msg":"정상처리","trace_no":"","va_name":"홍길동","va_date":"20220616235959","shop_user_id":"1","res_vat_mny":"91","res_tax_flag":"TG01","van_txid":"DP002022060900642961","van_cd":"SCWR","bankcode":"BK11","amount":"1000","van_apptime":"20220609103013","res_free_mny":"0","pay_method":"PAVC","res_cd":"0000","escw_yn":"N","app_time":"20220609103013","tno":"20220609985470","res_en_msg":"processing completed","res_tax_mny":"909","depositor":"한국사이버결제","bankname":"NH농협","account":"T1109260001857"}

        $newsvc = new \App\Models\Client\Service();

        $service = Service::find($request->service_id);
        $service->request_cnt = $service->request_cnt+1;
        $service->save();

        $client_id = \Auth::guard('user')->user()->getKey();
        $newsvc->lang = \Lang::getLocale();
        $newsvc->client_id = $client_id;
        $newsvc->service_id = $request->service_id;
        $newsvc->site_id = $request->site_id;
        $newsvc->service_option = $pay_options[5];
        $newsvc->period_type = $pay_options[2];
        $newsvc->period = $pay_options[1];
        $newsvc->order_no = $ordr_idxx;
        $newsvc->save();

        $newpay = new Payment();
        $newpay->lang = \Lang::getLocale();
        $newpay->client_service_id = $newsvc->id;
        $newpay->client_id = $client_id;
        $newpay->agent_id = $newsvc->service->agent_id;
        $newpay->service_id = $request->service_id;
        $newpay->site_id = $request->site_id;
        $newpay->plan_id = $pay_options[0];//$request->svc_plan_option_name;
        $newpay->order_no = $json_res["order_no"];
        $newpay->amount = intval($pay_options[3]);//$request->svc_plan_option_amount;
        $newpay->currency = '￦'; // config('app.currency.'.$pay_options[4]);//$request->svc_plan_option_currency;
        $newpay->payment_type = 0;
        $newpay->service_start_at = $newsvc->service_start_at;
        $newpay->service_end_at = ($newsvc->service_end_at) ? $newsvc->service_end_at : NULL;
        $newpay->save();

        $reapply = \App\Models\Client\Service::where('client_id', $client_id)
                                                ->where('service_id', $request->service_id)
                                                ->where('site_id', $request->site_id)
                                                ->where('process', 5)
                                                ->first();

        if($reapply){
            $reapply->reapply = 1;
            $reapply->save();
        }

        \App\Models\Client\Service::where('client_id', $newsvc->client_id)
                                        ->where('service_id', $newsvc->service_id)
                                        ->where('site_id', $newsvc->site_id)
                                        ->update(['review_flag' => 1]);

        $request->session()->put('svc_ok' , 1);
        return redirect('service/detail/'.$request->service_id);
    }

    /**
     * 서비스 신청 등록 Mobile
     *
     * @param Illuminate\Http\Request $request
     */
    public function mreqsvcpay(Request $request)
    {
        header("Content-type: text/html; charset=utf-8");
        $target_URL = config('services.kcp.target_url');

        $pay_options = explode("_", $request->param_opt_1);

        if(!array_key_exists("tran_cd", $_POST))
        {
            return redirect('service/detail/'.$request->param_opt_2);
        }
        //==========================================================================
        //    요청정보
        //--------------------------------------------------------------------------
        $tran_cd            = $_POST["tran_cd"]; // 요청코드
        $site_cd            = $_POST["site_cd"]; // 사이트코드
        $kcp_cert_info      = config('services.kcp.kcp_cert_info');
        $enc_data           = $_POST["enc_data"]; // 암호화 인증데이터
        $enc_info           = $_POST["enc_info"]; // 암호화 인증데이터
        $ordr_mony          = $pay_options[3]; // 결제요청금액   ** 1 원은 실제로 업체에서 결제하셔야 될 원 금액을 넣어주셔야 합니다. 결제금액 유효성 검증 **
        if($pay_options[4] == 1)
        {
            $ordr_mony .= "00";
        }
        //= -------------------------------------------------------------------------- =
        $use_pay_method     = $_POST["use_pay_method"]; // 결제 방법
        $ordr_idxx          = $_POST["ordr_idxx"]; // 주문번호

        $data = array( "tran_cd"        => $tran_cd,
                    "site_cd"        => $site_cd,
                    "kcp_cert_info"  => $kcp_cert_info,
                    "enc_data"       => $enc_data,
                    "enc_info"       => $enc_info,
                    "ordr_mony"      => $ordr_mony
                    );

        $req_data = json_encode($data);

        $header_data = array( "Content-Type: application/json", "charset=utf-8" );

        // API REQ
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $target_URL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_data);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // API RES
        $res_data  = curl_exec($ch);
        // RES JSON DATA Parsing
        $json_res = json_decode($res_data, true);

        $res_cd = $json_res["res_cd"];
        $res_msg = $json_res["res_msg"];

        // $res_data = {"order_no":"2_1_5","mall_taxno":"1138521083","res_msg":"정상처리","trace_no":"","va_name":"홍길동","va_date":"20220616235959","shop_user_id":"1","res_vat_mny":"91","res_tax_flag":"TG01","van_txid":"DP002022060900642961","van_cd":"SCWR","bankcode":"BK11","amount":"1000","van_apptime":"20220609103013","res_free_mny":"0","pay_method":"PAVC","res_cd":"0000","escw_yn":"N","app_time":"20220609103013","tno":"20220609985470","res_en_msg":"processing completed","res_tax_mny":"909","depositor":"한국사이버결제","bankname":"NH농협","account":"T1109260001857"}

        $newsvc = new \App\Models\Client\Service();

        $service = Service::find($request->param_opt_2);
        $service->request_cnt = $service->request_cnt+1;
        $service->save();

        $client_id = \Auth::guard('user')->user()->getKey();
        $newsvc->lang = \Lang::getLocale();
        $newsvc->client_id = $client_id;
        $newsvc->service_id = $request->param_opt_2;
        $newsvc->site_id = $request->param_opt_3;
        $newsvc->service_option = $pay_options[5];
        $newsvc->period_type = $pay_options[2];
        $newsvc->period = $pay_options[1];
        $newsvc->order_no = $ordr_idxx;
        $newsvc->save();

        $newpay = new Payment();
        $newpay->lang = \Lang::getLocale();
        $newpay->client_service_id = $newsvc->id;
        $newpay->client_id = $client_id;
        $newpay->agent_id = $newsvc->service->agent_id;
        $newpay->service_id = $request->param_opt_2;
        $newpay->site_id = $request->param_opt_3;
        $newpay->plan_id = $pay_options[0];//$request->svc_plan_option_name;
        $newpay->order_no = $json_res["order_no"];
        $newpay->amount = intval($pay_options[3]);//$request->svc_plan_option_amount;
        $newpay->currency = '￦'; // config('app.currency.'.$pay_options[4]);//$request->svc_plan_option_currency;
        $newpay->payment_type = 0;
        $newpay->service_start_at = $newsvc->service_start_at;
        // $newpay->service_end_at = $newsvc->service_end_at;
        $newpay->service_end_at = ($newsvc->service_end_at) ? $newsvc->service_end_at : NULL;
        $newpay->save();

        $reapply = \App\Models\Client\Service::where('client_id', $client_id)
                                                ->where('service_id', $request->service_id)
                                                ->where('site_id', $request->site_id)
                                                ->where('process', 5)
                                                ->first();

        if($reapply){
            $reapply->reapply = 1;
            $reapply->save();
        }

        \App\Models\Client\Service::where('client_id', $newsvc->client_id)
                                        ->where('service_id', $newsvc->service_id)
                                        ->where('site_id', $newsvc->site_id)
                                        ->update(['review_flag' => 1]);

        return redirect('service/detail/'.$request->param_opt_2);
    }
    /**
     * 서비스 신청 등록 Mobile
     *
     * @param Illuminate\Http\Request $request
     */
    public function kcp_api_trade_reg(Request $request)
    {
        header("Content-type: text/html; charset=utf-8");
        $target_URL = config('services.kcp.Mtarget_url');

        $pay_options = explode("_", $request->pay_option);

        //==========================================================================
        //    요청정보
        //--------------------------------------------------------------------------
        $site_cd            = $_POST["site_cd"]; // 사이트코드
        $kcp_cert_info      = config('services.kcp.kcp_cert_info');
        $ordr_idxx          = $_POST[ "ordr_idxx" ]; // 주문번호
        $good_mny           = $_POST[ "good_mny" ]; // 결제 금액
        $good_name          = $_POST[ "good_name" ]; // 상품명
        $pay_method         = $_POST[ "pay_method" ]; // 결제수단
        $Ret_URL            = $_POST[ "Ret_URL" ]; // 리턴 URL
        /* ============================================================================== */
        $actionResult       = $_POST[ "ActionResult" ]; // pay_method에 매칭되는 값 (인증창 호출 시 필요)
        $van_code           = $_POST[ "van_code" ]; // (포인트,상품권 인증창 호출 시 필요)

        $pay_option           = $_POST[ "pay_option" ];
        $service_id           = $_POST[ "service_id" ];
        $site_id           = $_POST[ "site_id" ];

        $data = array(
            "site_cd"        => $site_cd,
            "kcp_cert_info"  => $kcp_cert_info,
            "ordr_idxx"      => $ordr_idxx,
            "good_mny"       => $good_mny,
            "good_name"      => $good_name,
            "pay_method"     => $pay_method,
            "Ret_URL"        => $Ret_URL,
            "escw_used"      => "N",
            "user_agent"     => "",
        );

        $req_data = json_encode($data);

        $header_data = array( "Content-Type: application/json", "charset=utf-8" );

        // API REQ
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $target_URL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_data);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // API RES
        $res_data  = curl_exec($ch);

        /*
        ==========================================================================
        거래등록 응답정보
        --------------------------------------------------------------------------
        */
        $res_cd      = ""; // 응답코드
        $res_msg     = ""; // 응답메세지
        $approvalKey = ""; // 거래등록키
        $traceNo     = ""; // 추적번호
        $PayUrl      = ""; // 거래등록 PAY URL

        // RES JSON DATA Parsing
        $json_res = json_decode($res_data, true);

        $res_cd      = $json_res["Code"];
        $res_msg     = $json_res["Message"];
        $approvalKey = $json_res["approvalKey"];
        $traceNo     = $json_res["traceNo"];
        $PayUrl      = $json_res["PayUrl"];

        curl_close($ch);
        if ( $res_cd == "0000" )
        {
            return view('store.service.kcp_api_trade_reg', [
                'res_cd' => $res_cd,
                'res_msg' => $res_msg,
                'site_cd' => $site_cd,
                'ordr_idxx' => $ordr_idxx,
                'good_mny' => $good_mny,
                'good_name' => $good_name,
                'pay_method' => $pay_method,
                'actionResult' => $actionResult,
                'van_code' => $van_code,
                'Ret_URL' => $Ret_URL,
                'approvalKey' => $approvalKey,
                'traceNo' => $traceNo,
                'PayUrl' => $PayUrl,
                'pay_option' => $pay_option,
                'service_id' => $service_id,
                'site_id' => $site_id,
            ]);
        }else{
            return redirect('service/detail/'.$service_id);
        }
    }

    /**
     * 서비스 신청 등록 Mobile
     *
     * @param Illuminate\Http\Request $request
     */
    public function order_mobile(Request $request)
    {
        header("Content-type: text/html; charset=utf-8");
        // 거래등록 응답 값
        $approvalKey    = $_POST[ "approvalKey" ]; // 거래등록키
        $traceNo        = $_POST[ "traceNo" ]; // 추적번호
        $PayUrl         = $_POST[ "PayUrl" ]; // 거래등록 PAY URL
        // 인증시 필요한 결제수단 세팅 값
        $pay_method     = $_POST[ "pay_method" ]; // 결제수단
        $actionResult   = $_POST[ "ActionResult" ];
        $van_code       = $_POST[ "van_code" ];
        // 가맹점 리턴 URL
        $Ret_URL        = $_POST[ "Ret_URL"     ];

        $req_tx          = ""; // 요청 종류
        $res_cd          = ""; // 응답 코드
        $site_cd         = $_POST[ "site_cd" ]; // 사이트 코드
        $tran_cd         = ""; // 트랜잭션 코드
        $ordr_idxx       = $_POST[ "ordr_idxx" ]; // 쇼핑몰 주문번호
        $good_name       = $_POST[ "good_name" ]; // 상품명
        $good_mny        = $_POST[ "good_mny" ]; // 결제 총금액
        $buyr_name       = \Auth::guard('user')->user()->manager_name; // 주문자명
        $buyr_tel1       = ""; // 주문자 전화번호
        $buyr_tel2       = ""; // 주문자 핸드폰 번호
        $buyr_mail       = \Auth::guard('user')->user()->manager_email; // 주문자 E-mail 주소
        $use_pay_method  = ""; // 결제 방법
        $enc_info        = ""; // 암호화 정보
        $enc_data        = ""; // 암호화 데이터
        $cash_yn         = "";
        $cash_tr_code    = "";
        /* 기타 파라메터 추가 부분 - Start - */
        $pay_option           = $_POST[ "pay_option" ];
        $service_id           = $_POST[ "service_id" ];
        $site_id           = $_POST[ "site_id" ];
        /* 기타 파라메터 추가 부분 - End -   */
        /* kcp와 통신후 kcp 서버에서 전송되는 결제 요청 정보 */
        if(array_key_exists("enc_info", $_POST))
        {
            $req_tx          = $_POST[ "req_tx"]; // 요청 종류
            $res_cd          = $_POST[ "res_cd" ]; // 응답 코드
            $site_cd         = $_POST[ "site_cd" ]; // 사이트 코드
            $tran_cd         = $_POST[ "tran_cd" ]; // 트랜잭션 코드
            $ordr_idxx       = $_POST[ "ordr_idxx" ]; // 쇼핑몰 주문번호
            $good_name       = $_POST[ "good_name" ]; // 상품명
            $good_mny        = $_POST[ "good_mny" ]; // 결제 총금액
            $buyr_name       = $_POST[ "buyr_name" ]; // 주문자명
            $buyr_tel1       = $_POST[ "buyr_tel1" ]; // 주문자 전화번호
            $buyr_tel2       = $_POST[ "buyr_tel2" ]; // 주문자 핸드폰 번호
            $buyr_mail       = $_POST[ "buyr_mail" ]; // 주문자 E-mail 주소
            $use_pay_method  = $_POST[ "use_pay_method" ]; // 결제 방법
            $enc_info        = $_POST[ "enc_info" ]; // 암호화 정보
            $enc_data        = $_POST[ "enc_data" ]; // 암호화 데이터
            $cash_yn         = $_POST[ "cash_yn" ];
            $cash_tr_code    = $_POST[ "cash_tr_code" ];
            /* 기타 파라메터 추가 부분 - Start - */
            $pay_option           = $_POST[ "param_opt_1" ];
            $service_id           = $_POST[ "param_opt_2" ];
            $site_id           = $_POST[ "param_opt_3" ];
            /* 기타 파라메터 추가 부분 - End -   */
        }


        return view('store.service.order_mobile', [
            'approvalKey' => $approvalKey,// 거래등록키
            'traceNo' => $traceNo,// 추적번호
            'PayUrl' => $PayUrl,// 거래등록 PAY URL
            // 인증시 필요한 결제수단 세팅 값
            'pay_method' => $pay_method,// 결제수단
            'actionResult' => $actionResult,
            'van_code' => $van_code,
            // 가맹점 리턴 URL
            'Ret_URL' => $Ret_URL,
            /* kcp와 통신후 kcp 서버에서 전송되는 결제 요청 정보 */
            'req_tx' => $req_tx, // 요청 종류
            'res_cd' => $res_cd, // 응답 코드
            'site_cd' => $site_cd, // 사이트 코드
            'tran_cd' => $tran_cd, // 트랜잭션 코드
            'ordr_idxx' => $ordr_idxx, // 쇼핑몰 주문번호
            'good_name' => $good_name, // 상품명
            'good_mny' => $good_mny, // 결제 총금액
            'buyr_name' => $buyr_name, // 주문자명
            'buyr_tel1' => $buyr_tel1, // 주문자 전화번호
            'buyr_tel2' => $buyr_tel2, // 주문자 핸드폰 번호
            'buyr_mail' => $buyr_mail, // 주문자 E-mail 주소
            'use_pay_method' => $use_pay_method, // 결제 방법
            'enc_info' => $enc_info, // 암호화 정보
            'enc_data' => $enc_data, // 암호화 데이터
            'cash_yn' => $cash_yn,
            'cash_tr_code' => $cash_tr_code,
            'pay_option' => $pay_option,
            'service_id' => $service_id,
            'site_id' => $site_id,
        ]);
    }

    /**
     * 리뷰 등록
     *
     * @param Illuminate\Http\Request $request
     */
    public function add_review(Request $request)
    {
        $client_id = \Auth::guard('user')->user()->getKey();
        $pickid = Review::where('client_id', $client_id)->where('service_id', $request->service_id)->count();
        // if($pickid == 0)
        // {
            $review = new Review();
            $review->lang = \Lang::getLocale();
            $review->client_id = $client_id;
            $review->agent_id = \App\Models\Agent\Service::find($request->service_id)->agent_id;
            $review->service_id = $request->service_id;
            $review->rating = $request->rating;
            $review->content = $request->content;
            $review->save();

            $alim = new \App\Models\Agent\Alim();

            $alim->agent_id = \App\Models\Agent\Service::find($request->service_id)->agent_id;
            $alim->content = \App\Models\Agent\Service::find($request->service_id)->name.' 서비스 리뷰가 등록되었습니다.';
            $alim->type = 6;
            $alim->save();

            \App\Models\Client\Service::where('client_id', $review->client_id)
                                        ->where('service_id', $review->service_id)
                                        ->update(['review_flag' => 0]);

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);
    }

    /**
     * 만족율 200%
     *
     * @param Illuminate\Http\Request $request
     */
    public function satisfylist(Request $request)
    {
        $request->validate([
        ]);
        $prev_monday = date("Y-m-d", strtotime("last week monday"));
        $prev_sunday = date("Y-m-d", strtotime("last week sunday"));

        $sort = 0;//:1 = 최신순, :2 = 판매량순, :3 = 낮은가격순, :4 = 별점순, :0=감추가

        $limit = 8;
        if($request->limit)
        {
            $limit = $request->limit;
        }

        $category = -1;
        $freecost = 0;
        $filtercatory="";
        $categorys = ServiceCategory::getCate();//Header에 필요함
        $funclists = Store::getFuncKind();//Header에 필요함

        $cateinf = __('main.percent');

        $lang = \App::getLocale();
        $services = Store::getSatisfy200($prev_monday, $prev_sunday, $limit);
        $filtercates = [];

        return view('front.auth.item_list', compact('services', 'filtercates', 'categorys', 'cateinf', 'sort', 'limit', 'category', 'freecost', 'filtercatory', 'funclists'));
    }

     /**
      * 필수 마테크
      *
      * @param Illuminate\Http\Request $request
      */
    public function matechlist(Request $request)
    {
        $sort = 0;//:1 = 최신순, :2 = 판매량순, :3 = 낮은가격순, :4 = 별점순, :0=감추가

        $limit = 8;
        if($request->limit)
        {
            $limit = $request->limit;
        }

        $category = -1;
        $freecost = 0;
        $filtercatory="";
        $categorys = ServiceCategory::getCate();//Header에 필요함
        $funclists = Store::getFuncKind();//Header에 필요함

        $cateinf = __('main.matech');

        $lang = \App::getLocale();
        $services = Store::getMatech($limit);
        $filtercates = [];

        return view('front.auth.item_list', compact('services', 'filtercates', 'categorys', 'cateinf', 'sort', 'limit', 'category', 'freecost', 'filtercatory', 'funclists'));
    }

    public function get_order_no(Request $request)
    {
        $cur_date = date('Y-m-d');

        $payment = \App\Models\Payment::where('created_at', 'LIKE', "{$cur_date}%")
                                        ->whereNotNull('order_no')
                                        ->where('order_no', '<>', '')
                                        ->orderBy('id','desc')
                                        ->first();

        $order_no = '';

        if($payment) {
            $tmp = (int)substr($payment->order_no, 8);
            $order_no = date('Ymd').substr(('00'.($tmp + 1)), -3);
        }
        else
            $order_no = date('Ymd').'001';

        return response()->json([
            'code' => 200,
            'order_no' => $order_no
        ]);
    }

	/**
      * 약관 페이지
      *
      * @param Illuminate\Http\Request $request
    */
    public function terms(Request $request , $type)
    {
        $viewdata = [
            'categorys' => ServiceCategory::getCate(),
            'funclists' => Store::getFuncKind(),
            'terms' => Terms::select('content')->where('type', $type)->first(),
        ];

        return view('store.terms', $viewdata);
    }

    /**
      * FAQ 페이지
      *
      * @param Illuminate\Http\Request $request
    */
    public function faq(Request $request)
    {
        $viewdata = [
            'categorys' => ServiceCategory::getCate(),
            'funclists' => Store::getFuncKind(),
            'faq' => Faq::orderby('order', 'asc')->get(),
        ];

        return view('store.faq', $viewdata);
    }

    /**
      * MAPS CONTENT 페이지
      *
      * @param Illuminate\Http\Request $request
    */
    public function mapscontent(Request $request)
    {
        $top_array = [];

        //상단 6개 컨텐츠
        $top_list = Content::orderBy('order', 'asc')->limit(6)->get();

        //하단 컨텐츠는 상단6개를 제외하고 뿌려주기 위해 id값을 담는다
        foreach($top_list as $list){
            array_push($top_array, $list->id);
        }

        //배너등록 컨텐츠 메인 한개
        $banner_item = Content::where('banner',1)->inRandomOrder()->first();

        $banner_list = [];
        if($banner_item){
            //배너등록 함께보면 좋은 컨텐츠
            $banner_list = Content::where('banner',1)->where('id', '<>', $banner_item->id)->inRandomOrder()->get();
        }

        //하단 컨텐츠
        $bottom_list = Content::whereNotIn('id',$top_array)->orderBy('order', 'asc')->offset(0)->limit(6)->get();

        $viewdata = [
            'categorys' => ServiceCategory::getCate(),
            'funclists' => Store::getFuncKind(),
            'top_list' => $top_list,
            'banner_item' => $banner_item,
            'banner_list' => $banner_list,
            'bottom_list' => $bottom_list,
            'top_array' => implode(',', $top_array),
        ];

        return view('store.maps_content', $viewdata);
    }

    /**
      * MAPS CONTENT 페이지 백업본
      *
      * @param Illuminate\Http\Request $request
    */
    public function mapscontent_bak(Request $request)
    {

        $viewdata = [
            'categorys' => ServiceCategory::getCate(),
            'funclists' => Store::getFuncKind(),
        ];

        return view('store.maps_content_bak', $viewdata);
    }

    /**
      * MAPS CONTENT 페이지 더보기
      *
      * @param Illuminate\Http\Request $request
    */
    public function mapscontent_more(Request $request)
    {
        $top_array = explode(',', $request->top_list);

        //더보기 컨텐츠
        $bottom_list = Content::whereNotIn('id',$top_array)->orderBy('order', 'asc')->offset($request->offset)->limit(6)->get();

        $viewdata = [
            'bottom_list' => $bottom_list,
        ];

        return response()->json([
            'html' => view('store.maps_content_more', $viewdata)->render(),
        ]);
    }

    /**
      * MAPS CONTENT_DETAIL 페이지
      *
      * @param Illuminate\Http\Request $request
    */
    public function mapscontentdetail (Request $request)
    {

        $id = $request->content;
        $content = Content::find($id);
        $content->hits = $content->hits + 1;
        $content->save();

        $viewdata = [
            'categorys' => ServiceCategory::getCate(),
            'funclists' => Store::getFuncKind(),
            'content' => $content,
        ];

        return view('store.maps_content_detail', $viewdata);
    }

    /**
      * FAQ 조회수 증가
      *
      * @param Illuminate\Http\Request $request
    */
    public function faq_hits(Request $request)
    {
        $faq_id = $request->id;
        $faq = Faq::find($faq_id);
        $faq->hits = $faq->hits+1;
        $faq->save();

        return response()->json([
            'code' => 200,
        ]);
    }

    /**
      * byapps appid 복호화
      *
      * @param secret key , encoding string
    */
    protected function aes_decrypt ($key, $value)
    {
        $KEY_128 = substr($key, 0, 128 / 8);
        $KEY_256 = substr($key, 0, 256 / 8);
        return openssl_decrypt($value, 'AES-256-CBC', $KEY_256, 0, $KEY_128);
    }

    /**
      * session 삭제
      *
      * @param Illuminate\Http\Request $request
    */
    public function modal_status(Request $request)
    {
        $request->session()->forget('svc_ok');

        return response()->json([
            'code' => 200,
        ]);
    }
}
