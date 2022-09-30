<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Rules\{PhoneNumber, Password, Url, BusinessNo};
use Illuminate\Http\Request;
use App\Models\Client\Site;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Validator;
use Hash;
use View;

class AgentController extends Controller
{
    /**
     * Dashboard View
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function __invoke(Request $request)
    {
        return view('agent.dashboard');
    }

    public function my_info(Request $request)
    {
        if (
            $request->session()->has('password_auth_time') &&
            $request->session()->get('password_auth_time') > now()->subMonths(10) &&
            $request->session()->get('password_auth_id') == $request->user()->getKey()
        ) {
            $request->session()->put('password_auth_time', now());

            $apply = \App\Models\UserDrop::where('account', \Auth::user()->account)->whereNull('dropped_at')->orderBy('id','desc')->first();

            $drop_options = \App\Models\Cms\QuestionOption::where('type',3)->first();
            $d_option = explode(',', $drop_options->content);

            return view('agent.auth.my', [
                'user' => $request->user(),
                'banks' => config('app.banks'),
                'tab' => $request->tab ?? 1,
                'drop' => $apply ?? null,
                'options' => $d_option
            ]);
        }
        else
            return view('agent.auth.password');
    }

    /**
      * 회원탈퇴
      *
      * @param Illuminate\Http\Request $request
    */
    public function goodbye(Request $request)
    {

        $apply = \App\Models\UserDrop::where('account', \Auth::user()->account)->whereNull('dropped_at')->orderBy('id','desc')->first();

        if($apply){
            $code = 401;
        }else{
            $drop = new \App\Models\UserDrop();
            $drop->client_id = \Auth::user()->getKey();
            $drop->user_type = '제휴사';
            $drop->account = \Auth::user()->account;
            $drop->company_name = \Auth::user()->company_name;
            $drop->reason = $request->reason;
            $drop->save();

            $code = 200;
        }

        return response()->json([
            'code' => $code
        ]);
    }

    /**
     * 비밀번호 재확인
     *
     * @param Illuminate\Http\Request $request
     * @return array
     *
     * @throws Illuminate\Validation\ValidationException 유효성 검사 실패시
     */
    public function check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required'
        ]);

        if (!$validator->passes())
            return response()->json(['code' => 401, 'error' => $validator->errors()]);

        if (!Hash::check($request->password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'password' => __('validation.confirmed')
            ]);
        }
        $request->session()->put('password_auth_time', now());
        $request->session()->put('password_auth_id', $request->user()->getKey());

        return response()->json([
            'code' => 200,
            'message' => __('messages.password.success')
        ]);
    }

    /**
     * 회원정보 수정
     *
     * @param Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {
        if ($request->tab == 1) {
            $validator = Validator::make($request->all(), [
                'c_password' => 'same:password',
                'company_name' => 'string',
                'manager_email' => 'email|nullable',
                'business_no' => 'string|nullable', // new BusinessNo,
                'homepage_url' => 'string|nullable',
                'director_phone' => new PhoneNumber,
                'manager_name' => 'string|nullable',
                'manager_phone' => new PhoneNumber,
                'director_email' => 'email|nullable',
            ], [
                'c_password.same' => __('validation.confirmed'),
            ]);

            if (!$validator->passes())
                return response()->json(['code' => 401, 'error' => $validator->errors()]);

            if ($request->input('password', ''))
                $request->user()->password = Hash::make($request->password);

            if ($request->hasFile('business_registration'))
                $request->user()->business_registration = $request->business_registration->store('client/business_registration', 'public');

            $request->user()->fill($request->only(['company_name', 'business_no', 'director_name', 'address', 'order_report_number',
                            'specialized_field', 'homepage_url', 'director_phone', 'director_email', 'inquiry_time', 'manager_name',
                            'manager_phone', 'manager_email']));

            $request->user()->save();
        }
        else if ($request->tab == 2) {
            $validator = Validator::make($request->all(), [
                'tax_email' => 'email|nullable',
                'tax_business_no' => 'string|nullable', // new BusinessNo,
            ]);

            if (!$validator->passes())
                return response()->json(['code' => 401, 'error' => $validator->errors()]);

            if ($request->hasFile('tax_business_registration'))
                $request->user()->tax_business_registration = $request->tax_business_registration->store('client/tax_business_registration', 'public');

            $request->user()->fill($request->only(['account_holder', 'bank_name', 'account_number', 'tax_email', 'tax_company_name',
                'tax_business_no', 'tax_director_name', 'tax_address']));

            $request->user()->save();
        }

        return response()->json([
            'code' => 200,
            'message' => __('messages.save')
        ]);
    }
}
