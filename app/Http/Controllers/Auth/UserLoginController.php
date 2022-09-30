<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Users;
use Validator;
use Cookie;

class UserLoginController extends Controller
{
    /**
     * Home View
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function __invoke(Request $request)
    {
        return view('auth.login');
    }

    public function login_from_cms(Request $request)
    {
        // $user = Users::find($request->id);
        // $this->guard()->login($user);

        // return redirect($request->type == 2 ? config('app.pre_url').'://'.config('app.domain.agent') : config('app.pre_url').'://'.config('app.domain.client'));
        return redirect(config('app.pre_url').'://'.config('app.domain.front').'/login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!$validator->passes())
            return response()->json(['code' => 401, 'error' => $validator->errors()]);

        $remember_me = $request->has('remember') ? true : false;

        if($remember_me)
            Cookie::queue('account', $request->account, 1440);
        else
            Cookie::queue(Cookie::forget('account'));

        if($this->guard()->attempt($this->credentials($request), $remember_me))
        {

            if(!$this->guard()->user()->dropped_at){
                $user_log = \App\Models\UserLog::where('user_id', $this->guard()->user()->id)
                                            ->where('created_at', date('Y-m-d'))
                                            ->first();

                if(!$user_log)
                {
                    $user_log = new \App\Models\UserLog();
                    $user_log->user_id = $this->guard()->user()->id;
                    $user_log->created_at = date('Y-m-d');
                    $user_log->save();
                }

                $redirect = $request->session()->get('service_url');
                $byapps = $request->session()->get('byapps');
                if(empty($byapps)){
                    $redirect = config('app.pre_url').'://'.($this->guard()->user()->type == 2 ? config('app.domain.agent') : config('app.domain.client'));
                }

                $request->session()->forget('service_url');
                $request->session()->forget('byapps_user');
                $request->session()->forget('byapps_user_app');
                $request->session()->forget('byapps_id');
                $request->session()->forget('byapps_app_id');
                $request->session()->forget('byapps');

                return response()->json([
                    'code' => 200,
                    'message' => __('messages.login.success', [
                        'user' => $this->guard()->user()->company_name,
                    ]),
                    'redirect' => $redirect
                ]);
            }else{
                return response()->json(['code' => 402, 'error' => '탈퇴된 회원 입니다.']);
            }

        }
        else
            return response()->json(['code' => 402, 'error' => __('messages.login.fail')]);
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        // Auth::logout();
        $request->session()->forget('service_url');
        $request->session()->forget('byapps_user');
        $request->session()->forget('byapps_user_app');
        $request->session()->forget('byapps_id');
        $request->session()->forget('byapps_app_id');
        $request->session()->forget('byapps');
        return redirect('');
    }

    protected function guard()
    {
        return Auth::guard('user');
    }

    protected function credentials($request)
    {
        return $request->only('account', 'password');
    }

    public function check_account(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone1' => 'required',
        ]);

        if (!$validator->passes())
            return response()->json(['code' => 401, 'error' => $validator->errors()]);

        $user = Users::where('manager_phone', $request->phone1)->first();

        if($user)
            return response()->json(['code' => 200, 'account' => $user->account]);
        else
            return response()->json(['code' => 402, 'error' => __('messages.account.notfound')]);
    }

    public function find_account(Request $request)
    {
        return view('auth.find_account', ['account' => $request->account]);
    }

    public function reset_pw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account' => 'required',
            'phone2' => 'required',
        ]);

        if (!$validator->passes())
            return response()->json(['code' => 401, 'error' => $validator->errors()]);

        $user = Users::where('account', $request->account)
                    ->where('manager_phone', $request->phone2)
                    ->first();

        if($user)
        {
            $password = \Str::random(12);

            $response = Http::asForm()
                ->withHeaders([
                    'Authorization' => config('services.phone_api.authorization')
                ])
                ->post(config('services.phone_api.url'), [
                    'phone' => str_replace("-", "", $request->phone2),
                    'msg' => $password,
                ]);

            if($response->json()['code'] == 200)
            {
                $user->password = bcrypt($password);
                $user->save();

                return response()->json(['code' => 200, 'phone' => $request->phone2]);
            }
            else
                return response()->json(['code' => 402, 'error' => __('messages.send_fail')]);
        }
        else
            return response()->json(['code' => 402, 'error' => __('messages.account.notfound')]);
    }

    public function find_pw(Request $request)
    {
        return view('auth.find_pw', ['phone' => $request->phone]);
    }
}
