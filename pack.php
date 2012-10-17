<?
	require_once('includes/initialize.php');
	check_session();
	$message = $session->message();

	$dataString = base64_decode($_GET['q']);
	$dataList = explode('&', $dataString);
	foreach($dataList as $query){
		$q = explode('=', $query);
		$newpack[$q[0]] = $q[1];
	}
	$title = ($_GET['q']) ? "Repackage Application" : "Package New Application";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
	<head>
		<title>Pack Application &laquo; <? echo Department::find_by_id($_SESSION['department'])->name; ?> &laquo; Content Management System</title>
		<? include('includes/head.php'); ?>
	    <script type="text/javascript">
			//<![CDATA[
			var gCurrentView = "pack";
			var gCurrentDepartment = "<? echo $_SESSION['department']; ?>";
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
				
					<h2 class="tab-package"><? echo $title; ?> <span>DO NOT USE</span></h2>
					<div id="alert-box">&nbsp;</div>
					<form id="editor" name="packer" action="" method="post">
						<table>
							<tr>
								<th><label for="">Application Name</label></th>
								<td><input type="text" id="name" name="name" value="<? echo $newpack['name']; ?>" /></td>
								<td><label class="error" for="name" id="name_error">This field is required.</label></td>
							</tr>
							<tr>
								<th><label for="">Application Version</label></th>
								<td><input type="text" id="version" name="version" value="<? echo ($newpack['version'])?$newpack['version']:'1.0.0'; ?>" /></td>
								<td><label class="error" for="version" id="version_error">This field is required.</label></td>
							</tr>
							<tr>
								<th><label for="">Application Description</label></th>
								<td><textarea id="description" name="description"><? echo $newpack['description']; ?></textarea></td>
								<td><label class="error" for="description" id="description_error">This field is required.</label></td>
							</tr>
							<tr>
								<th><label for="">Application Change Log</label></th>
								<td><textarea id="changelog" name="changelog"></textarea></td>
								<td><label class="error" for="changelog" id="changelog_error">This field is required.</label></td>
							</tr>
							<tr>
								<th><label for="">Developer's Name</label></th>
								<td><input type="text" id="author" name="author" value="<? echo $newpack['author']; ?>" /></td>
								<td><label class="error" for="author" id="author_error">This field is required.</label></td>
							</tr>
							<tr>
								<th><label for="">Author's Email Address</label></th>
								<td><input type="text" id="email" name="email" value="<? echo $newpack['email']; ?>" /></td>
								<td><label class="error" for="email" id="email_error">This field is required.</label></td>
							</tr>
							<tr>
								<th><label for="">Screenshot</label></th>
								<td><input disabled="disabled" type="text" id="screen" name="screen" value="" />
									<button type="button" onclick="Upload()">Upload...</button></td>
								<td><label class="error" for="screen" id="screen_error">This field is required.</label></td>
							</tr>
							<tr>
								<th><label for="">Development Directory</label></th>
								<td><input disabled="disabled" type="text" id="directory" name="directory" value="" />
									<button type="button" onclick="Browse('open')">Browse...</button></td>
								<td><label class="error" for="directory" id="directory_error">This field is required.</label></td>
							</tr>
							<tr>
								<td colspan="2">
									<div class="controls">
										<button type="button" onclick="Pack()">Package</button>
									</div>
								</td>
							</tr>
						</table>
					</form>
					
				</div>
			</div><!-- .container -->
		</div><!-- #main -->
		<div id="loader">
			&nbsp;
		</div>
		<!--[if lte IE 7]>
		<div id="bwarn"><p>Please use Mozilla Firefox 3.6+, Google Chrome 9.0+, Apple Safari 5+ or Internet Explorer 8+ or you may risk losing data!</p></div>
		<![endif]-->
	</body>
</html>