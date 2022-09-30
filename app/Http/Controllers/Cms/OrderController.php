<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Cms\OrderExportController;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Agent\{Service, ServiceCategory, Alim};
use App\Models\Payment;
use View;
use DateTime;
use Illuminate\Support\Facades\Http;

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
        View::share('activeMenu', 'order.list');

        $data_range = $request->data_range;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $keyword = $request->keyword;

        $agent_id = $request->agent_id ?? 0;
        $service_id = $request->service_id ?? 0;
        $category_id = $request->category_id ?? 0;

        if(!$st_date || !$ed_date) {
            $stdate = new DateTime();
            $stdate->modify('-90 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }


        $agent_list = \App\Models\Users::where('type', 2)->orderBy('id','desc')->get();
        $service_list = Service::where(function($q) use ($agent_id) {
                                    if($agent_id > 0) {
                                        $q->where('agent_id', $agent_id);
                                    }
                                })
                                ->orderBy('id','desc')
                                ->get();
        $total_service_list = Service::orderBy('id','desc')->get();
        $category_list = ServiceCategory::getCate();

        $results = \App\Models\Client\Service::
                        whereHas('service', function($q) use ($agent_id){
                            $q->where(function($q) use ($agent_id) {
                                if($agent_id > 0) {
                                    $q->where('agent_id', $agent_id);
                                }
                            });
                        })
                        ->where(function($q) use ($service_id) {
                            if($service_id > 0) {
                                $q->where('service_id', $service_id);
                            }
                        })
                        ->whereHas('service', function($q) use ($category_id) {
                            $q->where(function($q) use ($category_id) {
                                if($category_id > 0) {
                                    $q->where('category1', $category_id);
                                }
                            });
                        })
                        ->whereBetween('created_at', [$st_date, $ed_date.' 23:59:59']);

        if($keyword) {
            $results = $results->whereHas('site', function($q) use ($keyword) {
                $q->where('name', 'LIKE', "%${keyword}%");
            });
            $results = $results->orWhereHas('service', function($q) use ($keyword){
                $q->where('name', 'LIKE', "%${keyword}%");
            });
            $results = $results->orWhere('order_no','LIKE',"%{$keyword}%");
        }

        $results = $results->orderBy('created_at', 'desc')->paginate(20);

        $write_permission = \Auth::guard('cms')->user()->getWritePermission(11);

        return view('cms.order.home',
            compact( 'st_date', 'ed_date', 'keyword', 'results',
            'agent_id', 'service_id', 'category_id',
            'agent_list', 'service_list', 'total_service_list', 'category_list', 'write_permission' ));

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
        $agent_id    = $request->agent_id;
        $service_id  = $request->service_id;
        $category_id = $request->category_id;
        $type    = $request->type ?? 'order';

        $filename = '신청내역';

        switch ($type) {
            case 'order':        $filename = '신청내역'; break;
            case 'payment_list': $filename = '결제내역'; break;
            case 'refund':       $filename = '환불내역'; break;
            case 'settle_summary': $filename = '정산요약'; break;
            case 'settle_detail':  $filename = '정산상세'; break;
        }

        return Excel::download(new OrderExportController($type, $st_date, $ed_date, $keyword, $agent_id, $service_id, $category_id), $filename.'_'.time().'.xlsx');
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
            $service->process     = 1;
            $service->set_req_url = $url;
            $service->save();
        }
        else if($type == 'reqComplete') {
            $service = \App\Models\Client\Service::find($id);
            $service->process     = 2;
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
        View::share('activeMenu', 'order.payment');

        $data_range = $request->data_range;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $keyword = $request->keyword;

        $agent_id = $request->agent_id ?? 0;
        $service_id = $request->service_id ?? 0;
        $category_id = $request->category_id ?? 0;

        if(!$st_date || !$ed_date) {
            $stdate = new DateTime();
            $stdate->modify('-90 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }

        $agent_list = \App\Models\Users::where('type', 2)->orderBy('id','desc')->get();
        $service_list = Service::where(function($q) use ($agent_id) {
                                    if($agent_id > 0) {
                                        $q->where('agent_id', $agent_id);
                                    }
                                })
                                ->orderBy('id','desc')
                                ->get();
        $total_service_list = Service::orderBy('id','desc')->get();
        $category_list = ServiceCategory::getCate();

        $results = Payment::
                        where('refund_flag', 0)
                        ->whereHas('service', function($q) use ($agent_id){
                            $q->where(function($q) use ($agent_id) {
                                if($agent_id > 0) {
                                    $q->where('agent_id', $agent_id);
                                }
                            });
                        })
                        ->where(function($q) use ($service_id) {
                            if($service_id > 0) {
                                $q->where('service_id', $service_id);
                            }
                        })
                        ->whereHas('service', function($q) use ($category_id) {
                            $q->where(function($q) use ($category_id) {
                                if($category_id > 0) {
                                    $q->where('category1', $category_id);
                                }
                            });
                        })
                        ->whereBetween('created_at', [$st_date, $ed_date.' 23:59:59']);
        if($keyword) {
            $results = $results->whereHas('site', function($q) use ($keyword) {
                $q->where('name', 'LIKE', "%${keyword}%");
            });
            $results = $results->orWhereHas('service', function($q) use ($keyword){
                $q->where('name', 'LIKE', "%${keyword}%");
            });
            $results = $results->orWhere('order_no','LIKE',"%{$keyword}%");
        }

        $results = $results->orderBy('created_at', 'desc')->paginate(20);

        return view('cms.order.payment',
            compact( 'st_date', 'ed_date', 'keyword', 'results', 'agent_id', 'service_id', 'category_id', 'agent_list', 'service_list',
            'total_service_list', 'category_list' ));

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
        View::share('activeMenu', 'order.refund');

        $data_range = $request->data_range;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $keyword = $request->keyword;

        $agent_id = $request->agent_id ?? 0;
        $service_id = $request->service_id ?? 0;
        $category_id = $request->category_id ?? 0;

        if(!$st_date || !$ed_date) {
            $stdate = new DateTime();
            $stdate->modify('-90 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }

        $agent_list = \App\Models\Users::where('type', 2)->orderBy('id','desc')->get();
        $service_list = Service::where(function($q) use ($agent_id) {
                                    if($agent_id > 0) {
                                        $q->where('agent_id', $agent_id);
                                    }
                                })
                                ->orderBy('id','desc')
                                ->get();
        $total_service_list = Service::orderBy('id','desc')->get();
        $category_list = ServiceCategory::getCate();

        $results = Payment::
                        where('refund_flag', 1)
                        ->whereHas('service', function($q) use ($agent_id){
                            $q->where(function($q) use ($agent_id) {
                                if($agent_id > 0) {
                                    $q->where('agent_id', $agent_id);
                                }
                            });
                        })
                        ->where(function($q) use ($service_id) {
                            if($service_id > 0) {
                                $q->where('service_id', $service_id);
                            }
                        })
                        ->whereHas('service', function($q) use ($category_id) {
                            $q->where(function($q) use ($category_id) {
                                if($category_id > 0) {
                                    $q->where('category1', $category_id);
                                }
                            });
                        })
                        ->whereBetween('refund_request_at', [$st_date, $ed_date.' 23:59:59']);
        if($keyword) {
            $results = $results->whereHas('site', function($q) use ($keyword) {
                $q->where('name', 'LIKE', "%${keyword}%");
            });
            $results = $results->orWhereHas('service', function($q) use ($keyword){
                $q->where('name', 'LIKE', "%${keyword}%");
            });
            $results = $results->orWhere('order_no','LIKE',"%{$keyword}%");
        }

        $results = $results->orderBy('created_at', 'desc')->paginate(20);

        $write_permission = \Auth::guard('cms')->user()->getWritePermission(13);

        return view('cms.order.refund',
            compact( 'st_date', 'ed_date', 'keyword', 'results', 'agent_id', 'service_id', 'category_id', 'agent_list',
                         'service_list', 'total_service_list', 'category_list', 'write_permission' ));

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
        if($type == 'refundend') {
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
    public function settle_summary(Request $request)
    {
        View::share('activeMenu', 'order.settle_summary');

        $st_date  = $request->st_date;
        $agent_id = $request->agent_id ?? 0;
        $keyword  = $request->keyword ?? '';

        if(!$st_date) {
            $stdate = new DateTime();
            $stdate->modify('-1 month');
            $st_date = $stdate->format('Y-m');
        }
        //
        $agent_list = \App\Models\Users::where('type', 2)->orderBy('id','desc')->get();
        // 세금계산서 정보
        $sql = 'SELECT * FROM tbl_site_info';
        $tax_info = \DB::select($sql)[0];

        $results = Payment::
                        // where('refund_flag', 1)
                        selectRaw('*, COUNT(id) AS req_cnt, SUM(amount) AS total_sum')
                        ->whereHas('service', function($q) use ($agent_id){
                            $q->where(function($q) use ($agent_id) {
                                if($agent_id > 0) {
                                    $q->where('agent_id', $agent_id);
                                }
                            });
                        })
                        ->whereHas('site', function($q) use ($keyword){
                            $q->where(function($q) use ($keyword) {
                                if($keyword) {
                                    $q->where('name', 'LIKE', "%{$keyword}%");
                                }
                            });
                        })
                        ->where('created_at', 'LIKE', "{$st_date}%")
                        ->orderBy('created_at', 'desc')
                        ->groupBy('agent_id')
                        ->paginate(20);

        $write_permission = \Auth::guard('cms')->user()->getWritePermission(14);

        return view('cms.order.settle_summary', compact( 'st_date', 'keyword', 'results', 'agent_list', 'agent_id', 'write_permission' ));

    }
    /**
     * 정산관리 - 상세내역
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     * @return Yajra\DataTables\EloquentDataTable
     */
    public function settle_detail(Request $request)
    {
        View::share('activeMenu', 'order.settle_detail');

        $st_date  = $request->st_date;
        $keyword  = $request->keyword ?? '';

        $agent_id = $request->agent_id ?? 0;
        $service_id = $request->service_id ?? 0;
        $category_id = $request->category_id ?? 0;

        if(!$st_date) {
            $stdate = new DateTime();
            $stdate->modify('-1 month');
            $st_date = $stdate->format('Y-m');
        }
        //

        $agent_list = \App\Models\Users::where('type', 2)->orderBy('id','desc')->get();
        $service_list = Service::where(function($q) use ($agent_id) {
                                    if($agent_id > 0) {
                                        $q->where('agent_id', $agent_id);
                                    }
                                })
                                ->orderBy('id','desc')
                                ->get();
        $total_service_list = Service::orderBy('id','desc')->get();
        $category_list = ServiceCategory::getCate();

        $results = Payment::
                        whereHas('site', function($q) use ($keyword){
                            $q->where(function($q) use ($keyword) {
                                if($keyword) {
                                    $q->where('name', 'LIKE', "%{$keyword}%");
                                }
                            });
                        })
                        ->whereHas('service', function($q) use ($agent_id){
                            $q->where(function($q) use ($agent_id) {
                                if($agent_id > 0) {
                                    $q->where('agent_id', $agent_id);
                                }
                            });
                        })
                        ->where(function($q) use ($service_id) {
                            if($service_id > 0) {
                                $q->where('service_id', $service_id);
                            }
                        })
                        ->whereHas('service', function($q) use ($category_id) {
                            $q->where(function($q) use ($category_id) {
                                if($category_id > 0) {
                                    $q->where('category1', $category_id);
                                }
                            });
                        })
                        ->where('created_at', 'LIKE', "{$st_date}%")
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);

        return view('cms.order.settle_detail',
            compact( 'st_date', 'keyword', 'results', 'agent_id', 'service_id', 'category_id', 'agent_list', 'service_list',
            'total_service_list', 'category_list' ));

    }
    /**
     * sattle change
     *
     * @param Illuminate\Http\Request $request
     * @param App\Models\Agent\Service $service
     * @return array
     */
    public function changeSattle(Request $request)
    {
        $st_date        = $request->st_date;
        $agent_id       = $request->agent_id;
        $settle_status  = $request->settle_status;
        $settle_reason  = $request->settle_reason;

        $payment = Payment::where('agent_id', $agent_id)
                                ->where('created_at', 'LIKE', "{$st_date}%")
                                ->update([
                                    'settle_status' => $settle_status,
                                    'settle_reason' => $settle_reason ?? ''
                                ]);

        return response()->json([
            'code' => 200
        ]);
    }

    /**
     * 내역 업데이트 및 정산처리결과 통보 : 내역업데이트- 매월 1일 통보, 정산처리결과 - 매월 15일 통보
     *
     * @param Illuminate\Http\Request $request
     * @param App\Models\Agent\Service $service
     * @param App\Models\Client\Site $site
     * @return array
     */
    public function changeRefundAlarm(Request $request)
    {
        $type = $request->type ?? 1;    // 1: 매월 1일, 2: 매월 15일
        $prev_date = date('Y-m', strtotime('first day of last month'));
        $results = Payment::
                        where('created_at', 'LIKE', "{$prev_date}%")
                        ->groupBy('agent_id')
                        ->get();

        foreach ($results as $key => $value) {
            $alim = new Alim();
            $alim->type     = 3;
            $alim->agent_id = $value->agent_id;

            if($type == 1) {
                $alim->content  = '정산내역이 업데이트 되었습니다.';
            }
            else {
                $status = '';
                switch ($value->settle_status) {
                    case 1: $status = '대기상태 입니다.'; break;
                    case 2: $status = '불가 합니다.'; break;
                    case 3: $status = '완료# 되었습니다.'; break;
                }
                $alim->content  = '정산처리가 '.$status;
            }
            $alim->save();

        }
    }
}
