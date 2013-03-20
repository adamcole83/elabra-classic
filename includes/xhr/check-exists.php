<?php

require_once '../initialize.php';

$content = new Content();

// Define a destination
$targetFolder = $_POST['uploadpath']; // Absolute to the root and should match the upload folder in the uploader script
$url = str_replace( PUBLIC_ROOT, '', $targetFolder ) . $_POST['filename'];

// Check for existing database content
$exists = $content->find_by_url( $url );

if (file_exists( $targetFolder . '/' . $_POST['filename'] ) || $exists)
{
	echo 1;
}
else
{
	echo 0;
}