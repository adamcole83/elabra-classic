<?php

class DirectoryListing
{
	/*
	Include URL - If you are including this script in another file, 
	please define the URL to the Directory Listing script (relative
	from the host)
	*/
	private $includedir = false;
	
	/*
	Start Directory - To list the files contained within the current 
	directory enter '.', otherwise enter the path to the directory 
	you wish to list. The path must be relative to the current 
	directory and cannot be above the location of index.php within the 
	directory structure.
	*/	
	private $startdir = '.';
	
	/*
	Show Thumbnails? - Set to true if you wish to use the 
	scripts auto-thumbnail generation capabilities.
	This requires that GD2 is installed.
	*/
	private $showthumnails = true;
	
	/*
	Memory Limit - The image processor that creates the thumbnails
	may require more memory than defined in your PHP.INI file for 
	larger images. If a file is too large, the image processor will
	fail and not generate thumbs. If you require more memory, 
	define the amount (in megabytes) below
	*/
	private $memorylimit = false;
	
	/*
	Show Directories - Do you want to make subdirectories available?
	If not set this to false
	*/
	private $showdirs = true;
	
	/* 
	Force downloads - Do you want to force people to download the files
	rather than viewing them in their browser?
	*/
	private $forcedownloads = false;
	
	/*
	Hide Files - If you wish to hide certain files or directories 
	then enter their details here. The values entered are matched
	against the file/directory names. If any part of the name 
	matches what is entered below then it is not shown.
	*/
	private $hide = array('.htaccess', '.htpasswd');

	/* Only Display Files With Extension... - if you only wish the user
	to be able to view files with certain extensions, add those extensions
	to the following array. If the array is commented out, all file
	types will be displayed.
	*/
	# private $showtypes = array('jpg', 'png', 'gif', 'zip', 'txt');
	
	/* 
	Show index files - if an index file is found in a directory
	to you want to display that rather than the listing output 
	from this script?
	*/	
	private $displayindex = false;
	
	/*
	Allow uploads? - If enabled users will be able to upload 
	files to any viewable directory. You should really only enable
	this if the area this script is in is already password protected.
	*/
	public $allowuploads = false;
	
	/* Upload Types - If you are allowing uploads but only want
	users to be able to upload file with specific extensions,
	you can specify these extensions below. All other file
	types will be rejected. Comment out this array to allow
	all file types to be uploaded.
	*/
	/* private $uploadtypes = array(
							'zip',
							'gif',
							'doc',
							'png'
							);*/
	
	/*
	Overwrite files - If a user uploads a file with the same
	name as an existing file do you want the existing file
	to be overwritten?
	*/
	private $overwrite = false;
	
	/*
	Index files - The follow array contains all the index files
	that will be used if $displayindex (above) is set to true.
	Feel free to add, delete or alter these
	*/
	private $indexfiles = array(
							'index.html',
							'index.htm',
							'default.htm',
							'default.html',
							'index.php',
							'default.php'
							);
	
	/*
	File Icons - Each entry relates to the extension of the 
	given file, in the form <extension> => <filename>. 
	These files must be located within the img/ico directory.
	*/
	private $filetypes = array(
							'png' => 'jpg.gif',
							'jpeg' => 'jpg.gif',
							'bmp' => 'jpg.gif',
							'jpg' => 'jpg.gif', 
							'gif' => 'gif.gif',
							'zip' => 'archive.png',
							'rar' => 'archive.png',
							'exe' => 'exe.gif',
							'setup' => 'setup.gif',
							'txt' => 'text.png',
							'htm' => 'html.gif',
							'html' => 'html.gif',
							'fla' => 'fla.gif',
							'swf' => 'swf.gif',
							'xls' => 'xls.gif',
							'doc' => 'doc.gif',
							'docx' => 'doc.gif',
							'sig' => 'sig.gif',
							'fh10' => 'fh10.gif',
							'pdf' => 'pdf.gif',
							'psd' => 'psd.gif',
							'rm' => 'real.gif',
							'mpg' => 'video.gif',
							'mpeg' => 'video.gif',
							'mov' => 'video2.gif',
							'avi' => 'video.gif',
							'eps' => 'eps.gif',
							'gz' => 'archive.png',
							'asc' => 'sig.gif',
							);
	
	
	
} // end DirectoryListing

?>