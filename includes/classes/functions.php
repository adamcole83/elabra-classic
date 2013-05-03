<?php
/**
 * Remove zeros from dates
 *
 * @param (string) marked_string
 * @return (string) cleaned_string
 *
 */
function strip_zeros_from_date( $marked_string="" ) {
	$no_zeros = str_replace('*0', '', $marked_string);
	$cleaned_string = str_replace('*', '', $no_zeros);
	return $cleaned_string;
}

function get($part,$return=false)
{
	global $site;
	if($return==true) {
		return $site->$part;
	}else{
		echo $site->$part;
	}
}

function image($class, $name)
{
	return '<img class="'.$class.'" alt="'.$name.'" src="images/'.$name.'" />';
}

function get_parent($parent)
{
	if( $parent == 0) {
		echo '(no parent)';
	}else{
		echo Content::find_by_id($parent)->title;
	}
}
function showRequired($post='', $title='', $submit='submit')
{
	if(isset($_POST[$submit]) && empty($post)) {
		$title = !empty($title) ? $title.' is ' : null;
		return '<span class="red">'.ucfirst($title.'required').'</span>';
	}
}
function sc_branch()
{
	return "git branch: " .exec("git rev-parse --abbrev-ref HEAD");
}

function increment_string($str, $separator = '_', $first = 1)
{
	preg_match('/(.+)'.$separator.'([0-9]+)$/', $str, $match);

	return isset($match[2]) ? $match[1].$separator.($match[2] + 1) : $str.$separator.$first;
}

function _n($one,$other)
{
	return (!empty($one)) ? $one : $other;
}

function _l($uri)
{
	echo HTTP."://".$uri;
}

function aasort (&$array, $key)
{
	$sorter=array();
	$ret=array();
	reset($array);
	foreach ($array as $ii => $va) {
		$sorter[$ii]= $va[$key];
	}
	asort($sorter);
	foreach ($sorter as $ii => $va) {
		$ret[]= (object) $array[$ii];
	}
	return $ret;
}

function resize_image($width,$height,$max_width=480,$max_height=320)
{
	$dimensions = array();
	
	// proportionally resize image to max sizes
	$x_ratio = $max_width / $width;
	$y_ratio = $max_height / $height;
	
	if( ($width <= $max_width) && ($height <= $max_height) )
	{
		$size[0] = $width;
		$size[1] = $height;
		$size[2] = "1";
	}
	elseif( ($x_ratio * $height) < $max_height )
	{
		$size[0] = ceil($x_ratio * $height);
		$size[1] = $max_width;
		$size[2] = "2";
	}
	else
	{
		$size[0] = ceil($y_ratio * $width);
		$size[1] = $max_height;
		$size[2] = "3";
	}
	return $size;
}

function instantiate($result_set=null, $fields=array(), $cast='object')
{
	global $database;
	
	if(empty($fields)) {
		return false;
	}
	if($database->num_rows($result_set) == 0) {
		return false;
	}
	
	$object_array = array();
	foreach($fields as $field) {
		$attributes[$field] = $field;
	}
    while ($row = $database->fetch_array($result_set)) {
    	$object = array();
    	foreach($row as $attribute=>$value) {
    		if(array_key_exists($attribute, $attributes)) {
    			$object[$attribute] = $value;
    		}
    	}
    	if($cast == 'object') {
    		$object_array[] = (object) $object;
    	}else{
    		$object_array[] = $object;
    	}
    }
    
    return $object_array;
}


/**
 * Redirects to the location given
 *
 * @param (string) location
 *
 */
function redirect_to( $location = NULL ) {
	if ($location != NULL) {
		header("Location: {$location}");
		exit;
	}
}

function check_session() {
	global $session;
	
	if(!$session->is_logged_in()){ redirect_to('login.php'); }
	
	check_environment();
	is_restricted();
}

function is_restricted() {
	global $session;
	if(Group::can('no_access')){
		$session->message('You currently have no access to this system.');
		$session->logout();
		redirect_to('login.php');		
	}
}

function check_environment()
{
	if(defined('ENVIRONMENT'))
	{
		if(ENVIRONMENT == 'maintenance')
		{
			if( ! Group::can('access_offline'))
			{
				redirect_to('maintenance.php');
			}
		}
	}
}

function get_page_info($part) {
	// set action
	$action = isset($_GET['action']) ? $_GET['action'] : 'select';
	// set current view
	$gCurrentView = str_replace('.php','',basename($_SERVER['PHP_SELF']));
	if( $action )
		$gCurrentView .= '.'.$action;
	if( isset($_GET['id']) )
		$gCurrentView .= '.'.$_GET['id'];
	
	switch($part) {
		case 'action':
			return $action;
			break;
		case 'viewarray':
			return explode('.',$gCurrentView);
			break;
		case 'view':
			return $gCurrentView;
			break;
	}
}

function ldapAuth($usr,$pwd)
{
	$ds = ldap_connect("ldap.missouri.edu",3268);
	$ldapbind = false;
	if( ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3)
		&& ldap_start_tls($ds)
		&& !empty($usr)
		&& !empty($pwd))
	{
		$ldapbind = ((@ldap_bind($ds, $usr.'@umh.edu', $pwd)
				|| $ldapbind = @ldap_bind($ds, $usr.'@col.missouri.edu', $pwd)
				|| $ldapbind = @ldap_bind($ds, $usr.'@umsystem.umsystem.edu', $pwd)
				|| $ldapbind = @ldap_bind($ds, $usr.'@tig.mizzou.edu', $pwd))
			&& ($usr != "" && $pwd != ""));
	}
	ldap_close($ds);
	return $ldapbind;
}

