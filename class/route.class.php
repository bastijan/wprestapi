<?php
class Route {
    protected $route_match      = false;
    protected $route_call       = false;
    protected $route_call_args  = false;
	protected $parse_params		= false;
 
    protected $routes           = array( );
 
    public function __construct( ) {
 
    } // function __construct( )
 
public function pathroute() {
	global $conf;
   

	if (isset($_GET['_route_'])) {
		$pathroute=$_GET['_route_'];
		if ($conf["domain"]=='test.bktvnews.com' || $conf["domain"]=='www.adbuka.com'){
			//$pathroute=preg_replace("'_adbuka.com/'","",$pathroute);
			$pathroute=preg_replace("'test.bktvnews.com/'","",$pathroute);
		}
	}
	else {
		$pathroute="";
	}
	return $pathroute;
} 
    public function setRoutes( $routes ) {
        $this->routes = $routes;
		
    } // function setRoutes
 
    public function routeURL( $url = false ) {
        // Look for exact matches

		$method = $_SERVER['REQUEST_METHOD'];
		//provera: ruta je bez parametara
        if( isset( $this->routes[$method][$url] ) ) {
            $this->route_match = $url;
            $this->route_call = $this->routes[$method][$url];
            $this->callRoute();
            return true;
        }

        foreach( $this->routes[$method] as $path => $call ) {

            if( empty( $path ) ) {
                continue;
            }
			$routeParts = explode('/', $path);		// niz delova staze (path)
			$uriParts = explode('/', $url);			// niz delova originalne adrese (url)
			$uriPartsSize = sizeof($uriParts);		// velicina delova originalnog url
			
			if (preg_match("'/:'",$path,$check) == 1) {  //postoje oznaka za parametar i kakva
				$varDelimiter='/:';
			}
			else {
				$varDelimiter=':';
			}
			
			if (preg_match("'".$varDelimiter."'",$path,$check)){	// da li postoji znak /: u path?
				$numOfParams = substr_count($path, $varDelimiter); // bpoj parametara
		/*
		echo '<pre>';				
        echo 'broj parametara: ';
        var_dump($numOfParams);
        var_dump($path);
        */
				// da li je broj delova url jednak broju delova path? ako jeste mozda je ista osnova tj staticni deo adrese
				if (count($uriParts)==count($routeParts)){	
					
					$params = $this->parse_params($uriParts, $routeParts);
					//var_dump($params);
					if ($numOfParams == count($params)) {
						$urlModified="";
						
						$urlStat=strstr($path, $varDelimiter,true);
		/*							
		echo 'staticni deo url: ';						
		var_dump($urlStat);				
		echo 'izvorni url: ';		
		var_dump($url);
		echo 'poklapanje: ';				
		var_dump(strpos($url,$urlStat));		
		*/
						if(strstr($url,$urlStat)!==false) {
							//echo '<br/><h1>PRONADJEN!!!</h1><br/>'	;
							if( !in_array(substr($path,0,strpos($path,$varDelimiter)),$this->routes[$method])) {
								$path_pocetak=substr($path,0,strpos($path,$varDelimiter));
							}
							else {
								$path_pocetak=substr($path,0,strpos($path,$varDelimiter));						
							}
	/*
	echo '<pre>';
	var_dump($path);
	var_dump($call);
	var_dump($params);
	var_dump($uriPartsSize);

	*/
							$arrPathPocetak=explode("/",$path_pocetak);
								
	/*
	echo ' arrPathPocetak ';
	var_dump($arrPathPocetak);
	echo ' count '.count($arrPathPocetak);
	echo ' zbir '; echo count($params)+count($arrPathPocetak);	
	*/
							//if( preg_match("'^{$path_pocetak}'i",$url,$match2) && $uriPartsSize==(count($params)+count($arrPathPocetak))) {
							if ($arrPathPocetak[0]=='') {
								$countArrPathPocetak=0;
							}
							else {
								$countArrPathPocetak=count($arrPathPocetak);
							}

							if( $uriPartsSize==(count($params)+$countArrPathPocetak)) {	
									$this->route_match = $path;					  
									$this->route_call = $call;
									$this->route_call_args = $params;
							
									$this->callRoute();
									return true;			
							}							

						}						
						
						
					}	
				}	

			}
        }

        // If no match was found, call the default route if there is one
        if( $this->route_call === false ) {
            if( !empty( $this->routes[$method]['_not_found_'] ) ) {
                $this->route_call = $this->routes[$method]['_not_found_'];
                $this->callRoute( );
                return true;
            }
        }
 
    } // function routeURL( )
 
    private function callRoute( ) {
        $call = $this->route_call;
	
        if( is_array( $call ) ) {
            $call_obj = new $call[0]( );
			$func=$call[1];
			if ($this->route_call_args!=false) {
				//$call_obj->$call[1]( $this->route_call_args );
				$call_obj->$func( $this->route_call_args );
			}
			else {
				$call_obj->$func();
			}
            
        }
        else {
            $call( $this->route_call_args );
        }
    } // function callRoute
 


   /**
     * Get the parameter list found in the URI which matched a user defined route
     *
     * @param array $req_route The requested route
     * @param array $defined_route Route defined by the user
     * @return array An array of parameters found in the requested URI
     */
    function parse_params($req_route, $defined_route){
        $params = array();
		$size = sizeof($req_route);
        for($i=0; $i<$size; $i++){
            $param_key = $defined_route[$i];
			if ($param_key == '') {
				continue;
			} elseif($param_key[0]===':'){
                $param_key = str_replace(':', '', $param_key);
				if ($param_key!='partnerkey') {
					$params[$param_key] = strtolower($req_route[$i]);
				}	
				else {
					$params[$param_key] = $req_route[$i];
				}	
            }
        }
        return $params;
    }
} // class Route	
?>
