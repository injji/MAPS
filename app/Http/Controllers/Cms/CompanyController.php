<?php

namespace App\Http\Controllers\Cms;

use Str;
use App\Http\Controllers\Controller;
use App\Models\Agent\Service;
use App\Models\Agent\ServiceCategory;
use App\Models\Client\Review;
use App\Models\Client\Site;
use App\Http\Controllers\Cms\CompanyExportController;
use App\Http\Controllers\Cms\UserDropExportController;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use App\Rules\{PhoneNumber, Password, Url, BusinessNo};
use View;
use Validator;
use DateTime;
use Hash;
use Illuminate\Support\Facades\Http;
use DB;

class CompanyController extends Controller
{
    /***/
    public function __construct()
    {
    }

    /**
     * 서비스 미리보기
     *
     * @param Illuminate\Http\Request $request
     * @return mixed
     */
    public function __invoke(Request $request)
    {
    }

    /**
     * 고객사관리
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function client_list(Request $request) {
        View::share('activeMenu', 'company.client');

        $data_range = $request->data_range;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $keyword = $request->keyword ?? '';

        if (!$st_date || !$ed_date) {
            $stdate = new DateTime();
            $stdate->modify('-90 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }

        $list = \App\Models\Users::whereBetween('created_at', [$st_date, $ed_date.' 23:59:59'])
                ->where('type', 1)
                ->orderBy('id', 'desc')
                ->paginate(20);

        $write_permission = \Auth::guard('cms')->user()->getWritePermission(5);

        return view('cms.company.client_list', compact('st_date', 'ed_date', 'list', 'write_permission'));
    }

    /**
     * 고객사정보
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function client_edit(Request $request)
    {
        View::share('activeMenu', 'company.client');
        $user = \App\Models\Users::find($request->id);
        $bank_list = config('app.banks');

        return view('cms.company.client_edit', compact('user', 'bank_list'));
    }

    /**
     * 고객사정보 수정
     *
     * @param Illuminate\Http\Request $request
     */
    public function user_info_store(Request $request)
    {
        $user = \App\Models\Users::find($request->id);
        $user->self_payment = $request->self_payment;
        $user->save();

        $validator = Validator::make($request->all(), [
            'c_password' => 'same:password',
            'company_name' => 'string',
            'manager_email' => 'email|nullable',
            'business_no' => 'string|nullable', // new BusinessNo,
            'homepage_url' => 'string|nullable',
            'director_phone' => ['nullable', new PhoneNumber],
            'manager_name' => 'string|nullable',
            'manager_phone' => new PhoneNumber,
            'director_email' => 'email|nullable',
        ], [
            'c_password.same' => __('validation.confirmed'),
        ]);

        if (!$validator->passes())
            return response()->json(['code' => 401, 'error' => $validator->errors()]);

        if ($request->input('password', ''))
            $user->password = Hash::make($request->password);

        $user->fill($request->except('password'))->save();

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);
    }

    /**
     * 제휴사관리
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function agent_list(Request $request) {
        View::share('activeMenu', 'company.agent');

        $data_range = $request->data_range;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $keyword = $request->keyword ?? '';

        if (!$st_date || !$ed_date) {
            $stdate = new DateTime();
            $stdate->modify('-90 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }

        $list = \App\Models\Users::whereBetween('created_at', [$st_date, $ed_date.' 23:59:59'])
                ->where('type', 2)
                ->orderBy('id', 'desc')
                ->paginate(20);

        $write_permission = \Auth::guard('cms')->user()->getWritePermission(6);

        return view('cms.company.agent_list', compact('st_date', 'ed_date', 'list', 'write_permission'));
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
        $type = $request->type;
        $agent_id = $request->agent_id;
        $service_id = $request->service_id;
        $keyword = $request->keyword;

        if ($type == 1) // 고객사관리
            return Excel::download(new CompanyExportController($st_date, $ed_date, 0, 0, 1), '고객사관리_'.time().'.xlsx');
        else if ($type == 2) // 제휴사관리
            return Excel::download(new CompanyExportController($st_date, $ed_date, 0, 0, 2), '제휴사관리_'.time().'.xlsx');
        else if ($type == 3) // 문의관리 고객사문의
            return Excel::download(new CompanyExportController($st_date, $ed_date, $agent_id, $service_id, 3), '고객사문의_'.time().'.xlsx');
        else if ($type == 4) // 문의관리 제휴사문의
            return Excel::download(new CompanyExportController($st_date, $ed_date, $agent_id, 0, 4), '제휴사문의_'.time().'.xlsx');
        else if ($type == 5) // 리뷰관리
            return Excel::download(new CompanyExportController($st_date, $ed_date, $agent_id, $service_id, 5), '리뷰관리_'.time().'.xlsx');
        else if ($type == 6) // 회원탈퇴
            return Excel::download(new UserDropExportController('drop', $st_date, $ed_date, $keyword), '회원탈퇴_'.time().'.xlsx');
    }

    /**
     * 제휴사정보
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function agent_edit(Request $request)
    {
        View::share('activeMenu', 'company.agent');
        $user = \App\Models\Users::find($request->id);
        $bank_list = config('app.banks');

        return view('cms.company.agent_edit', compact('user', 'bank_list'));
    }

    /**
     * 고객사문의
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function inquiry_client(Request $request) {
        View::share('activeMenu', 'company.client_question');

        $data_range = $request->data_range;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $agent_id = $request->agent_id ?? 0;
        $service_id = $request->service_id ?? 0;

        if (!$st_date || !$ed_date) {
            $stdate = new DateTime();
            $stdate->modify('-90 day');
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

        $list = \App\Models\Client\Inquiry::whereBetween('created_at', [$st_date, $ed_date.' 23:59:59'])
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
                ->paginate(20);

        $write_permission = \Auth::guard('cms')->user()->getWritePermission(7);

        return view('cms.company.client_question', compact('st_date', 'ed_date', 'agent_list', 'service_list', 'total_service_list',
                                'agent_id', 'service_id', 'list', 'write_permission'));
    }

    /**
     * 고객사문의 정보 얻기
     *
     * @param Illuminate\Http\Request $request
     */
    public function client_inquiry_info(Request $request)
    {
        $inquiry = \App\Models\Client\Inquiry::find($request->id);

        return response()->json([
            'code' => 200,
            'content' => $inquiry,
            'service' => $inquiry->service,
            'author' => $inquiry->author,
            'agent_name' => $inquiry->service->user->company_name,
        ]);
    }

    /**
     * 고객사문의 답변 등록
     *
     * @param Illuminate\Http\Request $request
     */
    public function client_inquiry_add_answer(Request $request)
    {
        $inquiry = \App\Models\Client\Inquiry::find($request->id);

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
            'msg' => '[MAPSTREND] 고객님의 문의에 답변완료되었습니다. '.PHP_EOL.config('app.domain.client'),
        ]);

        if($response->json()['code'] != 200)
        {
            return response()->json(['code' => 402, 'error' => __('messages.send_fail')]);
        }

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);
    }

    /**
     * 제휴사문의
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function inquiry_agent(Request $request) {
        View::share('activeMenu', 'company.agent_question');

        $data_range = $request->data_range;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $agent_id = $request->agent_id ?? 0;
        $service_id = $request->service_id ?? 0;

        if (!$st_date || !$ed_date) {
            $stdate = new DateTime();
            $stdate->modify('-90 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }

        $agent_list = \App\Models\Users::where('type', 2)->orderBy('id','desc')->get();

        $list = \App\Models\Agent\Inquiry::whereBetween('created_at', [$st_date, $ed_date.' 23:59:59'])
                ->where(function($q) use ($agent_id) {
                    if($agent_id > 0) {
                        $q->where('agent_id', $agent_id);
                    }
                })
                ->orderBy('id', 'desc')
                ->paginate(20);

        $write_permission = \Auth::guard('cms')->user()->getWritePermission(8);

        return view('cms.company.agent_question', compact('st_date', 'ed_date', 'agent_list', 'agent_id', 'list', 'write_permission'));
    }

    /**
     * 제휴사문의 정보 얻기
     *
     * @param Illuminate\Http\Request $request
     */
    public function agent_inquiry_info(Request $request)
    {
        $inquiry = \App\Models\Agent\Inquiry::find($request->id);

        return response()->json([
            'code' => 200,
            'content' => $inquiry,
            'user' => $inquiry->user
        ]);
    }

    /**
     * 제휴사문의 답변 등록
     *
     * @param Illuminate\Http\Request $request
     */
    public function agent_inquiry_add_answer(Request $request)
    {
        $inquiry = \App\Models\Agent\Inquiry::find($request->id);

        $inquiry->answer = $request->answer;

        if ($request->hasFile('answer_file'))
            $inquiry->answer_file = $request->answer_file->store('cms/answer', 'public');

        $inquiry->answered_at = date('Y-m-d H:i:s');

        $inquiry->save();

        $alim = new \App\Models\Agent\Alim();

        $alim->agent_id = $inquiry->agent_id;
        $alim->content = '제공사문의 답변이 등록되었습니다.';
        $alim->type = 5;
        $alim->save();

        // 메시지발송
        $phone = \App\Models\Users::find($inquiry->agent_id);
        if( $phone )
        {
            $response = Http::asForm()
            ->withHeaders([
                'Authorization' => config('services.phone_api.authorization')
            ])
            ->post(config('services.phone_api.url'), [
                'phone' => str_replace("-", "", $phone->manager_phone),
                'msg' => '[MAPSTREND] 고객님의 문의에 답변완료되었습니다. '.PHP_EOL.config('app.domain.agent'),
            ]);

            if($response->json()['code'] != 200)
            {
                return response()->json(['code' => 402, 'error' => __('messages.send_fail')]);
            }
        }

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);
    }

    /**
     * 리뷰관리
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function review(Request $request) {
        View::share('activeMenu', 'company.review');

        $data_range = $request->data_range;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $agent_id = $request->agent_id ?? 0;
        $service_id = $request->service_id ?? 0;

        if (!$st_date || !$ed_date) {
            $stdate = new DateTime();
            $stdate->modify('-90 day');
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

        $list = Review::whereBetween('created_at', [$st_date, $ed_date.' 23:59:59'])
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
                ->paginate(20);

        $write_permission = \Auth::guard('cms')->user()->getWritePermission(9);

        return view('cms.company.review', compact('st_date', 'ed_date', 'agent_list', 'service_list', 'total_service_list',
                                'agent_id', 'service_id', 'list', 'write_permission'));
    }

    /**
     * 리뷰 노출상태 설정
     *
     * @param Illuminate\Http\Request $request
     */
    public function update_review_visible(Request $request)
    {
        $review = Review::find($request->id);

        $review->visible = $request->visible;
        $review->save();

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);
    }

    /**
     * 리뷰 삭제
     *
     * @param Illuminate\Http\Request $request
     */
    public function del_review(Request $request)
    {
        Review::find($request->id)->delete();

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);
    }

    /**
     * 게시글 리뷰 정보 얻기
     *
     * @param Illuminate\Http\Request $request
     */
    public function review_info(Request $request)
    {
        $review = Review::find($request->id);

        return response()->json([
            'code' => 200,
            'content' => $review,
            'service' => $review->service
        ]);
    }

    /**
     * 사이트 서비스 개수 얻기
     *
     * @param Illuminate\Http\Request $request
     */
    public function site_count(Request $request)
    {
        $site_cnt = Site::find($request->id)->service->count();

        return response()->json([
            'code' => 200,
            'cnt' => $site_cnt
        ]);
    }


    /**
     * 회원탈퇴
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function goodbye(Request $request)
    {
        View::share('activeMenu', 'company.goodbye');

        $data_range = $request->data_range;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $keyword = $request->keyword ?? '';

        if(!$st_date || !$ed_date) {
            $stdate = new DateTime();
            // $stdate->modify('-30 day');
            $stdate->modify('-90 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }
        // dd($st_date);

        $total_cnt = \App\Models\UserDrop::count();

        $list = \App\Models\UserDrop::orderBy('id', 'desc')
                    ->where('created_at', '>=', "{$st_date}")
                    ->where('created_at', '<=', "{$ed_date} 23:59:59");

        if($keyword) {
            $list = $list->where(DB::raw("CONCAT(account,' ',company_name)"), 'LIKE', '%'.$keyword.'%');
        }
        $list = $list->paginate(20);

        return view('cms.company.goodbye', compact( 'st_date', 'ed_date', 'keyword', 'list', 'total_cnt' ));
    }

    public function goodbye_export(Request $request){

        $type = $request->type;
        $st_date = $request->st_date;
        $ed_date = $request->ed_date;
        $keyword = $request->keyword;

        if ($type == 'drop')
            return Excel::download(new UserDropExportController('drop', $st_date, $ed_date, $keyword), '회원탈퇴_'.time().'.xlsx');
    }

    /**
     * 회원탈퇴 상태변경
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function goodbye_status(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => ['integer','required'],
            'status' => ['integer','required'],
            'reason' => ['required_if:status,1']
        ]);

        if (!$validator->passes())
            return response()->json(['code' => 401, 'error' => $validator->errors()]);

        $drop = \App\Models\UserDrop::find($request->id);
        $drop->update([
            'status' => $request->status,
            'admin_reason' => ($request->status == 1) ? $request->reason : null,
            'dropped_at' => ($request->status == 2) ? date('Y-m-d H:i:s') : null
        ]);

        $user = \App\Models\Users::find($drop->client_id);
        if($request->status == 2){
            $user->update(['dropped_at' => date('Y-m-d H:i:s')]);
        }else{
            $user->update(['dropped_at' => null]);
        }

        return response()->json([
            'code' => 200,
            'status' => $request->status,
            'reason' => $request->reason
        ]);
    }
}
