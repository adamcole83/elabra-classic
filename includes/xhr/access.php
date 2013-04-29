<?php
require_once('../initialize.php');

if($_POST['action'])
{
	echo (Group::can($_POST['action'])) ? 200 : 500;
}
elseif($_POST['session'])
{
	$array = explode(":", $_POST['session']);
	$key = $array[0];
	$value = $array[1];

	if(!empty($value)) {
		unset($_SESSION[$key]);
		$_SESSION[$key] = $value;
		echo 200;
	}else{
		echo $_SESSION[$key];	
	}
}
elseif($_POST['timeout'])
{
	if(isset($_SESSION['timeout'])){
		$session_life = time() - $_SESSION['timeout'];
		if($session_life > $session->inactive){
			$session->message("No activity within ". $session->inactive / 60 ." minutes, please log in again.");
			echo 500;
		}else{
			echo 200;
			$_SESSION['timeout'] = time();
		}
	}
}

?>