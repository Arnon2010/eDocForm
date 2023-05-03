<?php

$ldapserver = "ldap://ldap.rmutsv.ac.th";
//$ldapserver = "ldap://203.158.177.202"; // test ระบบ
$ldapport = 389;
$ldapbasedn = "ou=e-passport,dc=rmutsv,dc=ac,dc=th";
//$ldapadmin = 'uid=arnn.l,ou=people,ou=staff,ou=arit,ou=e-passport,dc=rmutsv,dc=ac,dc=th';


$encoding = "md5crypt";

class rmutsv_login
{
  function login($user,$passwd)
  {
    if(!($ds = ldap_connect($GLOBALS['ldapserver'], $GLOBALS['ldapport'])))  return FALSE;
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    if(!($r=ldap_bind($ds)))  return FALSE;
    if(!($sr=ldap_search($ds, $GLOBALS['ldapbasedn'], "(uid=$user)")))  return FALSE;
  
    $entry_count = ldap_count_entries($ds, $sr);

    if($entry_count != 1 )  return FALSE;
    if (!($entry = ldap_first_entry($ds, $sr)))  return FALSE;
    if (!($dn = ldap_get_dn($ds, $entry)))  return FALSE;

    ldap_close($ds);

    $ds = ldap_connect($GLOBALS['ldapserver'],$GLOBALS['ldapport']);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

    if(!($b = ldap_bind($ds,$dn,$passwd)))  return FALSE;

    ldap_close($ds);

    return TRUE;
  }

  function chpass($user, $opass, $npass)
  {
    if(!($ds = ldap_connect($GLOBALS['ldapserver'], $GLOBALS['ldapport'])))  return FALSE;

    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

    if(!($r=ldap_bind($ds)))  return FALSE;
    if(!($sr=ldap_search($ds, $GLOBALS['ldapbasedn'], "(uid=$user)")))  return FALSE;

    $entry_count = ldap_count_entries($ds, $sr);

    if($entry_count != 1 )  return FALSE;
    if (!($entry = ldap_first_entry($ds, $sr)))  return FALSE;
    if (!($dn = ldap_get_dn($ds, $entry)))  return FALSE;

    ldap_close($ds);

    $ds = ldap_connect($GLOBALS['ldapserver'],$GLOBALS['ldapport']);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

    if(!($b = ldap_bind($ds,$dn,$opass)))  return FALSE;
      
    $encodedpass = rmutsv_login::encode_password($npass, $GLOBALS['encoding']);
    if (!(ldap_mod_replace($ds, $dn, array('userpassword' => $encodedpass))))  return FALSE;

    ldap_close($ds);

    return TRUE;
  }

  function random_salt( $length )
  {
    $possible = '0123456789'.
                   'abcdefghijklmnopqrstuvwxyz'.
                   'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.
                   './';
    $str = "";
    mt_srand((double)microtime() * 1000000);

    while( strlen( $str ) < $length )
      $str .= substr( $possible, ( rand() % strlen( $possible ) ), 1 );
    return $str;
  }

  function encode_password ($password = "", $encoding = "clear") {
	  if (strcasecmp($GLOBALS['encoding'], "clear") == 0) {
		  $encodedpass = $password;
  	} elseif (strcasecmp($GLOBALS['encoding'], "crypt") == 0) {
	  	$encodedpass = "{CRYPT}".crypt($password);
	  } elseif (strcasecmp($GLOBALS['encoding'], "md5crypt") == 0) {
		  $encodedpass = "{CRYPT}".crypt($password, "$1$" . rmutsv_login::random_salt(9));
  	} elseif (strcasecmp($GLOBALS['encoding'], "md5") == 0) {
	  	$encodedpass = "{MD5}".base64_encode(pack("H*",md5($password)));
  	} elseif (strcasecmp($GLOBALS['encoding'], "ssha") == 0) {
	  	mt_srand((double)microtime()*1000000);
		  $salt = mhash_keygen_s2k(MHASH_SHA1, $password, substr(pack('h*', md5(mt_rand())), 0, 8), 4);
  		$encodedpass = "{SSHA}".base64_encode(mhash(MHASH_SHA1, $password.$salt).$salt);
  	} else {
	  	$encodedpass = "";
  	}
	  return($encodedpass);
  }
}
?>
