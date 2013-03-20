<?php
	
	// initialize classes
	require_once('../initialize.php');
	
	// declare giveJSON function
	function giveJSON($arr) {
		die( json_encode($arr) );
	}
	
	if(!empty($_POST['check']) && !empty($_POST['forthis'])) {
		$result_set = $db->query("SELECT * FROM {$_POST['check']} WHERE name = '{$_POST['forthis']}'");
		$query = $db->fetch_array($result_set);
		giveJSON($query);
	}else{
		echo 'false';
	}
?>