<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use App\Models\Api\Service;
use Closure;

class CheckScriptInit
{
    public function handle(Request $request, Closure $next)
    {

        $clientId = $request->input('client_id');
        $clientSid = $request->input('client_sid');
        $serviceKey = $request->input('service_key');
        $scriptSrc = $request->input('script_src');
        $scriptInit = $request->input('script_init');

        //서비스 등록시 설정한 스크립트 설정값과 scriptInit 비교
        $init = Service::from('tbl_agent_service as agent')
        ->select(Service::raw('count(id) as cnt'),'api_key_note')->where('api_id', $serviceKey)->first();

        if( $init->cnt == 1 ){

            if( !empty($init->api_key_note) && !empty($scriptInit) ){

                return response()->json($init->api_key_note);
                $initArr = explode("\r\n", $init->api_key_note);
                $getInitCnt = count($initArr);
                $scriptInitCnt = count($scriptInit);

                if( $getInitCnt == $scriptInitCnt ){
                    //설정키값과 넘어온 키값 비교

                    $initData = [];
                    foreach($initArr as $i => $initValue){
                        foreach($scriptInit as $j => $scriptValue){
                            $changeArr = ["\"","{","}"];
                            $initKeyArr = explode(":",str_replace($changeArr,"",json_encode($scriptValue)));
                            if(strpos($initValue,$initKeyArr[0]) !== false){
                                array_push($initData,str_replace('$'.$initKeyArr[0],$initKeyArr[1],$initValue));
                            }
                        }
                    }

                    $initDataCnt = count($initData);

                    if( $getInitCnt != $initDataCnt ){
                        //유효하지 않은 설정값
                        $httpCode = 422;
                        return response()->json([
                            'message' => "유효하지 않은 설정값",
                        ],$httpCode);
                    }
                }else{
                    //유효하지 않은 설정값
                    $httpCode = 422;
                    return response()->json([
                        'message' => "유효하지 않은 설정값",
                    ],$httpCode);
                }
            }else if( empty($init->api_key_note) && !empty($scriptInit) ){
                //유효하지 않은 설정값
                $httpCode = 422;
                return response()->json([
                    'message' => "유효하지 않은 설정값",
                ],$httpCode);
            }

        }else{
            //유효하지않은 서비스키
            $httpCode = 401;
            return response()->json([
                'message' => "유효하지 않은 서비스키",
            ],$httpCode);
        }

        return $next($request);
    }

}
