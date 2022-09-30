<?php

namespace App\Http\Controllers\Cms;

use DateTime;

use App\Models\Cms\ServiceStat;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
  
class StatExportController implements FromCollection,WithHeadings, WithColumnWidths, WithEvents
{
    private $type = '', $st_date = '', $ed_date = '', $agent_id = 0, $service_id = 0, $category_id = 0, $tab = 1, $row_cnt = 1;

    public function __construct($type = '', $st_date = '', $ed_date = '', $tab = '', $agent_id = 0, $service_id = 0, $category_id = 0)
    {
        $this->type     = $type;
        $this->st_date  = $st_date;
        $this->ed_date  = $ed_date;        
        $this->tab      = $tab;
        $this->agent_id    = $agent_id;
        $this->service_id  = $service_id;
        $this->category_id = $category_id;
    }
    
    public function columnWidths(): array
    {
        if ($this->type == 'using') {
            return [
                'A' => 15,
                'B' => 15,
                'C' => 25,
                'D' => 25,
                'E' => 15,
                'F' => 15            
            ];
        }
        else if ($this->type == 'service') {
            return [
                'A' => 20,
                'B' => 20,
                'C' => 50,
                'D' => 15,
                'E' => 15,
                'F' => 15,
                'G' => 15,
                'H' => 15,
                'I' => 15,
                'J' => 20
            ];
        }
        else if ($this->type == 'category') {
            return [
                'A' => 50,
                'B' => 15,
                'C' => 15,
                'D' => 15,
                'E' => 15,
                'F' => 15                
            ];
        }
        else if ($this->type == 'agent') {
            return [
                'A' => 20,
                'B' => 15,
                'C' => 15,
                'D' => 15,
                'E' => 15,
                'F' => 15,
                'G' => 15,
                'H' => 20
            ];
        }
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registerEvents(): array
    {
        $char = '';

        if ($this->type == 'using' || $this->type == 'category') {
            return [
                AfterSheet::class => function(AfterSheet $event) {   
                    $event->sheet->getDelegate()->getStyle('A1:F'.$this->row_cnt)
                                    ->getAlignment()
                                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                },
            ];
        }
        else if ($this->type == 'service') {
            return [
                AfterSheet::class => function(AfterSheet $event) {   
                    $event->sheet->getDelegate()->getStyle('A1:J'.$this->row_cnt)
                                    ->getAlignment()
                                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                },
            ];
        }  
        else if ($this->type == 'agent') {
            return [
                AfterSheet::class => function(AfterSheet $event) {   
                    $event->sheet->getDelegate()->getStyle('A1:H'.$this->row_cnt)
                                    ->getAlignment()
                                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                },
            ];
        }  
    }

    /**
    * @return \Illuminate\Support\Collection
    */ 
    public function headings() : array {
        $header = array();

        if($this->type == 'using')
            $header = ['DATE', 'DAU', '신규가입(제휴사)', '신규가입(고객사)', '신청수', '매출액'];        
        else if($this->type == 'service')
            $header = ['제휴사', '서비스명', '카테고리', '클릭수', '리뷰', '신규', '연장', '매출건수', '매출합계', '등록일'];
        else if($this->type == 'category')
            $header = ['카테고리', '클릭수', '신규', '연장', '매출건수', '매출합계'];
        else if($this->type == 'agent')
            $header = ['제휴사명', '서비스', '리뷰', '신규', '연장', '매출건수', '매출합계', '가입일시'];
                
        return $header;
    } 
   
    public function collection()
    {
        $results = [];

        if($this->type == 'using') {    // 이용통계
            $result = ServiceStat::getUsingDatas($this->st_date, $this->ed_date, $this->tab);
            $result = collect($result)->sortBy('day')->reverse()->toArray();
            foreach ($result as $key => $value) {
                $row_arr = [
                    $value->day,
                    number_format($value->dau_cnt),
                    number_format($value->dau_cnt),
                    number_format($value->dau_cnt),
                    number_format($value->dau_cnt),
                    number_format($value->dau_cnt),
                ];
                
                array_push($results, $row_arr);
            }

            $this->row_cnt = count($results) + 1;

            return collect($results);
        }
        else if($this->type == 'service') {    // 서비스별 통계
            $agent_id = $this->agent_id;
            $service_id = $this->service_id;
            $category_id = $this->category_id;

            $result = \App\Models\Agent\Service::whereBetween('created_at', [$this->st_date, $this->ed_date.' 23:59:59'])
                    ->when($agent_id > 0, function($q) use ($agent_id) {
                        $q->where('agent_id', $agent_id);                            
                    })
                    ->when($service_id > 0, function($q) use ($service_id) {
                        $q->where('id', $service_id);                            
                    })
                    ->when($category_id > 0, function($q) use ($category_id) {                            
                        $q->where('category1', $category_id);                            
                    })                    
                    ->orderBy('id', 'desc')
                    ->get();

            $result_arr = array();
            $this->row_cnt = $result->count() + 1;

            foreach ($result as $key => $value)
            {
                $row_arr = [
                    $value->user->company_name,
                    $value->name,
                    $value->cat1->text.' > '.$value->cat2->text,
                    number_format($value->view_cnt),
                    number_format($value->review->count()),
                    number_format($value->payment0->count()),
                    number_format($value->payment1->count()),
                    number_format($value->payment->count()),
                    number_format($value->payment->sum('amount')),                    
                    $value->created_at
                ];
                
                array_push($result_arr, $row_arr);
            }

            return collect($result_arr);
        }
        else if($this->type == 'category') {    // 카테고리별 통계
            $sub_query = \App\Models\Agent\Service::selectRaw('*, 
                        (SELECT COUNT(*) FROM tbl_payment WHERE TYPE=0 AND service_id=tbl_agent_service.id) AS payment0_cnt,
                        (SELECT COUNT(*) FROM tbl_payment WHERE TYPE=1 AND service_id=tbl_agent_service.id) AS payment1_cnt,
                        (SELECT COUNT(*) FROM tbl_payment WHERE service_id=tbl_agent_service.id) AS payment_cnt,
                        (SELECT SUM(amount) FROM tbl_payment WHERE service_id=tbl_agent_service.id) AS sum_amount')
                        ->whereBetween('created_at', [$this->st_date, $this->ed_date.' 23:59:59']);

            $result = \App\Models\Agent\Service::selectRaw('T.*, 
                    SUM(T.view_cnt) AS sum_view_cnt, SUM(T.payment0_cnt) AS sum_payment0_cnt,
                    SUM(T.payment1_cnt) AS sum_payment1_cnt, SUM(T.payment_cnt) AS sum_payment_cnt,
                    SUM(T.sum_amount) AS total_amount')
                    ->from($sub_query, 'T')
                    ->groupBy('T.category1', 'T.category2')                    
                    ->orderBy('sum_view_cnt', 'desc')                    
                    ->get();

            $result_arr = array();
            $this->row_cnt = $result->count() + 1;

            foreach ($result as $key => $value)
            {
                $row_arr = [
                    $value->cat1->text.' > '.$value->cat2->text,                    
                    number_format($value->sum_view_cnt),
                    number_format($value->sum_payment0_cnt),
                    number_format($value->sum_payment1_cnt),
                    number_format($value->sum_payment_cnt),
                    number_format($value->total_amount)
                ];
                
                array_push($result_arr, $row_arr);
            }

            return collect($result_arr);
        }
        else if($this->type == 'agent') {    // 제휴사 통계            
            $result = \App\Models\Users::whereBetween('created_at', [$this->st_date, $this->ed_date.' 23:59:59'])
                    ->where('type', 2)                 
                    ->orderBy('id', 'desc')
                    ->get();

            $result_arr = array();
            $this->row_cnt = $result->count() + 1;

            foreach ($result as $key => $value)
            {
                $row_arr = [
                    $value->company_name,                    
                    number_format($value->service->count()),
                    number_format($value->review->count()),
                    number_format($value->payment0->count()),
                    number_format($value->payment1->count()),
                    number_format($value->payment->count()),
                    number_format($value->payment->sum('amount')),                    
                    $value->created_at
                ];
                
                array_push($result_arr, $row_arr);
            }

            return collect($result_arr);
        }
    }
}
