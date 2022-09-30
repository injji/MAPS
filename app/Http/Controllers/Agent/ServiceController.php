<?php

namespace App\Http\Controllers\Agent;

use Str;
use App\Http\Controllers\Controller;
use App\Models\Agent\Service;
use Illuminate\Http\Request;
use App\Rules\{Url, PhoneNumber, Email, Category};
use Illuminate\Validation\ValidationException;
use View;
use Validator;

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
        if ($request->user()->service->count() == 0) {
            return redirect()->route('agent.service_append');
        } else {
            return redirect()->route('agent.service_list');
        }
    }

    /**
     * 서비스 목록 view
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function list(Request $request) {
        View::share('activeMenu', 'agent.service_list');
        return view('agent.service.home', [
            'services' => $request->user()->service
        ]);
    }

    /**
     * 서비스등록 VIEW
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function createView(Request $request)
    {
        View::share('activeMenu', 'agent.service_append');

        $api_id = \Str::random(32);

        return view('agent.service.create', [
            'api_id' => $api_id,
            'api_key' => bcrypt($api_id)
        ]);
    }

    /**
     * 서비스 수정
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function edit(Request $request)
    {
        $service = Service::find($request->id);

        return view('agent.service.edit', compact('service'));
    }

    /**
     * 서비스 등록
     *
     * @param Illuminate\Http\Request $request
     * @return array
     *
     * @throws Illuminate\Validation\ValidationException 유효성 검사 실패시
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'url' => ['required', new Url],
            'category' => new Category,
            'icon' => 'required',
            'redirect_url' => new Url,
            'script_url' => new Url,
            'banner_image' => 'required',
            'short_description' => 'array',
            'image_description.*' => 'image',
            'ad_url' => new Url,
            'sample_url' => new Url,
            'contact_phone' => new PhoneNumber,
            'contact_email' => new Email
        ], [
            'name.required' => __('validation.service_name'),
            'url.required' => __('validation.service_url'),
            'icon.required' => __('validation.service_icon'),
            'banner_image.required' => __('validation.banner_image'),
        ]);

        if (!$validator->passes())
            return response()->json(['code' => 401, 'error' => $validator->errors()]);

        $service = new Service();

        $service->fill($request->only([
            'lang', 'default_lang', 'name', 'url', 'redirect_url', 'api_id', 'api_key', 'api_key_note',
            'version', 'script_url', 'release_note', 'youtube_url', 'service_info', 'full_description', 'ad_url', 'sample_url', 'contact_type',
            'contact_phone', 'contact_email', 'search_keyword', 'specification', 'currency', 'amount_min', 'amount_max'
        ]));

        $service->agent_id = $request->user()->id;

        if ($request->process == 1)
        {
            if ($service->request_at)
                $service->process = 4;
            else
                $service->process = 1;

            $service->request_at = date('Y-m-d H:i:s');
            // $service->request_cnt += 1;
        }
        else
            $service->process = $request->process;

        if ($request->input('category', ''))
        {
            $service->category1 = $request->category[0];
            $service->category2 = $request->category[1];
        }

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
            'message' => __('messages.save'),
            'id' => $service->getKey()
        ]);
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

        if ($service->process != $request->process && $request->process == 1)
        {
            if ($service->request_at)
                $service->process = 4;
            else
                $service->process = 1;

            $service->request_at = date('Y-m-d H:i:s');
            // $service->request_cnt += 1;
        }
        else
            $service->process = $request->process;

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
        View::share('activeMenu', 'agent.service_append');

        return response()->json([
            'code' => 200,
            'key' => bcrypt($request->api_id)
        ]);
    }
}
