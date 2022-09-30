<?php

$fullPath = __FILE__;
$secondPath = '\public\plugins\smarteditor2\sample\photo_uploader\file_uploader_html5.php';

$subPath = '/home/marutm/public_dev/public/assets/uploads/';
$UPLOAD_PATH .= $subPath;

define("SERVER_SUB", "https://dev.mapstrend.com/assets/uploads/");

$sFileInfo = '';
$headers = array();
 
foreach($_SERVER as $k => $v) {
	if(substr($k, 0, 9) == "HTTP_FILE") {
		$k = substr(strtolower($k), 5);
		$headers[$k] = $v;
	} 
}

$filename = rawurldecode($headers['file_name']);
$filename_ext = strtolower(array_pop(explode('.',$filename)));
$allow_file = array("jpg", "png", "bmp", "gif"); 

if(!in_array($filename_ext, $allow_file)) {
	echo "NOTALLOW_".$filename;
} else {
	$file = new stdClass;
	$file->name = time().'.'.$filename_ext;	// 파일명 변경
	$file->content = file_get_contents("php://input");

	$newPath = $UPLOAD_PATH.$file->name;

	if(file_put_contents($newPath, $file->content)) {
		$sFileInfo .= "&bNewLine=true";
		$sFileInfo .= "&sFileName=".$file->name;
		$sFileInfo .= "&sFileURL=".SERVER_SUB.$file->name;
	}

	echo $sFileInfo;
}
?>
