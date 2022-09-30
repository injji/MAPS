<?php

namespace App\Http\Middleware;

use App\Models\Api\AccessToken;
use Illuminate\Http\Request;
use Closure;

class CheckAccessToken
{
    public function handle(Request $request, Closure $next)
    {
        $authorizationHeader = $request->header('Authorization');


        if( $authorizationHeader ){
            $authorizationArr = explode(' ', $authorizationHeader);
            $accessToken = $authorizationArr[1];
        }else{
            $httpCode = 401;
            return response()->json([
                "message" => "유효하지 않은 요청",
            ],$httpCode);
        }

        $today = date('Y-m-d H:i:s');

        $token = AccessToken::select('expires_date')
            ->where('access_token', $accessToken)
            ->first();

        //토큰이 있는지 , 유효한 토큰인지 체크
        if( !isset($token) || ($token->expires_date < $today) ){

            $httpCode = 401;
            $result = [
                "message" => "유효한 토큰이 아닙니다",
            ];

            return response()->json($result,$httpCode);
        }

        return $next($request);
    }

}
