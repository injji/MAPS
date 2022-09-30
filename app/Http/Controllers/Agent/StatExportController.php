<?php

namespace App\Http\Controllers\Agent;

use DateTime;

use App\Models\Agent\{Service, ServiceStat};
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
  
class StatExportController implements FromCollection,WithHeadings
{
    private $type='', $st_date='', $ed_date='', $lang='', $keyword=''    ;

    public function __construct($type='', $st_date='', $ed_date='', $lang='', $keyword='')
    {
        $this->type     = $type;
        $this->st_date  = $st_date;
        $this->ed_date  = $ed_date;
        $this->lang     = $lang;
        $this->keyword  = $keyword;
    }

    /**
    * @return \Illuminate\Support\Collection
    */ 
    public function headings():array{
        $header = array();
        if($this->type == 'order') {
            $header = [__('sub.agent-date'), __('sub.agent-state1'), __('sub.agent-state2'), __('sub.agent-state3'), __('sub.agent-state4')];
        }
        else if($this->type == 'sales') {
            $header = [
                __('sub.agent-date'), 
                __('sub.agent-new').'_'.__('sub.agent-su'), 
                __('sub.agent-new').'_'.__('sub.agent-pay'), 
                __('sub.agent-more').'_'.__('sub.agent-su'), 
                __('sub.agent-more').'_'.__('sub.agent-pay'), 
                __('sub.agent-hab').'_'.__('sub.agent-su'), 
                __('sub.agent-hab').'_'.__('sub.agent-pay'), 
            ];
        }
        else if($this->type == 'service') {
            $header = [__('sub.agent-date'), __('sub.agent-nu_service'), __('sub.agent-nu_service_go'), __('sub.agent-nu_service_ug')];
        }
        return $header;
    } 
   
    public function collection()
    {
        $results = [];
        if($this->type == 'order') {    // 신청
            $result = [];
            $result = ServiceStat::getOrderDatas($this->st_date, $this->ed_date, $this->lang);
            $total=0; $wait_cnt=0; $complete_cnt=0; $expire_cnt=0;
            // 
            $result = collect($result)->sortBy('day')->reverse()->toArray();
            foreach ($result as $key => $item) {
                $sum = $item->wait_cnt + $item->complete_cnt + $item->expire_cnt; 
                $total          += $sum;
                $wait_cnt       += $item->wait_cnt; 
                $complete_cnt   += $item->complete_cnt; 
                $expire_cnt     += $item->expire_cnt; 
                $tmp  = [$item->day, $sum, $item->wait_cnt, $item->complete_cnt, $item->expire_cnt];
                array_push($results, $tmp);
            }
            $tmp  = [__('sub.agent-total'), $total, $wait_cnt, $complete_cnt, $expire_cnt];
            array_push($results, $tmp);
        }
        else if($this->type == 'sales') {    // 매출
            $result = [];
            $result = ServiceStat::getSalesDatas($this->st_date, $this->ed_date, $this->lang);
            // 
            $result = collect($result)->sortBy('day')->reverse()->toArray();
            foreach ($result as $key => $item) {
                $extend_cnt = $item->new_cnt + $item->extend_cnt; 
                $extend_sum = $item->new_sum + $item->extend_sum; 

                $tmp  = [$item->day, $item->new_cnt, $item->new_sum, $item->extend_cnt, $item->extend_sum, $extend_cnt, $extend_sum];
                array_push($results, $tmp);
            }
        }
        else if($this->type == 'service') {    // service
            $result = [];
            $result = ServiceStat::getServiceDatas($this->st_date, $this->ed_date, $this->lang);
            // 
            $result = collect($result)->sortBy('day')->reverse()->toArray();
            foreach ($result as $key => $item) {
                $using_cnt = $item->prev_using_cnt + ($item->using_cnt - $item->expire_cnt); 
                $wait_cnt  = $item->prev_wait_cnt + $item->wait_cnt; 
                $percent   = $wait_cnt > 0 ? ($using_cnt / $wait_cnt * 100) : 0;

                $tmp  = [$item->day, $using_cnt, $wait_cnt, $percent];
                array_push($results, $tmp);
            }
        }
        // $results = collect($results)->sortBy('day')->reverse()->toArray();
        // 
        return collect($results);
    }
}
