<?php
	
	require_once('../initialize.php');
	
	$status = $_POST['status'];
	$user_id = $_SESSION['user_id'];
	
	if( $status == '1' )
	{
		$db->update('cms.users', array('id' => $user_id), array('online'=> 1));
	}
	elseif( $status == '0' )
	{
		$db->update('cms.users', array('id' => $user_id), array('online'=> 0));
	}
		
?>