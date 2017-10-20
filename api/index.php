<?php
ob_start("ob_gzhandler");
if ($_SERVER['REMOTE_ADDR']!='94.130.77.95') {exit('No direct script access allowed');}

header("Cache-Control: max-age=300");
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 300)); 
if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {   
// if the browser has a cached version of this image, send 304   
	header('Last-Modified: '.$_SERVER['HTTP_IF_MODIFIED_SINCE'],true,304);	   
	exit; 
}

date_default_timezone_set('Europe/Belgrade');

require ("/wp-config.php");

require ("logic.class.php");

require ("routes.inc.php");

?>