function listfiles($leadon='')
{
	$files = array();
	$dirs = array();
	if ($handle = opendir($leadon)) {
		while (false !== ($file = readdir($handle))) {
			if($file == "." || $file == "..")
				continue;
			
			if(@filetype($leadon.$file) == "dir") {
				$n++;
				$applications[$file]['id'] = rand(0, 99).$n;
				$applications[$file]['directory'] = $file."/";
				
				if($aHandle = opendir($leadon.$file)) {
					while (false !== ($aFile = readdir($aHandle))) {
						if($aFile == "." || $aFile == "..")
							continue;
						
						if(preg_match('/yaml/i', $aFile)) {
							$applications[$file]['yaml'] = $aFile;
							$yaml = $leadon.$file.DS.$aFile;
							$lines = file($yaml);
							for ($i=0;$i<count($lines);$i++) {
								$option = explode(": ", $lines[$i]);
								$oName = strtolower($option[0]);
								$applications[$file][$oName] = preg_replace("/\r\n/", "", $option[1]);
							}
							$filename = $leadon.$file."/".$applications[$file]['file'];
							$applications[$file]['updated'] = date ('D M j, Y g:i a', filemtime($filename));
						}
					}
					closedir($aHandle);
				}
			}
		}
		closedir($handle);
	}
	
	return $applications;
}

/**
 * Outputs the message given formed in paragraph tags
 *
 * @param (string) message
 * @return (string) message or null
 *
 */
function output_message($message="") {
	if (!empty($message)) {
		return "<p class=\"message\">{$message}</p>";
	} else {
		return "";
	}
}

function parse_php($string)
{
	$pattern = '/\[php\](.*)\[\/php\]/msi';
	preg_match($pattern, $string, $matches);
	$code = preg_replace($pattern, '$1', $matches[0]);
	$string = preg_replace($pattern, $code, $string);

	return $code;
}

function write_to_file($filname, $content)
{
	$handle = fopen($filename, 'w');
	fwrite($handle, $content);
	fclose($handle);
}

function output_page()
{
	$url = basename($_SERVER['REQUEST_URI']);
	$url = " - ".ucfirst(preg_replace("/\.php/i", "", $url));
	if(!preg_match('/index/i', $url)) {
		echo $url;
	}
}

function systemError($message="") {
	if (!empty($message)) {
		$div = "<div id=\"systemerror\"><h1>System Error</h1><p>{$message}</p><a href=\"\">Close</a></div>";
		echo $div;
	} else {
		return "";
	}
}

/**
 * Checks if class instantiated is available. Checks Core location and Lib location
 *
 * @param (DataType) class_name
 *
 */
function __autoload($class_name) {
	$class_name = strtolower($class_name);
	$core_path = CORE_PATH.DS."{$class_name}.php";
	$lib_path = LIB_PATH.DS."{$class_name}.php";
	if(file_exists($core_path)) {
		require_once($core_path);
	} else {
		if(file_exists($lib_path)){
			require_once($lib_path);
		}else{
			die("The file {$class_name}.php could not be found.");
		}
	}
}

/**
 * Includes template given
 *
 * @param (DataType) template
 * @include template
 *
 */
function include_global_template($template="") {
	require(PUBLIC_ROOT.DS.'includes'.DS.'layouts'.DS.$template);
}

function global_head() {
	include_global_template('head.base.php');
}

function global_analytics() {
	include_global_template('script.ga.php');
}

function global_masthead() {
	include_global_template('div.masthead.php');
}

function global_footer() {
	include_global_template('div.footer.php');
}



function include_local_template($template="") {
	include(LIB_PATH.DS.$template);
}

function include_widget($name="") {
	include(PUBLIC_ROOT.DS.'includes'.DS.$name);
}


function site_title($title)
{
	if(!preg_match('/home/i', $title))
		$string = "$title | ";

	echo $string.SITE_NAME." | ".SCHOOL;
}

function getRevision()
{
	$filename = basename($_SERVER['REQUEST_URI']);
	if(!preg_match("/php/i", $filename)){
		$filename = 'index.php';
	}
	if(file_exists($filename)) {
		echo "Revised: " . date("F d, Y", filemtime($filename));
	}
}

function siteHead()
{
	if(file_exists(LIB_PATH.DS.'custom_header.php')){
		echo file_get_contents(LIB_PATH.DS.'custom_header.php');
	}
}

function siteFooter()
{
	if(file_exists(LIB_PATH.DS.'custom_footer.php')){
		echo file_get_contents(LIB_PATH.DS.'custom_footer.php');
	}
}

function js_navigational_array()
{
	global $_SITE;
	if($_SITE->page){
		echo "var oSelected = [];\r\n\t\t\t";
		foreach($_SITE->page as $parent=>$child){
			echo "oSelected['$parent'] \t= [ \"";
			foreach($_SITE->page[$parent] as $child=>$attribute){
				$temp[] = $child;
			}
			echo join('", "', $temp) ."\" ];\r\n\t\t\t";
			$temp = '';
		}
	}
}

function pageinfo($attr)
{
	global $_PAGE;
	global $page_attribute;

	if(isset($page_attribute)){
		echo $page_attribute->$attr;
	}else{
		echo $_PAGE->$attr;
	}
}


/**
 * Writes to site log file
 *
 * @param (DataType) action, message
 *
 */
function log_action($action, $message="", $user='') {
	global $session;
	$logfile = ADMIN_ROOT.DS.'includes'.DS.'log.txt';
	$new = file_exists($logfile) ? false : true;
	if($handle = fopen($logfile, 'a')) { // append
		$timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
		$client = "client ".$_SERVER['REMOTE_ADDR'];
		$user = "user ".$user;
		$content = "[{$timestamp}] [{$client}] [{$user}] {$action}: {$message}\n";
		fwrite($handle, $content);
		fclose($handle);
		if($new) { chmod($logfile, 0765); }
	} else {
		echo "Could not open log file for writing.";
	}
}

// gets directory size information
function getDirectorySize($path)
{
	$totalsize = 0;
	$totalcount = 0;
	$dircount = 0;
	if ($handle = @opendir($path))
	{
		while (false !== ($file = readdir($handle)))
		{
			$nextpath = $path . DS . $file;
			if ($file != '.' && $file != '..' && !is_link($nextpath))
			{
				if (is_dir($nextpath))
				{
					$dircount++;
					$result = getDirectorySize($nextpath);
					$totalsize += $result['size'];
					$totalcount += $result['count'];
					$dircount += $result['dircount'];
				}
				elseif (is_file($nextpath))
				{
					$totalsize += filesize($nextpath);
					$totalcount++;
				}
			}
		}
		closedir($handle);
	}
	$total['size'] = $totalsize;
	$total['count'] = $totalcount;
	$total['dircount'] = $dircount;
	return $total;
}

