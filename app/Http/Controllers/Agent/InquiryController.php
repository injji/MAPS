<?php

namespace App\Http\Controllers\Agent;

use Str;
use App\Http\Controllers\Controller;
use App\Models\Client\Inquiry;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Agent\InquiryClientExportController;
use View;
use DateTime;
use Illuminate\Support\Facades\Http;

class InquiryController extends Controller
{
    /***/
    public function __construct()
    {
    }

    /**
     * 문의 사용자문의
     *
     * @param Illuminate\Http\Request $request
     */
    public function client(Request $request)
    {
        View::share('activeMenu', 'agent.inquiry_client');

        $search_info = $request->search_info ?? '';
        $data_range = $request->data_range;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $keyword = $request->keyword ?? '';
        $sort    = $request->sort ?? '1';

        if(!$st_date || !$ed_date) {
            $stdate = new DateTime();
            $stdate->modify('-30 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }
        $sort_type = $request->sort_type ?? 0;

        $list = array();

        $order = 'tbl_client_inquiry.id';
        if($sort_type == 1)
            $order = 'tbl_agent_service.name';
        else if($sort_type == 2)
            $order = 'tbl_client_inquiry.type';
        else if($sort_type == 3)
            $order = 'tbl_client_inquiry.answer';

        $total_cnt = Inquiry::leftJoin('tbl_agent_service', 'tbl_agent_service.id', '=', 'tbl_client_inquiry.service_id')
                        ->where('tbl_agent_service.agent_id', $request->user()->getKey())
                        ->where('tbl_client_inquiry.lang', \Lang::getLocale())
                        ->count();
        $no_answer_cnt = 0;
        $list = array();

        if($search_info != '')
        {
            $list = Inquiry::select('tbl_client_inquiry.*', 'tbl_agent_service.name as service_name', 'tbl_users.manager_name as client_name')
                        ->leftJoin('tbl_agent_service', 'tbl_agent_service.id', '=', 'tbl_client_inquiry.service_id')
                        ->join('tbl_users', 'tbl_users.id', '=', 'tbl_client_inquiry.client_id')
                        ->where('tbl_agent_service.agent_id', $request->user()->getKey())
                        ->where('tbl_client_inquiry.lang', \Lang::getLocale())
                        ->where(function ($query) use ($search_info) {
                            $query->where('tbl_users.manager_name', 'LIKE', "%{$search_info}%")
                                  ->orWhere('tbl_client_inquiry.title', 'LIKE', "%{$search_info}%");
                        })
                        ->whereBetween('tbl_client_inquiry.created_at', [$st_date, $ed_date.' 23:59:59'])
                        ->orderBy($order, 'desc')
                        ->paginate(20);

            $no_answer_cnt = Inquiry::leftJoin('tbl_agent_service', 'tbl_agent_service.id', '=', 'tbl_client_inquiry.service_id')
                        ->join('tbl_users', 'tbl_users.id', '=', 'tbl_client_inquiry.client_id')
                        ->where('tbl_agent_service.agent_id', $request->user()->getKey())
                        ->where('tbl_client_inquiry.lang', \Lang::getLocale())
                        ->where(function ($query) use ($search_info) {
                            $query->where('tbl_users.manager_name', 'LIKE', "%{$search_info}%")
                                  ->orWhere('tbl_client_inquiry.title', 'LIKE', "%{$search_info}%");
                        })
                        ->whereNull('tbl_client_inquiry.answer')
                        ->whereBetween('tbl_client_inquiry.created_at', [$st_date, $ed_date.' 23:59:59'])
                        ->count();
        }
        else
        {
            $list = Inquiry::select('tbl_client_inquiry.*', 'tbl_agent_service.name as service_name', 'tbl_users.manager_name as client_name')
                        ->leftJoin('tbl_agent_service', 'tbl_agent_service.id', '=', 'tbl_client_inquiry.service_id')
                        ->join('tbl_users', 'tbl_users.id', '=', 'tbl_client_inquiry.client_id')
                        ->where('tbl_agent_service.agent_id', $request->user()->getKey())
                        ->where('tbl_client_inquiry.lang', \Lang::getLocale())
                        ->whereBetween('tbl_client_inquiry.created_at', [$st_date, $ed_date.' 23:59:59'])
                        ->orderBy($order, 'desc')
                        ->paginate(20);

            $no_answer_cnt = Inquiry::leftJoin('tbl_agent_service', 'tbl_agent_service.id', '=', 'tbl_client_inquiry.service_id')
                        ->where('tbl_agent_service.agent_id', $request->user()->getKey())
                        ->where('tbl_client_inquiry.lang', \Lang::getLocale())
                        ->whereNull('tbl_client_inquiry.answer')
                        ->whereBetween('tbl_client_inquiry.created_at', [$st_date, $ed_date.' 23:59:59'])
                        ->count();
        }

        return view('agent.inquiry.client', compact('list', 'search_info', 'st_date', 'ed_date', 'sort_type', 'total_cnt', 'no_answer_cnt'));
    }

    /**
     * 게시글 사용자문의 정보 얻기
     *
     * @param Illuminate\Http\Request $request
     */
    public function inquiry_info(Request $request)
    {
        $inquiry = Inquiry::select('tbl_client_inquiry.*', 'tbl_users.manager_name as client_name')
                ->join('tbl_users', 'tbl_users.id', '=', 'tbl_client_inquiry.client_id')
                ->where('tbl_client_inquiry.id', $request->id)
                ->first();

        return response()->json([
            'code' => 200,
            'content' => $inquiry,
            'service' => $inquiry->service
        ]);
    }

    /**
     * 게시글 문의 답변 등록
     *
     * @param Illuminate\Http\Request $request
     */
    public function add_answer(Request $request)
    {
        $inquiry = Inquiry::where('id', $request->id)->first();

        $inquiry->answer = $request->answer;

        if ($request->hasFile('answer_file'))
            $inquiry->answer_file = $request->answer_file->store('agent/answer', 'public');

        $inquiry->answered_at = date('Y-m-d H:i:s');

        $inquiry->save();

        // 메시지발송
        $phone = \App\Models\Users::find($inquiry->client_id);
        $response = Http::asForm()
        ->withHeaders([
            'Authorization' => config('services.phone_api.authorization')
        ])
        ->post(config('services.phone_api.url'), [
            'phone' => str_replace("-", "", $phone->manager_phone),
            'msg' => '[MAPSTREND] 고객님의 문의에 답변완료되었습니다.'.PHP_EOL.config('app.domain.client'),
        ]);

        if($response->json()['code'] != 200)
        {
            return response()->json(['code' => 402, 'error' => __('messages.send_fail')]);
        }

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
    public function client_excel_download(Request $request)
    {
        $st_date = $request->st_date;
        $ed_date = $request->ed_date;

        return Excel::download(new InquiryClientExportController($st_date, $ed_date), '사용자문의_'.time().'.xlsx');
    }

    public function agent(Request $request)
    {
        View::share('activeMenu', 'agent.inquiry_agent');

        $search_info = $request->search_info ?? '';
        $data_range = $request->data_range;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $keyword = $request->keyword ?? '';
        $sort    = $request->sort ?? '1';

        if(!$st_date || !$ed_date) {
            $stdate = new DateTime();
            $stdate->modify('-30 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }
        $sort_type = $request->sort_type ?? 0;

        $list = array();

        $order = 'id';
        if($sort_type == 1)
            $order = 'type';
        else if($sort_type == 2)
            $order = 'answer';

        $total_cnt = \App\Models\Agent\Inquiry::where('agent_id', $request->user()->getKey())
                        ->where('lang', \Lang::getLocale())
                        ->count();
        $no_answer_cnt = 0;
        $list = array();

        if($search_info != '')
        {
            $list = \App\Models\Agent\Inquiry::where('agent_id', $request->user()->getKey())
                        ->where('lang', \Lang::getLocale())
                        ->where('title', 'LIKE', "%{$search_info}%")
                        ->whereBetween('created_at', [$st_date, $ed_date.' 23:59:59'])
                        ->orderBy($order, 'desc')
                        ->paginate(20);

            $no_answer_cnt = \App\Models\Agent\Inquiry::where('agent_id', $request->user()->getKey())
                        ->where('lang', \Lang::getLocale())
                        ->where('title', 'LIKE', "%{$search_info}%")
                        ->whereNull('answer')
                        ->whereBetween('created_at', [$st_date, $ed_date.' 23:59:59'])
                        ->count();
        }
        else
        {
            $list = \App\Models\Agent\Inquiry::where('agent_id', $request->user()->getKey())
                        ->where('lang', \Lang::getLocale())
                        ->whereBetween('created_at', [$st_date, $ed_date.' 23:59:59'])
                        ->orderBy($order, 'desc')
                        ->paginate(20);

            $no_answer_cnt = \App\Models\Agent\Inquiry::where('agent_id', $request->user()->getKey())
                        ->where('lang', \Lang::getLocale())
                        ->whereNull('answer')
                        ->whereBetween('created_at', [$st_date, $ed_date.' 23:59:59'])
                        ->count();
        }

        return view('agent.inquiry.agent', compact('list', 'search_info', 'st_date', 'ed_date', 'sort_type', 'total_cnt', 'no_answer_cnt'));
    }

    /**
     * 문의 제공사문의 정보 얻기
     *
     * @param Illuminate\Http\Request $request
     */
    public function agent_inquiry_info(Request $request)
    {
        $inquiry = \App\Models\Agent\Inquiry::find($request->id);

        return response()->json([
            'code' => 200,
            'content' => $inquiry
        ]);
    }

    /**
     * 문의 제공사문의 등록
     *
     * @param Illuminate\Http\Request $request
     */
    public function add_agent_inquiry(Request $request)
    {
        $inquiry = new \App\Models\Agent\Inquiry();

        $inquiry->lang = \Lang::getLocale();
        $inquiry->agent_id = $request->user()->getKey();
        $inquiry->type = $request->type;
        $inquiry->title = $request->title;
        $inquiry->content = $request->content;
        $inquiry->contact_phone = $request->contact_phone;

        if ($request->hasFile('question_file'))
            $inquiry->question_file = $request->question_file->store('agent/question', 'public');

        $inquiry->save();

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);
    }
}
