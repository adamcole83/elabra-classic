<?php
require_once('../initialize.php');


$aForm = explode("-", $_POST['form']);
$id = (int) $aForm[2];

//$form = $_POST['form'];
//$do = $_POST['action'];
//$id = $_POST['id'];

if($aForm[0]) {
	
	switch($aForm[0]) {
		case "page":
			$code = ProcessForm_Page($aForm[1], $id, $aForm[3]);
			break;
		case "department":
			$code = ProcessForm_Department($aForm[1], $id);
			break;
		case "user":
			$code = ProcessForm_User($aForm[1], $id);
			break;
		case "group":
			$code = ProcessForm_Group($aForm[1], $id);
			break;
		case "permission":
			$code = ProcessForm_Permission($aForm[1], $id);
			break;
		case "register":
			$code = ProcessForm_Register();
			break;
		case "login":
			$code = ProcessForm_Login();
			break;
		case "media":
			$code = ProcessForm_Media($aForm[1], $id);
			break;
		case "article":
			$code = ProcessForm_News($aForm[1], $id);
			break;
	}
	
	echo ($code) ? $code : 300;
	
}else{
	echo 100;
}

function ProcessForm_Page($do, $id=null)
{
	global $session;
	$page = new Content();
	
	if($id)
		$page->id = $id;
	
	if($do == 'save')
	{		
		unset($_POST['form']);
		
		foreach($_POST as $property => $value) {
			$page->$property = $value;
		}
		
		$page->updated = time();
		
		$return = $page->save();
		if($return)
		{
			if($session->user()->group_id != 9)
			{
				$mail = new EmailNotification();
				$mail->subject = 'SOM CMS Page Update';
				$mail->to = 'medweb@health.missouri.edu';
				$mail->message = 'The "'.$_POST['title'].'" page was updated by '.$session->user()->username.' for the Department of '.Department::grab($_SESSION['department'])->name.' on '.date('F j Y').' at '.date('H:i:s').'.';
				
				$mail->send();
			}
			
			return 'id'.$return;
		}
		else
		{
			return 500;
		}
	}
	elseif($do == 'delete')
	{
		$quickdata = $page->get($id);
		$page->url = $quickdata->url;
		$page->department = $quickdata->department;
		return ($page->delete()) ? 200 : 500;
	}
	elseif($do == 'publish')
	{
		if( ProcessForm_Page('save',$id) != 500 ) {
			return ($page->publish()) ? 200 : 500;
		}else{		
			return 500;
		}
	}
	else
	{
		return 900;
	}
}

function ProcessForm_News($do, $id=null)
{
	global $session;
	$post = new Content();
	
	if($id)
		$post->id = $id;
	
	if($do == 'save')
	{
		$post->title = $_POST['title'];
		$post->status = 'draft';
		
		if(preg_match('/now/i',$_POST['post_created'])){
			$post->post_created = time();
		}
		else{
			$post->post_created = strtotime($_POST['post_created']);
		}
		
		$post->department = $_POST['department'];
		$post->body = $_POST['body'];
		$post->url = $_POST['url'];
		$post->updatedBy = $session->user()->id;
		$post->updated = time();
		$post->post_type = 'article';
		
		return ($post->save()) ? 200: 500;
	}
	elseif($do === 'delete')
	{
		return ($post->delete()) ? 200 : 500;
	}
	elseif($do === 'publish')
	{
		if( ProcessForm_News('save',$id) == 200 ) {
			return ($post->publish())? 200 : 500;
		}else{
			return 500;
		}
	}
}

function ProcessForm_Department($do, $id=null)
{
	$dept = new Department();
	
	if($id)
		$dept->id = $id;
	
	if($do == 'save')
	{
/*
		$dept->name = $_POST['name'];
		$dept->code = $_POST['code'];
		$dept->subdir = $_POST['subdir'];
		$dept->index_id = $_POST['index_id'];
		$dept->dev_mode = $_POST['dev_mode'];
*/
		// meta data
		
		foreach($_POST as $property => $value) {
			$dept->$property = $value;
		}
		
		return ($dept->save()) ? 200: 500;
	}
	elseif($do == 'relocate')
	{
		error_log('Relocating...', 0);
		return $dept->relocate($_POST['relocateFrom'], $_POST['relocateTo']) ? 200 : 500;
	}
	elseif($do == 'delete')
	{
		return ($dept->delete()) ? 200 : 500;
	}
}