// declare sizeFormat function
function sizeFormat($size)
{
	if(empty($size)) {
		return "0 bytes";
	}
	
	if($size<1024)
	{
		return $size." bytes";
	}
	else if($size<(1024*1024))
		{
			$size=round($size/1024,1);
			return $size." KB";
		}
	else if($size<(1024*1024*1024))
		{
			$size=round($size/(1024*1024),1);
			return $size." MB";
		}
	else
	{
		$size=round($size/(1024*1024*1024),1);
		return $size." GB";
	}
}

function get_allowed_mime_types() {
	static $mimes = false;

	if ( !$mimes ) {
		// Accepted MIME types are set here as PCRE unless provided.
		$mimes = array(
		'jpg' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpe' => 'image/jpeg',
		'gif' => 'image/gif',
		'png' => 'image/png',
		'bmp' => 'image/bmp',
	//	'tif' => 'image/tiff',
	//	'tiff' => 'image/tiff',
		'ico' => 'image/x-icon',
		'asf' => 'video/asf',
		'asx' => 'video/asf',
		'wax' => 'video/asf',
		'wmv' => 'video/asf',
		'wmx' => 'video/asf',
		'avi' => 'video/avi',
		'divx' => 'video/divx',
		'flv' => 'video/x-flv',
		'mov' => 'video/quicktime',
		'qt' => 'video/quicktime',
		'mpeg' => 'video/mpeg',
		'mpg' => 'video/mpeg',
		'mpe' => 'video/mpeg',
		'txt' => 'text/plain',
		'asc' => 'text/plain',
		'c' => 'text/plain',
		'cc' => 'text/plain',
		'h' => 'text/plain',
		'csv' => 'text/csv',
		'tsv' => 'text/tab-separated-values',
		'ics' => 'text/calendar',
		'rtx' => 'text/richtext',
		'css' => 'text/css',
		'htm' => 'text/html',
		'html' => 'text/html',
		'mp3' => 'audio/mpeg',
		'm4a' => 'audio/mpeg',
		'm4b' => 'audio/mpeg',
		'mp4' => 'video/mp4',
		'm4v' => 'video/mp4',
		'ra' => 'video/mp4',
		'ram' => 'audio/x-realaudio',
		'wav' => 'audio/wav',
		'ogg' => 'audio/ogg',
		'oga' => 'audio/ogg',
		'ogv' => 'video/ogg',
		'mid' => 'audio/midi',
		'midi' => 'audio/midi',
		'wma' => 'audio/wma',
		'mka' => 'audio/x-matroska',
		'mkv' => 'video/x-matroska',
		'rtf' => 'application/rtf',
		'js' => 'application/javascript',
		'pdf' => 'application/pdf',
		'doc' => 'application/msword',
		'docx' => 'application/msword',
		'pot' => 'application/vnd.ms-powerpoint',
		'pps' => 'application/vnd.ms-powerpoint',
		'ppt' => 'application/vnd.ms-powerpoint',
		'pptx' => 'application/vnd.ms-powerpoint',
		'ppam' => 'application/vnd.ms-powerpoint',
		'pptm' => 'application/vnd.ms-powerpoint',
		'sldm' => 'application/vnd.ms-powerpoint',
		'ppsm' => 'application/vnd.ms-powerpoint',
		'potm' => 'application/vnd.ms-powerpoint',
		'wri' => 'application/vnd.ms-write',
		'xla' => 'application/vnd.ms-excel',
		'xls' => 'application/vnd.ms-excel',
		'xlsx' => 'application/vnd.ms-excel',
		'xlt' => 'application/vnd.ms-excel',
		'xlw' => 'application/vnd.ms-excel',
		'xlam' => 'application/vnd.ms-excel',
		'xlsb' => 'application/vnd.ms-excel',
		'xlsm' => 'application/vnd.ms-excel',
		'xltm' => 'application/vnd.ms-excel',
		'mdb' => 'application/vnd.ms-access',
		'mpp' => 'application/vnd.ms-project',
		'docm' => 'application/vnd.ms-word',
		'dotm' => 'application/vnd.ms-word',
		'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml',
		'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml',
		'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml',
		'pps' => 'application/vnd.openxmlformats-officedocument.presentationml',
		'potx' => 'application/vnd.openxmlformats-officedocument.presentationml',
		'xlsm' => 'application/vnd.openxmlformats-officedocument.spreadsheetml',
		'xls' => 'application/vnd.openxmlformats-officedocument.spreadsheetml',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml',
		'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml',
		'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml',
		'onetoc' => 'application/onenote',
		'onetoc2' => 'application/onenote',
		'onetmp' => 'application/onenote',
		'onepkg' => 'application/onenote',
		'swf' => 'application/x-shockwave-flash',
		'class' => 'application/java',
		'tar' => 'application/x-tar',
		'zip' => 'application/zip',
		'gz' => 'application/x-gzip',
		'gzip' => 'application/x-gzip',
		'exe' => 'application/x-msdownload',
		// openoffice formats
		'odt' => 'application/vnd.oasis.opendocument.text',
		'odp' => 'application/vnd.oasis.opendocument.presentation',
		'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		'odg' => 'application/vnd.oasis.opendocument.graphics',
		'odc' => 'application/vnd.oasis.opendocument.chart',
		'odb' => 'application/vnd.oasis.opendocument.database',
		'odf' => 'application/vnd.oasis.opendocument.formula',
		// wordperfect formats
		'wp' => 'application/wordperfect',
		'wpd' => 'application/wordperfect',
		);
	}

	return $mimes;
}

