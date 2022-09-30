<?php

namespace App\Console\Commands;

// use App\Console\Commands\File;
use Illuminate\Console\Command;
use DB;
use App\Models\Api\Script;

class ScriptDataFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '매일 스크립트 데이터 파일 재생성';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $serviceEndDate = date('Y-m-d')." 23:59:59";

        //배치돌릴시 client_sid 를 그룹으로 묶어 반복
        // $sql = "SELECT * FROM tbl_scripts WHERE client_id = '{$clientId}' AND client_sid = '{$clientSid}' AND service_end_at > '{$serviceEndDate}'";
        $sql = "SELECT * FROM tbl_scripts GROUP BY client_id,client_sid";
        $groupRes = DB::select($sql);

        foreach($groupRes as $groupRow){
            $sql = "SELECT * FROM tbl_scripts WHERE client_id = '{$groupRow->client_id}' AND client_sid = '{$groupRow->client_sid}' AND service_end_at > '{$serviceEndDate}'";
            $res = DB::select($sql);

            //tbl_scripts 테이블의 행만큼 반복
            $agentAssignScriptData = [];

            foreach($res as $key => $row){

                $serviceKey = $row->service_key;

                $agentInitSql = "SELECT * FROM tbl_agent_service WHERE api_id = '{$serviceKey}'";
                $agentInitRes = DB::select($agentInitSql);

                $initArr = explode("\r\n", $agentInitRes[0]->api_key_note);

                $initData = [];
                foreach($initArr as $i => $initValue){
                    // return response();
                    $scriptInit = json_decode($row->script_init,true);
                    foreach($scriptInit as $j => $scriptValue){
                        $changeArr = ["\"","{","}"];
                        $initKeyArr = explode(":",str_replace($changeArr,"",json_encode($scriptValue)));

                        if(strpos($initValue,$initKeyArr[0]) !== false){
                            array_push($initData,str_replace('$'.$initKeyArr[0],$initKeyArr[1],$initValue));
                        }
                    }
                }

                $scriptSrc = json_decode($row->script_src,true);
                foreach($scriptSrc as $k => $src){
                    $srciprScrArr = [
                        "src" => $src,
                        "trigger" => $initData,
                    ];
                    array_push($agentAssignScriptData,$srciprScrArr);
                }

            }

            // return response()->json($agentAssignScriptData);

            $str = '';
            foreach($agentAssignScriptData as $key => $value){
                $str .= json_encode($value);
                if( next($agentAssignScriptData) == true ) $str .= ",";
            }

            $str = str_replace("'","\'",$str);
            $includeData = '$agent_assign_script_data = \''.$str.'\'';

            //agentAssignScriptData 내용을 담은 php 파일 생성
            $fileName = $groupRow->client_sid.'_script.php';

            $destinationPath = 'public/client_script/'.$groupRow->client_id;
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath,0777,true);
                chmod($destinationPath,0777);
            }
            $file = fopen($destinationPath."/".$fileName, "w");
            fwrite($file, $includeData);
            fclose($file);
        }
    }
}
