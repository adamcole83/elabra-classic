<?
	require_once('includes/initialize.php');
	check_session();
	$message = $session->message();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
	<head>
		<title>Deploy Application &laquo; <? echo Department::find_by_id($_SESSION['department'])->name; ?> &laquo; Content Management System</title>
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
					<? $applications = listfiles(SITE_ROOT.'/applications/'); ?>
					<h2 class="tab-deploy">Deploy Application <span>DO NOT USE</span></h2>

					<div id="conn-list">
						<? foreach($applications as $app): ?>
							<div class="app">
								<div class="thumb" id="app-<? echo $app['id']; ?>">
<!-- 									<img src="includes/thumb.php?f=<? echo urlencode(trim($leadon.$app['directory'].$app['screenshot'])); ?>" /> -->
								</div><!-- .thumb -->
								<div class="info">
									<h3 id="title-<? echo $app['id'] ?>"><? echo $app['title']; ?></h3>
									<p id="description-<? echo $app['id']; ?>"><? echo $app['description'] ?></p>
									<ul>
										<li>Author: <span><a id="author-<? echo $app['id']; ?>" href="mailto:<? echo $app['email']; ?>"><? echo $app['author']; ?></a></span></li>
										<li>Filename: <span id="filename-<? echo $app['id'] ?>"><? echo $app['file']; ?></span></li>
									</ul>
									<ul>
										<li>Version: <span id="version-<? echo $app['id'] ?>"><? echo $app['version']; ?></span></li>
										<li>Last modified: <span><? echo $app['updated']; ?></span></li>
									</ul>
								</div><!-- .info -->
								<p class="newpack" onclick="NewPackage(<? echo $app['id']; ?>)">New Package</p>
								<p class="deploy" onclick="Browse('extract',<? echo $app['id'] ?>)">Deploy</p>
								<p class="dir" id="dir-<? echo $app['id'] ?>"><? echo $app['directory']; ?></p>
							</div><!-- .app -->
						<? endforeach; ?>
					</div><!-- #app-list -->
					<div id="version_select">
						<div class="container">
							<h1>Select release...</h1>
							<ul>
								<li><button type="button" class="vbtn">Major</button></li>
								<li><button type="button" class="vbtn">Minor</button></li>
								<li><button type="button" class="vbtn">Debugged</button></li>
							</ul>
						</div>
					</div>
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