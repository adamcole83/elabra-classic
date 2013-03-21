<?php
	
	require_once('../initialize.php');
	
	switch( $_POST['type'] )
	{
		case 'department' :
			switch( $_POST['action'] )
			{
				case 'add':
					$db->insert("cms.deptmeta", array( "dept_id" => $_POST['id'], "meta_key" => $_POST['key'], "meta_value" => $_POST['value'] ));
					break;
				case 'delete':
					$db->delete('cms.deptmeta', array( "umeta_id" => $_POST['umeta_id'] ));
					break;
			}
			break;
		
		case 'menu-item' :
			if( preg_match('/d[0-9]+/i', $_POST['umeta_id']) )
			{
				$db->delete('cms.deptmeta', array( "umeta_id" => str_ireplace('d', '' ,$_POST['umeta_id']) ));
			}
			else
			{
				$db->update('cms.posts', array( 'id' => $_POST['umeta_id'] ), array( 'parent_id' => '0' ));
			}
			break;
			
	}
	
	switch( $_POST['action'] )
	{
		case 'check-slug':
			$post = Content::find_by_sql("SELECT * FROM posts WHERE url = '{$_POST['slug']}' AND department = '{$_SESSION['department']}' LIMIT 1");
			if($post)
			{
				die( increment_string($_POST['slug'], '-') );
			}
			else
			{
				die( $_POST['slug'] );
			}
			break;
	}
	
?>