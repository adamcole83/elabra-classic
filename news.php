<?
	require_once('includes/initialize.php');
	check_session();
	$message = $session->message();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
	<head>
		<title>News &laquo; <? echo Department::find_by_id($_SESSION['department'])->name; ?> &laquo; Content Management System</title>
		<? include('includes/head.php'); ?>
	    <script type="text/javascript">
			//<![CDATA[
			var gCurrentView = "<? echo get_page_info('view'); ?>";
			var gCurrentDepartment = "<? echo $session->user()->department ?>";
			//]]>
			<? if(!empty($message)): ?>
			$(function() {
				ShowDialog("<? echo $message; ?>");
			});
			<? endif; ?>

		</script>
		
	</head>
	<body>
		<? include('includes/tools.php'); ?>
		<div id="header">
			<? include('includes/header.php'); ?>
		</div><!-- #header -->
		<div id="main">
			<div class="container">
				<div id="content">
					<div style="display:block;" class="notification high">
						<p><strong>Scheduled Maintenance:</strong> Wednesday, March 20, 2013 9:00am - 12:00pm. During this time, you will not be able to access this system.</p>
					</div>
					<? include("includes/layouts/".get_page_info('action')."_news.php") ?>
				</div>
			</div><!-- .container -->
		</div><!-- #main -->
		<div id="loader">
			&nbsp;
		</div>
		<!--[if lte IE 7]>
		<div id="bwarn"><p>Please use Mozilla Firefox 3.6+, Google Chrome 9.0+, Apple Safari 5+ or Internet Explorer 8+ or you may risk losing data!</p></div>
		<![endif]-->
		<?php include_once('includes/googleanalytics.php'); ?>
	</body>
</html>