function get_file_types()
{
	static $types = false;
	
	if( !$types ) {
		$types = array(
			'audio'       => array( 'aac', 'ac3',  'aif',  'aiff', 'm3a',  'm4a',   'm4b', 'mka', 'mp1', 'mp2',  'mp3', 'ogg', 'oga', 'ram', 'wav', 'wma' ),
			'video'       => array( 'asf', 'avi',  'divx', 'dv',   'flv',  'm4v',   'mkv', 'mov', 'mp4', 'mpeg', 'mpg', 'mpv', 'ogm', 'ogv', 'qt',  'rm', 'swf', 'vob', 'wmv' ),
			'document'    => array( 'doc', 'docx', 'docm', 'dotm', 'odt',  'pages', 'pdf', 'rtf', 'wp',  'wpd' ),
			'spreadsheet' => array( 'numbers',     'ods',  'xls',  'xlsx', 'xlsb',  'xlsm' ),
			'interactive' => array( 'key', 'ppt',  'pptx', 'pptm', 'odp', 'pot', 'pps' ),
			'text'        => array( 'asc', 'csv',  'tsv',  'txt' ),
			'archive'     => array( 'bz2', 'cab',  'dmg',  'gz',   'rar',  'sea',   'sit', 'sqx', 'tar', 'tgz',  'zip' ),
			'code'        => array( 'css', 'htm',  'html', 'php',  'js' ),
			'image'		  => array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp', 'tif', 'tiff', 'ico' )
		);
	}
	
	return $types;
}

function ext2type($ext) {
	
	$ext2type = get_file_types();
	foreach ( $ext2type as $type => $exts )
		if ( in_array( $ext, $exts ) )
			return $type;
}

function mime2ext($mime)
{
	$mime_type = get_allowed_mime_types();
	foreach( $mime_type as $ext => $type ) {
		if ( $mime == $type )
			return $ext;
	}
}

function mime2type($mime) {
	return ext2type(mime2ext($mime));
}

function file_extension($filename)
{
	$filename = explode('.', basename($filename));
	return array_pop($filename);
}

function type_icon($mime)
{
	$type = ext2type(mime2ext($mime));
	return '/admin/images/mediaicons/'.$type.".png";
}

/**
 * Converts computer date to human readible
 *
 * @param (DataType) datetime
 * @return (string) date
 *
 */
function datetime_to_text($datetime="") {
	$unixdatetime = strtotime($datetime);
	return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
}

function time_to_text($time=""){
	if(!empty($time)) {
		return date('F j, Y @ g:i a', $time);
	}else{
		return date('F j, Y @ g:i a', time());
	}
}

function _d($time='') {
	if(!empty($time)) {
		return date('n/j/Y g:i a', $time);
	}else{
		return date('n/j/Y g:i a', time());
	}
}

/**
 * Truncates text
 *
 * @param (DataType) string
 * @return (string) string
 *
 */
function _t($string, $length=100, $delim='...'){
	$string = strip_tags($string);
	$slen = strlen($string);
	if($slen > $length) {
		$length = ($slen > $length && $slen < $length + 10) ? $length - 10 : $length;
		$string = rtrim(substr($string,0,strpos($string,' ',$length)).$delim);
	}
	return $string;
}

function _tw($string, $width, $delim='...') {
	if (strlen($string) > $width) {
	    $string = wordwrap($string, $width);
	    $string = rtrim(substr($string, 0, strpos($string, "\n")).$delim);
	}
	return $string;
}

/**
 * File: Helpers
 * Various functions used throughout Chyrp's code.
 */

# Integer: $time_start
# Times Chyrp.
$time_start = 0;

# Array: $l10n
# Stores loaded gettext domains.
$l10n = array();

/**
 * Function: show_403
 * Shows an error message with a 403 HTTP header.
 *
 * Parameters:
 *     $title - The title for the error dialog.
 *     $body - The message for the error dialog.
 */
function show_403($title, $body) {
	header("Status: 403");
	error($title, $body);
}


/**
 * Function: __
 * Returns a translated string.
 *
 * Parameters:
 *     $text - The string to translate.
 *     $domain - The translation domain to read from.
 */
function __($text, $domain = "chyrp") {
	global $l10n;
	return (isset($l10n[$domain])) ? $l10n[$domain]->translate($text) : $text ;
}

/**
 * Function: _p
 * Returns a plural (or not) form of a translated string.
 *
 * Parameters:
 *     $single - Singular string.
 *     $plural - Pluralized string.
 *     $number - The number to judge by.
 *     $domain - The translation domain to read from.
 */
function _p($number, $single) {
	$single = (intval($number) !== 1) ? $single."s" : $single;
	return $number." ".$single;
}

/**
 * Function: _f
 * Returns a formatted translated string.
 *
 * Parameters:
 *     $string - String to translate and format.
 *     $args - One arg or an array of arguments to format with.
 *     $domain - The translation domain to read from.
 */
function _f($string, $args = array(), $domain = "chyrp") {
	$args = (array) $args;
	array_unshift($args, __($string, $domain));
	return call_user_func_array("sprintf", $args);
}

/**
 * Function: pluralize
 * Returns a pluralized string. This is a port of Rails's pluralizer.
 *
 * Parameters:
 *     $string - The string to pluralize.
 *     $number - If passed, and this number is 1, it will not pluralize.
 */
function pluralize($string, $number = null) {
	$uncountable = array("moose", "sheep", "fish", "series", "species",
		"rice", "money", "information", "equipment", "piss");

	if (in_array($string, $uncountable) or $number == 1)
		return $string;

	$replacements = array("/person/i" => "people",
		"/man/i" => "men",
		"/child/i" => "children",
		"/cow/i" => "kine",
		"/goose/i" => "geese",
		"/(penis)$/i" => "\\1es", # Take that, Rails!
		"/(ax|test)is$/i" => "\\1es",
		"/(octop|vir)us$/i" => "\\1ii",
		"/(cact)us$/i" => "\\1i",
		"/(alias|status)$/i" => "\\1es",
		"/(bu)s$/i" => "\\1ses",
		"/(buffal|tomat)o$/i" => "\\1oes",
		"/([ti])um$/i" => "\\1a",
		"/sis$/i" => "ses",
		"/(hive)$/i" => "\\1s",
		"/([^aeiouy]|qu)y$/i" => "\\1ies",
		"/^(ox)$/i" => "\\1en",
		"/(matr|vert|ind)(?:ix|ex)$/i" => "\\1ices",
		"/(x|ch|ss|sh)$/i" => "\\1es",
		"/([m|l])ouse$/i" => "\\1ice",
		"/(quiz)$/i" => "\\1zes");

	$replaced = preg_replace(array_keys($replacements), array_values($replacements), $string, 1);

	if ($replaced == $string)
		return $string."s";
	else
		return $replaced;
}

