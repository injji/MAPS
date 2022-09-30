<?php

namespace App\Http\Controllers\Agent;

use Str;
use App\Http\Controllers\Controller;
use App\Models\Client\Review;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Agent\ReviewExportController;
use View;
use DateTime;

class ReviewController extends Controller
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
        View::share('activeMenu', 'agent.store.review');

        $search_info = $request->search_info ?? '';
        $data_range = $request->data_range;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $keyword = $request->keyword ?? '';
        $sort    = $request->sort ?? '1';

        if(!$st_date || !$ed_date) {
            $stdate = new DateTime();
            // $stdate->modify('-30 day');
            $stdate->modify('-90 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }
        $sort_type = $request->sort_type ?? 0;

        $list = array();

        $order = 'tbl_client_review.id';
        if($sort_type == 1)
            $order = 'tbl_client_review.answer';

        $total_cnt = Review::leftJoin('tbl_agent_service', 'tbl_agent_service.id', '=', 'tbl_client_review.service_id')
                        ->where('tbl_agent_service.agent_id', $request->user()->getKey())
                        ->where('tbl_client_review.lang', \Lang::getLocale())
                        ->count();
        $no_answer_cnt = 0;
        $list = array();

        if($search_info != '')
        {
            $list = Review::select('tbl_client_review.*', 'tbl_agent_service.name as service_name', 'tbl_users.manager_name as client_name')
                        ->leftJoin('tbl_agent_service', 'tbl_agent_service.id', '=', 'tbl_client_review.service_id')
                        ->join('tbl_users', 'tbl_users.id', '=', 'tbl_client_review.client_id')
                        ->where('tbl_agent_service.agent_id', $request->user()->getKey())
                        ->where('tbl_client_review.lang', \Lang::getLocale())
                        ->where('tbl_users.manager_name', 'LIKE', "%{$search_info}%")
                        ->whereBetween('tbl_client_review.created_at', [$st_date, $ed_date.' 23:59:59'])
                        ->orderBy($order, 'desc')
                        ->paginate(20);

            $no_answer_cnt = Review::leftJoin('tbl_agent_service', 'tbl_agent_service.id', '=', 'tbl_client_review.service_id')
                        ->join('tbl_users', 'tbl_users.id', '=', 'tbl_client_review.client_id')
                        ->where('tbl_agent_service.agent_id', $request->user()->getKey())
                        ->where('tbl_client_review.lang', \Lang::getLocale())
                        ->where('tbl_users.manager_name', 'LIKE', "%{$search_info}%")
                        ->whereNull('tbl_client_review.answer')
                        ->whereBetween('tbl_client_review.created_at', [$st_date, $ed_date.' 23:59:59'])
                        ->count();
        }
        else
        {
            $list = Review::select('tbl_client_review.*', 'tbl_agent_service.name as service_name', 'tbl_users.manager_name as client_name')
                        ->leftJoin('tbl_agent_service', 'tbl_agent_service.id', '=', 'tbl_client_review.service_id')
                        ->join('tbl_users', 'tbl_users.id', '=', 'tbl_client_review.client_id')
                        ->where('tbl_agent_service.agent_id', $request->user()->getKey())
                        ->where('tbl_client_review.lang', \Lang::getLocale())
                        ->whereBetween('tbl_client_review.created_at', [$st_date, $ed_date.' 23:59:59'])
                        ->orderBy($order, 'desc')
                        ->paginate(20);

            $no_answer_cnt = Review::leftJoin('tbl_agent_service', 'tbl_agent_service.id', '=', 'tbl_client_review.service_id')
                        ->where('tbl_agent_service.agent_id', $request->user()->getKey())
                        ->where('tbl_client_review.lang', \Lang::getLocale())
                        ->whereNull('tbl_client_review.answer')
                        ->whereBetween('tbl_client_review.created_at', [$st_date, $ed_date.' 23:59:59'])
                        ->count();
        }

        return view('agent.review', compact('list', 'search_info', 'st_date', 'ed_date', 'sort_type', 'total_cnt', 'no_answer_cnt'));
    }

    /**
     * 게시글 리뷰 정보 얻기
     *
     * @param Illuminate\Http\Request $request
     */
    public function review_info(Request $request)
    {
        $review = Review::where('id', $request->id)->first();

        return response()->json([
            'code' => 200,
            'content' => $review,
            'service' => $review->service
        ]);
    }

    /**
     * 게시글 리뷰 답변 등록
     *
     * @param Illuminate\Http\Request $request
     */
    public function add_answer(Request $request)
    {
        $review = Review::where('id', $request->id)->first();

        $review->answer = $request->answer;
        $review->answered_at = date('Y-m-d H:i:s');

        $review->save();

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);
    }

    /**
     * 엑셀 다운로드
     *
     * @param Illuminate\Http\Request $request
     */
    public function excel_download(Request $request)
    {
        $st_date = $request->st_date;
        $ed_date = $request->ed_date;

        return Excel::download(new ReviewExportController($st_date, $ed_date), '리뷰_'.time().'.xlsx');
    }
}
