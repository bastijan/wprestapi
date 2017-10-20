<?php
require_once 'filter.inc.php';
//TODO: triger_error zameniti sa user error funkcijom koja ce prikazati smislenu stranu korisniku
class FilterClass
{
	private static $instance;
	
	//filteri za registraciju
	private static $functions=array(); 

	//parametri za registraciju
	private static $input_parameters=array();
	
	//niz parametar=>filter za registraciju
	private static $input_parameters_functions=array();
	
	public static $paramFilter=array();
	
	public static $globalArrays=array();
	
	private function Filter()
	{
	
		//self::$paramFilter=$paramFilterArray;
		self::$paramFilter=array(
					'username' 		=>  'noCheck',
					'name' 			=>  'noCheck',
					'password' 		=>  'getStrParam',	
					'tos'			=>	'getCharOne',	
					'short'			=>	'getStrAny',
					'long'			=>	'getStrAny',
					'quantity'		=>	'getFloat',
					'price'			=>	'getFloat',
					'daily_budget'	=>	'getFloat',					
					'metric'		=>	'getChar',
					'currency'		=>	'getChar',
					'post_type'		=>	'getCharOne',
					'cat_id'		=>	'getInt',
					'limit' 		=>  'getInt',
					'offset' 		=>  'getInt',
					'page' 		=>  'getInt',
					'logged' 		=>  'getBool',
					'categories'	=>	'getStrAny',
					'category'		=>	'getInt',
					'tip'			=>	'getCharOne',
					'id'			=>	'getInt',
					'desiredPosts'	=>	'getInt',
					'fcbklist_values'=>	'getStrAny',
					'start'			=>	'getInt',
					'email'			=>	'noCheck',
					'remember'		=>	'getInt',
					'user_id'		=>	'getInt',
					'thing'			=>	'getStrParam',
					'order'			=>	'getChar',
					'thingid'		=>	'getInt',
					'shortdesc'		=>	'getName',
					'databank_id'	=>	'getInt',
					'address'		=>	'getStrAny',
					'city'			=>	'getName',
					'postcode'		=>	'getName',
					'country'		=>	'getName',					
					'tel'			=>	'getPhoneNumber',	
					'mob'			=>	'getPhoneNumber',	
					'www'			=>	'getStrAny',
					'adtype'		=>	'getStrParam',
					'scheme'		=>	'getStrParam',
					'width'			=>	'getInt',
					'height'		=>	'getInt',
					'page_id'		=>	'getInt',
					'title'			=>	'getStrAny',
					'text'			=>	'getStrAny',
					'company'		=>	'getStrAny',
					'regnumber'		=>  'getStrAny',
					'taxnumber'		=>  'getStrAny',
					'user_account'	=>	'getCharOne',
					'user_type'		=>	'getCharOne',
					'activation'	=>	'getStrAny',
					'idnumber'		=>	'getStrAny',
					'country_iso'	=>	'getCharTwo',
					'phone_2'		=>	'getNumbers',
					'contact_person'=>	'getStrAny',
					'full_name'		=>	'getStrAny',
					'domain'		=>	'getStrAny',
					'key'			=>	'getStrAny',
                    'ad_type'       =>  'getStrAny',
                    'ad_color'      =>  'getStrAny',
                    'ad_width'      =>  'getNumbers',
                    'ad_height'     =>  'getNumbers',
                    'partner_ad_id' =>  'getNumbers',
                    'month'		    =>	'getInt',
                    'year'		    =>	'getInt',
                    'date_from'     =>  'getStrAny',
                    'date_until'    =>  'getStrAny',
                    'tp'            =>  'getNumbers',
					'bank_account'	=>	'getStrAny',
					'bank_name'		=>	'getStrAny',
					'pgid'          =>  'getStrAny',
                    '_url'          =>  'getStrAny',
                    'pid'           =>  'getInt',
					'preclickurl'	=>	'getStrAny',
					'overtext'		=>	'getInt',
					'banners'		=>	'getInt',
					'urlparams'		=>	'getInt',
					'pwd1'			=>	'getStrParam',
					'bonus'			=>	'getNumbers',
					'keyword'		=>	'getStrParam',
					'lng'			=>	'getCharTwo',
					'opt'			=>	'getStrAny' //RUPA
				  );

		self::$globalArrays=array('GET_VARS','POST_VARS','COOKIE_VARS','SERVER_VARS');
	}
	
