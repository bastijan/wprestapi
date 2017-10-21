<?php
//data version
/*----------------------------------------------------------------------------------
// ROUTING
// EXAMPLES
// $routes[HTTP_METHOD][path]					= array(CLASS, METHDO_NAME)
// $routes['GET']['']                      			= array( 'logic', 'homePage' );
// $routes['GET']['_not_found_'] 				= 'logic_not_found';
// $routes['GET']['info.html']					= array( 'logic', 'info' );
// $routes['GET']['color/black']           			= array( 'logic', 'colorBlack' );
// $routes['GET']['color']                 			= array( 'logic', 'color' );
// $routes['GET']['^archive/year/(\d{4})\/(\d{2})$'] 	= array( 'logic', 'arhiva');
// $routes['GET']['korisnik/:ime/:prezime/:godina'] 	= array( 'logic', 'param');

// $routes['GET']['sitemap']	 				= 'sitemap';
// $routes['GET']['sitemapgz'] 					= 'sitemapgz';
----------------------------------------------------------------------------------*/

$routes['GET']['data']                     			= array( 'logic', 'ws' );

$routes['GET']['data/vendor/usermeta'] 				= array( 'logic', 'getVendorUsermeta' );
$routes['GET']['data/vendor/usermeta/:id'] 			= array( 'logic', 'getVendorUsermeta' );

$routes['GET']['data/vendor/customers/:id']			= array( 'logic', 'getVendorCustomersByVendorId' );

$routes['GET']['data/users'] 					= array( 'logic', 'getUsersShort' );
$routes['GET']['data/users/usermeta'] 				= array( 'logic', 'getUsersUsermeta' );
$routes['GET']['data/users/usermeta/:id'] 			= array( 'logic', 'getUsersUsermeta' );

$routes['GET']['data/user/:id'] 				= array( 'logic', 'getUserById' );

$routes['GET']['data/gen/:table']           			= array( 'logic', 'genericTableManipulation' );
$routes['GET']['data/gen/:table/:id']          			= array( 'logic', 'genericTableManipulation' );
$routes['POST']['data/gen/:table']           			= array( 'logic', 'genericTableManipulation' );
$routes['PUT']['data/gen/:table/:id']          			= array( 'logic', 'genericTableManipulation' );

$routes['GET']['data/postmeta']					= array( 'logic', 'postmetaTableManipulation' ); 
$routes['GET']['data/postmeta/:post_id']			= array( 'logic', 'postmetaTableManipulation' ); 
$routes['POST']['data/postmeta/:post_id']			= array( 'logic', 'postmetaTableManipulation' );
$routes['PUT']['data/postmeta/:post_id']			= array( 'logic', 'postmetaTableManipulation' );
/** OLD **/

$routes['GET']['data/latest/:limit/:page']       		= array( 'logic', 'getLatestPosts' );
$routes['GET']['data/latest/:limit']           			= array( 'logic', 'getLatestPosts' );
$routes['GET']['data/latest']                			= array( 'logic', 'getLatestPosts' );

$routes['GET']['data/popular/:limit/:page']     		= array( 'logic', 'getMostPopularPosts' );
$routes['GET']['data/popular/:limit']           		= array( 'logic', 'getMostPopularPosts' );
$routes['GET']['data/popular']                  		= array( 'logic', 'getMostPopularPosts' );

$routes['GET']['data/favorite']                   		= array( 'logic', 'getFavoritePosts' );

$routes['GET']['data/slider']                      		= array( 'logic', 'getFavoriteNewsAndImage' );
$routes['GET']['data/post/:id']                     		= array( 'logic', 'getPostByID' );
$routes['GET']['data/category/top']               		= array( 'logic', 'getTopCategoryList' );
$routes['GET']['data/topmenu']                    		= array( 'logic', 'getTopMenu' );

$routes['GET']['data/catbyname/:name/:limit/:page'] 		= array( 'logic', 'getPostsByCatName' );
$routes['GET']['data/catbyname/:name/:limit'] 			= array( 'logic', 'getPostsByCatName' );
$routes['GET']['data/catbyname/:name'] 				= array( 'logic', 'getPostsByCatName' );

$routes['GET']['data/subcatbyparent/:name/:limit/:page'] 	= array( 'logic', 'getPostsByParentName' );
$routes['GET']['data/subcatbyparent/:name/:limit'] 		= array( 'logic', 'getPostsByParentName' );
$routes['GET']['data/subcatbyparent/:name'] 			= array( 'logic', 'getPostsByParentName' );

$routes['GET']['data/category/:id/:limit/:page']  		= array( 'logic', 'getPostsByCatID' );
$routes['GET']['data/subcategory/:id']            		= array( 'logic', 'getSubCategoryListForParentID' );
$routes['GET']['data/allposts']                 		= array( 'logic', 'getPostsWOutIzdvajamo' );
$routes['GET']['data/allposts/:limit/:page']        		= array( 'logic', 'getPostsWOutIzdvajamo' );
$routes['GET']['data/:url']                      		= array( 'logic', 'getPost' );

$routes['GET']['data/menu']                      		= array( 'logic', 'jsonTopMenu' );
$routes['GET']['data/footer/menu']                  		= array( 'logic', 'jsonFooterMenu' );

$routes['GET']['data/related/:id/:limit']           		= array( 'logic', 'getRelatedPosts' );
$routes['GET']['data/pages/:limit/:page']           		= array( 'logic', 'getPages' );




if (!isset($_GET['cron']) || !isset($_GET['gen'])){
	$route = new Route( );
	$route->setRoutes( $routes );
	$pathroute=$route->pathroute();
	$route->routeURL( preg_replace( '|/$|', '', $pathroute ));
}
?>