function ProcessForm_User($do, $id=null)
{
	$user = new User();
	
	if($id)
		$user->id = (int) $id;
	
	if($do == 'save')
	{
		if(!isset($id) && $user->username_exists($_POST['username']))
		{
			return 505;
		}
		
		foreach($_POST as $property => $value) {
			$user->$property = $value;
		}
		
		if( $user->save() ) {
			if($_POST['sendto'] == '1') {
				$mail = new EmailNotification();
				$mail->to = $_POST['email'];
				$mail->sitename = base64_encode(Department::grab($_POST['department'])->name);
				$mail->redirect = base64_encode('http://medicine.missouri.edu/'.Department::grab($_POST['department'])->subdir.'/');
				$mail->send_new_password($_POST['username'], $_POST['password']);
			}
			return 200;
		}else{
			return 500;
		}
	}
	elseif($do == 'delete')
	{
		return ($user->delete()) ? 200 : 500;
	}
}

function ProcessForm_Login()
{
	global $session;
	// trim & encode
	$usr = trim($_POST['username']);
	$pwd = trim($_POST['password']);
	$dom = trim($_POST['domain']);
		
	if( $_POST['ldap'] === 'yes' ) {
		if( ldapAuth($usr,$pwd) == true ) {
			$found_user = User::dbAuthenticate($usr);
		}else{
			return 510;
		}
	}else{
		$found_user = User::authenticate($usr,$pwd);
		if( $found_user == false )
			return 520;
	}
	
	if( $found_user )
	{
		switch( $found_user->active )
		{
			case '0':
				return 400;
				break;
			case '1':
				$session->login($found_user);
				return 200;
				break;
			case '2':
				return 420;
				break;
		}
	}
	else
	{
		return 501;
	}
}

function ProcessForm_Register()
{
	if( ProcessForm_User('save') === 200 )
	{
		foreach($_POST as $key=>$val) $uData[$key] = $val;
		$uData['group_id'] = Group::get_name($uData['group_id'])->name;
		//$uData['dept'] = Department::grab($uData['dept'])->name;
		
		$mail = new EmailNotification();
		$mail->to = IT_EMAIL;
		if($mail->send_registration_notification($uData))
			return 202;
		else
			return 500;
	}
}

function ProcessForm_Group($do, $id=null)
{
	$group = new Group();
	
	if($do == 'save')
	{
		$permissions = explode(":", $_POST['permissions']);
		$group->name = $_POST['name'];
		if($_POST['id']) {
			return ($group->update($_POST['name'], $permissions)) ? 200 : 500;
		}else{
			return ($group->add($_POST['name'], $permissions)) ? 200 : 500;
		}
	}
	elseif($do == 'delete')
	{
		return (Group::delete($id)) ? 200 : 500;
	}
	elseif($do == 'addpermission')
	{
		return (Group::add_group_permission($id, $_POST['permission'])) ? 200 : 500;
	}
	elseif($do == 'deletepermission')
	{
		return (Group::delete_group_permission($id, $_POST['permission'])) ? 200 : 500;
	}
	elseif($do == 'name')
	{
		return ($group->update_name($id, $_POST['name'])) ? 200 : 500;
	}
}

function ProcessForm_Permission($do, $id=null)
{
	if($do == 'save')
	{
		$groups = explode(":", $_POST['groups']);
		$id = decamelize($_POST['name']);
		return (Group::add_permission($id, $groups)) ? 200 : 500;
	}
	elseif($do == 'delete')
	{
		return (Group::remove_permission($id)) ? 200 : 500;
	}
}

function ProcessForm_Media($do, $id=null)
{
	$post = new Content();
	
	if($id)
		$post->id = $id;

	if($do == 'save')
	{				
		foreach($_POST as $property => $value) {
			$post->$property = $value;
		}
		
		return ($post->save()) ? 200 : 500;
	}
	elseif($do == 'delete')
	{
		$obj = new Content();
		$obj->id = $id;
		unlink(PUBLIC_ROOT.DS.$obj->get('attachment')->url);
		return ($post->delete()) ? 200 : 500;
	}
}


?>