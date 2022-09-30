<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Notice;
use App\Models\Agent\Alim;
use View;
use DB;

class DashBoardController extends Controller
{
    private function getDateRange(Carbon $start_date, Carbon $end_date, $step)
    {
        $dates = [];

        for($date = $start_date->copy(); $date->lte($end_date); $date->addDay($step))
        {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }

    private function getDateRange1(Carbon $start_date, Carbon $end_date, $step)
    {
        $dates = [];

        for($date = $start_date->copy(); $date->lte($end_date); $date->addDay($step))
        {
            $dates[] = $date->format('n.j');
        }

        return $dates;
    }

    /**
     * 대시보드 view
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function __invoke(Request $request)
    {
        View::share('activeMenu', 'agent.dashboard');

        $cur_month_cnt1 = \App\Models\Client\Service::whereMonth('tbl_client_service.created_at', Carbon::now()->month)
                        ->whereHas('service', function($q){
                            $q->where('agent_id', \Auth::user()->id);
                        })
                        ->count();
        $today_cnt1 = \App\Models\Client\Service::whereDate('tbl_client_service.created_at', Carbon::today())
                        ->whereHas('service', function($q){
                            $q->where('agent_id', \Auth::user()->id);
                        })
                        ->count();
        $total_cnt1 = \App\Models\Client\Service::whereHas('service', function($q){
                            $q->where('agent_id', \Auth::user()->id);
                        })->count();

        $cur_month_cnt2 = \App\Models\Client\Service::whereMonth('tbl_client_service.created_at', Carbon::now()->month)
                        ->where('tbl_client_service.process', 2)
                        ->whereHas('service', function($q){
                            $q->where('agent_id', \Auth::user()->id);
                        })
                        ->count();
        $today_cnt2 = \App\Models\Client\Service::whereDate('tbl_client_service.created_at', Carbon::today())
                        ->where('tbl_client_service.process', 2)
                        ->whereHas('service', function($q){
                            $q->where('agent_id', \Auth::user()->id);
                        })
                        ->count();
        $total_cnt2 = \App\Models\Client\Service::where('tbl_client_service.process', 2)
                        ->whereHas('service', function($q){
                            $q->where('agent_id', \Auth::user()->id);
                        })
                        ->count();

        $cur_month_cnt3 = \App\Models\Payment::whereMonth('tbl_payment.created_at', Carbon::now()->month)
                        ->whereHas('service', function($q){
                            $q->where('agent_id', \Auth::user()->id);
                        })
                        ->sum('tbl_payment.amount');
        $today_cnt3 = \App\Models\Payment::whereDate('tbl_payment.created_at', Carbon::today())
                        ->whereHas('service', function($q){
                            $q->where('agent_id', \Auth::user()->id);
                        })
                        ->sum('tbl_payment.amount');
        $total_cnt3 = \App\Models\Payment::whereHas('service', function($q){
                            $q->where('agent_id', \Auth::user()->id);
                        })
                        ->sum('tbl_payment.amount');

        $start_date = Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime("-31 days", strtotime(date('Y-m-d')))));
        $end_date = Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime("-1 days", strtotime(date('Y-m-d')))));

        $dates = self::getDateRange($start_date, $end_date, 5);

        $arr1 = \App\Models\Client\Service::whereBetween('tbl_client_service.created_at', [$start_date->format('Y-m-d')." 00:00:00", $end_date->format('Y-m-d')." 23:59:59"])
                    ->whereHas('service', function($q){
                        $q->where('agent_id', \Auth::user()->id);
                    })
                    ->orderBy('tbl_client_service.created_at')
                    ->get('tbl_client_service.created_at')
                    ->groupBy(function($item) {
                        return $item->created_at->format('Y-m-d');
                    });

        $arr2 = \App\Models\Client\Service::whereBetween('tbl_client_service.created_at', [$start_date->format('Y-m-d')." 00:00:00", $end_date->format('Y-m-d')." 23:59:59"])
                    ->where('tbl_client_service.process', 2)
                    ->whereHas('service', function($q){
                        $q->where('agent_id', \Auth::user()->id);
                    })
                    ->orderBy('tbl_client_service.created_at')
                    ->get('tbl_client_service.created_at')
                    ->groupBy(function($item) {
                        return $item->created_at->format('Y-m-d');
                    });

        $arr3 = \App\Models\Payment::whereBetween('tbl_payment.created_at', [$start_date->format('Y-m-d')." 00:00:00", $end_date->format('Y-m-d')." 23:59:59"])                    
                    ->select(DB::raw('DATE(tbl_payment.created_at) as date'), DB::raw('sum(tbl_payment.amount) as sum_amount'))
                    ->whereHas('service', function($q){
                        $q->where('agent_id', \Auth::user()->id);
                    })
                    ->orderBy('date')
                    ->groupBy('date')
                    ->get();

        $chart1_arr = array();
        $chart2_arr = array();
        $chart3_arr = array();
        $date_arr = self::getDateRange1($start_date, $end_date, 5);
        
        foreach ($dates as $key => $date)
        {
            if(isset($arr1[$date]))
                array_push($chart1_arr, $arr1[$date]->count());
            else
                array_push($chart1_arr, 0);

            if(isset($arr2[$date]))
                array_push($chart2_arr, $arr2[$date]->count());
            else
                array_push($chart2_arr, 0);

            $t_data = $arr3->firstWhere('date', $date);

            if($t_data)
                array_push($chart3_arr, $t_data->sum_amount);
            else
                array_push($chart3_arr, 0);
        }
        
        $notice_list = Notice::whereIn('type', [0, 2])
                    ->orderBy('id','desc')
                    ->limit(5)
                    ->get();

        $alim_list = Alim::where('agent_id', \Auth::user()->id)
                    ->where('created_at', '>=', Carbon::now()->subDays(30)->toDateTimeString())
                    ->orderBy('id','desc')
                    ->get();

        $popup_list = Notice::whereIn('type', [0, 2])
            ->where('popup', 1)
            ->whereNotNull('img')
            ->where('img', '<>', '')
            ->orderBy('id','asc')
            ->get();

        return view('agent.dashboard', compact('notice_list', 'alim_list', 'cur_month_cnt1', 'today_cnt1', 'total_cnt1', 
        'cur_month_cnt2', 'today_cnt2', 'total_cnt2', 'cur_month_cnt3', 'today_cnt3', 'total_cnt3', 'date_arr',
        'chart1_arr', 'chart2_arr', 'chart3_arr', 'popup_list'));
    }

    /**
     * 공지사항
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function notice(Request $request)
    {
        $list = Notice::whereIn('type', [0, 2])
                ->orderBy('id','desc')
                ->paginate(20);

        return view('agent.notice', compact('list'));
    }

    public function notice_hits(Request $request)
    {
        $notice = Notice::where('id', $request->id)->first();

        $notice->hits += 1;

        $notice->save();

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);
    }
}
