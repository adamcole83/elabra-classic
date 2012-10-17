<?php
	
	if($session->user()->username == 'demo'){
		echo 999;
		die;
	}
	
	// increase script timeout value
	ini_set("max_execution_time", 300);
	
	// set variables
	$root						= '/var/www/html/medicine.missouri.edu';
	$application_name			= $_POST['name'];
	$application_version		= $_POST['version'];
	$application_description	= $_POST['description'];
	$application_author			= $_POST['author'];
	$application_author_email	= $_POST['email'];
	$application_screenshot		= $_POST['screenshot'];
	$application_archival_dir	= $_POST['directory'];
	$application_directory		= str_replace(' ','',strtolower($application_name));
	$application_archival_file	= $application_directory.".".$application_version.".zip";
	$module_directory			= $root.'/admin/applications/'.$application_directory;
	$timestamp 					= date('D, F j, Y h:i:s A');
	
	// create application directory
	if(!file_exists($module_directory))
	{
		mkdir($module_directory, 0775) or die("Directory could not be created [$module_directory]");
	}
	
	// create change log
	$change_log = $module_directory.'/change.log';
	if(!file_exists($change_log))
	{
		$fh = fopen($change_log, 'w') or die("Change log could not be created [$change_log]");
		fwrite($fh, "[$timestamp] Initial package");
		fclose($fh);
		chmod($change_log, 0664);
	}else{
		die("Change log exists [$change_log]");
	}
	
	// create info.yaml
	$yaml = $module_directory.'/info.yaml';
	$fh = fopen($yaml, 'w') or die("YAML file could not be created [$yaml]");
	$data  = "Title: $application_name\r\n";
	$data .= "Description: $application_description\r\n";
	$data .= "Version: $application_version\r\n";
	$data .= "Author: $application_author\r\n";
	$data .= "Email: $application_author_email\r\n";
	$data .= "Screenshot: $application_screenshot\r\n";
	$data .= "File: $application_archival_file";
	fwrite($fh, $data) or die("Could not write to YAML");
	fclose($fh);
	chmod($yaml, 0664);
		
	// move screenshot into directory
	$temp_screenshot = $root.'/admin/includes/tmp/'.$application_screenshot;
	$dest_screenshot = $root.'/admin/applications/'.$application_directory.'/'.$application_screenshot;
	if(file_exists($temp_screenshot))
	{
		rename($temp_screenshot, $dest_screenshot) or die("Screenshot could not be moved [$temp_screenshot][$dest_screenshot]");
		chmod($des_screenshot, 0664);
	}else{
		die("Image could not be found [$temp_screenshot]");
	}
	
	// archive files
	$zip_file = $module_directory.'/'.$application_archival_file;
	$zip = new ZipArchive();
	if($zip->open($zip_file, ZIPARCHIVE::OVERWRITE) !== true)
	{
		die("Could not open archive [$zip_file]");
	}
	$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($application_archival_dir)) or die("Iterator could not be created");
	foreach($iterator as $key => $value)
	{
		$regex = addcslashes($application_archival_dir, '/');
		$file = preg_replace("/$regex/i", '', $key);
		error_log($file, 0);
		$zip->addFile(realpath($key), $file.'/forum/') or die("File could not be added [$key]");
	}
	$zip->close();
	chmod($zip_file, 0765);
	echo 1;
	
	
?>
