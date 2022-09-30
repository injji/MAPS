<?php

namespace App\Http\Controllers\Auth;

use App\Rules\{PhoneNumber, Email, Password, Url};
use App\Http\Controllers\Controller;
use App\Models\Client\Site;
use Illuminate\Http\Request;
use App\Models\Users;
use Validator;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Cookie;

class UserRegisterController extends Controller
{
    public function agree(Request $request)
    {
        return view('auth.agree', ['type' => $request->type]);
    }

    public function register1(Request $request)
    {
        $byapps_user = $request->session()->get('byapps_user');
        $byapps_user_app = $request->session()->get('byapps_user_app');

        return view('auth.register1', compact('byapps_user','byapps_user_app'));
    }

    public function register2(Request $request)
    {
        return view('auth.register2');
    }

    public function register3_byapps(Request $request)
    {
        // $app_id = $request->app_id;
        $key = 'xFAGzkn9PW8l21WSlovqlCBxmf6xWMOf';
        $app_id = $this->aes_decrypt($key,$request->app_id);
        $byapps_id = \App\Models\Byapps\Apps::select('mem_id')->where('app_id', $app_id)->first();

        return view('auth.register3',compact('app_id','byapps_id'));
    }

    public function client_save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account' => 'required|unique:'.(new Users)->getTable().'|string',
            'password' => ['required', new Password],
            'c_password' => 'required|same:password',
            'company_name' => 'required|string',
            'manager_name' => 'required|string',
            'manager_phone' => ['required', new PhoneNumber, 'unique:'.(new Users)->getTable()],
            'manager_email' => ['required', new Email],
        ], [
            'c_password.same' => __('validation.confirmed'),
            'manager_phone.unique' => __('validation.uniquephone'),
        ]);

        if (!$validator->passes())
            return response()->json(['code' => 401, 'error' => $validator->errors()]);

        $user = Users::create([
            'account' => $request->account,
            'password' => bcrypt($request->password),
            'type' => 1,
            'company_name' => $request->company_name ?? '',
            'manager_name' => $request->manager_name ?? '',
            'manager_phone' => $request->manager_phone ?? '',
            'manager_email' => $request->manager_email ?? '',
            'byapps_id' => $request->byapps_id ?? '',
        ]);

        $byapps_user_app = $request->session()->get('byapps_user_app');

        if($byapps_user_app){
            switch($byapps_user_app->host_name){
                case 'cafe24': $hostname = 1; break;
                case 'makeshop': $hostname = 2; break;
                case 'godo': $hostname = 3; break;
                default: $hostname = 0; break;
            }

            $user_site = Site::create([
                'name' => $byapps_user_app->app_name,
                'client_sid' => \Str::random(40),
                'client_id' => $user->getKey(),
                'url' => $byapps_user_app->surl,
                'type' => 0,
                'hostname' => $hostname,
            ]);

            $request->session()->put('byapps',true);
        }

        return response()->json(['code' => 200, 'account' => $request->account]);
    }

    public function agent_save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account' => 'required|unique:'.(new Users)->getTable().'|string',
            'password' => ['required', new Password],
            'c_password' => 'required|same:password',
            'company_name' => 'required|string',
            'business_no' => 'required|string',
            'director_name' => 'required|string',
            'address' => 'required|string',
            'homepage_url' => 'required|string',
            'director_phone' => ['required', new PhoneNumber],
            'director_email' => ['required', new Email],
        ], [
            'c_password.same' => __('validation.confirmed'),
        ]);

        if (!$validator->passes())
            return response()->json(['code' => 401, 'error' => $validator->errors()]);

        $user = Users::create([
            'account' => $request->account,
            'password' => bcrypt($request->password),
            'type' => 2,
            'company_name' => $request->company_name ?? '',
            'manager_name' => $request->director_name ?? '',
            'manager_phone' => $request->director_phone ?? '',
            'manager_email' => $request->director_email ?? '',
            'business_no' => $request->business_no ?? '',
            'director_name' => $request->director_name ?? '',
            'address' => $request->address ?? '',
            'homepage_url' => $request->homepage_url ?? '',
            'director_phone' => $request->director_phone ?? '',
            'director_email' => $request->director_email ?? '',
        ]);

        return response()->json(['code' => 200, 'account' => $request->account]);
    }

    public function byapps(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!$validator->passes())
            return response()->json(['code' => 401, 'error' => $validator->errors()]);

        try {
            $user = \App\Models\Byapps\User::where('mem_id', $request->account)->where('passwd', DB::raw("password('{$request->password}')"))->firstOrFail();

            //바이앱스에서 넘어왔을경우
            if($request->session()->get('byapps_id')){

                $byapps_app_id = $request->app_id;
                // $byapps_app_id = 'demoapp';
                $byapps = $user->apps->where('app_id',$byapps_app_id)->first();

                $request->session()->put('byapps_user', $user);
                $request->session()->put('byapps_user_app', $byapps);

                return [
                    'code' => 200,
                    'message' => __('messages.register.byapps_check.success'),
                    'userid' => $user->getKey(),
                    'byapps' => $byapps,
                    'user'  => $user,
                    'redirect' => '/register/register1'
                ];
            }else{
                //회원가입 눌러서 들어온 일반 바이앱스 연동
                //맵스 회원인경우
                if ($user->isRegistered($request->account)) {

                    return [
                        'code' => 201,
                        'message' => __('messages.register.byapps_check.already'),
                    ];

                }else{
                    //맵스 회원이 아닌경우
                    $request->session()->put('byapps_register_id', $user->getKey());

                    return [
                        'code' => 200,
                        'message' => __('messages.register.byapps_check.success'),
                        'userid' => $user->getKey(),
                        'redirect' => '/register/register1'
                    ];
                }
            }

        } catch (ModelNotFoundException $e) {
            // throw ValidationException::withMessages([
            //     'login_id' => [__('messages.register.byapps_check.fail')],
            // ]);
            return [
                'code' => 401,
                'message' => __('messages.register.byapps_check.fail'),
            ];
        }
    }

    public function done(Request $request)
    {
        if(!$request->account)
            abort(401);

        return view('auth.register_done', ['account' => $request->account]);
    }

    public function get_term(Request $request)
    {
        $term = \App\Models\Cms\Terms::where('type', $request->type)->first();

        return response()->json(['code' => 200, 'content' => $term]);
    }

    /**
      * byapps appid 복호화
      *
      * @param secret key , encoding string
    */
    protected function aes_decrypt ($key, $value)
    {
        $KEY_128 = substr($key, 0, 128 / 8);
        $KEY_256 = substr($key, 0, 256 / 8);
        return openssl_decrypt($value, 'AES-256-CBC', $KEY_256, 0, $KEY_128);
    }
}
