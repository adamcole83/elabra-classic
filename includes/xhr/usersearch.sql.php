<?php
	
	// initialize classes
	require_once('../initialize.php');
	
	// declare variables
	$usr = trim($_POST['username']);
	$pwd = trim($_POST['password']);
	
	// declare giveJSON function
	function giveJSON($arr) {
		die( json_encode($arr) );
	}
	
	// declare error function
	function dieError($err) {
		giveJSON( array("error"=>$err) );
	}
	
	// search for user in ldap
	if( empty($usr) && empty($pwd) )
		dieError('User not provided.');
	
	// authenticate user
	$found_user = User::authenticate($usr,$pwd);
	
	if( $found_user )
	{
		$session->login($found_user);
	}
	else
	{
		$found_user = null;
	}
	
	// return object
	giveJSON($found_user);
	
?>