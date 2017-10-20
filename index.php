<?php
ob_start("ob_gzhandler");
header('Content-type: application/vnd.api+json');
/*
$allowedIPs=array('93.86.35.86','94.130.77.95','178.221.147.1','178.149.81.225','178.222.151.173','109.92.196.5');

if (!in_array($_SERVER['REMOTE_ADDR'],$allowedIPs)) {
        if (!ip_in_range($_SERVER['REMOTE_ADDR'],'178.149.81.0/24')) {
                exit('No direct script access allowed');
        }
}    
*/        

require ("conf.inc.php");
require ("class/classlist.inc.php");

$param=new Params();
$logic=new Logic();

if(isset($_GET) || isset($_POST))
{
        $output=$param->findFilterSomeArrays(array('GET_VARS','POST_VARS'));
}
require ("routes.inc.php");

/**
 * Check if a given ip is in a network
 * @param  string $ip    IP to check in IPV4 format eg. 127.0.0.1
 * @param  string $range IP/CIDR netmask eg. 127.0.0.0/24, also 127.0.0.1 is accepted and /32 assumed
 * @return boolean true if the ip is in this range / false if not.
 */
function ip_in_range( $ip, $range ) {
	if ( strpos( $range, '/' ) == false ) {
		$range .= '/32';
	}
	// $range is in IP/CIDR format eg 127.0.0.1/24
	list( $range, $netmask ) = explode( '/', $range, 2 );
	$range_decimal = ip2long( $range );
	$ip_decimal = ip2long( $ip );
	$wildcard_decimal = pow( 2, ( 32 - $netmask ) ) - 1;
	$netmask_decimal = ~ $wildcard_decimal;
	return ( ( $ip_decimal & $netmask_decimal ) == ( $range_decimal & $netmask_decimal ) );
}
?>
