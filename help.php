<?
	require_once('includes/initialize.php');
	check_session();
	$message = $session->message();
	include('includes/uptime.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
	<head>
		<title>Help &amp; Support &laquo; <? echo Department::find_by_id($_SESSION['department'])->name; ?> &laquo; Content Management System</title>
		<? include('includes/head.php'); ?>
	    <script type="text/javascript">
			var gCurrentView = "<? echo get_page_info('view'); ?>";
			$(function() {
				$('#uptime').serverUptime({ upSeconds: <? echo $uptimeSecs; ?> });
			});
		</script>
		<link href="https://d3jyn100am7dxp.cloudfront.net/assets/widget_embed.cssgz?1335938180" media="screen" rel="stylesheet" type="text/css" />
		<!--If you already have fancybox on the page this script tag should be omitted-->
		<script src="https://d3jyn100am7dxp.cloudfront.net/assets/widget_embed_libraries.jsgz?1335938181" type="text/javascript"></script>

	</head>
	<body>
		<? include('includes/tools.php'); ?>
		<div id="header">
			<? include('includes/header.php'); ?>
		</div><!-- #header -->
		<div id="main">
			<div class="container">
				<div id="content">
					<h2 class="tab-help">Help &amp; Support <span id="uptime"><?php echo $staticUptime; ?></span></h2>
					<div id="alert-box" <?php if($message): ?>style="display:block;"<?php endif; ?>><p><?php echo $message; ?></p></div>
					
                    <p>If you are needing help or support with this system, please have a screenshot and a detailed description (including steps taken to cause the issue) readily available and shoot us an email at:  <a href="mailto:medweb@health.missouri.edu">medweb@health.missouri.edu</a>.  We will get back to you as soon as we can!  Thank you.</p>
                    
                    
                    
                    <!--<p>If you are needing help or support with this system, <strong>please have a screenshot and a detail description (including steps taken to cause the issue) readily available and complete the form below</strong>. A support agent will be with you within 48 hours. <strong>Support will only be available via this form.</strong></p>
					
					<center>
					<iframe src ="http://som.desk.com/customer/emails/new" width="670" height="100%" border="0" scrolling="no" style="border:none;"></iframe>
					</center>-->
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