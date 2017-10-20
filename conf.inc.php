<?php
date_default_timezone_set('Europe/Belgrade');

ini_set('display_errors','1');
error_reporting(1);
error_reporting(E_ALL & ~E_NOTICE);

$conf = array();

$dbprefix="";
$server="";

$conf['dbhost']="HOST";
$conf['dbpass']="PASSWORD";
$conf['dbname']="NAME";
$conf['dbuser']="USER";

$server="staging";
if ($server="production") {
    $conf['domain'] = "domain.com";
}
else {
    $conf['domain'] = "dev.domain.com";
}
$conf['base_url'] = "http://".$conf['domain']."/";
?>