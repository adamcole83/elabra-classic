<?
	require_once('../includes/initialize.php');
	header('Content-type: text/javascript');
	
	$jsDir = '/js/';
	$scripts = array(
	//	'jQuery Easing'			=> 'jquery.easing.1.3.min.js',
	//	'jQuery UI Core'		=> 'jquery.ui.core.min.js',
	//	'jQuery UI Widget'		=> 'jquery.ui.widget.min.js',
	//	'jQuery UI Accordion'	=> 'jquery.ui.accordion.min.js',
		'jQuery UI Custom'		=> 'jquery-ui-1.8.16.custom.min.js',
		'Content Management'	=> 'cms.js',
		'File Browser'			=> 'filebrowser.min.js',
		'jQuery Table Sorter'	=> 'jquery.tablesorter.min.js',
		'jQuery Quick Search'	=> 'jquery.quicksearch.min.js',
		'jQuery Color'			=> 'jquery.color.min.js',
		'jQuery Commander'		=> 'jquery.commander.min.js',
		'jQuery Uptime'			=> 'jquery.uptime.min.js',
		'jquery Clock'			=> 'jquery.jclock.min.js',
		'SWFUpload'				=> 'swfupload/swfupload.js',
		'SWFUpload Queue'		=> 'swfupload/swfupload.queue.js',
		'SWFUpload File Progress' => 'swfupload/fileprogress.js',
		'SWFUpload Handlers'	=> 'swfupload/handlers.js',
		'TinyMCE'				=> 'tinymce/jquery.tinymce.js',
		'Date Picker'			=> 'datepicker/js/datepicker.js',
		'Nano Scroller'			=> 'jquery.nanoscroller.min.js'
	);
	
	
	foreach( $scripts as $jsTitle => $jsfile ) {
		$path = SITE_ROOT.$jsDir.$jsfile;
		$contents = "// {$jsTitle}\r\n";
		$contents .= file_get_contents($path);
		$contents = preg_replace('#/\*.+?\*/#s', '', $contents);
		
		echo $contents."\r\n\r\n";
	}
	
?>