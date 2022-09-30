<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Agent\OrderExportController;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Agent\{Service, Alim};
use App\Models\Payment;
use View;
use DateTime;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use DB;

class OrderController extends Controller
{
    /**
     * order list view
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function __invoke(Request $request)
    {
    }

    /**
     * 주문내역
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     * @return Yajra\DataTables\EloquentDataTable
     */
    public function order(Request $request)
    {
        View::share('activeMenu', 'agent.order.home');

        $data_range = $request->data_range;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $keyword = $request->keyword ?? '';
        $sort    = $request->sort ?? '1';

        if(!$st_date || !$ed_date) {
            $stdate = new DateTime();
            // $stdate->modify('-30 day');
            $stdate->modify('-90 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }
        //
        $order_by = 'created_at';
        switch ($sort) {
            case 1: $order_by = 'tbl_client_service.created_at'; break;
            case 2: $order_by = 'AS1.name'; break;
            case 3: $order_by = 'tbl_client_service.process'; break;
        }

        $agent_id = $request->user()->getKey();
        $total_cnt = \App\Models\Client\Service::
                        whereHas('service', function($q) use ($agent_id){
                            $q->where(function($q) use ($agent_id) {
                                if($agent_id > 0) {
                                    $q->where('agent_id', $agent_id);
                                }
                            });
                        })
                        ->count();

        $results = \App\Models\Client\Service::
                        selectRaw('tbl_client_service.*, tbl_client_service.process AS process1')
                        ->where('tbl_client_service.lang', \Lang::getLocale())
                        ->where('tbl_client_service.created_at', '>=', "{$st_date}")
                        ->where('tbl_client_service.created_at', '<=', "{$ed_date} 23:59:59");
                        //
        $results = $results->leftJoin('tbl_agent_service AS AS1', 'AS1.id', '=', 'tbl_client_service.service_id')
                        ->leftJoin('tbl_client_site AS CS1', 'CS1.id', '=', 'tbl_client_service.site_id')
                        ->where('AS1.agent_id', $request->user()->getKey());

        if($keyword) {
            $results = $results->where(DB::raw("CONCAT(CS1.name,' ',tbl_client_service.order_no,' ',AS1.name)"), 'LIKE', '%'.$keyword.'%');

        }
        $results = $results->orderBy($order_by, 'desc')->paginate(20);

        return view('agent.order.home', compact( 'st_date', 'ed_date', 'keyword', 'results', 'total_cnt', 'sort' ));

    }
    /**
     * export excel data
     *
     * @param Illuminate\Http\Request $request
     */
    public function order_export(Request $request)
    {
        $st_date = $request->st_date;
        $ed_date = $request->ed_date;
        $keyword = $request->keyword ?? '';
        $type    = $request->type ?? 'order';

        $filename = '신청내역';

        switch ($type) {
            case 'order':        $filename = '신청내역'; break;
            case 'payment_list': $filename = '결제내역'; break;
            case 'refund':       $filename = '환불내역'; break;
            case 'settlement':   $filename = '정산관리'; break;
        }

        return Excel::download(new OrderExportController($type, $st_date, $ed_date, $keyword), $filename.'_'.time().'.xlsx');
    }
    /**
     * order 수정
     *
     * @param Illuminate\Http\Request $request
     * @param App\Models\Agent\Service $service
     * @param App\Models\Client\Site $site
     * @return array
     */
    public function changeOrder(Request $request)
    {
        $type    = $request->type;
        $id      = $request->id;
        $process = $request->pross;
        $ids     = $request->ids;
        $expire  = $request->expire;
        $url     = $request->url;

        // $order->fill($request->only('service_end_at'));
        // $order->save();
        if($type == 'process') {
            $service = \App\Models\Client\Service::find($id);
            $service->process = $process;

            if($process == 2) {
                $service->service_start_at = date('Y-m-d H:i:s');
                $date_time = new DateTime($service->service_start_at);

                if($service->period_type == 0)
                    $date_time->modify('+'.$service->period.' month');
                else if($service->period_type == 1)
                    $date_time->modify('+'.$service->period.' days');

                $service_end_at = ($service->period_type == 2) ? NULL : $date_time->format('Y-m-d H:i:s');
                $service->service_end_at = $service_end_at;

                // 메시지발송
                $phone = \App\Models\Users::find($service->client_id);
                $response = Http::asForm()
                ->withHeaders([
                    'Authorization' => config('services.phone_api.authorization')
                ])
                ->post(config('services.phone_api.url'), [
                    'phone' => str_replace("-", "", $phone->manager_phone),
                    'msg' => '[MAPSTREND] 신청하신 서비스 사용이 시작되었습니다. '.PHP_EOL.config('app.domain.client'),
                ]);

                if($response->json()['code'] != 200)
                {
                    return response()->json(['code' => 402, 'error' => __('messages.send_fail')]);
                }
            }
            $service->save();

            if($process == 2) {
                // change payment
                $payment = Payment::where('client_service_id', $id)->where('type', 0)->first();
                if($payment) {
                    $payment->service_start_at = date('Y-m-d H:i:s');
                    $payment->service_end_at   = $service_end_at;
                    $payment->save();
                }
            }
        }
        else if($type == 'expire') {
            foreach ($ids as $key => $id) {
                $service = \App\Models\Client\Service::find($id);
                $service->service_end_at = $expire;
                $service->save();
            }
        }
        else if($type == 'reqUrl') {
            $service = \App\Models\Client\Service::find($id);
            $service->status = 1;
            $service->set_req_url = $url;
            $service->save();
        }
        else if($type == 'reqComplete') {
            $service = \App\Models\Client\Service::find($id);
            $service->status = 2;
            $service->save();
        }

        return response()->json([
            'code' => 200,
            'data' => $ids
        ]);
    }

    /**
     * 결제내역
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     * @return Yajra\DataTables\EloquentDataTable
     */
    public function paymentList(Request $request)
    {
        View::share('activeMenu', 'agent.payment.list');

        $data_range = $request->data_range;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $keyword = $request->keyword ?? '';
        $sort    = $request->sort ?? '1';

        if(!$st_date || !$ed_date) {
            $stdate = new DateTime();
            // $stdate->modify('-30 day');
            $stdate->modify('-90 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }
        //
        $order_by = 'tbl_payment.created_at';
        switch ($sort) {
            case 1: $order_by = 'tbl_payment.created_at'; break;
            case 2: $order_by = 'AS1.name'; break;
            case 3: $order_by = 'tbl_payment.type'; break;
            case 4: $order_by = 'tbl_payment.amount'; break;
        }

        $agent_id = $request->user()->getKey();
        $total_cnt = Payment::where([['refund_flag', '<>', 2], ['agent_id', $agent_id]])->count();


        $results = Payment::
                        selectRaw('tbl_payment.*, AS1.name AS service_name, CS1.name AS site_name ')
                        ->where('tbl_payment.lang', \Lang::getLocale())
                        ->where('refund_flag', 0)
                        ->where('tbl_payment.created_at', '>=', "{$st_date}")
                        ->where('tbl_payment.created_at', '<=', "{$ed_date} 23:59:59")
                        ->where('tbl_payment.agent_id', $request->user()->getKey());
                        //

        $results = $results->leftJoin('tbl_agent_service AS AS1', 'AS1.id', '=', 'tbl_payment.service_id')
                        ->leftJoin('tbl_client_site AS CS1', 'CS1.id', '=', 'tbl_payment.site_id');

        if($keyword) {
            $results = $results->where(DB::raw("CONCAT(CS1.name,' ',tbl_payment.order_no,' ',AS1.name)"), 'LIKE', '%'.$keyword.'%');
        }
        $results = $results->orderBy($order_by, 'desc')->paginate(20);

        return view('agent.order.payment', compact( 'st_date', 'ed_date', 'keyword', 'results', 'total_cnt', 'sort' ));

    }

    /**
     * 환불내역
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     * @return Yajra\DataTables\EloquentDataTable
     */
    public function refundList(Request $request)
    {
        View::share('activeMenu', 'agent.payment.refund');

        $data_range = $request->data_range;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $keyword = $request->keyword ?? '';
        $sort    = $request->sort ?? '1';

        if(!$st_date || !$ed_date) {
            $stdate = new DateTime();
            // $stdate->modify('-30 day');
            $stdate->modify('-90 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }
        //
        $order_by = 'tbl_payment.created_at';
        switch ($sort) {
            case 1: $order_by = 'tbl_payment.created_at'; break;
            case 2: $order_by = 'AS1.name'; break;
            case 3: $order_by = 'tbl_payment.amount'; break;
            case 4: $order_by = 'tbl_payment.refund_status'; break;
        }

        $agent_id = $request->user()->getKey();
        $total_cnt = Payment::where([['refund_flag', 1], ['agent_id', $agent_id]])->count();

        $results = Payment::
                        selectRaw('tbl_payment.*, AS1.name AS service_name, CS1.name AS site_name ')
                        ->where('tbl_payment.lang', \Lang::getLocale())
                        ->where('refund_flag', 1)
                        ->where('tbl_payment.refund_request_at', '>=', "{$st_date}")
                        ->where('tbl_payment.refund_request_at', '<=', "{$ed_date} 23:59:59");
                        //

        $results = $results->leftJoin('tbl_agent_service AS AS1', 'AS1.id', '=', 'tbl_payment.service_id')
                        ->leftJoin('tbl_client_site AS CS1', 'CS1.id', '=', 'tbl_payment.site_id')
                        ->where('AS1.agent_id', $request->user()->getKey());

        if($keyword) {
            $results = $results->where(DB::raw("CONCAT(CS1.name,' ',tbl_payment.order_no,' ',AS1.name)"), 'LIKE', '%'.$keyword.'%');
        }
        $results = $results->orderBy($order_by, 'desc')->paginate(20);

        return view('agent.order.refund', compact( 'st_date', 'ed_date', 'keyword', 'results', 'total_cnt', 'sort' ));

    }
    /**
     * get refund data by id
     *
     * @param Illuminate\Http\Request $request
     */
    public function getRefundById(Request $request)
    {
        $payment_id = $request->id;

        // $result  = Payment::find($payment_id);
        $result = Payment::
                        selectRaw('tbl_payment.*, AS1.name AS service_name, tbl_payment.service_start_at + INTERVAL 10 DAY  < CURDATE() AS service_start ')
                        ->leftJoin('tbl_agent_service AS AS1', 'AS1.id', '=', 'tbl_payment.service_id')
                        ->where('tbl_payment.id', '=', $payment_id)
                        ->first();

        return response()->json([
            'code' => 200,
            'data' => $result
        ]);
    }
    /**
     * refund 수정
     *
     * @param Illuminate\Http\Request $request
     * @param App\Models\Agent\Service $service
     * @param App\Models\Client\Site $site
     * @return array
     */
    public function changeRefund(Request $request)
    {
        $type           = $request->tab;
        $payment_id     = $request->payment_id;
        $agent_id       = $request->agent_id;
        $refund_status  = 0;

        $payment = Payment::find($payment_id);
        if($type == 'refundend') {  // 환불 완료
            $alim = new Alim();
            $alim->agent_id = $agent_id;
            $alim->move_id  = $payment_id;
            $alim->content  = '환불요청이 환불 되었습니다.';
            $alim->type     = 2;
            $alim->save();
            //
            $refund_status = 5;
        }
        else if($type == 'hold') {
            $refund_status = 3;
        }
        else {
            $refund_status = 0;
        }
        // change refund_status
        $payment2 = Payment::find($payment_id);
        $payment2->refund_status = $refund_status;
        if($type == 'refundend') {
            $payment2->refund_complete_at = date('Y-m-d H:i:s');
        }
        $payment2->save();

        $payment->fill($request->only('refusal_reason', 'service_stop_at', 'refund_amount'));
        $payment->save();

        if( $refund_status == 5 || $refund_status == 3 ){
            // 메시지발송
            $phone = \App\Models\Users::find($payment->client_id);

            if( $refund_status == 3 ){
                $msg = "반려";
            }else{
                $msg = "완료";
            }

            $response = Http::asForm()
            ->withHeaders([
                'Authorization' => config('services.phone_api.authorization')
            ])
            ->post(config('services.phone_api.url'), [
                'phone' => str_replace("-", "", $phone->manager_phone),
                'msg' => '[MAPSTREND] 고객님께서 요청하신 환불이 '.$msg.' 처리 되었습니다. '.PHP_EOL.config('app.domain.client'),
            ]);

            if($response->json()['code'] != 200)
            {
                return response()->json(['code' => 402, 'error' => __('messages.send_fail')]);
            }
        }

        return response()->json([
            'code' => 200
        ]);
    }

    /**
     * 정산관리
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     * @return Yajra\DataTables\EloquentDataTable
     */
    public function settlement(Request $request)
    {
        View::share('activeMenu', 'agent.payment.settlement');

        $st_date = $request->st_date;
        $keyword = $request->keyword ?? '';
        $sort    = $request->sort ?? '1';

        if(!$st_date) {
            $stdate = new DateTime();
            $stdate->modify('-1 month');
            $st_date = $stdate->format('Y-m');
        }
        //
        $userId = $request->user()->getKey();
        $fee    = $request->user()->fees / 100;
        // sub 30day
        $prevdate = new DateTime();
        $prevdate->modify('-1 month');
        $prev_date = $prevdate->format('Y-m');
        //
        $order_by = 'tbl_payment.created_at';
        switch ($sort) {
            case 1: $order_by = 'tbl_payment.created_at'; break;
            case 2: $order_by = 'AS1.name'; break;
            case 3: $order_by = 'tbl_payment.type'; break;
            case 4: $order_by = 'tbl_payment.amount'; break;
        }

        //
        $sql = 'SELECT
                        (SELECT SUM(amount) FROM tbl_payment WHERE currency="$" AND agent_id="'.$userId.'") AS total_daller_sum,
                        (SELECT SUM(amount) FROM tbl_payment WHERE currency="￦" AND agent_id="'.$userId.'") AS total_won_sum,
                        (SELECT SUM(amount) FROM tbl_payment WHERE currency="$" AND agent_id="'.$userId.'" AND DATE_FORMAT(created_at, "%Y-%m") = DATE_FORMAT(CURDATE(), "%Y-%m")) AS this_daller_sum,
                        (SELECT SUM(amount) FROM tbl_payment WHERE currency="￦" AND agent_id="'.$userId.'" AND DATE_FORMAT(created_at, "%Y-%m") = DATE_FORMAT(CURDATE(), "%Y-%m")) AS this_won_sum,
                        (SELECT SUM(amount) FROM tbl_payment WHERE currency="$" AND agent_id="'.$userId.'" AND DATE_FORMAT(created_at, "%Y-%m") = "'.$prev_date.'") AS prev_daller_sum,
                        (SELECT SUM(amount) FROM tbl_payment WHERE currency="￦" AND agent_id="'.$userId.'" AND DATE_FORMAT(created_at, "%Y-%m") = "'.$prev_date.'") AS prev_won_sum,
                        (SELECT SUM(amount) FROM tbl_payment WHERE currency="$" AND agent_id="'.$userId.'" AND DATE_FORMAT(created_at, "%Y-%m") = "'.$st_date.'") AS st_daller_sum,
                        (SELECT SUM(amount) FROM tbl_payment WHERE currency="￦" AND agent_id="'.$userId.'" AND DATE_FORMAT(created_at, "%Y-%m") = "'.$st_date.'") AS st_won_sum
                            ';
                            // dd($sql);
        $total_cnt = \DB::select($sql)[0];
        // 세금계산서 정보
        $sql = 'SELECT * FROM tbl_site_info';
        $tax_info = \DB::select($sql)[0];

        $results = Payment::
                        selectRaw('tbl_payment.*, AS1.name AS service_name, CS1.name AS site_name ')
                        ->where('tbl_payment.lang', \Lang::getLocale())
                        ->where('tbl_payment.created_at', 'LIKE', "{$st_date}%")
                        ->where('tbl_payment.agent_id', $userId);
                        //
        $results = $results->leftJoin('tbl_agent_service AS AS1', 'AS1.id', '=', 'tbl_payment.service_id')
                        ->leftJoin('tbl_client_site AS CS1', 'CS1.id', '=', 'tbl_payment.site_id');

        if($keyword) {
            $results = $results->where(DB::raw("CONCAT(CS1.name,' ',tbl_payment.order_no,' ',AS1.name)"), 'LIKE', '%'.$keyword.'%');
        }
        $results = $results->orderBy($order_by, 'desc')->paginate(20);

        return view('agent.order.settlement', compact( 'st_date', 'keyword', 'results', 'total_cnt', 'tax_info', 'sort', 'fee' ));

    }

    /**
     * 결제정보 수동등록 주문번호 조회
     *
     * @param Illuminate\Http\Request $request
     */
    public function matchingOrderNo(Request $request){
        $order_no = $request->order_no;

        $service = \App\Models\Client\Service::where('order_no', $order_no)->orderBy('id','desc')->first();

        if($service){
            $plan = \App\Models\Agent\ServicePlan::where('name', $service->service_option)->where('service_id', $service->service_id)->first();
            $agent = \App\Models\Agent\Service::find($service->service_id);

            if($agent->agent_id != \Auth::user()->getKey()){
                $code = 401;
            }else{
                $code = 200;
            }
        }else{
            $code = 401;
        }

        return response()->json([
            'code' => $code,
            'order_no' => $order_no,
            'service' => $service,
            'plan' => $plan->id ?? NULL,
            'agent' => $agent->agent_id ?? NULL
        ]);
    }

    /**
     * 결제정보 수동등록 결제내역 생성
     *
     * @param Illuminate\Http\Request $request
     */
    public function createPayment(Request $request){

        $service = \App\Models\Client\Service::where('order_no', $request->order_num)->orderBy('id', 'desc')->first();

        $today = date('Y-m-d H:i:s');
        if($request->service_type == '기간상품'){
            $service_start_at = $request->pay_date_start.' 00:00:00';
            $service_end_at = $request->pay_date_end.' 23:59:59';
        }else{
            $service_start_at = date('Y-m-d H:i:s');
        }

        $payment = new Payment();
        $payment->client_service_id = $request->client_service_id;
        $payment->client_id = $request->client_id;
        $payment->agent_id = $request->agent_id;
        $payment->service_id = $request->service_id;
        $payment->site_id = $request->site_id;
        $payment->plan_id = $request->plan_id;
        $payment->order_no = $request->order_num;
        $payment->amount = $request->amount;
        $payment->currency = '￦';
        $payment->type = $request->type;
        $payment->payment_type = $request->payment_type;
        $payment->service_start_at = $service_start_at;
        $payment->service_end_at = $service_end_at ?? NULL;
        $payment->created_at = $request->pay_date.' '.$request->pay_date_hour.':'.$request->pay_date_min.':00';
        $payment->save();

        $service = \App\Models\Client\Service::where('order_no' , $request->order_num)->orderBy('id','desc')->first();
        $service->update([
            'process' => 2,
            // 'service_start_at' => ($request->type == 0) ? $service_start_at : $service->service_start_at,
            'service_start_at' => $service_start_at,
            'service_end_at' => $service_end_at ?? NULL
        ]);

        return response()->json([
            'code' => 200,
        ]);
    }
}
