<?
	require_once('includes/initialize.php');
	check_session();
	$message = $session->message();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
	<head>
		<title>Calendar Events &laquo; <? echo Department::find_by_id($_SESSION['department'])->name; ?> &laquo; Content Management System</title>
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
					<h2 class="tab-calendar">Calendar <span>Today is <? echo date('l, F j, Y') ?> &nbsp;&middot;&nbsp; <? echo _p(0,'event'); ?></span></h2>
					<p>Coming soon...</p>
				</div>
			</div><!-- .container -->
		</div><!-- #main -->
		<?php include('includes/footer.php'); ?>
	</body>
</html>