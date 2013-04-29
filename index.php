<?
	require_once('includes/initialize.php');
	check_session();
	$message = $session->message();

	$posts = new Content();
	$posts->department = $_SESSION['department'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
	<head>
		<title>Dashboard &laquo; <? echo Department::find_by_id($_SESSION['department'])->name; ?> &laquo; Content Management System</title>
		<? include('includes/head.php'); ?>
	    <script type="text/javascript">
	    	var gCurrentView = "dashboard";
			$(function() {
				$('#timestamp').clock();
			});
		</script>
	</head>
	<body>
		<? include('includes/tools.php'); ?>
		<div id="header">
			<? include('includes/header.php'); ?>
		</div><!-- #header -->
		<div id="main">
			<div class="container">
				<div id="content" class="dboard">
					<h2 class="tab-dashboard">Dashboard <span id="timestamp"></span></h2>
					<div class="clear"></div>
						
					<h3>Page Updates</h3>
					<div class="tableFull" style="margin-top:0px;">					
						<table class="selector" cellspacing="0">
							<thead>
								<tr>
									<th style="width:70%">Page</th>
									<th style="width:20%;">Date</th>
									<th style="width:10%;">Updated By</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($posts->getUpdated(10) as $post): ?>
								<?php $page_url = DOMAIN.'/'.Department::grab($_SESSION['department'])->subdir.'/'.$post->url.'.html'; ?>
								<tr>
									<td>
										<strong><a href="page.php?action=edit&id=<? echo $post->id ?>"><?php echo $post->title; ?></a></strong>
										<small class="floatRight"><a target="_blank" style="color:#999;" href="<?php echo $page_url; ?>">Preview</a></small>
									</td>
									<td><? echo date('D M j, Y g:i a', $post->updated); ?></td>
									<td><?php echo User::get($post->updatedBy)->username; ?></td>
								</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot>
								<tr>
									<th>Page</th>
									<th>Date</th>
									<th>Updated By</th>
								</tr>
							</tfoot>
						</table>
					</div>
					<br />
					<h3>Recent Pages</h3>
					<div class="tableFull" style="margin-top:0px;">					
						<table class="selector" cellspacing="0">
							<thead>
								<tr>
									<th style="width:70%">Page</th>
									<th style="width:20%;">Date</th>
									<th style="width:10%;">Created By</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($posts->getRecent(5) as $post): ?>
								<?php $page_url = DOMAIN.'/'.Department::grab($_SESSION['department'])->subdir.'/'.$post->url.'.html'; ?>
								<tr>
									<td>
										<strong><a href="page.php?action=edit&id=<? echo $post->id ?>"><?php echo $post->title; ?></a></strong>
										<small class="floatRight"><a target="_blank" style="color:#999;" href="<?php echo $page_url; ?>">Preview</a></small>
									</td>
									<td><? echo date('D M j, Y g:i a', $post->updated); ?></td>
									<td><?php echo User::get($post->updatedBy)->username; ?></td>
								</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot>
								<tr>
									<th>Page</th>
									<th>Date</th>
									<th>Updated By</th>
								</tr>
							</tfoot>
						</table>
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