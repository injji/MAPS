<?php

namespace App\Http\Controllers\Cms;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class UserDropExportController implements FromCollection,WithHeadings
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
        if($this->type == 'drop') {
            $header = ['요청일','구분','아이디','회사명','사유','서비스','완료일'];
        }

        return $header;
    }

    public function collection()
    {
        $results = [];
        if($this->type == 'drop') {    // 신청내역

            $results = \App\Models\UserDrop::orderBy('id', 'desc')
                        ->where('created_at', '>=', "{$this->st_date}")
                        ->where('created_at', '<=', "{$this->ed_date} 23:59:59");

            if($this->keyword) {
                $results = $results->where(DB::raw("CONCAT(account,' ',company_name)"), 'LIKE', '%'.$this->keyword.'%');
            }

            $results = $results->get();

            $list = [];
            foreach ($results as $key => $value) {
                $list[$key]['created_at'] = $value->created_at;
                $list[$key]['user_type'] = $value->user_type;
                $list[$key]['account'] = $value->account;
                $list[$key]['company_name'] = $value->company_name;
                $list[$key]['reason'] = $value->reason;
                switch ($value->user_type) {
                    case '고객사': $list[$key]['site_count'] = $value->client_service->count(); break;
                    case '제휴사': $list[$key]['site_count'] = $value->agent_service->count(); break;
                }
                $list[$key]['dropped_at'] = $value->created_at;

            }
        }

        //
        return collect($list);
    }
}
