<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client\Service;
use App\Models\Client\Inquiry;
use App\Models\Client\Review;
use App\Models\Client\Banner;
use App\Models\Client\MyPick;
use App\Models\Notice;
use App\Models\Payment;
use App\Rules\{PhoneNumber, Password, Url, BusinessNo};
use Illuminate\Http\Request;
use App\Models\Client\Site;
use Illuminate\Validation\Rule;
use Validator;
use Hash;
use View;
use DateTime;
use Illuminate\Support\Facades\Http;
use Guide;

class ClientController extends Controller
{
    /**
     * Dashboard View
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function __invoke(Request $request)
    {
        if ($request->site_id)
            $request->user()->sel_site_id = $request->site_id;
        else {
            if ($request->user()->sel_site_id == 0)
                $request->user()->sel_site_id = \Auth::user()->site->count() > 0 ? \Auth::user()->site->first()->id : 0;
        }

        $request->user()->save();

        View::share('activeMenu', 'client.dashboard');

        $use_service_list = null;

        if (\Auth::user()->current_site) {
            $use_service_list = Service::where('client_id', \Auth::user()->id)
                        ->where('site_id', \Auth::user()->current_site->id)
                        ->where('lang', \Lang::getLocale())
                        ->where('process', '<>', 5)
                        ->orderBy('id','desc')
                        ->get();
        } else {
            $use_service_list = Service::where('client_id', \Auth::user()->id)
                        ->where('lang', \Lang::getLocale())
                        ->orderBy('id','desc')
                        ->where('process', '<>', 5)
                        ->get();
        }

        $my_pick_list = MyPick::where('client_id', \Auth::user()->id)
                        ->get();

        $notice_list = Notice::whereIn('type', [0, 1])
                    ->orderBy('id','desc')
                    ->limit(5)
                    ->get();

        $banner = Banner::where('type', 4)
                        ->where('status', 1)
                        ->where('st_date', '<=', date('Y-m-d'))
                        ->where('end_date', '>=', date('Y-m-d'))
                        ->orderBy('id','desc')
                        ->first();
        $banner_img = '';
        $img = Banner::where('type', 4)
                        ->where('status', 1)
                        ->where('st_date', '<=', date('Y-m-d'))
                        ->where('end_date', '>=', date('Y-m-d'))
                        ->orderBy('id','desc')
                        ->first();

        if($img)
            $banner_img = \Storage::url($img[\Lang::getLocale()]);

        $popup_list = Notice::whereIn('type', [0, 1])
            ->where('popup', 1)
            ->whereNotNull('img')
            ->where('img', '<>', '')
            ->orderBy('id','asc')
            ->get();


        $hmac_client_id = \Auth::user()->account;
        $hmac_query = "";
        $hmac = "";
        if( \Auth::user()->current_site ){
            $hmac_client_sid = \Auth::user()->current_site->client_sid;
            $hmac_timestamp = time();
            $hmac_query = "client_id=".$hmac_client_id."&client_sid=".$hmac_client_sid."&timestamp=".$hmac_timestamp;
            $hmac_secret = "MAPSTREND";
            $hmac = base64_encode(hash_hmac('sha256',$hmac_query,$hmac_secret,true));
        }

        return view('client.dashboard', compact('use_service_list', 'my_pick_list', 'notice_list', 'banner_img', 'banner', 'popup_list', 'hmac_query', 'hmac'));
    }

    /**
     * 공지사항
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function notice(Request $request)
    {
        $list = Notice::whereIn('type', [0, 1])
                ->orderBy('id','desc')
                ->paginate(20);

        return view('client.notice', compact('list'));
    }

    public function my_info(Request $request)
    {
        if (
            $request->session()->has('password_auth_time') &&
            $request->session()->get('password_auth_time') > now()->subMonths(10) &&
            $request->session()->get('password_auth_id') == $request->user()->getKey()
        ) {
            $request->session()->put('password_auth_time', now());

            $apply = \App\Models\UserDrop::where('account', \Auth::user()->account)->whereNull('dropped_at')->orderBy('id','desc')->first();

            $drop_options = \App\Models\Cms\QuestionOption::where('type',3)->first();
            $d_option = explode(',', $drop_options->content);

            return view('client.auth.my', [
                'user' => $request->user(),
                'banks' => config('app.banks'),
                'tab' => $request->tab ?? 1,
                'drop' => $apply ?? null,
                'options' => $d_option
            ]);
        }
        else
            return view('client.auth.password');
    }

    /**
      * 회원탈퇴
      *
      * @param Illuminate\Http\Request $request
    */
    public function goodbye(Request $request)
    {

        $apply = \App\Models\UserDrop::where('account', \Auth::user()->account)->whereNull('dropped_at')->orderBy('id','desc')->first();

        if($apply){
            $code = 401;
        }else{
            $drop = new \App\Models\UserDrop();
            $drop->client_id = \Auth::user()->getKey();
            $drop->user_type = '고객사';
            $drop->account = \Auth::user()->account;
            $drop->company_name = \Auth::user()->company_name;
            $drop->reason = $request->reason;
            $drop->save();

            $code = 200;
        }

        return response()->json([
            'code' => $code
        ]);
    }



