<?php
		
	if( ! isset($_REQUEST['action']))
	{
		respond(array('error'=>'No action given'));
	}
	
	require_once('../initialize.php');
	
	if( ! $session->is_logged_in())
	{
		respond(array('error'=>'User not logged in'));
	}
	
	if(isset($_REQUEST['action']))
	{
		
		switch($action = $_REQUEST['action']) :
			
			case 'add-page-menu':
				
				if( ! isset($_POST['object_id']))
				{
					respond(array('error'=>'Object ID not given'));
				}
				
				$post = Content::find_by_id($_POST['object_id']);
				$order = array_shift($db->fetch_array($db->query("SELECT (MAX(menu_order) + 1) FROM posts WHERE department = {$post->department} && post_type = 'nav-menu-item'")));
				$menuitem = array(
					'object_id'	=> $post->id,
					'object'	=> $post->post_type,
					'type'		=> 'Page',
					'title'		=> $post->title,
					'parent_id'	=> $post->parent_id,
					'position'	=> $order ? $order : 0,
					'department'=> $post->department,
					'url'		=> $post->guid
				);
				
				$db->query("INSERT INTO posts (title, menu_order, parent_id, post_type, updatedBy, department) 
							VALUES('{$db->escape_value($post->title)}', {$menuitem['position']}, {$post->id}, 'nav-menu-item', {$_SESSION['user_id']}, {$post->department})");
				$post_ID = $db->insert_id();
				$db->query("INSERT INTO postmeta (post_id, meta_key, meta_value) 
							VALUES({$post_ID}, 'nav-menu-item', '".$db->escape_value(serialize($menuitem))."')");
				
				respond(array(
					'id'			=> $db->insert_id(),
					'position'		=> $menuitem['position'],
					'menuitem'		=> $menuitem,
					'department'	=> $post->department,
					'title'			=> $post->title,
					'guid'			=> $post->guid
				));
				
				break;
			
			case 'add-custom-menu':
				
				if( ! isset($_POST['department']))
				{
					respond(array('error'=>'Department ID not given'));
				}
				$order = array_shift($db->fetch_array($db->query("SELECT (MAX(menu_order) + 1) FROM posts WHERE department = {$_POST['department']} && post_type = 'nav-menu-item'")));
				$menuitem = array(
					'object_id'	=> '',
					'object'	=> 'custom',
					'type'		=> 'Custom',
					'title'		=> $_POST['title'],
					'url'		=> $_POST['url'],
					'position'	=> $order ? $order : 0
				);
				
				$db->query("INSERT INTO posts (title, guid, menu_order, post_type, updatedBy, department) 
							VALUES('{$db->escape_value($_POST['title'])}', '{$db->escape_value($_POST['url'])}', {$menuitem['position']}, 'nav-menu-item', {$_SESSION['user_id']}, {$_POST['department']})");
							
				$post_ID = $db->insert_id();
				$menuitem['object_id'] = $post_ID;
				
				$db->query("UPDATE posts SET parent_id = {$post_ID} WHERE id = {$post_ID}");
				$db->query("INSERT INTO postmeta (post_id, meta_key, meta_value) 
							VALUES({$post_ID}, 'nav-menu-item', '".$db->escape_value(serialize($menuitem))."')");
				
				respond(array(
					'id'			=> $db->insert_id(),
					'guid'			=> $_POST['url'],
					'title'			=> $_POST['title'],
					'position'		=> $menuitem['position'],
					'menuitem'		=> $menuitem,
					'department'	=> $_POST['department']
				));
				
				break;
			
			case 'get-all-menus':
				
				if( ! isset($_POST['department']))
				{
					respond(array('error'=>'Department ID not given'));
				}
				
				$selects = array(
					'id'		=> 'postmeta.meta_id AS id',
					'guid'		=> 'b.guid',
					'title'		=> 'b.title',
					'position'	=> 'a.menu_order AS position',
					'menuitem'	=> 'postmeta.meta_value AS menuitem'
				);
				
				$sql = "SELECT ".join(', ', array_values($selects))."
						FROM postmeta
							JOIN posts a
								ON a.id = postmeta.post_id
							JOIN posts b
								ON b.id = a.parent_id
							WHERE a.post_type = 'nav-menu-item' && a.department = {$_POST['department']} 
							ORDER BY a.menu_order";
				
				$items = instantiate($db->query($sql), array_keys($selects));
				
				if($items)
				{
					foreach($items as $item)
					{
						$item->menuitem = unserialize($item->menuitem);
						$item->menuitem['url'] = $item->guid;
					}
					
					respond($items);
				}
				
				respond(array('error'=>'No menu items'));
				
				break;
			
			case 'update-menus':
				
				foreach($_POST as $menu)
				{
					if(is_array($menu))
					{
						$type = $menu['menuitem']['object'];
						$object_id = $menu['menuitem']['object_id'];
						
						$menu['menuitem'] = serialize($menu['menuitem']);
						
						$set = "posts.menu_order = {$menu['position']}, postmeta.meta_value = '{$db->escape_value($menu['menuitem'])}'";
						
						if($type == 'custom')
						{
							$set .= ", posts.title = '{$db->escape_value($menu['title'])}', posts.guid = '{$menu['guid']}'";
						}
						
						$sql = "UPDATE posts, postmeta SET {$set} WHERE posts.id = postmeta.post_id AND postmeta.meta_id = {$menu['id']}";
						
						$db->query($sql);
					}
				}
				
				respond(array('success'=>'Menus were updated successfully'));
				
				break;
			
			case 'remove-menu':
				
				$id = $_POST['id'];
				
				$db->query("DELETE posts, postmeta FROM posts, postmeta WHERE posts.id = postmeta.post_id AND postmeta.meta_id = {$id}");
				respond(array('success' => 'Menu was successfully deleted.'));
				
				break;
			
		endswitch;
		
	}
	
	function respond($args=array())
	{
		die(json_encode($args));
	}
	
?>