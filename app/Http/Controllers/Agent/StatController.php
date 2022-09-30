<?php

namespace App\Http\Controllers\Agent;

use Str;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Agent\StatExportController;
use App\Models\Agent\{Service, ServiceStat};
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use View;
use DateTime;

class StatController extends Controller
{
    /***/
    public function __construct()
    {
    }

    /**
     * 
     *
     * @param Illuminate\Http\Request $request
     * @return mixed
     */
    public function __invoke(Request $request)
    {
    
    }

    // /////////// 신청
    /**
     * 신청 view
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function order(Request $request) {
        View::share('activeMenu', 'agent.stat_order');

        $data_range = $request->data_range;
        $lang    = $request->lang ?? 'ko';
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $keyword = $request->keyword ?? '';
        $sort    = $request->sort ?? '1';
        
        if(!$st_date || !$ed_date) {
            $stdate = new DateTime();
            $stdate->modify('-7 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }

        $results = ServiceStat::getOrderDatas($st_date, $ed_date, $lang);
        
        return view('agent.stat.order', [
            'st_date' => $st_date,
            'ed_date' => $ed_date,
            'results' => $results,
            'lang'    => $lang,
        ]);
    }
    /**
     * get chart data
     *
     * @param Illuminate\Http\Request $request
     */
    public function getStatChart(Request $request)
    {
        $st_date = $request->st_date;
        $ed_date = $request->ed_date;
        $lang    = $request->lang ?? 'ko';
        $type    = $request->type ?? 'order';

        $results = [];
        if($type == 'order') {
            $results = ServiceStat::getOrderDatas($st_date, $ed_date, $lang);
        }
        else if($type == 'sales') {
            $results = ServiceStat::getSalesDatas($st_date, $ed_date, $lang);
        }
        else if($type == 'service') {
            $results = ServiceStat::getServiceDatas($st_date, $ed_date, $lang);
        }
        
        return response()->json([
            'code' => 200,
            'data' => $results
        ]);
    }
    /**
     * export excel data
     *
     * @param Illuminate\Http\Request $request
     */
    public function stat_export(Request $request) 
    {
        $type    = 'order';
        $st_date = $request->st_date;
        $ed_date = $request->ed_date;
        $lang    = $request->lang ?? 'ko';
        $type    = $request->type ?? 'order';

        if($type == 'order') {
            return Excel::download(new StatExportController($type, $st_date, $ed_date, $lang), '신청_'.time().'.xlsx');
        }
        else if($type == 'sales') {
            return Excel::download(new StatExportController($type, $st_date, $ed_date, $lang), '매출_'.time().'.xlsx');
        }
        else if($type == 'service') {
            return Excel::download(new StatExportController($type, $st_date, $ed_date, $lang), '서비스_'.time().'.xlsx');
        }
    }

    // /////////// 매출
    /**
     * 매출 view
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function sales(Request $request) {
        View::share('activeMenu', 'agent.stat_sales');

        $data_range = $request->data_range;
        $lang    = $request->lang ?? 'ko';
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $keyword = $request->keyword ?? '';
        $sort    = $request->sort ?? '1';
        
        if(!$st_date || !$ed_date) {
            $stdate = new DateTime();
            $stdate->modify('-7 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }

        $results = ServiceStat::getSalesDatas($st_date, $ed_date, $lang);
        
        return view('agent.stat.sales', [
            'st_date' => $st_date,
            'ed_date' => $ed_date,
            'results' => $results,
            'lang'    => $lang,
        ]);
    }

    // /////////// 매출
    /**
     * 매출 view
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function service(Request $request) {
        View::share('activeMenu', 'agent.stat_service');

        $data_range = $request->data_range;
        $lang    = $request->lang ?? 'ko';
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $keyword = $request->keyword ?? '';
        $sort    = $request->sort ?? '1';
        
        if(!$st_date || !$ed_date) {
            $stdate = new DateTime();
            $stdate->modify('-7 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }

        $results = ServiceStat::getServiceDatas($st_date, $ed_date, $lang);
        
        return view('agent.stat.service', [
            'st_date' => $st_date,
            'ed_date' => $ed_date,
            'results' => $results,
            'lang'    => $lang,
        ]);
    }

}
