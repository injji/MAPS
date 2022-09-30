<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\Script;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Agent\Service;
use App\Models\Api\AccessToken;
use App\Models\Api\Authorization;
use App\Models\Passport\Client;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Users as User;
use App\Models\Api\ClientService;

class OauthController extends Controller{

    public $today;

    public function __construct()
    {
        $this->today = date('Y-m-d H:i:s');
    }

    /**
     * Authorization Code 발행
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authorizationCode(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'client_id' => 'required|string',
            'client_sid' => 'required|string',
            'redirect_uri' => 'required|string',
            'scope' => 'required|string',
        ]);

        if($validator->fails()){
            $httpCode = 422;
            return response()->json([
                "message" => "필수 파라미터를 확인해주세요.",
            ],$httpCode);
        }

        $clientId = $request->input('client_id');
        $clientSid = $request->input('client_sid');
        $redirectUri = $request->input('redirect_uri');
        $scope = $request->input('scope');
        $authorizationCode = Str::random(64);

        $auth = Authorization::where('client_id', $clientId)->where('client_sid', $clientSid)->where('redirect_uri', $redirectUri)->exists();
        // return response()->json($auth);
        if( $auth ){
            $authorization = Authorization::where('client_id', $clientId)->where('redirect_uri', $redirectUri)->first();
        }else{
            $authorization = new Authorization();
        }

        $authorization->client_id = $clientId;
        $authorization->client_sid = $clientSid;
        $authorization->redirect_uri = $redirectUri;
        $authorization->scope = $scope;
        $authorization->authorization_code = $authorizationCode;
        $authorization->save();

        $returnURl = $redirectUri."?code=".$authorizationCode;

        return redirect($returnURl);
    }

    /**
     * Access Token 발행
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function token(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'client_id' => 'required|string',
            'client_sid' => 'required|string',
            'code' => 'required|string',
            'redirect_uri' => 'required|string',
        ]);

        if($validator->fails()){
            $httpCode = 422;
            return response()->json([
                "message" => "필수 파라미터를 확인해주세요.",
            ],$httpCode);
        }

        $clientId = $request->input('client_id');
        $clientSid = $request->input('client_sid');
        $authorizationCode = $request->input('code');
        $redirect = $request->input('redirect_uri');

        // $authCode = Authorization::select('authorization_code','scope')
        // ->where('client_id', $clientId)
        // ->where('authorization_code', $authorizationCode)
        // ->exists();

        // 클라이언트 서비스 테이블에 내역 존재하는지 체크 후에 엑세스 토큰 발급

        $auth = Authorization::from('tbl_api_authorization as auth')
        ->select(AccessToken::raw('count(id) as cnt'),'auth.*')
        ->where('client_id', $clientId)
        ->where('client_sid', $clientSid)
        ->where('authorization_code', $authorizationCode)
        ->first();

        $authorizationHeader = $request->header('Authorization');

        if( $authorizationHeader && ($auth->cnt == 1) ){
            $authorizationArr = explode(' ', $authorizationHeader);
            $authorization = base64_decode($authorizationArr[1]);
            $serviceArr = explode(':', $authorization);
            $serviceKey = $serviceArr[0];
            $serviceKeySecret = $serviceArr[1];
        }else{
            $httpCode = 401;
            return response()->json([
                "message" => "유효하지 않은 요청",
            ],$httpCode);
        }

        // $clientService = ClientService::where()

        $accessToken = Str::random(64);
        $refreshToken = Str::random(64);
        $expiresDate = date('Y-m-d H:i:s', strtotime("+2 hours"));
        $refreshExpiresDate = date('Y-m-d H:i:s', strtotime("+7 days"));

        // $client = Client::where('id', $serviceKey)
        //         ->where('secret', $serviceKeySecret)
        //         ->where('redirect', $redirect)
        //         ->exists();

        // $agent = AccessToken::from('oauth_clients as agent')
        // ->select(AccessToken::raw('count(id) as cnt'),'agent.*')
        // ->where('id', $serviceKey)
        // ->where('secret', $serviceKeySecret)
        // ->where('redirect', $redirect)
        // ->first();

        $agent = AccessToken::from('tbl_agent_service as agent')
        ->select(AccessToken::raw('count(id) as cnt'),'agent.*')
        ->where('api_id', $serviceKey)
        ->where('api_key', $serviceKeySecret)
        ->where('redirect_url', $redirect)
        ->first();

        $client_id = User::where('account', $clientId)->first();
        $clientService = ClientService::where('client_id', $client_id->id)
        ->where('service_id', $agent->id)
        ->exists();

        // return response()->json($clientService);

        if( $clientService ){

            if( $agent->cnt == 1 ){
                //토큰존재여부 체크
                //토큰테이블 조회
                $tokenRow = AccessToken::from('tbl_api_tokens as token')
                    ->select(AccessToken::raw('count(idx) as cnt'),'token.*')
                    ->where('service_key', $serviceKey)
                    ->where('service_key_secret', $serviceKeySecret)
                    ->first();

                if( $tokenRow->cnt == 1 ){
                    //토큰이 있으면 업데이트
                    $token = new AccessToken();
                    $token = AccessToken::where('service_key', $serviceKey)->where('service_key_secret', $serviceKeySecret)->first();
                    $token->access_token = $accessToken;
                    $token->refresh_token = $refreshToken;
                    $token->expires_date = $expiresDate;
                    $token->refresh_expires_date = $refreshExpiresDate;
                    $token->updated_date = $this->today;
                    $token->save();

                    $httpCode = 201;
                }else{
                    //토큰이 없으면 인서트
                    $token = new AccessToken();
                    $token->client_id = $clientId;
                    $token->redirect_uri = $redirect;
                    $token->service_key = $serviceKey;
                    $token->service_key_secret = $serviceKeySecret;
                    $token->access_token = $accessToken;
                    $token->refresh_token = $refreshToken;
                    $token->expires_date = $expiresDate;
                    $token->refresh_expires_date = $refreshExpiresDate;
                    $token->created_date = $this->today;
                    $token->save();

                    $httpCode = 201;
                }

                $authArr = explode(",", $auth->scope);
                $result = [
                    'access_token'=>$accessToken,
                    'refresh_token'=>$refreshToken,
                    'expires_date'=>$expiresDate,
                    'refresh_expires_date'=>$refreshExpiresDate,
                    'service_id'=>$agent->id,
                    'scope'=>$authArr,
                    'client_id'=>$clientId,
                    'issued_date'=>$this->today,
                ];

            }else{
                //유효하지 않은 요청
                $httpCode = 401;
                $result = [
                    'message' => '유효하지 않은 요청',
                ];
            }

            return response()->json($result,$httpCode);
        }else{
            //유효하지 않은 요청
            $httpCode = 401;
            $result = [
                'message' => '유효하지 않은 요청',
            ];

            return response()->json($result,$httpCode);
        }

    }

    /**
     * Refresh Token 발행
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'client_id' => 'required|string',
            'client_sid' => 'required|string',
            'refresh_token' => 'required|string',
        ]);

        if($validator->fails()){
            $httpCode = 422;
            return response()->json([
                "message" => "필수 파라미터를 확인해주세요.",
            ],$httpCode);
        }

        $clientId = $request->input('client_id');
        $clientSid = $request->input('client_sid');
        $refreshToken = $request->input('refresh_token');

        $auth = Authorization::from('tbl_api_authorization as auth')
        ->select(AccessToken::raw('count(id) as cnt'),'auth.*')
        ->where('client_id', $clientId)
        ->where('client_sid', $clientSid)
        ->first();

        $authorizationHeader = $request->header('Authorization');

        if( $authorizationHeader ){
            $authorizationArr = explode(' ', $authorizationHeader);
            $authorization = base64_decode($authorizationArr[1]);
            $serviceArr = explode(':', $authorization);
            $serviceKey = $serviceArr[0];
            $serviceKeySecret = $serviceArr[1];
        }else{
            $httpCode = 401;
            return response()->json([
                "message" => "유효하지 않은 요청",
            ],$httpCode);
        }

        $newAccessToken = Str::random(64);
        $newRefreshToken = Str::random(64);
        $expiresDate = date('Y-m-d H:i:s', strtotime("+2 hours"));
        $refreshExpiresDate = date('Y-m-d H:i:s', strtotime("+7 days"));

        // $client = AccessToken::from('oauth_clients as client')
        // ->select(AccessToken::raw('count(id) as cnt'),'client.*')
        // ->where('id', $serviceKey)
        // ->where('secret', $serviceKeySecret)
        // ->first();

        $agent = AccessToken::from('tbl_agent_service as agent')
        ->select(AccessToken::raw('count(id) as cnt'),'agent.*')
        ->where('api_id', $serviceKey)
        ->where('api_key', $serviceKeySecret)
        ->first();

        $client_id = User::where('account', $clientId)->first();
        $clientService = ClientService::where('client_id', $client_id->id)
        ->where('service_id', $agent->id)
        ->exists();

        if( $clientService ){

            if( $agent->cnt == 1 ){
                //리프레쉬토큰 유효성 검증
                //토큰테이블 조회
                $tokenRow = AccessToken::from('tbl_api_tokens as token')
                    ->select(AccessToken::raw('count(idx) as cnt'),'token.*')
                    ->where('service_key', $serviceKey)
                    ->where('service_key_secret', $serviceKeySecret)
                    ->where('refresh_token', $refreshToken)
                    ->first();

                if( $tokenRow->refresh_expires_date > $this->today){
                    //리프레쉬 토큰이 유효하면 업데이트
                    $token = new AccessToken();
                    $token = AccessToken::where('service_key', $serviceKey)->where('service_key_secret', $serviceKeySecret)->first();
                    $token->access_token = $newAccessToken;
                    $token->refresh_token = $newRefreshToken;
                    $token->expires_date = $expiresDate;
                    $token->refresh_expires_date = $refreshExpiresDate;
                    $token->updated_date = $this->today;
                    $token->save();

                    $httpCode = 201;
                    $authArr = explode(",", $auth->scope);
                    $result = [
                        'access_token'=>$newAccessToken,
                        'refresh_token'=>$newRefreshToken,
                        'expires_date'=>$expiresDate,
                        'refresh_expires_date'=>$refreshExpiresDate,
                        'service_id'=>$agent->id,
                        'scope'=>$authArr,
                        'client_id'=>$clientId,
                        'issued_date'=>$this->today,
                    ];

                }else{
                    //유효하지 않은 리프레쉬 토큰
                    $httpCode = 401;
                    $result = [
                        'message' => '유효하지 않은 리프레쉬 토큰',
                    ];
                }
            }else{
                //유효하지 않은 요청
                $httpCode = 401;
                $result = [
                    'message' => '유효하지 않은 요청',
                ];
            }

            return response()->json($result,$httpCode);
        }

    }

}

?>
