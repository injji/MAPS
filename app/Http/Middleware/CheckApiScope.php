<?php

namespace App\Http\Middleware;

use App\Models\Api\Authorization;
use App\Models\Api\Script;
use Illuminate\Http\Request;
use Closure;

class CheckApiScope
{
    public function handle(Request $request, Closure $next, $scope)
    {
        $scriptId = $request->route('script_id');

        if( isset($scriptId) ){
            $script = Script::select('client_id')->where('script_id', $scriptId)->first();
            $clientId = $script->client_id;
        }else{
            $clientId = $request->input('client_id');
        }

        $scopes = Authorization::select('scope')
            ->where('client_id', $clientId)
            ->first();

        if( isset($scope) ){
            $scopeArr = explode("," , $scopes->scope);

            //DB에서 조회한 권한에 포함되는지 체크
            if( !in_array($scope,$scopeArr) ){
                //포함되지 않으면 넘어온 권한이 read인지 체크
                if( strpos($scope, "read") ){
                    //read면 write권한이 있는지 체크(write는 읽기 쓰기 권한을 둘 다 가지기때문)
                    $requestScopeArr = explode(".", $scope);
                    if( !in_array($requestScopeArr[0]."write",$scopeArr) ){
                        $result = [
                            "message" => "권한이 없습니다",
                        ];

                        return response()->json($result,403);
                    }
                }else{
                    $result = [
                        "message" => "권한이 없습니다",
                    ];

                    return response()->json($result,403);
                }
            }
        }else{
            $result = [
                "message" => "유효하지 않은 요청",
            ];

            return response()->json($result,401);
        }

        return $next($request);
    }

}