    /**
      * guide
      *
      * @param Illuminate\Http\Request $request
    */
    public function guide(Request $request)
    {
        $sites = \Auth::user()->site;
        return view('client.guide' , compact('sites'));
    }

    /**
      * 스크립트 설치 요청
      *
      * @param Illuminate\Http\Request $request
    */
    public function script_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_name' => ['required', 'string'],
            'solution_shop' => ['required', 'integer'],
            'admin_url' => ['required', new Url],
            'account' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (!$validator->passes())
            return response()->json(['code' => 401, 'error' => $validator->errors()]);

        $site = $request->site_name;
        $type = $request->solution_shop;
        $admin_url = $request->admin_url;
        $account = $request->account;
        $password = $request->password;

        $script = new \App\Models\Client\ScriptRequest;
        $script->name = $site;
        $script->hostname = $type;
        $script->admin_url = $admin_url;
        $script->account = $account;
        $script->password = $password;
        $script->save();

        return response()->json([
            'code' => 200
        ]);
    }


    /**
     * 사이트 목록
     *
     * @param Illuminate\Http\Request $request
     */
    public function site_list(Request $request)
    {
        $list = Site::where('client_id', $request->user()->getKey())
                        ->orderBy('id', 'desc')
                        ->get();

        $stop_cnt = [];
        foreach($list as $item){
            $cnt = $item->service->where('process','<>',4)->count();
            array_push($stop_cnt,$cnt);
        }

        return view('client.auth.site', compact('list','stop_cnt'));
    }

    /**
     * 사이트 등록
     *
     * @param Illuminate\Http\Request $request
     */
    public function create_site(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_name' => ['required', 'string', Rule::notIn($request->user()->site->pluck('name')->toArray())],
            'site_url' => ['required', new Url],
            'site_type' => 'required',
            'site_hostname' => 'required_if:site.type,0',
        ], [
            'site.name.not_in' => __('validation.uniquesite'),
        ]);

        if (!$validator->passes())
            return response()->json(['code' => 401, 'error' => $validator->errors()]);

        $redirect = false;

        if ($request->user()->site->count() == 0)
            $redirect = true;

        $site = Site::create([
            'client_sid' => \Str::random(40),
            'client_id' => $request->user()->getKey(),
            'name' => $request->input('site_name'),
            'url' => $request->input('site_url'),
            'type' => $request->input('site_type'),
            'hostname' => $request->has('site_type') ? $request->input('site_hostname') : null,
        ]);

        return response()->json([
            'code' => 200,
            'message' => __('messages.create.0'),
            'site' => $site,
            'redirect' => $redirect
        ]);
    }

    /**
     * 사이트 수정
     *
     * @param Illuminate\Http\Request $request
     */
    public function store_site(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_name' => ['required', 'string', Rule::notIn($request->user()->site->pluck('name')->toArray())],
            'site_url' => ['required', new Url],
            'site_type' => 'required',
            'site_hostname' => 'required_if:site.type,0',
        ], [
            'site.name.not_in' => __('validation.uniquesite'),
        ]);

        $site = Site::find($request->id);
        $site->name = $request->site_name;
        $site->url = $request->site_url;
        $site->type = $request->site_type;
        $site->hostname = $request->has('site_type') ? $request->site_hostname : null;

        $site->save();

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);
    }

    /**
     * 사이트 삭제
     *
     * @param Illuminate\Http\Request $request
     */
    public function delete_site(Request $request)
    {
        Site::find($request->id)->delete();

        return response()->json([
            'code' => 200,
            'message' => __('messages.delete.0')
        ]);
    }

    /**
     * 비밀번호 재확인
     *
     * @param Illuminate\Http\Request $request
     * @return array
     *
     * @throws Illuminate\Validation\ValidationException 유효성 검사 실패시
     */
    public function check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required'
        ]);

        if (!$validator->passes())
            return response()->json(['code' => 401, 'error' => $validator->errors()]);

        if (!Hash::check($request->password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'password' => __('validation.confirmed')
            ]);
        }
        $request->session()->put('password_auth_time', now());
        $request->session()->put('password_auth_id', $request->user()->getKey());

        return response()->json([
            'code' => 200,
            'message' => __('messages.password.success')
        ]);
    }

    /**
     * 회원정보 수정
     *
     * @param Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {
        if ($request->tab == 1) {
            $validator = Validator::make($request->all(), [
                'c_password' => 'same:password',
                'company_name' => 'string',
                'manager_email' => 'email|nullable',
                'business_no' => 'string|nullable', // new BusinessNo,
                'homepage_url' => 'string|nullable',
                'director_phone' => new PhoneNumber,
                'manager_name' => 'string|nullable',
                'manager_phone' => new PhoneNumber,
                'director_email' => 'email|nullable',
            ], [
                'c_password.same' => __('validation.confirmed'),
            ]);

            if (!$validator->passes())
                return response()->json(['code' => 401, 'error' => $validator->errors()]);

            if ($request->input('password', ''))
                $request->user()->password = Hash::make($request->password);

            if ($request->hasFile('business_registration'))
                $request->user()->business_registration = $request->business_registration->store('client/business_registration', 'public');

            $request->user()->fill($request->only(['company_name', 'business_no', 'director_name', 'address', 'order_report_number',
                            'specialized_field', 'homepage_url', 'director_phone', 'director_email', 'inquiry_time', 'manager_name',
                            'manager_phone', 'manager_email']));

            $request->user()->save();
        }
        else if ($request->tab == 2) {
            $validator = Validator::make($request->all(), [
                'tax_email' => 'email|nullable',
                'tax_business_no' => 'string|nullable', // new BusinessNo,
            ]);

            if (!$validator->passes())
                return response()->json(['code' => 401, 'error' => $validator->errors()]);

            if ($request->hasFile('tax_business_registration'))
                $request->user()->tax_business_registration = $request->tax_business_registration->store('client/tax_business_registration', 'public');

            $request->user()->fill($request->only(['account_holder', 'bank_name', 'account_number', 'tax_email', 'tax_company_name',
                'tax_business_no', 'tax_director_name', 'tax_address']));

            $request->user()->save();
        }

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);
    }

    /**
     * 마이서비스
     *
     * @param Illuminate\Http\Request $request
     */
    public function myservice(Request $request)
    {
        View::share('activeMenu', 'client.myservice');

        $services = [];
        $cnt1 = 0;
        $cnt2 = 0;
        $cnt3 = 0;
        $cnt4 = 0;
        $cnt5 = 0;
        $cnt6 = 0;

        if(\Auth::user()->current_site){
            $services = $request->user()->client_service
                            ->where('site_id', \Auth::user()->current_site->id)
                            ->where('process', '<>', 5);
            $cnt1 = $request->user()->client_service
                            ->where('site_id', \Auth::user()->current_site->id)
                            ->where('process', '<>', 5)
                            ->count();
            $cnt2 = $request->user()->client_service->where('process', 2)
                            ->where('site_id', \Auth::user()->current_site->id)
                            ->count();
            $cnt3 = $request->user()->client_service->where('process', 1)
                            ->where('site_id', \Auth::user()->current_site->id)
                            ->count();
            $cnt4 = $request->user()->client_service->where('process', 3)
                            ->where('site_id', \Auth::user()->current_site->id)
                            ->count();
            $cnt5 = $request->user()->client_service->where('process', 4)
                            ->where('site_id', \Auth::user()->current_site->id)
                            ->count();
            $cnt6 = $request->user()->client_service->where('process', 5)
                            ->where('site_id', \Auth::user()->current_site->id)
                            ->where('reapply', 0)
                            ->count();
        }

        return view('client.myservice' , compact('services','cnt1','cnt2','cnt3','cnt4','cnt5','cnt6'));

    }

    /**
     * 마이서비스 검색
     *
     * @param Illuminate\Http\Request $request
     */
    public function myservice_search(Request $request)
    {
        View::share('activeMenu', 'client.myservice');

        return view('client.myservice', [
            'services' => $request->user()->client_service
                            ->where('process', $request->kind)
                            ->where('reapply', 0)
                            ->where('site_id', \Auth::user()->current_site->id),
            'cnt1' => $request->user()->client_service
                            ->where('site_id', \Auth::user()->current_site->id)
                            ->where('process', '<>', 5)
                            ->where('reapply', 0)
                            ->count(),
            'cnt2' => $request->user()->client_service->where('process', 2)
                            ->where('site_id', \Auth::user()->current_site->id)
                            ->count(),
            'cnt3' => $request->user()->client_service->where('process', 1)
                            ->where('site_id', \Auth::user()->current_site->id)
                            ->count(),
            'cnt4' => $request->user()->client_service->where('process', 3)
                            ->where('site_id', \Auth::user()->current_site->id)
                            ->count(),
            'cnt5' => $request->user()->client_service->where('process', 4)
                            ->where('site_id', \Auth::user()->current_site->id)
                            ->count(),
            'cnt6' => $request->user()->client_service->where('process', 5)
                            ->where('site_id', \Auth::user()->current_site->id)
                            ->where('reapply', 0)
                            ->count()
        ]);
    }

    /**
     * 마이서비스 상태 설정
     *
     * @param Illuminate\Http\Request $request
     */
    public function update_service_process(Request $request)
    {
        $service = Service::where('id', $request->id)->first();
        $service->process = $request->process;
        $service->save();

        if($request->process == 4){

            $alim = new \App\Models\Agent\Alim();

            $alim->agent_id = \App\Models\Agent\Service::find($service->service_id)->agent_id;
            $alim->content = $service->site->name.' 서비스가 중지 되었습니다.';
            $alim->type = 7;
            $alim->save();

            // 메시지발송
            $phone = \App\Models\Agent\Service::find($service->service_id)->contact_phone;
            $response = Http::asForm()
            ->withHeaders([
                'Authorization' => config('services.phone_api.authorization')
            ])
            ->post(config('services.phone_api.url'), [
                'phone' => str_replace("-", "", $phone),
                'msg' => '[MAPSTREND] 고객님의 서비스에 \'중지\'가 접수 되었습니다. '.PHP_EOL.config('app.domain.agent'),
            ]);

            if($response->json()['code'] != 200)
            {
                return response()->json(['code' => 402, 'error' => __('messages.send_fail')]);
            }
        }

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);
    }

    /**
     * 마이서비스 환불
     *
     * @param Illuminate\Http\Request $request
     */
    public function refund_service(Request $request)
    {
        $service = Service::where('id', $request->client_service_id)->first();

        $service->process = 4;
        $service->save();

        $payment = Payment::where('client_service_id', $request->client_service_id)
                            ->orderBy('created_at', 'desc')
                            ->first();

        $payment->refund_flag = 1;
        $payment->refund_reason = $request->refund_reason;
        $payment->bank_name = $request->bank_name;
        $payment->account_num = $request->account_num;
        $payment->account_holder = $request->account_holder;
        $payment->refund_request_at = date('Y-m-d H:i:s');
        $payment->service_stop_at = date('Y-m-d H:i:s');
        $payment->save();

        $alim = new \App\Models\Agent\Alim();

        $alim->agent_id = \App\Models\Agent\Service::find($service->service_id)->agent_id;
        $alim->content = '환불요청이 접수되었습니다.';
        $alim->type = 2;
        $alim->save();

        // 메시지발송
        $phone = \App\Models\Agent\Service::find($service->service_id)->contact_phone;
        $response = Http::asForm()
        ->withHeaders([
            'Authorization' => config('services.phone_api.authorization')
        ])
        ->post(config('services.phone_api.url'), [
            'phone' => str_replace("-", "", $phone),
            'msg' => '[MAPSTREND] 고객님의 서비스에 \'환불\'이 접수 되었습니다. '.PHP_EOL.config('app.domain.agent'),
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
     * 마이서비스 리뷰 정보 얻기
     *
     * @param Illuminate\Http\Request $request
     */
    public function get_service_review(Request $request)
    {
        $review = Review::where('client_id', $request->client_id)
                        ->where('service_id', $request->service_id)
                        ->first();

        if ($review)
        {
            return response()->json([
                'code' => 200,
                'content' => $review,
                'service' => $review->service,
            ]);
        }
        else
        {
            return response()->json([
                'code' => 200,
                'content' => '',
            ]);
        }
    }

    /**
     * 마이서비스 리뷰 등록, 수정
     *
     * @param Illuminate\Http\Request $request
     */
    public function add_service_review(Request $request)
    {
        if($request->review_id == 0)
        {
            $review = new Review();

            $review->lang = \Lang::getLocale();
            $review->client_id = $request->user()->getKey();
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

            \App\Models\Client\Service::where('client_id', $request->user()->getKey())
                                        ->where('service_id', $request->service_id)
                                        ->update(['review_flag' => 0]);

            return response()->json([
                'code' => 200,
                'message' => __('messages.save')
            ]);
        }
        else
        {
            $review = Review::find($request->review_id);

            $review->rating = $request->rating;
            $review->content = $request->content;
            $review->save();

            $alim = new \App\Models\Agent\Alim();

            $alim->agent_id = \App\Models\Agent\Service::find($review->service_id)->agent_id;
            $alim->content = \App\Models\Agent\Service::find($review->service_id)->name.' 서비스 리뷰가 수정되었습니다.';
            $alim->type = 6;
            $alim->save();

            return response()->json([
                'code' => 200,
                'message' => __('messages.save')
            ]);
        }
    }

    /**
     * 마이서비스 문의 등록
     *
     * @param Illuminate\Http\Request $request
     */
    public function add_service_inquiry(Request $request)
    {
        $inquiry = new Inquiry();

        $inquiry->lang = \Lang::getLocale();
        $inquiry->client_id = $request->user()->getKey();
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
     * 구매내역 결제내역
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function payment_list(Request $request)
    {
        View::share('activeMenu', 'client.payment_list');

        $sort_type = $request->sort_type ?? 0;

        $order = 'tbl_payment.id';
        if($sort_type == 1)
            $order = 'tbl_agent_service.name';

        $list = [];
        if(\Auth::user()->current_site){
            $list = Payment::where('tbl_payment.client_id', $request->user()->getKey())
                        ->leftJoin('tbl_agent_service', 'tbl_agent_service.id', '=', 'tbl_payment.service_id')
                        ->where('tbl_payment.lang', \Lang::getLocale())
                        ->where('tbl_payment.refund_flag', 0)
                        ->where('tbl_payment.site_id', \Auth::user()->current_site->id)
                        ->orderBy($order, 'desc')
                        ->select('tbl_payment.*', 'tbl_agent_service.name as service_name')
                        ->paginate(20);
        }

        return view('client.payment_list', compact('list', 'sort_type'));
    }

    /**
     * 구매내역 환불신청내역
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function refund(Request $request)
    {
        View::share('activeMenu', 'client.refund');

        $sort_type = $request->sort_type ?? 0;

        $order = 'tbl_payment.id';
        if($sort_type == 1)
            $order = 'tbl_agent_service.name';
        else if($sort_type == 2)
            $order = 'tbl_payment.refund_status';

        $list = [];
        if(\Auth::user()->current_site){
            $list = Payment::where('tbl_payment.client_id', $request->user()->getKey())
                        ->leftJoin('tbl_agent_service', 'tbl_agent_service.id', '=', 'tbl_payment.service_id')
                        ->where('tbl_payment.lang', \Lang::getLocale())
                        ->where('tbl_payment.refund_flag', 1)
                        ->where('tbl_payment.site_id', \Auth::user()->current_site->id)
                        ->orderBy($order, 'desc')
                        ->select('tbl_payment.*', 'tbl_agent_service.name as service_name')
                        ->paginate(20);
        }

        return view('client.refund', compact('list', 'sort_type'));
    }

    /**
     * 구매내역 환불신청내역 정보 얻기
     *
     * @param Illuminate\Http\Request $request
     */
    public function payment_info(Request $request)
    {
        $payment = Payment::where('id', $request->id)->first();

        return response()->json([
            'code' => 200,
            'content' => $payment,
            'service' => $payment->service
        ]);
    }

    /**
     * 구매내역 환불신청내역 환불 요청 사유 업데이트
     *
     * @param Illuminate\Http\Request $request
     */
    public function update_refund_reason(Request $request)
    {
        $payment = Payment::where('id', $request->id)->first();

        $payment->refund_reason = $request->refund_reason;

        $payment->save();

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);
    }

    /**
     * 구매내역 환불신청내역 보류 사유 업데이트
     *
     * @param Illuminate\Http\Request $request
     */
    public function update_refusal_reason(Request $request)
    {
        $payment = Payment::where('id', $request->id)->first();

        $payment->refusal_reason = $request->refusal_reason;

        $payment->save();

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);
    }

    /**
     * 게시글 문의
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function inquiry(Request $request)
    {
        View::share('activeMenu', 'client.inquiry');

        $search_info = $request->search_info ?? '';
        $sort_type = $request->sort_type ?? 0;

        $list = Inquiry::where('client_id', $request->user()->id)
                ->where('tbl_client_inquiry.lang', \Lang::getLocale())
                ->when($search_info != '', function($query) use ($search_info) {
                    $query->where(function ($query) use ($search_info) {
                        $query->where('title', 'LIKE', "%{$search_info}%")
                                ->orWhereHas('service', function($query) use ($search_info) {
                                    $query->where('name', 'LIKE', "%{$search_info}%");
                                });
                    });
                })
                ->when($sort_type == 0, function($query) {
                    $query->orderBy('id', 'desc');
                })
                ->when($sort_type == 1, function($query) {
                    $query->select('*')
                          ->join('tbl_agent_service', 'tbl_agent_service.id', '=', 'tbl_client_inquiry.service_id')
                          ->orderBy('tbl_agent_service.name', 'desc');
                })
                ->when($sort_type == 2, function($query) {
                    $query->orderBy('type', 'desc');
                })
                ->when($sort_type == 3, function($query) {
                    $query->orderBy('answer', 'desc');
                })
                ->paginate(20);

        return view('client.inquiry', compact('list', 'search_info', 'sort_type'));
    }

    /**
     * 게시글 문의 정보 얻기
     *
     * @param Illuminate\Http\Request $request
     */
    public function inquiry_info(Request $request)
    {
        $inquiry = Inquiry::find($request->id);

        return response()->json([
            'code' => 200,
            'content' => $inquiry,
            'service' => $inquiry->service,
            'author' => $inquiry->author
        ]);
    }

    /**
     * 게시글 리뷰
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function review(Request $request)
    {
        View::share('activeMenu', 'client.review');

        $search_info = $request->search_info ?? '';
        $sort_type = $request->sort_type ?? 0;

        $list = Review::where('client_id', $request->user()->id)
                    ->where('lang', \Lang::getLocale())
                    ->when($search_info != '', function($query) use ($search_info) {
                        $query->where(function ($query) use ($search_info) {
                            $query->where('content', 'LIKE', "%{$search_info}%")
                                  ->orWhereHas('service', function($query) use ($search_info) {
                                        $query->where('name', 'LIKE', "%{$search_info}%");
                                  });
                        });
                    })
                    ->when($sort_type == 0, function($query) {
                        $query->orderBy('id', 'desc');
                    })
                    ->when($sort_type == 1, function($query) {
                        $query->orderBy('answer', 'desc');
                    })
                    ->paginate(20);

        return view('client.review', compact('list', 'search_info', 'sort_type'));
    }

    /**
     * 게시글 리뷰 정보 얻기
     *
     * @param Illuminate\Http\Request $request
     */
    public function review_info(Request $request)
    {
        $review = Review::find($request->id);

        return response()->json([
            'code' => 200,
            'content' => $review,
            'service' => $review->service
        ]);
    }

    /**
     * 서비스 정보 얻기
     *
     * @param Illuminate\Http\Request $request
     */
    public function get_service_info(Request $request)
    {
        $service = \App\Models\Agent\Service::find($request->id);

        return response()->json([
            'code' => 200,
            'content' => $service,
            'user' => $service->user,
            'plan' => $service->plan,
            'cat2' => $service->cat2
        ]);
    }

    public function notice_hits(Request $request)
    {
        $notice = Notice::where('id', $request->id)->first();

        $notice->hits += 1;

        $notice->save();

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);
    }

    public function get_order_no(Request $request)
    {
        $cur_date = date('Y-m-d');
        $service = \App\Models\Client\Service::where('created_at', 'LIKE', "{$cur_date}%")
        ->whereNotNull('order_no')
        ->where('order_no', '<>', '')
        ->orderBy('id','desc')
        ->first();

        $order_no = '';

        if($service) {
            $tmp = (int)substr($service->order_no, 8);
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
     * 서비스 연장
     *
     * @param Illuminate\Http\Request $request
     */
    public function reqextendpay(Request $request)
    {
        header("Content-type: text/html; charset=utf-8");
        $target_URL = config('services.kcp.target_url');

        $pay_options = explode("_", $request->pay_option);

        //==========================================================================
        //    요청정보
        //--------------------------------------------------------------------------
        $tran_cd            = $_POST["tran_cd"]; // 요청코드
        $site_cd            = $_POST["site_cd"]; // 사이트코드
        // 인증서 정보(직렬화)
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

        // $res_data = {"order_no":"1654742859414_2","mall_taxno":"1138521083","res_msg":"정상처리","trace_no":"","va_name":"홍길동","va_date":"20220616235959","res_vat_mny":"12","res_tax_flag":"TG01","van_txid":"DP002022060900643004","van_cd":"SCWR","bankcode":"BK07","amount":"123","van_apptime":"20220609114800","res_free_mny":"0","pay_method":"PAVC","res_cd":"0000","escw_yn":"N","app_time":"20220609114800","tno":"20220609985536","res_en_msg":"processing completed","res_tax_mny":"111","depositor":"한국사이버결제","bankname":"수협","account":"T0709260000806"}

        $service = Service::where('id', $request->c_s_id)->first();

        $term = $pay_options[1];
        $term_unit = $pay_options[2];

        $today = date('Y-m-d H:i:s');
        $date_time = $service->service_end_at;

        //서비스 종료일이 결제날짜 보다 이전이라면
        //이미 만료된 서비스 이므로 결제일에 사용기간을 더해 연장한다.
        if($date_time < $today){
            $date_time = new DateTime($today);
        }

        if($service->period_type == 0)
            $date_time->modify('+'.$term.' month');
        else if($service->period_type == 1)
            $date_time->modify('+'.$term.' days');

        $service_end_arr = explode(' ', $date_time->format('Y-m-d H:i:s'));
        $service_end_at = ($service->period_type == 2) ? NULL : $service_end_arr[0].' 23:59:59';
        $service->service_end_at = $service_end_at;

        $service->save();

        $newpay = new Payment();
        $newpay->lang = \Lang::getLocale();
        $newpay->client_service_id = $service->id;
        $newpay->client_id = \Auth::guard('user')->user()->getKey();
        $newpay->agent_id = $service->service->agent_id;
        $newpay->service_id = $service->service_id;
        $newpay->site_id = $service->site_id;
        $newpay->plan_id = $pay_options[0];
        $newpay->order_no = $service->order_no;
        $newpay->amount = intval($pay_options[3]);
        $newpay->currency = '￦';
        $newpay->type = 1;
        $newpay->payment_type = 0;
        $newpay->service_start_at = $today;
        $newpay->service_end_at = $service->service_end_at;
        $newpay->save();

        \App\Models\Client\Service::where('client_id', $newpay->client_id)
                                    ->where('service_id', $newpay->service_id)
                                    ->update([
                                        'review_flag' => 1,
                                        'process' => 2,
                                    ]);

        return redirect('my/service');
    }



}
