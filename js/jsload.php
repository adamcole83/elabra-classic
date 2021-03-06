<?
	require_once('../includes/initialize.php');
	header('Content-type: text/javascript');

	$jsDir = '/js/';
	$scripts = array(

		'jQuery UI Custom'		=> 'jquery-ui-1.8.16.custom.min.js',
		'File Browser'			=> 'filebrowser.min.js',
		'jQuery Table Sorter'	=> 'jquery.tablesorter.min.js',
		'jQuery Quick Search'	=> 'jquery.quicksearch.min.js',
		'jQuery Color'			=> 'jquery.color.min.js',
		'jQuery Commander'		=> 'jquery.commander.min.js',
		'jQuery Uptime'			=> 'jquery.uptime.min.js',
		'jquery Clock'			=> 'jquery.jclock.min.js',
		'TinyMCE'				=> 'tinymce/jquery.tinymce.js',
		'Date Picker'			=> 'datepicker/js/datepicker.js',
		'Nano Scroller'			=> 'jquery.nanoscroller.min.js',
		'UploadiFive'			=> 'jquery.uploadifive.min.js',
		'qTip'					=> 'jquery.qtip-1.0.0-rc3.js',

		'Notification Center'	=> 'notifications.js',
		'Content Management'	=> 'cms.js',

	);


	foreach( $scripts as $jsTitle => $jsfile ) {
		$path = SITE_ROOT.$jsDir.$jsfile;
		$contents = "// {$jsTitle}\r\n";
		$contents .= file_get_contents($path);
		$contents = preg_replace('#/\*.+?\*/#s', '', $contents);

		echo $contents."\r\n\r\n";
	}

?>