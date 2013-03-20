<?php

$usr = 'jenkinsac';
$dom = '@umh.edu';
//$pwd = 'd3st1Ny7';
$dn = "dc=edu";
$filter = 'sAMAccountName='.$_GET['user'];
$attributes = array('sn', 'samaccountname', 'title', 'telephonenumber', 'givenname', 'department', 'userprincipalname', 'mail');

$ds = ldap_connect("ldap.missouri.edu",3268); 

if(	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3)
	&& ldap_start_tls($ds))
{
	$ldapbind = ldap_bind($ds, $usr.$dom, $pwd);
	
	$sr = ldap_search($ds, $dn, $filter, $attributes);
	$entries = ldap_get_entries($ds,$sr);
	$entry = $entries[0];
	
	foreach( $entry as $key => $array ) {
		if(is_array($array)) {
			foreach( $array as $that => $value ) {
				if( $that !== 'count' )
					$user[$key] = $value;
			}
		}
	}
}


?>

<pre><? print_r($entries); ?></pre>

Done