<?php

namespace App\Http\Controllers\Cms;

use Str;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Cms\StatExportController;
use App\Models\Payment;
use App\Models\Agent\Service;
use App\Models\Agent\ServiceCategory;
use App\Models\Cms\ServiceStat;
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

    // /////////// 이용통계
    /**
     * 이용통계 view
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function using(Request $request) {
        View::share('activeMenu', 'stat.using');

        $data_range = $request->data_range;
        $tab     = $request->tab ?? 1;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        
        if(!$st_date || !$ed_date) {
            $stdate = new DateTime();
            $stdate->modify('-7 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }

        $results = ServiceStat::getUsingDatas($st_date, $ed_date, $tab);
        
        return view('cms.stat.using', [
            'st_date' => $st_date,
            'ed_date' => $ed_date,
            'results' => $results,
            'tab'     => $tab,
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
        $tab     = $request->tab ?? 1;

        $results = ServiceStat::getUsingDatas($st_date, $ed_date, $tab);
        
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
        $st_date = $request->st_date;
        $ed_date = $request->ed_date;
        $tab     = $request->tab;
        $type    = $request->type;
        $agent_id = $request->agent_id;
        $service_id = $request->service_id;
        $category_id = $request->category_id;

        if($type == 'using') {
            return Excel::download(new StatExportController($type, $st_date, $ed_date, $tab, 0, 0, 0), '이용통계_'.time().'.xlsx');
        }
        else if($type == 'service') {
            return Excel::download(new StatExportController($type, $st_date, $ed_date, 0, $agent_id, $service_id, $category_id), '서비스별 통계_'.time().'.xlsx');
        }
        else if($type == 'category') {
            return Excel::download(new StatExportController($type, $st_date, $ed_date, 0, 0, 0, 0), '카테고리별 통계_'.time().'.xlsx');
        }
        else if($type == 'agent') {
            return Excel::download(new StatExportController($type, $st_date, $ed_date, 0, 0, 0, 0), '제휴사 통계_'.time().'.xlsx');
        }
    }

    // /////////// 서비스별 통계
    /**
     * 서비스별 통계 view
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function service(Request $request) {
        View::share('activeMenu', 'stat.service');

        $data_range = $request->data_range;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';        
        $sort_type = $request->sort_type ?? 0;

        $agent_id = $request->agent_id ?? 0;
        $service_id = $request->service_id ?? 0;
        $category_id = $request->category_id ?? 0;
        
        if(!$st_date || !$ed_date) {
            $stdate = new DateTime();
            $stdate->modify('-30 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }

        $agent_list = \App\Models\Users::where('type', 2)->orderBy('id','desc')->get();
        $service_list = Service::where(function($q) use ($agent_id) {
                                    if($agent_id > 0) {
                                        $q->where('agent_id', $agent_id);
                                    }
                                })
                                ->orderBy('id','desc')
                                ->get();
        $total_service_list = Service::orderBy('id','desc')->get();
        $category_list = ServiceCategory::getCate();

        $results = \App\Models\Agent\Service::whereBetween('created_at', [$st_date, $ed_date.' 23:59:59'])
                        ->when($agent_id > 0, function($query) use ($agent_id) {
                            $query->where('agent_id', $agent_id);                            
                        })
                        ->when($service_id > 0, function($query) use ($service_id) {
                            $query->where('id', $service_id);                            
                        })
                        ->when($category_id > 0, function($query) use ($category_id) {
                            $query->where('category1', $category_id);
                        })
                        ->when($sort_type == 0, function($query) {
                            $query->orderBy('id', 'desc');
                        })
                        ->when($sort_type == 1, function($query) {
                            $query->orderBy('view_cnt', 'desc');
                        })
                        ->when($sort_type == 2, function($query) {
                            $query->withCount('review')
                                  ->orderBy('review_count', 'desc');
                        })
                        ->when($sort_type == 3, function($query) {
                            $query->withCount('payment0')
                                  ->orderBy('payment0_count', 'desc');
                        })
                        ->when($sort_type == 4, function($query) {
                            $query->withCount('payment1')
                                  ->orderBy('payment1_count', 'desc');
                        })
                        ->when($sort_type == 5, function($query) {
                            $query->withCount('payment')
                                  ->orderBy('payment_count', 'desc');
                        })                        
                        ->when($sort_type == 6, function($query) {                            
                            $query->selectRaw('*, (SELECT SUM(amount) FROM tbl_payment WHERE service_id=tbl_agent_service.id) AS sum_amount')
                                  ->orderBy('sum_amount', 'desc');
                        })
                        ->paginate(20);
        
        return view('cms.stat.service', compact('st_date', 'ed_date', 'results', 'agent_id', 'service_id', 
            'category_id', 'agent_list', 'service_list', 'total_service_list', 'category_list', 'sort_type'));
    }

    // /////////// 카테고리별 통계
    /**
     * 카테고리별 통계 view
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function category(Request $request) {
        View::share('activeMenu', 'stat.category');

        $data_range = $request->data_range;        
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';        
        $sort_type = $request->sort_type ?? 0;
        
        if(!$st_date || !$ed_date) {
            $stdate = new DateTime();
            $stdate->modify('-30 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }
        
        $sub_query = \App\Models\Agent\Service::selectRaw('*, 
                        (SELECT COUNT(*) FROM tbl_payment WHERE TYPE=0 AND service_id=tbl_agent_service.id) AS payment0_cnt,
                        (SELECT COUNT(*) FROM tbl_payment WHERE TYPE=1 AND service_id=tbl_agent_service.id) AS payment1_cnt,
                        (SELECT COUNT(*) FROM tbl_payment WHERE service_id=tbl_agent_service.id) AS payment_cnt,
                        (SELECT SUM(amount) FROM tbl_payment WHERE service_id=tbl_agent_service.id) AS sum_amount')
                        ->whereBetween('created_at', [$st_date, $ed_date.' 23:59:59']);

        $results = \App\Models\Agent\Service::selectRaw('T.*, 
                        SUM(T.view_cnt) AS sum_view_cnt, SUM(T.payment0_cnt) AS sum_payment0_cnt,
                        SUM(T.payment1_cnt) AS sum_payment1_cnt, SUM(T.payment_cnt) AS sum_payment_cnt,
                        SUM(T.sum_amount) AS total_amount')
                        ->from($sub_query, 'T')
                        ->groupBy('T.category1', 'T.category2')
                        ->when($sort_type == 0, function($query) {
                            $query->orderBy('sum_view_cnt', 'desc');
                        })
                        ->when($sort_type == 1, function($query) {
                            $query->orderBy('sum_view_cnt', 'desc');
                        })
                        ->when($sort_type == 2, function($query) {
                            $query->orderBy('sum_payment0_cnt', 'desc');
                        })
                        ->when($sort_type == 3, function($query) {
                            $query->orderBy('sum_payment1_cnt', 'desc');
                        })
                        ->when($sort_type == 4, function($query) {
                            $query->orderBy('sum_payment_cnt', 'desc');
                        })                        
                        ->when($sort_type == 5, function($query) {                            
                            $query->orderBy('total_amount', 'desc');
                        })
                        ->paginate(20);
        
        return view('cms.stat.category', compact('st_date', 'ed_date', 'results', 'sort_type'));        
    }

    // /////////// 제휴사 통계
    /**
     * 제휴사 통계 view
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function agent(Request $request) {
        View::share('activeMenu', 'stat.agent');

        $data_range = $request->data_range;        
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';        
        $sort_type = $request->sort_type ?? 0;
        
        if(!$st_date || !$ed_date) {
            $stdate = new DateTime();
            $stdate->modify('-30 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }

        $results = \App\Models\Users::whereBetween('created_at', [$st_date, $ed_date.' 23:59:59'])
                        ->where('type', 2)                        
                        ->when($sort_type == 0, function($query) {
                            $query->orderBy('id', 'desc');
                        })
                        ->when($sort_type == 1, function($query) {
                            $query->withCount('service')
                                  ->orderBy('service_count', 'desc');
                        })
                        ->when($sort_type == 2, function($query) {
                            $query->withCount('review')
                                  ->orderBy('review_count', 'desc');
                        })
                        ->when($sort_type == 3, function($query) {
                            $query->withCount('payment0')
                                  ->orderBy('payment0_count', 'desc');
                        })
                        ->when($sort_type == 4, function($query) {
                            $query->withCount('payment1')
                                  ->orderBy('payment1_count', 'desc');
                        })
                        ->when($sort_type == 5, function($query) {
                            $query->withCount('payment')
                                  ->orderBy('payment_count', 'desc');
                        })                        
                        ->when($sort_type == 6, function($query) {                            
                            $query->selectRaw('*, (SELECT SUM(amount) FROM tbl_payment WHERE tbl_payment.agent_id=tbl_users.id) AS sum_amount')
                                  ->orderBy('sum_amount', 'desc');
                        })
                        ->paginate(20);
        
        return view('cms.stat.agent', compact('st_date', 'ed_date', 'results', 'sort_type'));
    }
}