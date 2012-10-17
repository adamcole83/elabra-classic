<?php
	
	// initialize classes
	require_once('../initialize.php');
	
	// declare variables
	$usr = trim($_POST['username']);
	$pwd = trim($_POST['password']);
	$dom = '@umh.edu';
	$dn = "dc=edu";
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
	
	// declare giveJSON function
	function giveJSON($arr) {
		die( json_encode($arr) );
	}
	
	// declare error function
	function dieError($err) {
		giveJSON( array("error"=>$err) );
	}
		
	// ldap bind
	function bind($ds,$usr,$pwd) {
		if( ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3) && ldap_start_tls($ds) ) {
			return @ldap_bind($ds, $usr.$dom, $pwd);
		}
	}
	
	// connect to ldap
	$ds = ldap_connect("ldap.missouri.edu",3268);
	$initbind = bind($ds, $usr.$dom, $pwd);
	
	// search for user in ldap
	if( empty($usr) && empty($pwd) )
		dieError('User not provided.');
	
	if($initbind) {
		// search ldap for user data
		$sr = ldap_search($ds, $dn, $filter, array_values($attributes));
		$entries = ldap_get_entries($ds,$sr);
		$entry = $entries[0];
		
		// organize entry
		foreach( $entry as $key => $arr ){
			if( is_array($arr) ){
				foreach( $arr as $that => $value ) {
					if( $that !== 'count' )
						$ldap[$key] = $value;
				}
			}
		}
	}
	
	// auto register user
	$found_user = User::dbAuthenticate($usr);
	if( !$found_user && $initbind ) {
		$dept = Department::find_by_name($ldap['department']);
		$user = new User();
		foreach( $attributes as $key => $value ) {
			$user->$key = $ldap[$value];
		}
		$user->department	= $dept ? $dept->id : 12;
		$user->group_id		= $_POST['group_id'];
		$user->pawprint		= 1;
		$user->save();
		$found_user = User::dbAuthenticate($usr);
		
		// notify admin of registration
		foreach( $ldap as $k => $v ) $data .= "$k: $v\r\n";
		mail(IT_EMAIL, 'User Registration', $usr." has been registered.\r\n\r\n".$data);
	}
	
	// if initbind and user found, log in or throw error
	if( $initbind && $found_user ) {
		$session->login($found_user);
	}
	
	// return object
	echo giveJSON($ldap);
		
?>