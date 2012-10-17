<?php

	require_once('../initialize.php');
	
	$table = 'cms.usermeta';
		
	switch( $_POST['action'] )
	{
		case 'add':
			$db->insert($table, array('user_id' => $_POST['user_id'], 'meta_key' => $_POST['meta_key'], 'meta_value' => $_POST['meta_value']));
			break;
		case 'edit':
			$db->update($table, array('user_id' => $_POST['user_id'], 'meta_key' => $_POST['meta_key']), array('meta_value'=>$_POST['meta_value']));
			break;
		case 'delete':
			$db->delete($table, array('user_id' => $_POST['user_id'], 'meta_key' => $_POST['meta_key'], 'meta_value' => $_POST['meta_value']));
			break;
		default:
			die( json_encode($db->fetch_array($db->select($table, '*', array('user_id'=>$_POST['user_id'], $_POST['meta_key'])))) );
			break;
	}
	
	echo ($db->affected_rows() == 1) ? 200 : 404;

?>