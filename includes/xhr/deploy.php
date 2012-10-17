<?php
require_once('../initialize.php');

	if($session->user()->username == 'demo'){
		echo 999;
		die;
	}
	
	$appFile = SITE_ROOT.DS.'applications'.DS.$_POST['appFile'];
	$extractLocation = $_POST['extractLocation'];
			
	$zip = new ZipArchive();
	if($zip->open($appFile)) {
		$zip->extractTo($extractLocation);
		$zip->close();
		echo 1;
	}else{
		echo 500;
	}

?>