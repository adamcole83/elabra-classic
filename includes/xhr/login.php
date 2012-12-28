<?php
	
	// initialize classes
	require_once('../initialize.php');
	
	// Set variables
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	$domain = trim($_POST['domain']);
	$dn = "dc=edu";
	$ds = ldap_connect("ldap.missouri.edu",3268);
	$filter = 'sAMAccountName='.$usr;
	$attributes = array(
						'last_name'		=> 'sn',
						'email'			=> 'mail',
						'title'			=> 'title',
						'first_name'	=> 'givenname',
						'department'	=> 'department',
						'username'		=> 'samaccountname',
						'phone_number'	=> 'telephonenumber',
						'domain'		=> 'userprincipalname'
					);
	
	// Basic functions
	function giveJSON($arr) {
		die( json_encode($arr) );
	}
	function dieError($err) {
		giveJSON( array("error"=>$err) );
	}
	function bind($ds,$usr,$pwd) {
		if( ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3) /*&& ldap_start_tls($ds)*/ ) {
			return (   @ldap_bind($ds, $usr.'@umh.edu', $pwd)
					|| @ldap_bind($ds, $usr.'@col.missouri.edu', $pwd)
					|| @ldap_bind($ds, $usr.'@umsystem.umsystem.edu', $pwd)
					|| @ldap_bind($ds, $usr.'@missouri.edu', $pwd)
					|| @ldap_bind($ds, $usr.'@tig.mizzou.edu', $pwd))
				 ? true
				 : false
			;
		}
	}
	
	// Validate
	if(empty($username) OR empty($password))
	{
		dieError('Username or password invalid');
	}
	
	if(isset($_POST['bypass']) && $_POST['bypass'] == 'cms')
	{
		$initbind = bind($ds, $username, $password);
		if($initbind == true) {
			$_SESSION['user_id'] = $username;
			exit(giveJSON(array('first_name' => $username, 'last_name' => '')));
		}
	}
	
	// Find user in database
	$user = User::dbAuthenticate($username);
	
	if($user)
	{
		if($user->active != '1')
		{
			dieError('Your access is currently suspended.');
		}
		
		if($user->pawprintuser == '1')
		{
			// connect to ldap
			$initbind = bind($ds, $username, $password);
			if($initbind) {
				$session->login($user);
				echo giveJSON($user);
			}
		}
		else
		{
			$user = User::authenticate($username, $password);
			if($user)
			{
				$session->login($user);
				echo giveJSON($user);
			}
		}
	}
	else
	{
		dieError('Username or password invalid!');
	}
