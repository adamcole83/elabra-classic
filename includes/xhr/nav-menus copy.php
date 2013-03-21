<?php
	require_once('../initialize.php');
	
	if( $_POST['insert'] )
	{
		$data = $_POST['insert'];
		
		if( ! is_array($id))
		{
			// Query original post
			$post = Content::find_by_id($data);
			$menuitem['object_id'] = $post->id;
			$menuitem['object'] = $post->post_type;
			$menuitem['type'] = 'Page';
			$menuitem['title'] = $post->title;
			$department = $post->department;
		}
		else
		{
			$menuitem['object_id'] = array_shift($db->fetch_array($db->query("SELECT MAX(id) FROM posts"))) + 1;
			$menuitem['object'] = 'custom';
			$menuitem['type'] = 'Custom';
			$menuitem['title'] = $data['title'];
			$menuitem['url'] = $data['url'];
			$department = $data['department'];
		}
		
		$db->query("INSERT INTO deptmeta (dept_id, meta_key, meta_value) VALUES({$department}, 'nav-menu-item', '".serialize($menuitem)."')");
		$db->query("");
		
		$data = null;
		$data['id'] = $db->insert_id();
		$data['department'] = $department;
		$data['menuitem'] = $menuitem;
		
		die(json_encode($data));
	}
	
	
/*
	$fields = array(
		'deptmeta.umeta_id' => 'id',
		'deptmeta.dept_id' => 'department',
		'deptmeta.meta_value' => 'menuitem',
		''
	);
	
	foreach($fields as $key=>$value)
		$selects[] = "$key AS $value";
	
	if(isset($_POST['single']))
	{
		$id = $_POST['single'];
		
		$sql = "SELECT deptmeta.umeta_is"
		
		$item = instantiate($db->query('SELECT '.join(', ', $selects).' FROM deptmeta WHERE umeta_id = '.$id), array_values($fields));
		
		if($item)
		{
			if(is_array($item)){
				$item = $item[0];
			}
			$item->menuitem = (object) unserialize($item->menuitem);
			
			die(json_encode($item));
		}
	}
	else if(isset($_POST['all']))
	{
		$dept = $_POST['all'];
		$items = instantiate($db->query('SELECT '.join(', ', $selects).' FROM deptmeta WHERE dept_id = '.$dept), array_values($fields));
		
		if($items)
		{
			foreach($items as $item) {
				$item->menuitem = unserialize($item->menuitem);
			}
			
			
			die(json_encode($items));
		}
	}
	
	if($_POST['update'])
	{
		$data = $_POST;
		unset($_POST, $data['update']);
		
		foreach($data as $item)
		{
			$item = (object) $item;
			$item->menuitem = serialize($item->menuitem);
			$db->query("UPDATE deptmeta SET meta_value='{$item->menuitem}' WHERE umeta_id = {$item->id}");
			
			die(json_encode(array('success' => 'Menu saved')));
		}
	}
	
	die(json_encode(array('error' => 'No menu(s) found.')));
*/
	
?>