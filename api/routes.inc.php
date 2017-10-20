<?php
//data version
/*----------------------------------------------------------------------------------
//	RUTIRANJE
//  Ako treba, logic klasu zameniti odgovarajucom klasom gde se funkcija nalazi
// PRIMERI
// $routes['GET']['']                      			= array( 'logic', 'homePage' );
// $routes['GET']['_not_found_'] 						= 'logic_not_found';
// $routes['GET']['info.html']							= array( 'logic', 'info' );
// $routes['GET']['color/black']           			= array( 'logic', 'colorBlack' );
// $routes['GET']['color']                 			= array( 'logic', 'color' );
// $routes['GET']['^archive/year/(\d{4})\/(\d{2})$'] 	= array( 'logic', 'arhiva');
// $routes['GET']['korisnik/:ime/:prezime/:godina'] 	= array( 'logic', 'param');

// $routes['GET']['sitemap']	 						= 'sitemap';
// $routes['GET']['sitemapgz'] 						= 'sitemapgz';
----------------------------------------------------------------------------------*/

$routes['GET']['']                     				= array( 'logic', 'home' );
$routes['GET']['v']                      			= array( 'logic', 'version' );

$routes['GET']['_not_found_'] 						= array( 'logic', 'func_not_found' );

$routes['GET']['booking/:car_category/:duration']   = array( 'logic', 'bookingForm' ); //maybach|s-class
$routes['GET']['booking/:car_category']             = array( 'logic', 'bookingForm' ); //maybach|s-class
$routes['GET']['booking']                     		= array( 'logic', 'bookingForm' );

$routes['POST']['booking/auth']                 	= array( 'logic', 'bookingAuth' ); 
$routes['POST']['booking/preview']                  = array( 'logic', 'bookingPreview' ); 
$routes['POST']['booking/save']                    	= array( 'logic', 'bookingSave' ); 

$routes['POST']['booking/accept']                   = array( 'logic', 'bookingAcceptSave' ); 
$routes['POST']['booking/reject']                   = array( 'logic', 'bookingRejectSave' ); 
$routes['POST']['booking/transfer']                 = array( 'logic', 'bookingTransferSave' ); 
$routes['POST']['booking/busy']                 	= array( 'logic', 'bookingBusySave' ); 

$routes['GET']['thx']								= array( 'logic', 'thx' );

$routes['GET']['car/status/:id']					= array( 'logic', 'getCarStatus' );
$routes['POST']['car/status/']						= array( 'logic', 'postCarStatus' );

$routes['GET']['accept/:id']						= array( 'logic', 'acceptBooking' );
$routes['GET']['transfer/:id']						= array( 'logic', 'transferBooking' );
$routes['GET']['reject/:id']						= array( 'logic', 'rejectBooking' );

//send msg, accept, cancel
//payment
/*---------------------------------------------------------------------------
	LOGIN/REG
---------------------------------------------------------------------------*/

$routes['GET']['sign_in']                    		= array( 'logic', 'loginForm' );
$routes['POST']['login']							= array( 'logic', 'login' );				//ok
$routes['POST']['loginBooking']						= array( 'logic', 'loginBooking' );				//ok

$routes['GET']['sign_up']                    		= array( 'logic', 'registerForm' );
$routes['POST']['register']							= array( 'logic', 'register' );			//ok
$routes['POST']['registerBooking']					= array( 'logic', 'registerBooking' );			//ok

$routes['GET']['forgot']							= array( 'logic', 'reset_password_form' );
$routes['POST']['pwd/reset']						= array( 'logic', 'reset_password' );
$routes['GET']['code']								= array( 'logic', 'change_password' );
$routes['POST']['changepwd/save']					= array( 'logic', 'save_password' );
$routes['POST']['changepwd2/save']					= array( 'logic', 'save_password_v2' );

$routes['GET']['logout']							= array( 'logic', 'logout' );				//ok

$routes['GET']['activate/:activation']				= array( 'logic', 'activate_account');		
$routes['GET']['activate']							= array( 'logic', 'activate_form' );	
$routes['GET']['dashboard']							= array( 'logic', 'dashboard' );	

$routes['POST']['emailcheck']						= array( 'logic', 'isEmailRegistered'); 	//ok
$routes['POST']['emailcheck2']						= array( 'logic', 'isEmailRegistered2'); 	//ok

$routes['GET']['billing/settings']					= array( 'logic', 'billingSettings' );
$routes['GET']['billing/ok']						= array( 'logic', 'billingOKGet' );
$routes['POST']['billing/ok']						= array( 'logic', 'billingOKPost' );

$routes['GET']['drive/status']						= array( 'logic', 'driveStatus' );

if (!isset($_GET['cron']) || !isset($_GET['gen'])){
	$route = new Route( );
	$route->setRoutes( $routes );
	$pathroute=$route->pathroute();
	/*
	echo 'pocetni url: ';
	var_dump($pathroute);
	*/
	//$route->routeURL( strtolower(preg_replace( '|/$|', '', $pathroute )));
	$route->routeURL( preg_replace( '|/$|', '', $pathroute ));
/*
	echo '<pre>';
	echo "\n pronadjena ruta: ";
	var_dump($route->routeURL);
	die();
*/	
}
?>