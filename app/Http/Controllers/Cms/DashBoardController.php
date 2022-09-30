<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use View;

class DashBoardController extends Controller
{
    /**
     * @param Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(Request $request)
    {        
        View::share('activeMenu', 'home');
    }

    /**
     * cms 대시보드 view
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
    */
    public function cmsView(Request $request)
    {        
        $service_list = \App\Models\Agent\Service::whereIn('process', [1, 2, 3, 4, 5])
                        ->orderBy('id','desc')
                        ->limit(5)
                        ->get();

        $agent_inquiry_list = \App\Models\Agent\Inquiry::orderBy('id','desc')
                        ->limit(5)
                        ->get();

        $cur_month_cnt1 = \App\Models\Users::whereMonth('created_at', Carbon::now()->month)
                        ->where('type', 2)
                        ->count();
        $today_cnt1 = \App\Models\Users::whereDate('created_at', Carbon::today())
                        ->where('type', 2)
                        ->count();
        $total_cnt1 = \App\Models\Users::where('type', 2)
                        ->count();

        $cur_month_cnt2 = \App\Models\Users::whereMonth('created_at', Carbon::now()->month)
                        ->where('type', 1)
                        ->count();
        $today_cnt2 = \App\Models\Users::whereDate('created_at', Carbon::today())
                        ->where('type', 1)
                        ->count();
        $total_cnt2 = \App\Models\Users::where('type', 1)
                        ->count();

        $cur_month_cnt3 = \App\Models\Agent\Service::whereMonth('request_at', Carbon::now()->month)
                        ->whereIn('process', [1, 2, 3, 4, 5])
                        ->count();
        $today_cnt3 = \App\Models\Agent\Service::whereDate('request_at', Carbon::today())
                        ->whereIn('process', [1, 2, 3, 4, 5])
                        ->count();
        $total_cnt3 = \App\Models\Agent\Service::whereIn('process', [1, 2, 3, 4, 5])
                        ->count();

        $cur_month_cnt4 = \App\Models\Client\Service::whereMonth('created_at', Carbon::now()->month)
                        ->count();
        $today_cnt4 = \App\Models\Client\Service::whereDate('created_at', Carbon::today())
                        ->count();
        $total_cnt4 = \App\Models\Client\Service::count();

        $cur_month_cnt5 = \App\Models\Client\Service::whereMonth('created_at', Carbon::now()->month)
                        ->where('process', 2)
                        ->count();
        $today_cnt5 = \App\Models\Client\Service::whereDate('created_at', Carbon::today())
                        ->where('process', 2)
                        ->count();
        $total_cnt5 = \App\Models\Client\Service::where('process', 2)
                        ->count();

        $cur_month_cnt6 = \App\Models\Payment::whereMonth('created_at', Carbon::now()->month)
                        ->sum('amount');
        $today_cnt6 = \App\Models\Payment::whereDate('created_at', Carbon::today())
                        ->sum('amount');
        $total_cnt6 = \App\Models\Payment::sum('amount');

        $write_permission3 = \Auth::guard('cms')->user()->getWritePermission(3);
        $write_permission8 = \Auth::guard('cms')->user()->getWritePermission(8);
        
        return view('cms.dashboard', compact('service_list', 'agent_inquiry_list', 
        'cur_month_cnt1', 'today_cnt1', 'total_cnt1',
        'cur_month_cnt2', 'today_cnt2', 'total_cnt2',
        'cur_month_cnt3', 'today_cnt3', 'total_cnt3',
        'cur_month_cnt4', 'today_cnt4', 'total_cnt4', 
        'cur_month_cnt5', 'today_cnt5', 'total_cnt5',
        'cur_month_cnt6', 'today_cnt6', 'total_cnt6',
        'write_permission3', 'write_permission8'));
    }
}
