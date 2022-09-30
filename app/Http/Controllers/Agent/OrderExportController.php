<?php

namespace App\Http\Controllers\Agent;

use DateTime;
use App\Models\Payment;

use App\Models\Agent\{Service};
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;

class OrderExportController implements FromCollection,WithHeadings
{
    private $type='', $st_date='', $ed_date='', $keyword=''    ;

    public function __construct($type='', $st_date='', $ed_date='', $keyword='')
    {
        $this->type     = $type;
        $this->st_date  = $st_date;
        $this->ed_date  = $ed_date;
        $this->keyword  = $keyword;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings():array{
        $header = array();
        if($this->type == 'order') {
            $header = [__('payment.field10'), __('sub.agent-created'), __('sub.agent-service'), __('sub.agent-site'), __('sub.agent-pay_option'), __('sub.agent-service_option'), __('sub.agent-status'), __('sub.agent-expire')];
        }
        else if($this->type == 'payment_list') {
            $header = [__('payment.field10'), __('sub.agent-pay_date'), __('sub.agent-service'), __('sub.agent-site'), __('sub.agent-pay_type'), __('sub.agent-pay_method'), __('sub.agent-pay_option'), __('sub.agent-service_option'), __('sub.agent-pay_amount'), __('sub.agent-pay_term')];
        }
        else if($this->type == 'refund') {
            $header = [__('payment.field10'), __('sub.agent-pay_date'), __('sub.agent-service'), __('sub.agent-site'), __('sub.agent-pay_method'), __('sub.agent-pay_amount'), __('sub.agent-pay_term'), __('sub.agent-refund_date'), __('sub.agent-refund_status')];
        }
        else if($this->type == 'settlement') {
            $header = [__('payment.field10'), __('sub.agent-pay_date'), __('sub.agent-service'), __('sub.agent-site'), __('sub.agent-pay_type'), __('sub.agent-pay_term'), __('sub.agent-pay_amount'), __('sub.agent-settle_amount')];
        }
        return $header;
    }

    public function collection()
    {
        $results = [];
        if($this->type == 'order') {    // 신청내역
            // $result = \DB::table('tbl_client_service AS CS')
            //                 ->selectRaw('CS.order_no, CS.created_at, AS1.name, CS1.name AS cs1_name,CS.process, CS.service_end_at')
            //                 ->where('CS.lang', \Lang::getLocale())
            //                 ->where('CS.created_at', '>=', "{$this->st_date}")
            //                 ->where('CS.created_at', '<=', "{$this->ed_date} 23:59:59");
            //                 //
            // if($this->keyword) {
            //     $result = $result->where('CS1.name', 'LIKE', "%{$this->keyword}%");

            // }
            // $result = $result->leftJoin('tbl_agent_service AS AS1', 'AS1.id', '=', 'CS.service_id')
            //                 ->leftJoin('tbl_client_site AS CS1', 'CS1.id', '=', 'CS.site_id')
            //                 ->where('AS1.agent_id', \Auth::user()->id)
            //                 ->get();

            $result = \App\Models\Client\Service::
                        selectRaw('tbl_client_service.*, tbl_client_service.process AS process1')
                        ->where('tbl_client_service.lang', \Lang::getLocale())
                        ->where('tbl_client_service.created_at', '>=', "{$this->st_date}")
                        ->where('tbl_client_service.created_at', '<=', "{$this->ed_date} 23:59:59");
                        //
            $result = $result->leftJoin('tbl_agent_service AS AS1', 'AS1.id', '=', 'tbl_client_service.service_id')
                            ->leftJoin('tbl_client_site AS CS1', 'CS1.id', '=', 'tbl_client_service.site_id')
                            ->where('AS1.agent_id', \Auth::user()->id);

            if($this->keyword) {
                $result = $result->where(\DB::raw("CONCAT(CS1.name,' ',tbl_client_service.order_no,' ',AS1.name)"), 'LIKE', '%'.$this->keyword.'%');

            }
            $result = $result->get();

            foreach ($result as $key => $value) {
                switch ($value->process) {
                    case 0: $value->process = __('process.wait_request');break;
                    case 1: $value->process = __('process.apply');break;
                    case 2: $value->process = __('process.using');break;
                    case 3: $value->process = __('process.expired');break;
                    case 4: case 5: $value->process = __('process.stop');break;
                }

                $period = "";
                switch($value->period_type){
                    case 0 : $period = $value->period.' 개월'; break;
                    case 1 : $period = $value->period.' 일'; break;
                    case 2 : $period = $value->period; break;
                    default : break;
                }

                $row_arr = [
                    $value->order_no ?? '',
                    $value->created_at,
                    $value->service->name,
                    $value->site->name,
                    $value->service_option,
                    ($value->service_option == '인앱') ? '' : $period ,
                    $value->process,
                    $value->service_end_at
                ];

                array_push($results, $row_arr);
            }

        }
        else if($this->type == 'payment_list') {    // 결제내역
            $result = Payment::
                            selectRaw('tbl_payment.*, AS1.name AS service_name, CS1.name AS site_name ')
                            ->where('tbl_payment.lang', \Lang::getLocale())
                            ->where('tbl_payment.refund_flag', 0)
                            ->where('tbl_payment.created_at', '>=', "{$this->st_date}")
                            ->where('tbl_payment.created_at', '<=', "{$this->ed_date}")
                            ->where('tbl_payment.agent_id', \Auth::user()->id);
                            //
            // if($this->keyword) {
            //     $result = $result->where('CS1.name', 'LIKE', "%{$this->keyword}%");

            // }
            $result = $result->leftJoin('tbl_agent_service AS AS1', 'AS1.id', '=', 'tbl_payment.service_id')
                            ->leftJoin('tbl_client_site AS CS1', 'CS1.id', '=', 'tbl_payment.site_id');

            if($this->keyword) {
                $result = $result->where(DB::raw("CONCAT(CS1.name,' ',tbl_payment.order_no,' ',AS1.name)"), 'LIKE', '%'.$this->keyword.'%');
            }
            $result = $result->orderBy('tbl_payment.id', 'desc')->get();

            foreach ($result as $key => $value) {
                $type = '';
                $payment_type = '';
                switch ($value->type) {
                    case 0: $type = __('process.new');break;
                    case 1: $type = __('process.extension');break;
                    case 2: $type = __('process.refund');break;
                }
                switch ($value->payment_type) {
                    case 0: $payment_type = __('payment.card'); break;
                    case 1: $payment_type = __('payment.account'); break;
                }

                $service_start = '';
                $service_end = '';

                if($value->service_start_at){
                    $service_start = $value->service_start_at->format('Y.m.d');
                }
                if($value->service_end_at){
                    $service_end = ' ~ '.$value->service_end_at->format('Y.m.d');
                }

                $term = "";
                if($value->plan){
                    switch($value->plan->term_unit){
                        case 0 : $term = $value->plan->term.' 개월'; break;
                        case 1 : $term = $value->plan->term.' 일'; break;
                        case 2 : $term = $value->plan->term; break;
                        default : break;
                    }
                }
                $row_arr = [
                    $value->order_no ?? '',
                    $value->created_at,
                    $value->service->name,
                    $value->site->name,
                    $value->type_text ?? '',
                    $value->payment_type_text ?? '',
                    $value->plan->name ?? '',
                    $term,
                    number_format($value->amount),
                    $service_start.$service_end
                ];

                array_push($results, $row_arr);
            }
        }
        else if($this->type == 'refund') {    // 환불내역
            $result = Payment::
                            selectRaw('tbl_payment.*, AS1.name AS service_name, CS1.name AS site_name ')
                            ->where('tbl_payment.lang', \Lang::getLocale())
                            ->where('refund_flag', 1)
                            ->where('tbl_payment.created_at', '>=', "{$this->st_date}")
                            ->where('tbl_payment.created_at', '<=', "{$this->ed_date}")
                            ->where('tbl_payment.agent_id', \Auth::user()->id);
                            //
            // if($this->keyword) {
            //     $result = $result->where('CS1.name', 'LIKE', "%{$this->keyword}%");

            // }
            $result = $result->leftJoin('tbl_agent_service AS AS1', 'AS1.id', '=', 'tbl_payment.service_id')
                            ->leftJoin('tbl_client_site AS CS1', 'CS1.id', '=', 'tbl_payment.site_id');

            if($this->keyword) {
                $result = $result->where(DB::raw("CONCAT(CS1.name,' ',tbl_payment.order_no,' ',AS1.name)"), 'LIKE', '%'.$this->keyword.'%');
            }

            $result = $result->orderBy('tbl_payment.id', 'desc')->get();

            foreach ($result as $key => $value) {

                $row_arr = [
                    $value->order_no ?? '',
                    $value->created_at,
                    $value->service->name,
                    $value->site->name,
                    $value->payment_type_text ?? '',
                    number_format($value->amount),
                    $value->service_start_at ? $value->service_start_at->format('Y.m.d').' ~ ' .$value->service_end_at->format('Y.m.d') : '',
                    $value->refund_request_at,
                    $value->refund_status_text ?? '',
                ];

                array_push($results, $row_arr);
            }
        }
        else if($this->type == 'settlement') {    // 정산관리
            $result = \DB::table('tbl_payment AS P')
                            // ->selectRaw('P.created_at, AS1.name AS service_name, CS1.name AS site_name, P.type, CONCAT(DATE_FORMAT(service_start_at, "%Y.%m.%d"), " ~ ", DATE_FORMAT(service_end_at, "%Y.%m.%d")), CONCAT("(", P.currency, ") ", P.amount), CONCAT("(", P.currency, ") ", P.amount)')
                            ->selectRaw('P.created_at, AS1.name AS service_name, CS1.name AS site_name, P.type, CONCAT(DATE_FORMAT(service_start_at, "%Y.%m.%d"), " ~ ", DATE_FORMAT(service_end_at, "%Y.%m.%d")) AS service_at, P.currency, P.amount')
                            ->where('P.lang', \Lang::getLocale())
                            ->where('P.created_at', 'LIKE', "{$this->st_date}%")
                            ->where('P.agent_id', \Auth::user()->id);
                            //
            // if($this->keyword) {
            //     $result = $result->where('CS1.name', 'LIKE', "%{$this->keyword}%");

            // }
            $result = $result->leftJoin('tbl_agent_service AS AS1', 'AS1.id', '=', 'P.service_id')
                            ->leftJoin('tbl_client_site AS CS1', 'CS1.id', '=', 'P.site_id');

            if($this->keyword) {
                $result = $result->where(DB::raw("CONCAT(CS1.name,' ',P.order_no,' ',AS1.name)"), 'LIKE', '%'.$this->keyword.'%');
            }

            $result = $result->orderBy('P.id', 'desc')->get();

            $fee    = \Auth::user()->fees / 100;
            foreach ($result as $key => $value) {
                switch ($value->type) {
                    case 0: $value->type = __('process.new');break;
                    case 1: $value->type = __('process.extension');break;
                    case 2: $value->type = __('process.refund');break;
                }

                $row_arr = [
                    $value->order_no ?? '',
                    $value->created_at,
                    $value->service_name,
                    $value->site_name,
                    $value->type ?? '',
                    $value->service_at,
                    '('.$value->currency.')'.number_format($value->amount),
                    '('.$value->currency.')'.number_format($value->amount - $value->amount * $fee),
                ];

                array_push($results, $row_arr);
            }
        }
        //
        return collect($results);
    }
}
