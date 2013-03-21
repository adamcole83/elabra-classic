<?php
	
	$expire = $_GET['t'];
	$time = time();
	
	if($time_to > 0)
	{
		$expire_readable = date('', $time + $time_to);
	}
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
	<head>
		<title>Undergoing Maintenance</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
		<link type="text/css" rel="stylesheet" media="screen" href="css/offline.css" />
		
		<?php if($error): ?>
		<style type="text/css">
			input[type="password"] { border-color: red; }
			
		</style>
		<?php endif; ?>
	</head>
	<body>
		<div id="wrapper">
			
			<h1>Undergoing Maintenance</h1>
			<div class="container">
				<img style="top: -20px;" src="images/maintenance-pic.png" alt="maintenance-pic" width="256" height="256" />
				<br /><br /><br />
				<p>This system is currently<br />undergoing maintenance.<br />Please check back later.</p>
			</div>
			
		</div>
		<?php include_once('includes/googleanalytics.php'); ?>
	</body>
</html>
