<?
	// global initializer: needs to be relative to this file
	require_once('includes/initialize.php');
	// logout
	if($_GET['do']=='logout') $session->logout();
	// if user is logged in, redirect to index
	if($session->is_logged_in()) redirect_to('index.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
		<title>Login « Content Management System</title>
		
		<!-- School of Medicine SEO -->
		<meta name="description" content="">
		<meta name="author" content="Steve Thompson, Adam Jenkins, Beau Borucki">
		<meta name="copyright" content="Copyright (c) 2011 - The Curators of the University of Missouri">
		
		<!-- Styles -->
		<link type="text/css" rel="stylesheet" media="screen" href="css/login3.css">
		
		<!-- Scripts -->
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
	</head>
	<body>
		<img class="logo" src="images/stackedlogo-bg-large.png" alt="stackedlogo-bg-large" width="131" height="150" />
		<div id="content">
			<h1>Secure Access</h1>
			<noscript><p class="noscript">Please enable javascript to login.</p><style type="text/css">#login,h1{display:none;}</style></noscript>
			
			<form id="login" class="auth" action="" method="post">
				<div id="dialog">Authentication required.</div>
				<label for="username">Pawprint</label>
				<div class="input-container">
					<input type="text" id="username" name="username" class="required" />
				</div>
				
				<label for="password">Password</label>
				<div class="input-container">
					<input type="password" id="password" name="password" class="required" />
				</div>
				
				<a id="retrievepwd">Forgot password?</a>
				<span id="bubbledrop"></span>
				<input type="submit" name="submit" class="submit" value="Submit" />
			</form>			
		</div><!-- #content -->
		
		<script type="text/javascript" src="js/jquery.easing.1.3.min.js"></script>
		<script src="js/notifications.js"></script>
		<script type="text/javascript" src="js/jquery.login.js"></script>
		<script type="text/javascript">
			$('#login').auth({
				dialogTop		: '+=25',
				wiggleDialog	: false
			});
		</script>
		<?php include_once('includes/googleanalytics.php'); ?>
	</body>
</html>
