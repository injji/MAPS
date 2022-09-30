<?php

namespace App\Http\Controllers\Cms;

use DateTime;

use App\Models\Agent\{Service};
use App\Models\Payment;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class OrderExportController implements FromCollection,WithHeadings, WithColumnWidths, WithEvents
{
    private $type='', $st_date='', $ed_date='', $keyword='', $agent_id='', $service_id='', $category_id='', $row_cnt = 1;

    public function __construct($type='', $st_date='', $ed_date='', $keyword='', $agent_id='', $service_id='', $category_id='')
    {
        $this->type     = $type;
        $this->st_date  = $st_date;
        $this->ed_date  = $ed_date;
        $this->keyword  = $keyword;
        $this->agent_id    = $agent_id;
        $this->service_id  = $service_id;
        $this->category_id = $category_id;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 30,
            'C' => 20,
            'D' => 30,
            'E' => 30,
            'F' => 30,
            'G' => 30,
            'H' => 30,
            'I' => 30,
        ];
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:I'.$this->row_cnt)
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings():array{
        $header = array();
        if($this->type == 'order') {
            $header = ['주문번호', '신청일', '제휴사', '서비스명', '상품옵션', '서비스옵션', '사이트명', '상태', '만료일'];
        }
        else if($this->type == 'payment_list') {
            $header = ['주문번호', '결제일', '제휴사', '서비스명', '사이트', '구분', '결제방식', '상품옵션', '서비스옵션', '결제금액', '이용기간'];
        }
        else if($this->type == 'refund') {
            $header = ['주문번호', '결제일', '제휴사', '서비스명', '사이트', '결제방식', '결제금액', '이용기간', '환불요청일', '처리상태'];
        }
        else if($this->type == 'settle_summary') {
            $header = ['정산상태', '기준', '제휴사', '신청수', '결제금액', '정산금액', '수수료'];
        }
        else if($this->type == 'settle_detail') {
            $header = ['주문번호', '결제일', '제휴사', '서비스명', '사이트명', '구분', '결제수단', '이용기간', '결제금액', '정산금액'];
        }
        return $header;
    }

    public function collection()
    {
        $results = [];
        $agent_id    = $this->agent_id;
        $service_id  = $this->service_id;
        $category_id = $this->category_id;
        $keyword = $this->keyword;

        if($this->type == 'order') {    // 신청내역

            $result = \App\Models\Client\Service::
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
                            ->whereBetween('created_at', [$this->st_date, $this->ed_date.' 23:59:59']);

            if($keyword) {
                $result = $result->whereHas('site', function($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%${keyword}%");
                });
                $result = $result->orWhereHas('service', function($q) use ($keyword){
                    $q->where('name', 'LIKE', "%${keyword}%");
                });
                $result = $result->orWhere('order_no','LIKE',"%{$keyword}%");
            }


            $result = $result->orderBy('created_at', 'desc')->get();

            $this->row_cnt = $result->count() + 1;

            foreach ($result as $key => $value) {
                $period = "";
                switch($value->period_type){
                    case 0 : $period = $value->period.' 개월'; break;
                    case 1 : $period = $value->period.' 일'; break;
                    case 2 : $period = $value->period; break;
                    default : break;
                }
                $row_arr = [
                    $value->order_no,
                    substr($value->created_at, 0, 10),
                    $value->service->user->company_name,
                    $value->service->name,
                    $value->service_option,
                    ($value->service_option == '인앱') ? '' : $period,
                    $value->site->name,
                    $value->process_text,
                    substr($value->service_end_at, 0, 10)
                ];

                array_push($results, $row_arr);
            }
        }
        else if($this->type == 'payment_list') {    // 결제내역

            $result = Payment::
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
                            ->whereBetween('created_at', [$this->st_date, $this->ed_date.' 23:59:59']);

            if($keyword) {
                $result = $result->whereHas('site', function($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%${keyword}%");
                });
                $result = $result->orWhereHas('service', function($q) use ($keyword){
                    $q->where('name', 'LIKE', "%${keyword}%");
                });
                $result = $result->orWhere('order_no','LIKE',"%{$keyword}%");
            }

            $result = $result->orderBy('created_at', 'desc')->get();

            $this->row_cnt = $result->count() + 1;


            foreach ($result as $key => $value) {

                $service_start_at = ($value->service_start_at) ? $value->service_start_at->format('Y.m.d') : '';
                $service_end_at = ($value->service_end_at) ? ' ~ '.$value->service_end_at->format('Y.m.d') : '';

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
                    substr($value->created_at, 0, 10),
                    $value->service->user->company_name,
                    $value->service->name,
                    $value->site->name,
                    $value->type_text ?? '',
                    $value->payment_type_text ?? '',
                    $value->plan->name ?? '',
                    $term,
                    number_format($value->amount),
                    $service_start_at.$service_end_at
                ];

                array_push($results, $row_arr);
            }
        }
        else if($this->type == 'refund') {    // 환불내역
            $result = Payment::
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
                            ->whereBetween('created_at', [$this->st_date, $this->ed_date.' 23:59:59']);

            if($keyword) {
                $result = $result->whereHas('site', function($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%${keyword}%");
                });
                $result = $result->orWhereHas('service', function($q) use ($keyword){
                    $q->where('name', 'LIKE', "%${keyword}%");
                });
                $result = $result->orWhere('order_no','LIKE',"%{$keyword}%");
            }

            $result = $result->orderBy('created_at', 'desc')->get();

            $this->row_cnt = $result->count() + 1;

            foreach ($result as $key => $value) {

                $service_start_at = ($value->service_start_at) ? $value->service_start_at->format('Y.m.d') : '';
                $service_end_at = ($value->service_end_at) ? ' ~ '.$value->service_end_at->format('Y.m.d') : '';

                $row_arr = [
                    $value->order_no ?? '',
                    substr($value->created_at, 0, 10),
                    $value->service->user->company_name,
                    $value->service->name,
                    $value->site->name,
                    $value->payment_type_text ?? '',
                    number_format($value->amount),
                    $service_start_at.$service_end_at,
                    substr($value->created_at, 0, 10),
                    $value->refund_status_text ?? ''
                ];

                array_push($results, $row_arr);
            }
        }
        else if($this->type == 'settle_summary') {    // 정산요약

            $keyword = $this->keyword;
            $result = Payment::
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
                            ->where('created_at', 'LIKE', "{$this->st_date}%")
                            ->orderBy('created_at', 'desc')
                            ->get();

            $this->row_cnt = $result->count() + 1;

            foreach ($result as $key => $value) {
                $settle_status = '대기';
                switch ($value->settle_status) {
                    case 1: $settle_status = '대기'; break;
                    case 2: $settle_status = '불가'; break;
                    case 3: $settle_status = '완료'; break;
                }
                $row_arr = [
                    $settle_status,
                    substr($value->created_at, 0, 7),
                    $value->service->user->company_name,
                    number_format($value->req_cnt),
                    number_format($value->total_sum),
                    number_format($value->total_sum * 0.85)
                ];

                array_push($results, $row_arr);
            }
        }
        else if($this->type == 'settle_detail') {    // 정산상세내역

            $keyword = $this->keyword;
            $result  = Payment::
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
                            ->where('created_at', 'LIKE', "{$this->st_date}%")
                            ->orderBy('created_at', 'desc')
                            ->get();

            $this->row_cnt = $result->count() + 1;

            foreach ($result as $key => $value) {
                $row_arr = [
                    substr($value->created_at, 0, 7),
                    $value->service->user->company_name,
                    $value->service->name,
                    $value->service->cat1->text. ' > ' .$value->service->cat2->text,
                    $value->site->name,
                    $value->service->in_app_payment == 1 ? '인앱' : '자체',
                    $value->order_no ?? '',
                    $value->payment_type_text ?? '',
                    $value->service_start_at ? ($value->service_start_at->format('Y.m.d').' ~ ' .$value->service_end_at->format('Y.m.d')) : '',
                    number_format($value->amount),
                    number_format($value->amount * 0.85)
                ];

                array_push($results, $row_arr);
            }
        }
        //
        return collect($results);
    }
}