/**
 * Function: depluralize
 * Returns a depluralized string. This is the inverse of <pluralize>.
 *
 * Parameters:
 *     $string - The string to depluralize.
 *     $number - If passed, and this number is not 1, it will not depluralize.
 */
function depluralize($string, $number = null) {
	if (isset($number) and $number != 1)
		return $string;

	$replacements = array("/people/i" => "person",
		"/^men/i" => "man",
		"/children/i" => "child",
		"/kine/i" => "cow",
		"/geese/i" => "goose",
		"/(penis)es$/i" => "\\1",
		"/(ax|test)es$/i" => "\\1is",
		"/(octopi|viri|cact)i$/i" => "\\1us",
		"/(alias|status)es$/i" => "\\1",
		"/(bu)ses$/i" => "\\1s",
		"/(buffal|tomat)oes$/i" => "\\1o",
		"/([ti])a$/i" => "\\1um",
		"/ses$/i" => "sis",
		"/(hive)s$/i" => "\\1",
		"/([^aeiouy]|qu)ies$/i" => "\\1y",
		"/^(ox)en$/i" => "\\1",
		"/(vert|ind)ices$/i" => "\\1ex",
		"/(matr)ices$/i" => "\\1ix",
		"/(x|ch|ss|sh)es$/i" => "\\1",
		"/([ml])ice$/i" => "\\1ouse",
		"/(quiz)zes$/i" => "\\1");

	$replaced = preg_replace(array_keys($replacements), array_values($replacements), $string, 1);

	if ($replaced == $string and substr($string, -1) == "s")
		return substr($string, 0, -1);
	else
		return $replaced;
}

/**
 * Function: truncate
 * Truncates a string to the given length, optionally taking into account HTML tags, and/or keeping words in tact.
 *
 * Parameters:
 *     $text - String to shorten.
 *     $length - Length to truncate to.
 *     $ending - What to place at the end, e.g. "...".
 *     $exact - Break words?
 *     $html - Auto-close cut-off HTML tags?
 *
 * Author:
 *     CakePHP team, code style modified.
 */
function truncate($text, $length = 100, $ending = "...", $exact = false, $html = false) {
	if (is_array($ending))
		extract($ending);

	if ($html) {
		if (strlen(preg_replace("/<[^>]+>/", "", $text)) <= $length)
			return $text;

		$totalLength = strlen($ending);
		$openTags = array();
		$truncate = "";
		preg_match_all("/(<\/?([\w+]+)[^>]*>)?([^<>]*)/", $text, $tags, PREG_SET_ORDER);
		foreach ($tags as $tag) {
			if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])
				and preg_match('/<[\w]+[^>]*>/s', $tag[0]))
				array_unshift($openTags, $tag[2]);
			elseif (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
				$pos = array_search($closeTag[1], $openTags);
				if ($pos !== false)
					array_splice($openTags, $pos, 1);
			}

			$truncate .= $tag[1];

			$contentLength = strlen(preg_replace("/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i", " ", $tag[3]));
			if ($contentLength + $totalLength > $length) {
				$left = $length - $totalLength;
				$entitiesLength = 0;
				if (preg_match_all("/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i", $tag[3], $entities, PREG_OFFSET_CAPTURE))
					foreach ($entities[0] as $entity)
						if ($entity[1] + 1 - $entitiesLength <= $left) {
							$left--;
							$entitiesLength += strlen($entity[0]);
						} else
						break;

					$truncate .= substr($tag[3], 0 , $left + $entitiesLength);

				break;
			} else {
				$truncate .= $tag[3];
				$totalLength += $contentLength;
			}

			if ($totalLength >= $length)
				break;
		}
	} else {
		if (strlen($text) <= $length)
			return $text;
		else
			$truncate = substr($text, 0, $length - strlen($ending));
	}

	if (!$exact) {
		$spacepos = strrpos($truncate, " ");

		if (isset($spacepos)) {
			if ($html) {
				$bits = substr($truncate, $spacepos);
				preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
				if (!empty($droppedTags))
					foreach ($droppedTags as $closingTag)
						if (!in_array($closingTag[1], $openTags))
							array_unshift($openTags, $closingTag[1]);
			}

			$truncate = substr($truncate, 0, $spacepos);
		}
	}

	$truncate .= $ending;

	if ($html)
		foreach ($openTags as $tag)
			$truncate .= '</'.$tag.'>';

		return $truncate;
}

/**
 * Function: when
 * Returns date formatting for a string that isn't a regular time() value
 *
 * Parameters:
 *     $formatting - The formatting for date().
 *     $when - Time to base on. If it is not numeric it will be run through strtotime.
 *     $strftime - Use @strftime@ instead of @date@?
 */
function when($formatting, $when, $strftime = false) {
	$time = (is_numeric($when)) ? $when : strtotime($when) ;

	if ($strftime)
		return strftime($formatting, $time);
	else
		return date($formatting, $time);
}

/**
 * Function: datetime
 * Returns a standard datetime string based on either the passed timestamp or their time offset, usually for MySQL inserts.
 *
 * Parameters:
 *     $when - An optional timestamp.
 */
function datetime($when = null) {
	fallback($when, time());

	$time = (is_numeric($when)) ? $when : strtotime($when) ;

	return date("Y-m-d H:i:s", $time);
}

/**
 * Function: sanitize
 * Returns a sanitized string, typically for URLs.
 *
 * Parameters:
 *     $string - The string to sanitize.
 *     $force_lowercase - Force the string to lowercase?
 *     $anal - If set to *true*, will remove all non-alphanumeric characters.
 *     $trunc - Number of characters to truncate to (default 100, 0 to disable).
 */
function sanitize($string, $force_lowercase = true, $anal = false, $trunc = 100) {
	$strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "=", "+", "[", "{", "]",
		"}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
		",", "<", ".", ">", "/", "?");
	$clean = trim(str_replace($strip, "", strip_tags($string)));
	$clean = preg_replace('/\s+/', "-", $clean);
	$clean = ($anal ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean);
	$clean = ($trunc ? substr($clean, 0, $trunc) : $clean);
	return ($force_lowercase) ?
		(function_exists('mb_strtolower')) ?
		mb_strtolower($clean, 'UTF-8') :
		strtolower($clean) :
	$clean;
}


