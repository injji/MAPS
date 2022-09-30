<?php
    // CORS 및 캐쉬 설정
    header('Access-Control-Allow-Origin: *');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + (60 * 10)) . ' GMT'); // 유효기한
    header("Cache-Control: max-age=".(60 * 10)); // 캐시 최대 길이 (초 단위)
    header("Pragma: public");


    // 상용배포 MAPS JS 버전
    $_MAPS_js_version = '1.0.0';
    if( (isset($_REQUEST['id']) && $_REQUEST['id']) && (isset($_REQUEST['sid']) && $_REQUEST['sid']) ){
        // client_id
        $_client_id = $_REQUEST['id'];
        $_client_sid = $_REQUEST['sid'];

        // Agent Service
        @include "MAPS_agent_service.php";

        // MAPS JS 코드
        @include "MAPS_script_".$_MAPS_js_version.".php";
    }

