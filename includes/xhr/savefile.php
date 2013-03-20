<?php
	require_once('../initialize.php');
	
	$content = new Content();
	
	$allowed_mime_types = get_allowed_mime_types();
	$timestamp = time();
	$filename = explode('.',$_POST['name']);
	$filetype = array_pop($filename);
	
	$valid_chars_regex = '.A-Z0-9_!@#$%^&()+={}\[\]\',~`-';	
	$file_name = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "-", basename($_POST['name']));	
	
	$guid = "http://medicine.missouri.edu".$_POST['directory'].$file_name;
	
	$exists = $content->find_by_sql("SELECT id FROM posts WHERE post_type = 'attachment' AND guid='{$guid}' AND department = ".$_SESSION['department']);
	
	error_log($exits,0);
	
	if($exists)
	{
		return;
	}
	
	error_log($guid,0);
	
	$datatype = ext2type(mime2ext($allowed_mime_types[$filetype]));
	$dimensions = ($datatype=='image') ? getimagesize(PUBLIC_ROOT.$_POST['directory'].$file_name) : null;
	$banner = ($dimensions[0] == 550 && ($dimensions[1] == 200 || $dimensions[1] == 196 )) ? true : false;
	
	$post = new Content();
	$post->title = ($banner) ? 'RotatingBanner' : $filename[0];
	$post->body = ($banner) ? '' : $filename[0];
	$post->description = ($banner) ? '' : $filename[0];
	$post->department = $_SESSION['department'];
	$post->post_type = "attachment";
	$post->url = $_POST['directory'].$file_name;
	$post->guid = $guid;
	$post->post_created = $timestamp;
	$post->updated = $timestamp;
	$post->post_mime_type = $allowed_mime_types[$filetype];
	$post->updatedBy = $_SESSION['user_id'];
	echo $post->save();
?>