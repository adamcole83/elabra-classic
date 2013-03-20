<?php

require_once '../initialize.php';

$posts = new Content();

// Set the uplaod directory
$uploadDir = $_POST['uploadpath'];

// Set the allowed file extensions
$allowed_mime_types = get_allowed_mime_types();
$fileTypes = array_keys( $allowed_mime_types );

$verifyToken = md5('unique_salt' . $_POST['timestamp']);

if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
	$tempFile   = $_FILES['Filedata']['tmp_name'];
	$uploadDir  = $_SERVER['DOCUMENT_ROOT'] . $uploadDir;
	$targetFile = $uploadDir . $_FILES['Filedata']['name'];

	// Validate the filetype
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	if (in_array( strtolower( $fileParts['extension'] ), $fileTypes )) {

		$d = (ext2type( $fileParts['extension'] ) == 'image') ? @getimagesize( $tempFile ) : null;
		if( ! Group::can( 'manage_banners', $_POST['user_id'] ) && ($d[0] == 550 && ($d[1] == 200 || $d[1] == 196 )))
		{
			echo "You are not authorized to manage banners.";
			die();
		}

		// Let's check if the file exists, if not we will
		// submit a new post to the db
		if ( ! $post = $posts->find_by_url( str_replace(PUBLIC_ROOT, '', $targetFile) ))
		{
			$filetype = array_pop(explode('.', $_FILES['Filedata']['name']));
			$datatype = ext2type( mime2ext( $allowed_mime_types[$filetype] ) );
			$dimensions = ($datatype=='image') ? getimagesize( $targetFile ) : null;
			$banner = ($dimensions[0] == 550 && ($dimensions[1] == 200 || $dimensions[1] == 196 )) ? true : false;
			$title = explode('.', $_FILES['Filedata']['name']);

			$post = new Content();
			$post->title = ($banner) ? 'RotatingBanner' : $title[0];
			//$post->body = ($banner) ? '' : $title[0];
			//$post->description = ($banner) ? '' : $title[0];
			$post->department = $_SESSION['department'];
			$post->post_type = "attachment";
			$post->url = str_replace(PUBLIC_ROOT, '', $targetFile);
			$post->guid = DOMAIN.str_replace(PUBLIC_ROOT, '', $targetFile);
			$post->post_created = $_POST['timestamp'];
			$post->updated = $_POST['timestamp'];
			$post->post_mime_type = $allowed_mime_types[$filetype];
			$post->updatedBy = $_POST['user_id'];
			$post->save();
		}

		//error_log($tempFile);
		error_log(dirname($targetFile));

		// Set permissions for upload
		chmod(dirname($targetFile), 0777);

		// Save the file
		move_uploaded_file( $tempFile, $targetFile );

		// Restore permissions for security
		chmod(dirname($targetFile), 0755);

		if ( ! file_exists($targetFile))
		{
			error_log(basename($targetFile)." doesn't exist.");
			echo 'File wasn\'t fully uploaded, please try again';
			die();
		}

		echo 1;

	} else {

		// The file type wasn't allowed
		echo 'Invalid file type.';

	}
}