	static public function getInstance()
	{
		if(!isset(self::$instance))
		{
			$c=__CLASS__;
			self::$instance=new $c;
		}
		return self::$instance;
	}
	
/*******************************************************************************
*  Function:    registerFilters
*  Description: Registruje f-je kao filtere (setuje niz $functions)
*  Input: 		$filter_function - naziv filter f-je koja se registruje
*  Return:      
*******************************************************************************/
	
	static public function registerFilters($filter_functions)
	{
		self::$functions=array_merge(self::$functions,(array)$filter_functions);
	}
	
	
/*******************************************************************************
*  Function:    registerParameters
*  Description: Registruje parametre koje treba proveriti (setuje niz $input_parameters)
*  Input: 		$parameters - naziv filter f-je koja se registruje
*  Return:      
*******************************************************************************/
	
	static public function registerParameters($parameters)
	{
		self::$input_parameters=array_merge(self::$input_parameters,(array)$parameters);
	}
	
/*******************************************************************************
*  Function:    registerParametersFilters - za sada se ne koristi
*  Description: Registruje niz parametar=>filter (setuje niz $input_parameters_functions)
*  Input: 		$parameters_filter_functions - niz parametar=>filter koji se registruje
*  Return:      
*******************************************************************************/
	
	public function registerParametersFilters($parameters_filter_functions)
	{
		self::$input_parameters_functions=array_merge(self::$input_parameters_functions,(array)$parameters_filter_functions);
	}
	
/*******************************************************************************
*  Function:    call
*  Description: omotac za poziv filter f-je
*  Input: 		$method - naziv filter f-je
*				$input - niz sa parametrima koji se preciscavaju
*  Return:      $output - niz sa parametrima nakon preciscavanja
*******************************************************************************/	
	public function call($method, $input) 
	{
		$output=array();
		if(in_array($method, self::$functions)) 
		{
			foreach((array)$input as $key=> $value)
			{
				if(isset($input[$key])&& in_array($key,self::$input_parameters))
				//if(in_array($key,self::$input_parameters))
				{
					$output[$key]=call_user_func($method,$input[$key]);
					//echo "<br>call output: ";
					//print_r($output);
				}
				else {
					$errtxt='Ne postoji trazeni parametar ili nije setovan'.$key;
					$errmsg='Detected error at line '.__LINE__."\r\n";
					$errmsg.='Description: '.$errtxt;
					mail('bug@adbuka.com','Error in filter.class',$errmsg);	
					trigger_error($errtxt);
				}
				
			}
		}
		else {
			$errtxt='Ne postoji trazeni filter'.$method;
			$errmsg='Detected error at line '.__LINE__."\r\n";
			$errmsg.='Description: '.$errtxt;
			mail('bug@adbuka.com','Error in filter.class',$errmsg);				
			trigger_error($errtxt);
		}	
		return $output;
	}

/*******************************************************************************
*  Function:    callOne($method,$inputParam,$value)
*  Description: omotac za poziv filter f-je za samo jedan parametar i jednu vrednost
*  Input: 		$method - naziv filter f-je
*				$inputParam - parametrima koji se preciscava
*				$value - vrednost parametra
*  Return:      $output - niz sa parametrima nakon preciscavanja
*******************************************************************************/	

function callOne($method,$inputParam,$value)
{
 if(!in_array($method, self::$functions)) {echo "<br>FILTER $method NIJE REGISTROVAN<br>";}
 if(!in_array($inputParam,self::$input_parameters)) {echo "<br>PARAM $inputParam NIJE REGISTROVAN<br>";}
 
if (!is_null($value)) {
	if(in_array($method, self::$functions) && in_array($inputParam,self::$input_parameters)) 
	{
		$output=call_user_func($method,$value);
	}
	else
	{
		$errtxt='Ne postoji trazeni parametar ili filter '.$inputParam;
		$errmsg='Detected error at line '.__LINE__."\r\n";
		$errmsg.='Description: '.$errtxt;
		mail('bug@adbuka.com','Error in filter.class',$errmsg);	
		trigger_error($errtxt);
	}
	return $output;
}
else {
	return false;
}	
		
}

}
?>
