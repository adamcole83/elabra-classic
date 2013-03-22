<?
require_once('includes/initialize.php');

error_log("GIT IS WORKING");

// if logged in, bypass
if($session->is_logged_in()){
	//redirect_to('logout.php?r=changepassword.php');
}

$sitename = ($_GET['site']) ? base64_decode($_GET['site']) : SITE_NAME;
$username = ($_GET['u']) ? base64_decode($_GET['u']) : null;
$redirect = 'login.php';

// check for messages
$message = $session->message();

$_user = new User();

if($_POST['submit']){
	$user = $_user->find_by_username($_POST['username']);
	if($user){
		if($_POST['oldpassword'] != $_POST['newpassword']) {
			if($_POST['newpassword'] == $_POST['confirmpassword']) {
				
				$_user->id = $user->id;
				$_user->password = $_POST['newpassword'];
				$_user->active = 1;
				if($_user->save()){
					$_user->timestamp($user->id);
					redirect_to($redirect);
				}	
			}else{ $message = "New password does not match confirmation."; }
		}else { $message = "Old password and new password cannot match."; }
	}else{ $message = "User was not found."; }
}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>University of Missouri - School of Medicine | Content Manager | <? echo $sitename; ?></title>
		<link rel="stylesheet" type="text/css" media="screen" href="css/screen.css" />
	</head>
	<body id="login" style="margin: 40px auto; width: 300px;">
		<div id="wrapper">
			<h1><img src="images/somLogo197x32.png" alt="University of Missouri - School of Medicine" width="197" height="32" /></h1>
			
			<div id="container">
				<h2><? echo $sitename ?></h2>
				<h3>Change Password</h3>
				<?php echo output_message($message); ?>
				<form id="login" action="<? $_SERVER['PHP_SELF'] ?>" method="POST">
					<table cellpadding="5" cellspacing="5">
						<tr>
							<td><label for="username">Username</label></td>
							<td><input type="text" id="username" name="username" value="<?php echo $username; ?>" /></td>
						</tr>
						<tr>
							<td><label for="oldpassword">Old Password</label></td>
							<td><input type="password" id="oldpassword" name="oldpassword" value="" /></td>
						</tr>
						<tr>
							<td><label for="newpassword">New Password</label></td>
							<td><input type="password" id="newpassword" name="newpassword" value="" /></td>
						</tr>
						<tr>
							<td><label for="confirmpassword">Confirm Password</label></td>
							<td><input type="password" id="confirmpassword" name="confirmpassword" value="" /></td>
						</tr>
						<tr>
							<td colspan="2"><input class="button floatright" type="submit" name="submit" value="Update" /></td>
						</tr>
					</table>
				</form>
			</div><!-- #container -->
		</div><!-- #wrapper -->
		<div id="legal">
			<h4>Content Manager</h4>
			<p>&copy;<? echo date('Y') ?>, University of Missouri - School of Medicine</p>
			<p>Office of Communications, All Right Reserved</p>
		</div><!-- #legal -->
		<?php include_once('includes/googleanalytics.php'); ?>
	</body>
</html>