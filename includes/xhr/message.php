<?php
	require_once('../initialize.php');
	
	$session->message($_POST['message']);
	echo '200';	
?>