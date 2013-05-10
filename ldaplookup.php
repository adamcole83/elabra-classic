<?php
	
	require_once('includes/initialize.php');
	
	error_reporting(E_ALL);

	if( $_POST['user'] ) {
		$usr = $_POST['username'];
		$pwd = $_POST['password'];
		$dom = '@umh.edu';
		$dn = "dc=edu";
		$filter = 'sAMAccountName='.$_POST['user'];
		
		if($_POST['detail'] == 'no')
		{
			$attributes = array('sn', 'samaccountname', 'title', 'telephonenumber', 'givenname', 'department', 'userprincipalname', 'mail');
		}
		else{
			$attributes = array();
		}
		
		$ds = ldap_connect("ldap.missouri.edu",3268);
		
		if(	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3)
			&& ldap_start_tls($ds))
		{
			$ldapbind = ldap_bind($ds, $usr.$dom, $pwd);
			
			$sr = ldap_search($ds, $dn, $filter, $attributes);
			$entry = ldap_first_entry($ds,$sr);
			
			foreach( $entry as $key => $array ) {
				if(is_array($array)) {
					foreach( $array as $that => $value ) {
						if( $that !== 'count' )
							$user[$key] = $value;
					}
				}
			}
		}
		
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- meta tags -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Department Lookup | School of Medicine | University of Missouri</title>
		<meta name="description" content="" />
		<style type="text/css">
			#content form {
				margin: 20px auto;
				width: 260px;
			}
			#content form label {
				color: #666;
				font-weight: bold;
				display: block;
				margin: 3px 0;
			}
		</style>
	</head>
	<body>
		<div id="wrapper">
			
			<div id="page" class="off">
				<div id="header">
					<h2>LDAP Lookup</h2>
				</div><!-- #header -->
				<div id="content">
					<form action="<? $_SERVER['PHP_SELF'] ?>" method="post">
						<p>
							<label for="username">Your Username</label>
							<input type="text" id="username" name="username" value="<? echo $_POST['username']; ?>" />
						</p>
						<p>
							<label for="password">Your Password</label>
							<input type="password" id="password" name="password" />
						</p>
						<p>
							<label for="detail">Unfiltered?</label>
							<input type="radio" name="detail" value="yes" /> Yes<br />
							<input type="radio" name="detail" value="no" checked="checked" /> No
						</p>
						<p>
							<label for="user">Lookup User</label>
							<input type="text" id="user" name="user" value="<? echo $_POST['user'] ?>" />
						</p>
						<p>
							<input type="submit" value="Search" />
						</p>
					</form>
					
					<? if( isset($user) ): ?>
					<table class="styled" border="1" style="border-collapse:collapse;border-color:#ccc;">
						<thead>
							<tr>
								<th colspan="2">LDAP Results</th>
							</tr>
							<tr>
								<td>Attribute</td>
								<td>Value</td>
							</tr>
						</thead>
						<tbody>
							<? foreach( $user as $key => $value ): ?>
							<tr>
								<td><? echo $key ?></td>
								<td><? echo $value; ?></td>
							</tr>
							<? endforeach; ?>
						</tbody>
					</table>
					<? endif; ?>
				</div><!-- #content -->
			</div><!-- #page -->
		</div><!-- #wrapper -->
	</body>
</html>