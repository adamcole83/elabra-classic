<?php
	
	session_start();
	
	if(!$_SESSION['offline_redirect'])
	{
		$_SESSION['offline_redirect'] = base64_decode($_GET['r']);
	}
	
	if($_POST['submit'])
	{
		$path = explode('/', $_SESSION['offline_redirect']);
		$password = $path[1];
		
		if($_POST['password'] == $password)
		{
			$redirect = $_SESSION['offline_redirect'];
			unset($_SESSION['offline_redirect']);
			
			if(!$_SESSION['user_id'])
			{
				$_SESSION['user_id'] = 'TEMP';
			}
			header('Location: '.$redirect);
		}
		else
		{
			$error = true;
		}
	}
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
	<head>
		<title>Under Construction</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
		<link type="text/css" rel="stylesheet" media="screen" href="css/offline.css" />
		
		<?php if($error): ?>
		<style type="text/css">
			input[type="password"] { border-color: red; }
		</style>
		<?php endif; ?>
	</head>
	<body>
		<div id="wrapper">
			
			<h1>Under Construction</h1>
			<div class="container">
				<img src="images/construction-pic.png" alt="construction-pic" width="" height="" />
				<p>
					<?php if($error): ?>
					<span class="red">Incorrect password!</span>
					<?php else: ?>
					This site is currently in development.
					<?php endif; ?>
					<br />Please enter your password for access.
				</p>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<input type="password" name="password" />
					<input type="submit" name="submit" value="Enter" />
				</form>
				<p class="small">If you feel you have reached this message in error, please contact the office of communication at (573) 882-0348.</p>
			</div>
		</div>
	</body>
</html>
