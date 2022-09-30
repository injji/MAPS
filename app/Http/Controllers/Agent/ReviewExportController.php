<?php

namespace App\Http\Controllers\Agent;

use DateTime;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use App\Models\Client\Review;
  
class ReviewExportController implements FromCollection, WithHeadings, WithColumnWidths, WithEvents
{
    private $st_date='', $ed_date='';

    public function __construct($st_date='', $ed_date='')
    {
        $this->st_date  = $st_date;
        $this->ed_date  = $ed_date;
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 10,
            'C' => 15,
            'D' => 15,
            'E' => 10,
            'F' => 30,
            'G' => 25,
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
            AfterSheet::class    => function(AfterSheet $event) {
   
                $event->sheet->getDelegate()->getStyle('A1:G1')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
   
            },
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */ 
    public function headings():array{        
        $header = [
            __('review.field2'), __('review.field7'), __('review.field8'), __('review.field3'),
            __('review.field4'), __('review.field5'), __('review.field6')
        ];
        
        return $header;
    } 
   
    public function collection()
    {
        $result = Review::select('tbl_client_review.*', 'tbl_agent_service.name as service_name', 'tbl_users.manager_name as client_name')
                        ->leftJoin('tbl_agent_service', 'tbl_agent_service.id', '=', 'tbl_client_review.service_id')
                        ->join('tbl_users', 'tbl_users.id', '=', 'tbl_client_review.client_id')
                        ->where('tbl_agent_service.agent_id', \Auth::user()->id)                        
                        ->where('tbl_client_review.lang', \Lang::getLocale())
                        ->whereBetween('tbl_client_review.created_at', [$this->st_date, $this->ed_date.' 23:59:59'])
                        ->orderBy('tbl_client_review.id', 'desc')
                        ->get();

        $result_arr = array();

        foreach ($result as $key => $value)
        {
            $row_arr = [
                $value->created_at,
                config('app.lang_text.'.$value->lang),
                $value->client_name,
                $value->service_name,
                $value->rating,
                $value->content,
                $value->answer ? __('review.answer1').' ('.substr($value->answered_at, 0, -9).')' : __('review.answer2')
            ];
            
            array_push($result_arr, $row_arr);
        }

        return collect($result_arr);
    }
}
