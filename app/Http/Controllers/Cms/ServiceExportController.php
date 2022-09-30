<?php

namespace App\Http\Controllers\Cms;

use DateTime;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ServiceExportController implements FromCollection, WithHeadings, WithColumnWidths, WithEvents
{
    private $st_date = '', $ed_date = '', $agent_id = 0, $service_id = 0, $category_id = 0, $row_cnt = 1;

    public function __construct($st_date = '', $ed_date = '', $agent_id = 0, $service_id = 0, $category_id = 0)
    {
        $this->st_date = $st_date;
        $this->ed_date = $ed_date;
        $this->agent_id = $agent_id;
        $this->service_id = $service_id;
        $this->category_id = $category_id;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 45,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 20,
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
        $header = ['제휴사명', '서비스명', '카테고리', '진행상태', '노출여부', '인앱결제', '조회수', '누적신청수', '등록일'];

        return $header;
    }

    public function collection()
    {
        $agent_id = $this->agent_id;
        $service_id = $this->service_id;
        $category_id = $this->category_id;

        $result = \App\Models\Agent\Service::whereBetween('created_at', [$this->st_date, $this->ed_date.' 23:59:59'])
                ->where(function($q) use ($agent_id) {
                    if($agent_id > 0) {
                        $q->where('agent_id', $agent_id);
                    }
                })
                ->where(function($q) use ($service_id) {
                    if($service_id > 0) {
                        $q->where('id', $service_id);
                    }
                })
                ->where(function($q) use ($category_id) {
                    if($category_id > 0) {
                        $q->where('category1', $category_id);
                    }
                })
                ->orderBy('id', 'desc')
                ->get();

        $result_arr = array();
        $this->row_cnt = $result->count() + 1;

        foreach ($result as $key => $value)
        {
            $cat1 = '';
            $cat2 = '';
            if($value->cat1){
                $cat1 = $value->cat1->text;
            }
            if($value->cat2){
                $cat2 = ' > '.$value->cat2->text;
            }
            $row_arr = [
                $value->user->company_name,
                $value->name,
                $cat1.$cat2,
                $value->process_text,
                $value->visible == 1 ? 'True' : 'False',
                $value->in_app_payment == 1 ? 'True' : 'False',
                number_format($value->view_cnt),
                number_format($value->request_cnt),
                $value->created_at,
            ];

            array_push($result_arr, $row_arr);
        }

        return collect($result_arr);
    }
}
