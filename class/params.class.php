<?php
define("PARAM_POST", "POST");
define("PARAM_GET", "GET");
define("PARAM_FILES", "FILES");
define("PARAM_GLOBALS", "GLOBALS");
define("PARAM_REQUEST", "REQUEST");
define("PARAM_SERVER", "SERVER");
define("PARAM_ENV", "ENV");
require_once 'filter.class.php';


class Params
{
	private $params=array();
	function __construct()
	{
		$this->params['GET_VARS']=$_GET; 
		$this->params['POST_VARS']=$_POST; 
		$this->params['COOKIE_VARS']=$_COOKIE;
		$this->params['SERVER_VARS']=$_SERVER; 
		$this->params['FILES_VARS']=$_FILES; 
		// $GLOBALS a ne $_GLOBALS
		$this->params['GLOBALS_VARS']=$GLOBALS; 
		$this->params['REQUEST_VARS']=$_REQUEST; 
		$this->params['ENV_VARS']=$_ENV; 
	}

/*******************************************************************************
*  Function:    getParam
*  Description: 
*  Input: 		
*  Return:      
*******************************************************************************/
	
	public function getParam($from,$parName)
	{
		if(array_key_exists($parName,$this->params[$from]))
		{
			//return $this->params[$from][$parName];
			$output=array();
			$output[$parName]=$this->params[$from][$parName];
		}
		else
		{
			//return null;
			$output=null;
		}
		return $output;
	}
	
/*******************************************************************************
*  Function:    getAllParams
*  Description: uzima sve parametre iz niza get, post...
*  Input: 		
*  Return:      
*******************************************************************************/

	public function getAllParams($from) 
	{
        if(array_key_exists($from,$this->params)) 
        {
		    return $this->params[$from];
        } 
        else 
        {
            return array();
        }
	}


/*******************************************************************************
*  Function:    callFilterMethod($from,$method)
*  Description: 
*  Input: 		
*  Return:      
*******************************************************************************/	
	public function callFilterMethod($from,$method)
    {
    	$instance=FilterClass::getInstance();
    	$input=array();
    	$output=array();
    	$input=$this->getAllParams($from);
    	$output=$instance->call($method,$input);
    	return $output;
    	
    }
    
    
/*******************************************************************************
*  Function:    callFilterMethodOneParam($from,$parName,$method)
*  Description: zove filter samo za jedan parametar
*  Input: 		
*  Return:      
*******************************************************************************/   
    public function callFilterMethodOneParam($from,$parName,$method)
    {
		$instance=FilterClass::getInstance();
		$input=array();
		$output=array();
		$input=$this->getParam($from,$parName);
		$output=$instance->call($method,$input);
		return $output;
	}
	
	
/*******************************************************************************
*  Function:    findFilter($from) 
*  Description: zove filtere za bilo koji ulazni niz ($_GET, $_POST,...) i preciscava sve parametre u tom nizu
*  Input: 		$from - ime niza iz koga se uzimaju paramteri - GET_VARS, POST_VARS, COOKIE_VARS, SERVER_VARS...
*  Return:      $output - niz sa preciscenim parametrima kome se pristupa sa $output[$from]['ime parametra']
*******************************************************************************/	
	public function findFilter($from)
	{
	 	$instance=FilterClass::getInstance();
		$input=array();
		$input=$this->getAllParams($from);
//		$allFrom=array();
		foreach($input as $key=>$value)
		{
			$allFrom[$from][$key]=$value;	//ceo $from (npr. $_GET) niz
		}
		$output=array();
		/*
		echo "<br>findFilter<br>";
		echo "<br>allFrom: ";
		var_dump($allFrom);
		echo "<br>from: ".$from;
		*/
		$output=array();
		if (isset($allFrom))
		{
			foreach($allFrom[$from] as $key=>$value)
			{
//IZMENE-09012008
// proveris da li je setovano i da nije null			 
				if (isset(FilterClass::$paramFilter[$key]) && !is_null(FilterClass::$paramFilter[$key]))
				{
				//	echo "<br>paramFilter: ".FilterClass::$paramFilter[$key]."<br>".$key." ".$value;
					$output[$from][$key]=$instance->callOne(FilterClass::$paramFilter[$key],$key,$value);
				//	echo "<br>output from key: ".$output[$from][$key];
				}	
			}
		}
		return $output;
	}

/*******************************************************************************
*  Function:    findFilterAllArrays() 
*  Description: zove filtere za bilo sve ulazne nizove (cita ih is promenljive $gobalArrays u filter.class)
*  Input: 		trenutno definisani u $globalArrays - get, post, cookie i server (u construct kod filter, prebaci u conf)
*  Return:      $output - niz sa preciscenim parametrima kome se pristupa sa $output[$from]['ime parametra']
*******************************************************************************/
	public function findFilterAllArrays()
	{
	 	$output=array();
	 	$instance=FilterClass::getInstance();
	 	$globalArrays=FilterClass::$globalArrays;
		foreach($globalArrays as $key=>$value)
		{
			$output=$this->findFilter($value);
			$res[$value]=$output[$value];
			//echo "<br>findFilterAllArrays RESULT: ";
			//var_dump($res);
			//echo "<br>";
		}
		//return $output;
		return $res;
	}

//sa zadavanjem nizova iz kojih se zele parametri
	public function findFilterSomeArrays($arrays)
	{
	 	$output=array();
	 	$instance=FilterClass::getInstance();
	 	$res=array();
		foreach($arrays as $key=>$value)
		{
			$output=$this->findFilter($value);
			/*
			echo "<br>findFilterSomeArrays / output<br>";
			var_dump($output);
			*/
//IZMENE-09012008
// proveris da li je setovano i da nije null
			if (isset($output[$value]) && !is_null($output[$value]))
			{
				$res[$value]=$output[$value];	
			}
			
		}
		/*
		echo "<br>findFilterSomeArrays<br>";
		print_r($res);
		*/
		return $res;
	}
	
	function getStrParam($from,$parName)
	{
		$instance=FilterClass::getInstance();
		$input=array();
		$output=array();
		$input=$this->getParam($from,$parName);
		$output=$instance->call('getStrParam',$input);
		return $output;
	}
	
	function getIntPos($from,$parName)
	{
		$instance=FilterClass::getInstance();
		$input=array();
		$output=array();
		$input=$this->getParam($from,$parName);
		$output=$instance->call('getIntPos',$input);
		return $output;
	}
	

	
	function getBool($form,$paramName)
	{
	//sj 08.12.2011
		$instance=FilterClass::getInstance();
		$input=array();
		$output=array();
		$input=$this->getParam($from,$parName);
		$output=$instance->call('getBool',$input);
		return $output;			
	}

}
?>
