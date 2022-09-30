<?php
    if( (!isset($_client_id) && !$_client_id) && (!isset($_client_sid) && !$_client_sid) ) die;

    //Agent Service Script 정보 예시
    // $agent_assign_script_data='{"src":"https://yourdomain-script.com/sample-script.js", "trigger":"barsQ(\'caid\', \'byapps\');barsQ(\'host\', \'cafe24\');"},
    //     {"src":"https://yourdomain-script.com/sample-script2.js","trigger":""}';
    @include $_SERVER['DOCUMENT_ROOT']."/client_script/".$_client_id."/".$_client_sid."_script.php";
?>

console.log('MAPS_SCRIPT_LOAD');

var MAPSAPI={
    version: '<?=$_MAPS_js_version?>',
    client_id: '<?=$_client_id?>',
    getAgentService: function(){
        return [<?=$agent_assign_script_data?>];
    }
}
