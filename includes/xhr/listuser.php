<?php
	
	// initialize classes
	require_once('../initialize.php');
	
	// declare variables
	$userArray = array();
	$uid = $_POST['id'];
	
	// declare giveJSON function
	function giveJSON($arr) {
		die( json_encode($arr) );
	}
	
	// declare error function
	function dieError($err) {
		giveJSON( array("error"=>$err) );
	}
	
	if( !empty($uid) ) {
		if( is_numeric($uid) )
			$userArray = User::get($uid);
		else if( is_string($uid) )
			$userArray = User::find_by_username($uid);
	}else{
		$num = 0;
		foreach(User::find_all() as $user) {
			$userArray[$num]['id'] = $user->id;
			$userArray[$num]['username'] = $user->username;
			$userArray[$num]['fullname'] = $user->first_name . " " . $user->last_name;
			$userArray[$num]['department'] = Department::grab($user->department)->name;
			$num++;
		}
	}
	
	// return object
	giveJSON($userArray);
	
?>