/**
 * Function: grab_urls
 * Crawls a string for links.
 *
 * Parameters:
 *     $string - The string to crawl.
 *
 * Returns:
 *     An array of all URLs found in the string.
 */
function grab_urls($string) {
	$regexp = "/<a[^>]+href=[\"|']([^\"]+)[\"|']>[^<]+<\/a>/";
	preg_match_all(Trigger::current()->filter($regexp, "link_regexp"), stripslashes($string), $matches);
	$matches = $matches[1];
	return $matches;
}


/**
 * Function: camelize
 * Converts a given string to camel-case.
 *
 * Parameters:
 *     $string - The string to camelize.
 *     $keep_spaces - Whether or not to convert underscores to spaces or remove them.
 *
 * Returns:
 *     A CamelCased string.
 *
 * See Also:
 *     <decamelize>
 */
function camelize($string, $keep_spaces = false) {
	$lower = strtolower($string);
	$deunderscore = str_replace("_", " ", $lower);
	$dehyphen = str_replace("-", " ", $deunderscore);
	$final = ucwords($dehyphen);

	if (!$keep_spaces)
		$final = str_replace(" ", "", $final);

	return $final;
}

/**
 * Function: decamelize
 * Decamelizes a string.
 *
 * Parameters:
 *     $string - The string to decamelize.
 *
 * Returns:
 *     A de_camel_cased string.
 *
 * See Also:
 *     <camelize>
 */
function decamelize($string) {
	$string = strtolower(preg_replace("/([a-z])([A-Z])/", "\\1_\\2", $string));
	return str_ireplace(" ", "_", $string);
}

/**
 * Function: selected
 * If $val1 == $val2, outputs or returns @ selected="selected"@
 *
 * Parameters:
 *     $val1 - First value.
 *     $val2 - Second value.
 *     $return - Return @ selected="selected"@ instead of outputting it
 */
function selected($val1, $val2, $return = false, $disabled=true) {
	if ($val1 == $val2)
		if ($return)
			return ' selected="selected"';
		else
			echo ' selected="selected"';

		if( !$disabled && $val1 !== $val2 ) {
			if ($return)
				return ' disabled="disabled"';
			else
				echo ' disabled="disabled"';
		}
}

/**
 * Function: checked
 * If $val == 1 (true), outputs ' checked="checked"'
 *
 * Parameters:
 *     $val - Value to check.
 */
function checked($val1,$val2) {
	if ($val1 == $val2) echo ' checked="checked"';
}

/**
 * Function: fallback
 * Sets a given variable if it is not set.
 *
 * The last of the arguments or the first non-empty value will be used.
 *
 * Parameters:
 *     &$variable - The variable to return or set.
 *
 * Returns:
 *     The value of whatever was chosen.
 */
function fallback(&$variable) {
	if (is_bool($variable))
		return $variable;

	$set = (!isset($variable) or (is_string($variable) and trim($variable) === "") or $variable === array());

	$args = func_get_args();
	array_shift($args);
	if (count($args) > 1) {
		foreach ($args as $arg) {
			$fallback = $arg;

			if (isset($arg) and (!is_string($arg) or (is_string($arg) and trim($arg) !== "")) and $arg !== array())
				break;
		}
	} else
		$fallback = isset($args[0]) ? $args[0] : null ;

	if ($set)
		$variable = $fallback;

	return $set ? $fallback : $variable ;
}

/**
 * Function: oneof
 * Returns the first argument that is set and non-empty.
 *
 * It will guess where to stop based on the types of the arguments, e.g. "" has priority over array() but not 1.
 */
function oneof() {
	$last = null;
	$args = func_get_args();
	foreach ($args as $index => $arg) {
		if (!isset($arg) or (is_string($arg) and trim($arg) === "") or $arg === array() or (is_object($arg) and empty($arg)) or ($arg === "0000-00-00 00:00:00"))
			$last = $arg;
		else
			return $arg;

		if ($index + 1 == count($args))
			break;

		$next = $args[$index + 1];

		$incomparable = ((is_array($arg) and !is_array($next)) or        # This is a big check but it should cover most "incomparable" cases.
			(!is_array($arg) and is_array($next)) or        # Using simple type comparison wouldn't work too well, for example
			(is_object($arg) and !is_object($next)) or      # when "" would take priority over 1 in oneof("", 1) because they're
			(!is_object($arg) and is_object($next)) or      # different types.
			(is_resource($arg) and !is_resource($next)) or
			(!is_resource($arg) and is_resource($next)));

		if (isset($arg) and isset($next) and $incomparable)
			return $arg;
	}

	return $last;
}

/**
 * Function: random
 * Returns a random string.
 *
 * Parameters:
 *     $length - How long the string should be.
 *     $specialchars - Use special characters in the resulting string?
 */
function random($length, $specialchars = false) {
	$pattern = "1234567890abcdefghijklmnopqrstuvwxyz";

	if ($specialchars)
		$pattern.= "!@#$%^&*()?~";

	$len = strlen($pattern) - 1;

	$key = "";
	for($i = 0; $i < $length; $i++)
		$key.= $pattern[rand(0, $len)];

	return $key;
}

/**
 * Function: unique_filename
 * Makes a given filename unique for the uploads directory.
 *
 * Parameters:
 *     $name - The name to check.
 *     $path - Path to check in.
 *     $num - Number suffix from which to start increasing if the filename exists.
 *
 * Returns:
 *     A unique version of the given $name.
 */
function unique_filename($name, $path = "", $num = 2) {
	$path = rtrim($path, "/");
	if (!file_exists(MAIN_DIR.Config::current()->uploads_path.$path."/".$name))
		return $name;

	$name = explode(".", $name);

	# Handle common double extensions
	foreach (array("tar.gz", "tar.bz", "tar.bz2") as $extension) {
		list($first, $second) = explode(".", $extension);
		$file_first =& $name[count($name) - 2];
		if ($file_first == $first and end($name) == $second) {
			$file_first = $first.".".$second;
			array_pop($name);
		}
	}

	$ext = ".".array_pop($name);

	$try = implode(".", $name)."-".$num.$ext;
	if (!file_exists(MAIN_DIR.Config::current()->uploads_path.$path."/".$try))
		return $try;

	return unique_filename(implode(".", $name).$ext, $path, $num + 1);
}

