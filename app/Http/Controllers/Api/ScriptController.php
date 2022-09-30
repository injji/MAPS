<?php

namespace App\Http\Controllers\Api;

use DB;
use App\Models\Api\Script;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Users as User;
use App\Models\Agent\Service;
use App\Models\Api\ClientSite;
use App\Models\Api\ClientService;
use Illuminate\Support\Facades\Storage;

class ScriptController extends Controller{

    public $today;

    public function __invoke(Request $request)
    {
        $id = $request->input('id');
        $sid = $request->input('sid');

        $clientSite = ClientSite::where('client_sid',$sid)
        ->first();
        $clientSite->header = 1;
        $clientSite->save();

        // $file = Storage::path('/MAPS/MAPS_JS.php?id='.$id.'&sid='.$sid);

        return redirect('/MAPS/MAPS_JS.php?id='.$id.'&sid='.$sid);
        // return response($file);
    }

    public function test()
    {
        return view('maps.test1');
    }

    public function __construct()
    {
        $this->today = date('Y-m-d H:i:s');
    }

    /**
     * 스크립트 추가 요청
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function insertScript(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'client_id' => 'required|string',
            'client_sid' => 'required|string',
            'service_key' => 'required|string',
            'script_src' => 'required|array',
            'script_init' => 'array',
        ]);

        if($validator->fails()){
            $httpCode = 422;
            return response()->json([
                "message" => "파라미터 오류",
            ],$httpCode);
        }

        // $sql = "SELECT * FROM tbl_scripts GROUP BY client_id,client_sid";
        // $groupRes = DB::select($sql);

        // return response()->json($groupRes[0]->client_id);

        $clientId = $request->input('client_id');
        $clientSid = $request->input('client_sid');
        $serviceKey = $request->input('service_key');
        $scriptSrc = $request->input('script_src');
        $scriptInit = $request->input('script_init');

        /*
            클라이언트가 서비스 신청한 내역에서
            client_id 와 client_sid , service_key 로 내역조회
            내역이 있으면 서비스기간에 현재시간이 포함되는가 체크
            내역이 없으면 유효하지않은 요청

            고객사가 설치를 완료할 때 , (고객사가 요청) -> 제휴사가 api 호출
        */
        $client_id = User::where('account', $clientId)->first();
        $service_id = Service::where('api_id', $serviceKey)->first();
        $site_id = ClientSite::where('client_sid', $clientSid)->first();

        if($site_id->header = 1){

            $clientService = ClientService::from('tbl_client_service as client')
                            ->select(ClientService::raw('count(id) as cnt'),'client.*')
                            ->where('client_id', $client_id->id)
                            ->where('service_id', $service_id->id)
                            ->where('site_id', $site_id->id)
                            ->first();
            // return response()->json($clientService);
            if( $clientService->cnt == 1 ){

                if( $clientService->service_end_at < $this->today ){
                    $httpCode = 401;
                    return response()->json([
                        "message" => "유효하지 않은 요청",
                    ],$httpCode);
                }

                /*
                    client_id와 service_key로 스크립트 테이블에 값이 존재하는지 체크
                    값이 존재하면 해당 스크립트에 업데이트
                    값이 존재하지 않으면 인서트
                */
                $scriptCheck = Script::where('client_id', $clientId)->where('service_key', $serviceKey)->exists();

                if( $scriptCheck ){
                    $script = Script::where('client_id', $clientId)->where('service_key', $serviceKey)->first();
                }else{
                    $script = new Script();
                }
                $script->client_id = $clientId;
                $script->client_sid = $clientSid;
                $script->service_key = $serviceKey;
                $script->script_src = json_encode($scriptSrc);
                $script->script_init = json_encode($scriptInit);
                $script->service_start_at = $clientService->service_start_at;
                $script->service_end_at = $clientService->service_end_at;
                $script->created_date = $this->today;
                $script->save();

                Script::makeScriptFile($clientId,$clientSid);

                $result = [
                    'scripts' =>
                        [
                            'script_id' => $script->script_id,
                            'client_id' => $clientId,
                            'created_date' => $this->today,
                        ]
                ];
                $httpCode = 201;
                return response()->json($result,$httpCode);

            }else{
                $httpCode = 401;
                return response()->json([
                    "message" => "유효하지 않은 요청",
                ],$httpCode);
            }
        }else{
            $httpCode = 402;
            return response()->json([
                "message" => "공통 스크립트 미설치",
            ],$httpCode);
        }



    }


    /**
     * 스크립트 수정 요청
     *
     * @param Illuminate\Http\Request $request
     * @param integer $scriptId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateScript(Request $request, $scriptId)
    {

        $validator = Validator::make($request->all(),[
            'client_id' => 'required|string',
            'client_sid' => 'required|string',
            'service_key' => 'required|string',
            'script_src' => 'required|array',
            'script_init' => 'array',
        ]);

        if($validator->fails()){
            $httpCode = 422;
            return response()->json([
                "message" => "파라미터 오류",
            ],$httpCode);
        }

        $clientId = $request->input('client_id');
        $clientSid = $request->input('client_sid');
        $serviceKey = $request->input('service_key');
        $scriptSrc = $request->input('script_src');
        $scriptInit = $request->input('script_init');

        $client_id = User::where('account', $clientId)->first();
        $service_id = Service::where('api_id', $serviceKey)->first();
        $site_id = ClientSite::where('client_sid', $clientSid)->first();

        $clientService = ClientService::from('tbl_client_service as client')
        ->select(ClientService::raw('count(id) as cnt'),'client.*')
        ->where('client_id', $client_id->id)
        ->where('service_id', $service_id->id)
        ->where('site_id', $site_id->id)
        ->first();

        if( $clientService->cnt == 1 ){

            if( $clientService->service_end_at < $this->today ){
                $httpCode = 401;
                return response()->json([
                    "message" => "유효하지 않은 요청",
                ],$httpCode);
            }

            $script = Script::where('script_id', $scriptId)->first();
            $script->client_id = $clientId;
            $script->client_sid = $clientSid;
            $script->service_key = $serviceKey;
            $script->script_src = $scriptSrc;
            $script->script_init = $scriptInit;
            $script->service_start_at = $clientService->service_start_at;
            $script->service_end_at = $clientService->service_end_at;
            $script->updated_date = $this->today;
            $script->save();

            $result = [
                'scripts' =>
                    [
                        'script_id' => $scriptId,
                        'client_id' => $clientId,
                        'created_date' => $script->created_date,
                        'updated_date' => $this->today,
                    ]
            ];

            $httpCode = 200;
            return response()->json($result,$httpCode);
        }else{
            $httpCode = 401;
            return response()->json([
                "message" => "유효하지 않은 요청",
            ],$httpCode);
        }

    }

    /**
     * 스크립트 삭제 요청
     *
     * @param Illuminate\Http\Request $request
     * @param integer $scriptId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteScript(Request $request, $scriptId)
    {

        $script = Script::where('script_id', $scriptId)->first();
        $script->delete();

        $result = [
            'scripts' =>
                [
                    'script_id' => $scriptId,
                    'client_id' => $script->client_id,
                    'created_date' => $script->created_date,
                    'deleted_date' => $this->today,
                ]
        ];

        $httpCode = 201;
        return response()->json($result,$httpCode);
    }

}
?>
