<?
	// initialize classes
	require_once('../includes/initialize.php');
	
	// instantiate department
	$department = new Department;
	$department->id = 0;
	$department->activate();
	
	// instantiate content
	$content = new Content();
	$content->department = $department->id;
	$page = $content->getContent();
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- meta tags -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><? site_title($page->title) ?></title>
		<meta name="description" content="<? echo $page->description; ?>" />
		<? global_head(); ?>
		<link type="text/css" rel="stylesheet" media="screen" href="css/site.css" />
	</head>
	<body>
		<div id="wrapper">
			
			<? global_masthead(); ?>
			
			<div id="page" class="compact">
				<div id="header">
					<? require_once(LIB_PATH.DS.'header.php'); ?>
				</div><!-- #header -->
				<div id="content" class="breadcrumbs toolbox">
					<? include(CONTENT_PATH.DS.$page->url.'.php'); ?>
				</div><!-- #content -->
			</div><!-- #page -->
			
			<? global_footer(); ?>
			
		</div><!-- #wrapper -->
		<? global_analytics(); ?>
	</body>
</html>
