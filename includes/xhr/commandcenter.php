<?
	require_once('../initialize.php');
	
	// declare department function
	function department($id) {
		global $db;
		$department = Department::find_by_id($id);
		
		$content = new Content();
		$content->department = $department->id;
		
		$path = PUBLIC_ROOT.DS.$department->subdir;
		$ds = getDirectorySize($path);
		$array['id'] 	= (string) $id;
		$array['dir'] 	= (string) $department->subdir;
		$array['code'] 	= (string) $department->code;
		$array['title'] = (string) $department->name;
		$array['size'] 	= (string) sizeFormat($ds['size']);
		$array['files'] = (string) $ds['count'];
		$array['pages'] = $content->count('post');
		$array['draft'] = $content->count('post',array('status'=>'draft'));
		$array['news'] 	= $content->count('article');
		$array['cal']	= (string) 0;
		return $array;
	}
	
	// declare giveJSON function
	function giveJSON($arr) {
		die( json_encode($arr) );
	}
	
	// declare error function
	function dieError($err) {
		giveJSON( array("error"=>$err) );
	}

	foreach(Department::listAvailable() as $list_dept) {
		$dept[$list_dept->id] = department($list_dept->id);
	}
	
	giveJSON($dept);
?>
