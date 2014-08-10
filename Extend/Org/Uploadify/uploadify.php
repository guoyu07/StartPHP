<?php
$targetFolder =C('UP_PIC_PATH');
is_dir($targetFolder)||mkdir($targetFolder,0777,true);
if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetFile = rtrim($targetFolder,'/') . '/' .date('Ymd_His').'_'.mt_rand(0, 10000). $_FILES['Filedata']['name'];	
	$fileTypes = array('jpg','jpeg','gif','png');
	$fileParts = pathinfo($_FILES['Filedata']['name']);	
	if (in_array($fileParts['extension'],$fileTypes)) {
		if(move_uploaded_file($tempFile,$targetFile)){
			$targetFile = substr($targetFile, strlen(dirname($_SERVER['SCRIPT_FILENAME'])));
			echo json_encode(array('state'=>true,'path'=>$targetFile));
		}else{
			echo json_encode(array('state'=>false,'message'=>'文件上传失败'));
		}
	} else {
		echo json_encode(array('state'=>false,'message'=>'文件类型不正确'));
	}
}
?>