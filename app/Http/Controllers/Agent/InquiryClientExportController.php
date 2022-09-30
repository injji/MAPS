<?php

namespace App\Http\Controllers\Agent;

use DateTime;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use App\Models\Client\Inquiry;
  
class InquiryClientExportController implements FromCollection, WithHeadings, WithColumnWidths, WithEvents
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
            'E' => 30,
            'F' => 30,            
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
   
                $event->sheet->getDelegate()->getStyle('A1:F1')
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
            __('inquiry.field2'), __('inquiry.field7'), __('inquiry.field3'),
            __('inquiry.field4'), __('inquiry.field5'), __('inquiry.field6')
        ];
        
        return $header;
    } 
   
    public function collection()
    {
        $result = Inquiry::select('tbl_client_inquiry.*', 'tbl_agent_service.name as service_name', 'tbl_users.manager_name as client_name')
                        ->leftJoin('tbl_agent_service', 'tbl_agent_service.id', '=', 'tbl_client_inquiry.service_id')
                        ->join('tbl_users', 'tbl_users.id', '=', 'tbl_client_inquiry.client_id')
                        ->where('tbl_agent_service.agent_id', \Auth::user()->id)                        
                        ->where('tbl_client_inquiry.lang', \Lang::getLocale())
                        ->whereBetween('tbl_client_inquiry.created_at', [$this->st_date, $this->ed_date.' 23:59:59'])
                        ->orderBy('tbl_client_inquiry.id', 'desc')
                        ->get();

        $result_arr = array();

        foreach ($result as $key => $value)
        {
            $row_arr = [
                $value->created_at,                
                $value->client_name,
                $value->service_name,
                $value->type_text,
                $value->title,
                $value->answer ? __('inquiry.answer1').' ('.substr($value->answered_at, 0, -9).')' : __('inquiry.answer2')
            ];
            
            array_push($result_arr, $row_arr);
        }

        return collect($result_arr);
    }
}