/**
 * Function: upload
 * Moves an uploaded file to the uploads directory.
 *
 * Parameters:
 *     $file - The $_FILES value.
 *     $extension - An array of valid extensions (case-insensitive).
 *     $path - A sub-folder in the uploads directory (optional).
 *     $put - Use copy() instead of move_uploaded_file()?
 *
 * Returns:
 *     The resulting filename from the upload.
 */
function upload($file, $extension = null, $path = "", $put = false) {
	$file_split = explode(".", $file['name']);
	$path = rtrim($path, "/");
	$dir = MAIN_DIR.Config::current()->uploads_path.$path;

	if (!file_exists($dir))
		mkdir($dir, 0777, true);

	$original_ext = end($file_split);

	# Handle common double extensions
	foreach (array("tar.gz", "tar.bz", "tar.bz2") as $ext) {
		list($first, $second) = explode(".", $ext);
		$file_first =& $file_split[count($file_split) - 2];
		if ($file_first == $first and end($file_split) == $second) {
			$file_first = $first.".".$second;
			array_pop($file_split);
		}
	}

	$file_ext = end($file_split);

	if (is_array($extension)) {
		if (!in_array(strtolower($file_ext), $extension) and !in_array(strtolower($original_ext), $extension)) {
			$list = "";
			for ($i = 0; $i < count($extension); $i++) {
				$comma = "";
				if (($i + 1) != count($extension)) $comma = ", ";
				if (($i + 2) == count($extension)) $comma = ", and ";
				$list.= "<code>*.".$extension[$i]."</code>".$comma;
			}
			error(__("Invalid Extension"), _f("Only %s files are accepted.", array($list)));
		}
	} elseif (isset($extension) and
		strtolower($file_ext) != strtolower($extension) and
		strtolower($original_ext) != strtolower($extension))
		error(__("Invalid Extension"), _f("Only %s files are supported.", array("*.".$extension)));

	array_pop($file_split);
	$file_clean = implode(".", $file_split);
	$file_clean = sanitize($file_clean, false).".".$file_ext;
	$filename = unique_filename($file_clean, $path);

	$message = __("Couldn't upload file. CHMOD <code>".$dir."</code> to 777 and try again. If this problem persists, it's probably timing out; in which case, you must contact your system administrator to increase the maximum POST and upload sizes.");

	if ($put) {
		if (!@copy($file['tmp_name'], $dir."/".$filename))
			error(__("Error"), $message);
	} elseif (!@move_uploaded_file($file['tmp_name'], $dir."/".$filename))
		error(__("Error"), $message);

	return ($path ? $path."/".$filename : $filename);
}

/**
 * Function: upload_from_url
 * Copy a file from a specified URL to their upload directory.
 *
 * Parameters:
 *     $url - The URL to copy.
 *     $extension - An array of valid extensions (case-insensitive).
 *     $path - A sub-folder in the uploads directory (optional).
 *
 * See Also:
 *     <upload>
 */
function upload_from_url($url, $extension = null, $path = "") {
	$file = tempnam(null, "chyrp");
	file_put_contents($file, get_remote($url));

	$fake_file = array("name" => basename(parse_url($url, PHP_URL_PATH)),
		"tmp_name" => $file);

	return upload($fake_file, $extension, $path, true);
}

/**
 * Function: uploaded
 * Returns a URL to an uploaded file.
 *
 * Parameters:
 *     $file - Filename relative to the uploads directory.
 */
function uploaded($file, $url = true) {
	if (empty($file))
		return "";

	$config = Config::current();
	return ($url ? $config->chyrp_url.$config->uploads_path.$file : MAIN_DIR.$config->uploads_path.$file);
}

/**
 * Function: timer_start
 * Starts the timer.
 */
function timer_start() {
	global $time_start;
	$mtime = explode(" ", microtime());
	$mtime = $mtime[1] + $mtime[0];
	$time_start = $mtime;
}

/**
 * Function: timer_stop
 * Stops the timer and returns the total time.
 *
 * Parameters:
 *     $precision - Number of decimals places to round to.
 *
 * Returns:
 *     A formatted number with the given $precision.
 */
function timer_stop($precision = 3) {
	global $time_start;
	$mtime = microtime();
	$mtime = explode(" ", $mtime);
	$mtime = $mtime[1] + $mtime[0];
	$time_end = $mtime;
	$time_total = $time_end - $time_start;
	return number_format($time_total, $precision);
}

/**
 * Function: normalize
 * Attempts to normalize all newlines and whitespace into single spaces.
 *
 * Returns:
 *     The normalized string.
 */
function normalize($string) {
	$trimmed = trim($string);
	$newlines = str_replace("\n\n", " ", $trimmed);
	$newlines = str_replace("\n", "", $newlines);
	$normalized = preg_replace("/[\s\n\r\t]+/", " ", $newlines);
	return $normalized;
}

/**
 * Function: get_remote
 * Grabs the contents of a website/location.
 *
 * Parameters:
 *     $url - The URL of the location to grab.
 *
 * Returns:
 *     The response from the remote URL.
 */
function get_remote($url) {
	extract(parse_url($url), EXTR_SKIP);

	if (ini_get("allow_url_fopen")) {
		$content = @file_get_contents($url);
		if ($http_response_header[0] != "HTTP/1.1 200 OK")
			$content = "Server returned a message: $http_response_header[0]";
	} elseif (function_exists("curl_init")) {
		$handle = curl_init();
		curl_setopt($handle, CURLOPT_URL, $url);
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($handle, CURLOPT_TIMEOUT, 60);
		$content = curl_exec($handle);
		$status = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		curl_close($handle);
		if ($status != 200)
			$content = "Server returned a message: $status";
	} else {
		$path = (!isset($path)) ? '/' : $path ;
		if (isset($query)) $path.= '?'.$query;
		$port = (isset($port)) ? $port : 80 ;

		$connect = @fsockopen($host, $port, $errno, $errstr, 2);
		if (!$connect) return false;

		# Send the GET headers
		fwrite($connect, "GET ".$path." HTTP/1.1\r\n");
		fwrite($connect, "Host: ".$host."\r\n");
		fwrite($connect, "User-Agent: Chyrp/".CHYRP_VERSION."\r\n\r\n");

		$content = "";
		while (!feof($connect)) {
			$line = fgets($connect, 128);
			if (preg_match("/\r\n/", $line)) continue;

			$content.= $line;
		}

		fclose($connect);
	}

	return $content;
}

