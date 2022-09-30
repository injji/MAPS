<?php

namespace App\Http\Controllers\Cms;

use DateTime;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CompanyExportController implements FromCollection, WithHeadings, WithColumnWidths, WithEvents
{
    private $st_date = '', $ed_date = '', $agent_id = 0, $service_id = 0, $type = 0, $row_cnt = 1;

    public function __construct($st_date = '', $ed_date = '', $agent_id = 0, $service_id = 0, $type = 0)
    {
        $this->st_date = $st_date;
        $this->ed_date = $ed_date;
        $this->agent_id = $agent_id;
        $this->service_id = $service_id;
        $this->type = $type;
    }

    public function columnWidths(): array
    {
        if (in_array($this->type, [1, 2]))
        {
            return [
                'A' => 20,
                'B' => 20,
                'C' => 15,
                'D' => 20,
                'E' => 25,
                'F' => 15,
                'G' => 20
            ];
        }
        else if (in_array($this->type, [3, 4, 5]))
        {
            return [
                'A' => 20,
                'B' => 20,
                'C' => 20,
                'D' => 25,
                'E' => 25,
                'F' => 15,
                'G' => 15
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
        if (in_array($this->type, [1, 2, 3, 5]))
        {
            return [
                AfterSheet::class => function(AfterSheet $event) {
                    $event->sheet->getDelegate()->getStyle('A1:G'.$this->row_cnt)
                                    ->getAlignment()
                                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                },
            ];
        }
        else if ($this->type == 4)
        {
            return [
                AfterSheet::class => function(AfterSheet $event) {
                    $event->sheet->getDelegate()->getStyle('A1:E'.$this->row_cnt)
                                    ->getAlignment()
                                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                },
            ];
        }
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings():array{
        $header = [];

        if ($this->type == 1)
            $header = ['아이디', '회사명', '담당자', '휴대폰번호', '이메일', '이용서비스', '가입일시'];
        else if ($this->type == 2)
            $header = ['아이디', '회사명', '담당자', '휴대폰번호', '이메일', '서비스', '가입일시'];
        else if ($this->type == 3)
            $header = ['등록일', '제휴사', '서비스', '문의유형', '제목', '고객사', '답변여부'];
        else if ($this->type == 4)
            $header = ['등록일', '제휴사', '문의유형', '제목', '답변여부'];
        else if ($this->type == 5)
            $header = ['등록일', '제휴사', '서비스명', '평점', '내용', '고객사', '노출여부'];

        return $header;
    }

    public function collection()
    {
        if ($this->type == 1)
        {
            $result = \App\Models\Users::whereBetween('created_at', [$this->st_date, $this->ed_date.' 23:59:59'])
                    ->where('type', 1)
                    ->orderBy('id', 'desc')
                    ->get();

            $result_arr = array();
            $this->row_cnt = $result->count() + 1;

            foreach ($result as $key => $value)
            {
                $row_arr = [
                    $value->account,
                    $value->company_name,
                    $value->manager_name,
                    $value->manager_phone,
                    $value->manager_email,
                    $value->client_service->count(),
                    $value->created_at
                ];

                array_push($result_arr, $row_arr);
            }

            return collect($result_arr);
        }
        else if ($this->type == 2)
        {
            $result = \App\Models\Users::whereBetween('created_at', [$this->st_date, $this->ed_date.' 23:59:59'])
                    ->where('type', 2)
                    ->orderBy('id', 'desc')
                    ->get();

            $result_arr = array();
            $this->row_cnt = $result->count() + 1;

            foreach ($result as $key => $value)
            {
                $row_arr = [
                    $value->account,
                    $value->company_name,
                    $value->manager_name,
                    $value->manager_phone,
                    $value->manager_email,
                    $value->service->count(),
                    $value->created_at
                ];

                array_push($result_arr, $row_arr);
            }

            return collect($result_arr);
        }
        else if ($this->type == 3)
        {
            $agent_id = $this->agent_id;
            $service_id = $this->service_id;

            $result = \App\Models\Client\Inquiry::whereBetween('created_at', [$this->st_date, $this->ed_date.' 23:59:59'])
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
                    ->orderBy('id', 'desc')
                    ->get();

            $result_arr = array();
            $this->row_cnt = $result->count() + 1;

            foreach ($result as $key => $value)
            {
                $row_arr = [
                    $value->created_at,
                    $value->service->user->company_name,
                    $value->service->name,
                    $value->type_text,
                    $value->title,
                    $value->author->company_name,
                    $value->answered_at != null ? '답변완료' : '미답변'
                ];

                array_push($result_arr, $row_arr);
            }

            return collect($result_arr);
        }
        else if ($this->type == 4)
        {
            $agent_id = $this->agent_id;

            $result = \App\Models\Agent\Inquiry::whereBetween('created_at', [$this->st_date, $this->ed_date.' 23:59:59'])
                    ->where(function($q) use ($agent_id) {
                        if($agent_id > 0) {
                            $q->where('agent_id', $agent_id);
                        }
                    })
                    ->orderBy('id', 'desc')
                    ->get();

            $result_arr = array();
            $this->row_cnt = $result->count() + 1;

            foreach ($result as $key => $value)
            {
                $row_arr = [
                    $value->created_at,
                    $value->user->company_name,
                    $value->type_text,
                    $value->title,
                    $value->answered_at != null ? '답변완료' : '미답변'
                ];

                array_push($result_arr, $row_arr);
            }

            return collect($result_arr);
        }
        else if ($this->type == 5)
        {
            $agent_id = $this->agent_id;
            $service_id = $this->service_id;

            $result = \App\Models\Client\Review::whereBetween('created_at', [$this->st_date, $this->ed_date.' 23:59:59'])
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
                    ->orderBy('id', 'desc')
                    ->get();

            $result_arr = array();
            $this->row_cnt = $result->count() + 1;

            foreach ($result as $key => $value)
            {
                $row_arr = [
                    $value->created_at,
                    $value->service->user->company_name,
                    $value->service->name,
                    $value->rating,
                    $value->content,
                    $value->author->company_name,
                    $value->visible == 1 ? 'True' : 'False'
                ];

                array_push($result_arr, $row_arr);
            }

            return collect($result_arr);
        }
    }
}
