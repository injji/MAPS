<?php

namespace App\Http\Controllers\Cms;

use Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Cms\ServiceExportController;
use App\Rules\{Url, PhoneNumber, Email, Category};
use App\Models\Agent\Service;
use App\Models\Agent\ServiceCategory;
use View;
use Validator;
use DateTime;
use Illuminate\Support\Facades\Http;

class ServiceController extends Controller
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
     * 서비스 목록 view
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function list(Request $request) {
        View::share('activeMenu', 'service.list');

        $data_range = $request->data_range;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $agent_id = $request->agent_id ?? 0;
        $service_id = $request->service_id ?? 0;
        $category_id = $request->category_id ?? 0;

        if (!$st_date || !$ed_date) {
            $stdate = new DateTime();
            // $stdate->modify('-30 day');
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

        $category_list = ServiceCategory::getCate();

        $list = Service::whereBetween('created_at', [$st_date, $ed_date.' 23:59:59'])
                ->where(function($q) use ($agent_id) {
                    if($agent_id > 0) {
                        $q->where('agent_id', $agent_id);
                    }
                })
                ->where(function($q) use ($service_id) {
                    if($service_id > 0) {
                        $q->where('id', $service_id);
                    }
                })
                ->where(function($q) use ($category_id) {
                    if($category_id > 0) {
                        $q->where('category1', $category_id);
                    }
                })
                ->orderBy('id', 'desc')
                ->paginate(20);

        $write_permission = \Auth::guard('cms')->user()->getWritePermission(2);

        return view('cms.service.list', compact('st_date', 'ed_date', 'agent_list', 'service_list', 'total_service_list',
                'category_list', 'agent_id', 'service_id', 'category_id', 'list', 'write_permission'));
    }

    /**
     * 엑셀 다운로드
     *
     * @param Illuminate\Http\Request $request
     */
    public function service_excel_download(Request $request)
    {
        $st_date = $request->st_date;
        $ed_date = $request->ed_date;
        $agent_id = $request->agent_id;
        $service_id = $request->service_id;
        $category_id = $request->category_id;

        return Excel::download(new ServiceExportController($st_date, $ed_date, $agent_id, $service_id, $category_id), '서비스관리_'.time().'.xlsx');
    }

    /**
     * 서비스 수정
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function edit(Request $request)
    {
        View::share('activeMenu', 'service.list');

        $service = Service::find($request->id);

        return view('cms.service.edit', compact('service'));
    }

    /**
     * 서비스 저장
     *
     * @param Illuminate\Http\Request $request
     * @return void
     *
     * @throws Illuminate\Validation\ValidationException 유효성 검사 실패시
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'url' => ['required', new Url],
            'category' => new Category,
            'redirect_url' => new Url,
            'script_url' => new Url,
            'short_description' => 'array',
            'image_description.*' => 'image',
            'ad_url' => new Url,
            'sample_url' => new Url
        ], [
            'name.required' => __('validation.service_name'),
            'url.required' => __('validation.service_url'),
        ]);

        if (!$validator->passes())
            return response()->json(['code' => 401, 'error' => $validator->errors()]);

        $service = Service::find($request->id);

        $service->fill($request->only([
            'lang', 'default_lang', 'name', 'url', 'redirect_url', 'api_id', 'api_key', 'api_key_note', 'api_scope',
            'version', 'script_url', 'release_note', 'youtube_url', 'service_info', 'full_description', 'ad_url', 'sample_url', 'contact_type',
            'contact_email', 'search_keyword', 'specification', 'currency', 'amount_min', 'amount_max'
        ]));

        if($service->process != $request->process && $request->process == 3)
        {
            $alim = new \App\Models\Agent\Alim();

            $alim->agent_id = $service->agent_id;
            $alim->content = $service->name.' 서비스가 판매중으로 변경되었습니다.';
            $alim->move_id = $service->id;
            $alim->type = 1;
            $alim->save();

            $service->complete_at = date('Y-m-d H:i:s');
        }

        if($service->process != $request->process && $request->process == 1)
        {
            if($service->request_at)
                $service->process = 4;
            else
                $service->process = 1;

            $service->request_at = date('Y-m-d H:i:s');
        }
        else
        {

            if($request->process == 2 || $request->process == 5){

                $alim = new \App\Models\Agent\Alim();

                $alim->agent_id = $service->agent_id;
                if($request->process == 2)
                    $alim->content = $service->name.' 서비스가 심사거절로 변경되었습니다.';
                else if($request->process == 5)
                    $alim->content = $service->name.' 서비스가 판매중지로 변경되었습니다.';

                $alim->move_id = $service->id;
                $alim->type = 1;
                $alim->save();
            }

            $service->process = $request->process;
        }
        if ($request->input('category', ''))
        {
            $service->category1 = $request->category[0];
            $service->category2 = $request->category[1];
        }

        if ($request->contact_phone == '--')
            $service->contact_phone = "";
        else
            $service->contact_phone = $request->contact_phone;

        if ($request->visible == 'on')
            $service->visible = 1;
        else
            $service->visible = 0;

        if ($request->in_app_payment == 'on')
            $service->in_app_payment = 1;
        else
            $service->in_app_payment = 0;

        if ($request->free_term == 'on')
            $service->free_term = $request->free_period;
        else
            $service->free_term = 0;

        if ($request->hasFile('icon'))
            $service->icon = $request->icon->store('agent/service', 'public');

        if ($request->hasFile('banner_image'))
            $service->banner_image = $request->banner_image->store('agent/service', 'public');

        if (is_array($request->short_description))
            $service->short_description = join(':::', $request->short_description);

        if ($request->hasFile('image_description')) {
            $img_arr = array();

            foreach ($request->file('image_description') as $image_file) {
                array_push($img_arr, $image_file->store('agent/service', 'public'));
            }
            $service->image_description = join(':::', $img_arr);
        }

        $service->save();

        $service->setPaymentPlan($request->input('plan', null));
        $service->setFaq($request->input('faq', null));

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);
    }

    public function get_key(Request $request)
    {
        return response()->json([
            'code' => 200,
            'key' => bcrypt($request->api_id)
        ]);
    }

    /**
     * 서비스 상태 설정
     *
     * @param Illuminate\Http\Request $request
     */
    public function update_service_process(Request $request)
    {
        $service = Service::find($request->id);

        if($service->process != $request->process && $request->process == 3)
        {
            $alim = new \App\Models\Agent\Alim();

            $alim->agent_id = $service->agent_id;
            $alim->content = $service->name.' 서비스가 판매중으로 변경되었습니다.';
            $alim->move_id = $service->id;
            $alim->type = 1;
            $alim->save();

            $service->complete_at = date('Y-m-d H:i:s');

            $msg = '승인';
        }

        if ($request->process == 1)
        {
            if ($service->process == 2)
            {
                if ($service->request_cnt == 1)
                    $service->process = 1;
                else
                    $service->process = 4;

                $service->reject_at = null;
                $service->reject_reason = null;
            }
            else
            {
                if ($service->request_at)
                    $service->process = 4;
                else
                    $service->process = 1;

                $service->request_at = date('Y-m-d H:i:s');
            }
        }
        else if ($request->process == 2)
        {
            $alim = new \App\Models\Agent\Alim();

            $alim->agent_id = $service->agent_id;
            $alim->content = $service->name.' 서비스가 심사거절로 변경되었습니다.';
            $alim->move_id = $service->id;
            $alim->type = 1;
            $alim->save();

            $service->process = $request->process;
            $service->reject_reason = $request->reject_reason;
            $service->reject_at = date('Y-m-d H:i:s');

            $msg = '반려';
        }
        else
        {
            if($request->process == 5)
            {
                $alim = new \App\Models\Agent\Alim();
                $alim->agent_id = $service->agent_id;
                $alim->content = $service->name.' 서비스가 판매중지로 변경되었습니다.';
                $alim->move_id = $service->id;
                $alim->type = 1;
                $alim->save();
            }

            $service->process = $request->process;
        }

        $service->save();

        if( $request->process == 3 || $request->process == 2 )
        {
            // 메시지발송
            $phone = $service->contact_phone;
            $response = Http::asForm()
            ->withHeaders([
                'Authorization' => config('services.phone_api.authorization')
            ])
            ->post(config('services.phone_api.url'), [
                'phone' => str_replace("-", "", $phone),
                'msg' => '[MAPSTREND] 고객님의 신규등록 서비스가 \''.$msg.'\'. 되었습니다. '.PHP_EOL.config('app.domain.agent'),
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

    }

    /**
     * 심사 관리
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function evaluate(Request $request) {
        View::share('activeMenu', 'service.evaluate');

        $list = Service::whereIn('process', [1, 4])
                ->orderBy('request_at', 'desc')
                ->paginate(20);

        $write_permission = \Auth::guard('cms')->user()->getWritePermission(3);

        return view('cms.service.evaluate', compact('list', 'write_permission'));
    }

    /**
     * 심사반려
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function restoration(Request $request) {
        View::share('activeMenu', 'service.restoration');

        $list = Service::where('process', 2)
                ->orderBy('request_at', 'desc')
                ->paginate(20);

        $write_permission = \Auth::guard('cms')->user()->getWritePermission(4);

        return view('cms.service.restoration', compact('list', 'write_permission'));
    }

     /**
     * service_display
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function service_display(Request $request) {
        View::share('activeMenu', 'service.service_display');

        $agent_list = \App\Models\Users::where('type', 2)->orderBy('id','desc')->get();
        $service_list = Service::orderBy('id','desc')->get();
        $category_list = ServiceCategory::getCate();

        $best = [];
        $best_id = "";

        $sort_best = \App\Models\Cms\SortDisplay::where('tab', 'best')->first();
        if($sort_best){
            $best_id = $sort_best->service_id;
            $bestArr = explode(',', $sort_best->service_id);

            foreach($bestArr as $service_id){
                array_push($best,Service::find($service_id));
            }
        }

        $new = [];
        $new_id = "";

        $sort_new = \App\Models\Cms\SortDisplay::where('tab', 'new')->first();
        if($sort_new){
            $new_id = $sort_new->service_id;
            $newArr = explode(',', $sort_new->service_id);

            foreach($newArr as $service_id){
                array_push($new,Service::find($service_id));
            }
        }

        $satisfy200 = [];
        $satisfy200_id = "";

        $sort_satisfy200 = \App\Models\Cms\SortDisplay::where('tab', 'satisfy200')->first();
        if($sort_satisfy200){
            $satisfy200_id = $sort_satisfy200->service_id;
            $satisfy200Arr = explode(',', $sort_satisfy200->service_id);

            foreach($satisfy200Arr as $service_id){
                array_push($satisfy200,Service::find($service_id));
            }
        }

        $martech = [];
        $martech_id = "";

        $sort_martech = \App\Models\Cms\SortDisplay::where('tab', 'martech')->first();
        if($sort_martech){
            $martech_id = $sort_martech->service_id;
            $martechArr = explode(',', $sort_martech->service_id);

            foreach($martechArr as $service_id){
                array_push($martech,Service::find($service_id));
            }
        }

        return view('cms.service.service_display', compact(
            'agent_list',
            'service_list',
            'category_list',
            'best',
            'best_id',
            'new',
            'new_id',
            'satisfy200',
            'satisfy200_id',
            'martech',
            'martech_id'
        ));
    }

     /**
     * sort_display
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function sort_display(Request $request) {

        $tab = $request->tab;
        $service = $request->service;

        $display = \App\Models\Cms\SortDisplay::where('tab', $tab);
        if(!$display->exists()){
            $display = new \App\Models\Cms\SortDisplay;
        }else{
            $display = \App\Models\Cms\SortDisplay::where('tab', $tab)->first();
        }

        $display->tab = $tab;
        $display->service_id = $service;
        $display->save();

        return response()->json([
            'code' => 200,
            'tab' => $tab,
            'service' => $service,
            'display' => $display
        ]);
    }


}
