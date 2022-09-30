<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\AdminUser;

class CmsLoginController extends Controller
{
    /**
     * SSO 로그인
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request)
    {
        return redirect(config('services.innofam.url').'authorize?'.http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.innofam.key'),
        ]));    }

    /**
     * 로그인 처리
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->guard()->attempt($this->credentials($request))) {
            // 로그인 성공
            return [
                'code' => 200,
                'message' => __('messages.login.success', [
                    'user' => $this->guard()->user()->login_id
                ]),
                'users' => $this->guard()->user(),
            ];
        } else {
            // 로그인 실패
            abort(422, __('messages.login.fail'));
        }
    }
    /**
     * 로그아웃
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\RedirectResponse
    */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$request->session()->pull('access_token')
        ])->get(config('services.innofam.url').'logout');
        return redirect('/login');
    }

    /**
     * 이노팸 SSO authorize
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\RedirectResponse
     */
    public function innofamLogin(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $response = Http::get(config('services.innofam.url').'token?'.http_build_query([
            'grant_type' => 'authorization_code',
            'client_id' => config('services.innofam.key'),
            'client_secret' => config('services.innofam.secret'),
            'code' => $request->code,
        ]));

        if ($response->json()['error'] == 1000) {
            $user = AdminUser::innofam($response->json()['info']['idx'])->first();
            if ($user == null) {
                // 회원 생성
                AdminUser::create([
                    'oauth_id' => $response->json()['info']['idx'],
                    'oauth_type' => AdminUser::getOuathType('innofam'),
                    'name' => $response->json()['info']['name'],
                ]);
                dd('관리자에게 승인 요청해 주세요.');
            } else {
                // 로그인
	   if ($user->use == 0)
                    dd('관리자에게 승인 요청해 주세요.');

                $user->name = $response->json()['info']['name'];
                $user->save();
                $this->guard()->login($user);
            }
            $request->session()->put('access_token', $response->json()['access_token']);
            $request->session()->put('refresh_token', $response->json()['refresh_token']);
            $request->session()->put('innofam.info', $response->json()['info']);
            return redirect($request->session()->pull('redirectUrl') ?? '/');
        } else {
            abort(422, __('messages.login.fail'));
        }
    }

    /**
     * 인증 정보
     *
     * @return Illuminate\Auth\SessionGuard
     */
    protected function guard()
    {
        return Auth::guard('cms');
    }
    /**
     * 유효성 겁사
     *
     * @param Illuminate\Http\Request $request
     * @return void
     */
    protected function validateLogin($request)
    {
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required|string',
        ]);
    }
    /**
     * 로그인 payload
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials($request)
    {
        return $request->only('login_id', 'password');
    }
}
