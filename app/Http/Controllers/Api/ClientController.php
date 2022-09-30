<?php

namespace App\Http\Controllers\Api;

// use App\Models\Api\User;
use App\Models\Users as User;
use Illuminate\Http\Request;
use App\Models\Api\Service;
use App\Models\Api\ClientSite;
use App\Models\Api\ClientService;
use App\Models\Api\Payment;
use App\Models\Api\InappPayment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller{

    public $today;
    public $todayDate;
    public $todaytime;

    public function __construct()
    {
        $this->today = date('Y-m-d H:i:s');
        $this->todayDate = date('Y-m-d');
        $this->todaytime = date('H:i:s');
    }

    /**
     * 클라이언트 정보요청
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clientInfo(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'client_id' => 'required|string',
            // 'client_sid' => 'required|string',
            'service_key' => 'required|string',
        ]);

        if($validator->fails()){
            $httpCode = 422;
            return response()->json([
                "message" => "파라미터 오류",
            ],$httpCode);
        }

        /*
            2022-05-02 HJY
            쿼리문 개선 필요..
         */

        $clientId = $request->input('client_id');
        $clientSid = $request->input('client_sid');
        $serviceKey = $request->input('service_key');

        $subQuery1 = DB::table('tbl_client_site');

        $query1 = User::from('tbl_users as users')
        ->select(User::raw('users.*'),ClientSite::raw('site.id as site_id'),'site.client_id','site.url','site.hostname','site.client_sid')
        // ->select(User::raw('users.*'),ClientSite::raw('site.id as site_id'),'site.client_id','site.url','site.hostname')
        ->joinSub($subQuery1,'site', function($join) {
            $join->on('users.id', '=' , 'site.client_id');
        });

        // client가 신청한 내역에서 조회한 service_id를 조건으로 조회
        $subQuery2 = DB::table('tbl_agent_service')
        ->where('api_id', $serviceKey);

        $query2 = ClientService::from('tbl_client_service')
        ->select('client_id','site_id')
        ->joinSub($subQuery2,'agent', function($join){
            $join->on('service_id', '=', 'agent.id');
        });

        $client = DB::table($query1 , 'a')
        ->select('*')
        ->joinSub($query2,'b', function($join) {
            $join->on('a.client_id', '=' , 'b.client_id')
            ->on('a.site_id', '=' , 'b.site_id');
        })
        ->where('account', $clientId)
        ->get();

        //해당 제휴사의 서비스를 신청한 url과 host는 복수이므로 client_sid조건으로 조회하지않음
        $url = [];
        $host = [];
        $sid = [];
        foreach($client as $key => $value){
            switch($client[$key]->hostname){
                case 1 : $hostName = "카페24"; break;
                case 2 : $hostName = "메이크샵"; break;
                case 3 : $hostName = "고도몰"; break;
                default : $hostName = "독립몰"; break;
            }
            array_push($host, $hostName);
            array_push($url, $client[$key]->url);
            array_push($sid, $client[$key]->client_sid);
        }

        if( isset($client) ){
            $httpCode = 200;

            $result = [
                "client_id" => $client[0]->account,
                "client_sid" => $sid,
                "client_name" => $client[0]->company_name,
                "client_url" => $url,
                "client_host_name" => $host,
                "admin_name" => $client[0]->manager_name,
                "admin_phone" => $client[0]->manager_phone,
                "admin_email" => $client[0]->manager_email,
            ];
        }else{
            $httpCode = 404;
            return response()->json([
                "message" => "일치하는 회원이 없습니다",
            ],$httpCode);
        }

        return response()->json($result,$httpCode);
    }

    /**
     * 클라이언트 서비스기간 조회
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPeriod(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'client_id' => 'required|string',
            'client_sid' => 'required|string',
            'service_key' => 'required|string',
        ]);

        if($validator->fails()){
            $httpCode = 422;
            return response()->json([
                "message" => "파라미터 오류",
            ],$httpCode);
        }

        $clientId = $request->input('client_id');
        $clientSid = $request->input('client_sid');
        $serviceKey = $request->input('service_key');

        // client가 신청한 내역에서 조회한 service_id를 조건으로 조회
        $subQuery1 = Service::where('api_id', $serviceKey);

        $query1 = ClientService::from('tbl_client_service as service')
        ->select(ClientService::raw('service.*'),Service::raw('agent.id as agent_id'))
        ->joinSub($subQuery1,'agent', function($join){
            $join->on('service.service_id', '=', 'agent.id');
        });

        // $client = DB::table($query1 , 'a')
        // ->select('a.*','b.account','b.company_name')
        // ->join(User::raw('tbl_users as b'),'a.client_id','=','b.id')
        // ->first();
        $subQuery2 = User::from('tbl_users as users')
        ->select('users.*','site.id as site_id','site.client_sid')
        ->join(ClientSite::raw('tbl_client_site as site'),'users.id','=','site.client_id');

        $client = DB::table($query1 , 'a')
        ->select('a.*','b.account','b.company_name','b.client_sid')
        ->joinSub($subQuery2,'b',function($join){
            $join->on('a.client_id','=','b.id');
        })->whereColumn('a.site_id','b.site_id')
        ->where('account',$clientId)
        ->where('client_sid',$clientSid)
        ->first();

        if( isset($client) ){
            $httpCode = 200;
            $result = [
                "client_id" => $client->account,
                "client_name" => $client->company_name,
                "end_date" => $client->service_end_at,
            ];
        }else{
            $httpCode = 404;
            return response()->json([
                "message" => "일치하는 회원이 없습니다",
            ],$httpCode);
        }

        return response()->json($result,$httpCode);
    }

    /**
     * 클라이언트 서비스기간 수정
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePeriod(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'client_id' => 'required|string',
            'client_sid' => 'required|string',
            'service_key' => 'required|string',
            'end_date' =>'required|date_format:Y-m-d',
        ]);

        if($validator->fails()){
            $httpCode = 422;
            return response()->json([
                "message" => "파라미터 오류",
            ],$httpCode);
        }

        $clientId = $request->input('client_id');
        $serviceKey = $request->input('service_key');
        $serviceEndDate = $request->input('end_date')." 23:59:59";
        $clientSid = $request->input('client_sid');

        // client가 신청한 내역에서 조회한 service_id를 조건으로 조회
        $subQuery1 = Service::where('api_id', $serviceKey);

        $agentService = $subQuery1->first();
        $clientUser = User::select('id')->where('account',$clientId)->first();
        $clientSite = ClientSite::select('id')->where('client_sid',$clientSid)->first();
        // $clientService = ClientService::where('service_id',$agentService->id)->where('client_id',$clientUser->id)->first();
        $clientService = ClientService::where('service_id',$agentService->id)
        ->where('client_id',$clientUser->id)
        ->where('site_id',$clientSite->id)
        ->first();
        $clientService->service_end_at = $serviceEndDate;
        $clientService->save();

        $query1 = ClientService::from('tbl_client_service as service')
        ->select(ClientService::raw('service.*'),Service::raw('agent.id as agent_id'))
        ->joinSub($subQuery1,'agent', function($join){
            $join->on('service.service_id', '=', 'agent.id');
        });

        // $client = DB::table($query1 , 'a')
        // ->select('a.*','b.account','b.company_name')
        // ->join(User::raw('tbl_users as b'),'a.client_id','=','b.id')
        // ->first();
        $subQuery2 = User::from('tbl_users as users')
        ->select('users.*','site.id as site_id','site.client_sid')
        ->join(ClientSite::raw('tbl_client_site as site'),'users.id','=','site.client_id');

        $client = DB::table($query1 , 'a')
        ->select('a.*','b.account','b.company_name','b.client_sid')
        ->joinSub($subQuery2,'b',function($join){
            $join->on('a.client_id','=','b.id');
        })->whereColumn('a.site_id','b.site_id')
        ->where('account',$clientId)
        ->where('client_sid',$clientSid)
        ->first();

        if( isset($client) ){
            $httpCode = 200;
            $result = [
                "client_id" => $client->account,
                "client_name" => $client->company_name,
                "end_date" => $client->service_end_at,
            ];
        }else{
            $httpCode = 404;
            return response()->json([
                "message" => "일치하는 회원이 없습니다",
            ],$httpCode);
        }

        return response()->json($result,$httpCode);
        // return response()->json($agentService->id);
    }

    /**
     * 고객사 인앱결제
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * 정보 받아서 payment테이블에 넣으면서 상태값 사용중으로 변경 , 주문번호 조회해서 업데이트
     * 파라메터 type 추가 (신규 , 연장 , 환불)
     * 일회성상품 서비스종료일 없는 api 추가
     * 매일밤 12시 서비스만료일 체크해서 만료로 상태변경하는 배치작성필요
     */
    public function payment(Request $request){
        $validator = Validator::make($request->all(),[
            'client_id' => 'required|string',
            'service_key' => 'required|string',
            'start_date' =>'required|date_format:Y-m-d',
            'end_date' =>'required|date_format:Y-m-d',
            'paid_price' =>'required|integer',
            'client_sid' => 'required|string',
            'type' => 'required|integer',
        ]);

        if($validator->fails()){
            $httpCode = 422;
            return response()->json([
                "message" => "파라미터 오류",
            ],$httpCode);
        }

        $clientId = $request->input('client_id');
        $serviceKey = $request->input('service_key');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $price = $request->input('paid_price');
        $clientSid = $request->input('client_sid');
        $paymentType = $request->input('type');

        if( $startDate > $this->todayDate ){
            $startDate.=" 00:00:00";
        }else{
            $startDate.=" ".$this->todaytime;
        }
        $endDate.=" 23:59:59";

        //인앱결제테이블에 저장
        $inapp_payment = new InappPayment();
        $inapp_payment->client_id = $clientId;
        $inapp_payment->service_key = $serviceKey;
        $inapp_payment->start_date = $startDate;
        $inapp_payment->end_date = $endDate;
        $inapp_payment->paid_price = $price;
        $inapp_payment->type = $paymentType;
        $inapp_payment->save();

        $client = User::select('id','company_name')->where('account', $clientId)->first();

        $subQuery1 = Service::from('tbl_agent_service as agent')
        ->select('agent.id as agent_id')
        // ->where('api_id', '7qAiYEkNUoHSVjaeZD9IadrLwAejqAy0');
        ->where('api_id', $serviceKey);

        //클라이언트 서비스 테이블에도 서비스 종료날짜 업데이트
        $clientUser = User::select('id')->where('account',$clientId)->first();
        $clientSite = ClientSite::select('id')->where('client_sid',$clientSid)->first();
        $agentService = Service::select('id')->where('api_id', $serviceKey)->first();
        $clientService = ClientService::from('tbl_client_service as a')
        ->select('*')
        ->joinSub($subQuery1,'b', function($join){
            $join->on('a.service_id', '=', 'b.agent_id');
        })
        ->where('client_id', $clientUser->id)
        ->where('site_id', $clientSite->id)
        ->first();

        $clientService->service_start_at = $startDate;
        $clientService->service_end_at = $endDate;
        $clientService->process = 2;
        $clientService->save();

        $inapp_payment->order_no = $clientService->order_no;
        $inapp_payment->save();

        //결제테이블에도 내역 생성
        $payment = new Payment();
        $payment->client_service_id = $clientService->id;
        $payment->client_id = $clientUser->id;
        $payment->agent_id = $agentService->id;
        $payment->site_id = $clientSite->id;
        $payment->service_start_at = $startDate;
        $payment->service_end_at = $endDate;
        $payment->amount = $price;
        $payment->type = $paymentType;
        $payment->currency = '￦';
        $payment->order_no = $clientService->order_no;
        $payment->save();

        $httpCode = 201;
        $result = [
            "client_id" => $clientId,
            "client_name" => $client->company_name,
            "start_date" => $startDate,
            "end_date" => $endDate,
            "paid_price" => $price,
            "reg_date" => $this->today,
        ];

        return response()->json($result,$httpCode);
        // return response()->json($clientService);

    }

    /**
     * 고객사 인앱결제 일회성상품
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function payment_onetime(Request $request){
        $validator = Validator::make($request->all(),[
            'client_id' => 'required|string',
            'service_key' => 'required|string',
            // 'start_date' =>'required|date_format:Y-m-d',
            'paid_price' =>'required|integer',
            'client_sid' => 'required|string',
            'type' => 'required|integer',
        ]);

        if($validator->fails()){
            $httpCode = 422;
            return response()->json([
                "message" => "파라미터 오류",
            ],$httpCode);
        }

        $clientId = $request->input('client_id');
        $serviceKey = $request->input('service_key');
        // $startDate = $request->input('start_date');
        $price = $request->input('paid_price');
        $clientSid = $request->input('client_sid');
        $paymentType = $request->input('type');

        // if( $startDate > $this->todayDate ){
        //     $startDate.=" 00:00:00";
        // }else{
        //     $startDate.=" ".$this->todaytime;
        // }

        $startDate = date('Y-m-d H:i:s');

        //인앱결제테이블에 저장
        $inapp_payment = new InappPayment();
        $inapp_payment->client_id = $clientId;
        $inapp_payment->service_key = $serviceKey;
        $inapp_payment->start_date = $startDate;
        $inapp_payment->paid_price = $price;
        $inapp_payment->type = $paymentType;
        $inapp_payment->save();

        $client = User::select('id','company_name')->where('account', $clientId)->first();

        $subQuery1 = Service::from('tbl_agent_service as agent')
        ->select('agent.id as agent_id')
        // ->where('api_id', '7qAiYEkNUoHSVjaeZD9IadrLwAejqAy0');
        ->where('api_id', $serviceKey);

        //클라이언트 서비스 테이블에도 서비스 종료날짜 업데이트
        $clientUser = User::select('id')->where('account',$clientId)->first();
        $clientSite = ClientSite::select('id')->where('client_sid',$clientSid)->first();
        $agentService = Service::select('id')->where('api_id', $serviceKey)->first();
        $clientService = ClientService::from('tbl_client_service as a')
        ->select('*')
        ->joinSub($subQuery1,'b', function($join){
            $join->on('a.service_id', '=', 'b.agent_id');
        })
        ->where('client_id', $clientUser->id)
        ->where('site_id', $clientSite->id)
        ->first();

        // $clientService->service_end_at = $endDate;
        $clientService->process = 2;
        $clientService->save();

        $inapp_payment->order_no = $clientService->order_no;
        $inapp_payment->save();

        //결제테이블에도 내역 생성
        $payment = new Payment();
        $payment->client_service_id = $clientService->id;
        $payment->client_id = $clientUser->id;
        $payment->agent_id = $agentService->id;
        $payment->site_id = $clientSite->id;
        $payment->service_start_at = $startDate;
        $payment->amount = $price;
        $payment->type = $paymentType;
        $payment->currency = '￦';
        $payment->order_no = $clientService->order_no;
        $payment->save();

        $httpCode = 201;
        $result = [
            "client_id" => $clientId,
            "client_name" => $client->company_name,
            "start_date" => $startDate,
            "paid_price" => $price,
            "reg_date" => $this->today,
        ];

        return response()->json($result,$httpCode);
        // return response()->json($clientService);

    }
}
?>