/**
 * Function: self_url
 * Returns the current URL.
 */
function self_url() {
	$split = explode("/", $_SERVER['SERVER_PROTOCOL']);
	$protocol = strtolower($split[0]);
	$default_port = ($protocol == "http") ? 80 : 443 ;
	$port = ($_SERVER['SERVER_PORT'] == $default_port) ? "" : ":".$_SERVER['SERVER_PORT'] ;
	return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
}


/**
 * Function: sanitize_input
 * Makes sure no inherently broken ideas such as magic_quotes break our application
 *
 * Parameters:
 *     $data - The array to be sanitized, usually one of @$_GET@, @$_POST@, @$_COOKIE@, or @$_REQUEST@
 */
function sanitize_input(&$data) {
	foreach ($data as &$value)
		if (is_array($value))
			sanitize_input($value);
		else
			$value = get_magic_quotes_gpc() ? stripslashes($value) : $value ;
}

/**
 * Function: error_panicker
 * Exits and states where the error occurred.
 */
function error_panicker($errno, $message, $file, $line) {
	if (error_reporting() === 0)
		return; # Suppressed error.

	exit("ERROR: ".$message." (".$file." on line ".$line.")");
}


/**
 * Function: relative_time
 * Returns the difference between the given timestamps or now.
 *
 * Parameters:
 *     $time - Timestamp to compare to.
 *     $from - Timestamp to compare from. If not specified, defaults to now.
 *
 * Returns:
 *     A string formatted like "3 days ago" or "3 days from now".
 */
function relative_time($when, $from = null) {
	fallback($from, time());

	$time = (is_numeric($when)) ? $when : strtotime($when) ;

	$difference = $from - $time;

	if ($difference < 0) {
		$word = "from now";
		$difference = -$difference;
	} elseif ($difference > 0)
		$word = "ago";
	else
		return "just now";

	$units = array("second"     => 1,
		"minute"     => 60,
		"hour"       => 60 * 60,
		"day"        => 60 * 60 * 24,
		"week"       => 60 * 60 * 24 * 7,
		"month"      => 60 * 60 * 24 * 30,
		"year"       => 60 * 60 * 24 * 365,
		"decade"     => 60 * 60 * 24 * 365 * 10,
		"century"    => 60 * 60 * 24 * 365 * 100,
		"millennium" => 60 * 60 * 24 * 365 * 1000);

	$possible_units = array();
	foreach ($units as $name => $val)
		if (($name == "week" and $difference >= ($val * 2)) or # Only say "weeks" after two have passed.
			($name != "week" and $difference >= $val))
			$unit = $possible_units[] = $name;

		$precision = (int) in_array("year", $possible_units);
	$amount = round($difference / $units[$unit], $precision);

	return $amount." ".pluralize($unit, $amount)." ".$word;
}

/**
 * Function: list_notate
 * Notates an array as a list of things.
 *
 * Parameters:
 *     $array - An array of things to notate.
 *     $quotes - Wrap quotes around strings?
 *
 * Returns:
 *     A string like "foo, bar, and baz".
 */
function list_notate($array, $quotes = false) {
	$count = 0;
	$items = array();
	foreach ($array as $item) {
		$string = (is_string($item) and $quotes) ? "&#8220;".$item."&#8221;" : $item ;
		if (count($array) == ++$count and $count !== 1)
			$items[] = __("and ").$string;
		else
			$items[] = $string;
	}

	return (count($array) == 2) ? implode(" ", $items) : implode(", ", $items) ;
}

/**
 * Function: now
 * Alias to strtotime, for prettiness like now("+1 day").
 */
function now($when) {
	return strtotime($when);
}

function is_really_writeable($path, $filemode = 0775)
{
	if (file_exists($path) && is_writable($path))
	{
		return true;
	}

	if ( ! file_exists($path))
	{
		if (mkdir($path, $filemode))
		{
			return true;
		}
	}

	if (chmod($path, $filemode))
	{
		return true;
	}
	
	return false;
}

/**
 * Function: comma_sep
 * Convert a comma-seperated string into an array of the listed values.
 */
function comma_sep($string) {
	$commas = explode(",", $string);
	$trimmed = array_map("trim", $commas);
	$cleaned = array_diff(array_unique($trimmed), array(""));
	return $cleaned;
}


function rrmdir($dir) {
	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
			}
		}
		reset($objects);
		rmdir($dir);
	}
}

function copyr($source, $dest)
{
    // recursive function to copy
    // all subdirectories and contents:
    if(is_dir($source)) {
        $dir_handle=opendir($source);
        $sourcefolder = basename($source);
        mkdir($dest."/".$sourcefolder);
        while($file=readdir($dir_handle)){
            if($file!="." && $file!=".."){
                if(is_dir($source."/".$file)){
                    self::copyr($source."/".$file, $dest."/".$sourcefolder);
                } else {
                    copy($source."/".$file, $dest."/".$file);
                }
            }
        }
        closedir($dir_handle);
    } else {
        // can also handle simple copy commands
        copy($source, $dest);
    }
}

function chmodr($path, $filemode) {
	if (!is_dir($path))
		return chmod($path, $filemode);

	$dh = opendir($path);
	while (($file = readdir($dh)) !== false) {
		if($file != '.' && $file != '..') {
			$fullpath = $path.'/'.$file;
			if(is_link($fullpath))
				return FALSE;
			elseif(!is_dir($fullpath) && !chmod($fullpath, $filemode))
				return FALSE;
			elseif(!chmodr($fullpath, $filemode))
				return FALSE;
		}
	}

	closedir($dh);

	if(chmod($path, $filemode))
		return TRUE;
	else
		return FALSE;
}

?>