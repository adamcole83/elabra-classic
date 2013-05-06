<?
	require_once('includes/initialize.php');
	check_session();
	$message = $session->message();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
	<head>
		<title>Pages &laquo; <? echo Department::find_by_id($_SESSION['department'])->name; ?> &laquo; Content Management System</title>
		<? include('includes/head.php'); ?>
	    <script type="text/javascript">
			var gCurrentView = "<? echo get_page_info('view'); ?>";
			var gCurrentDepartment = "<? echo $_SESSION['department'] ?>";
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
					<? include("includes/layouts/".get_page_info('action')."_page.php") ?>
				</div>
			</div><!-- .container -->
		</div><!-- #main -->
		<?php include('includes/footer.php'); ?>
	</body>
</html>