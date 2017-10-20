<?php
class dbClass {

	private $db;
	private $queryHandler;
	private $found_rows=0;
	private static $instance;
	private $version;


function __get($var){
		if (property_exists($this,$var) && (strpos($var,"pri_") !== 0) ) {
           return $this->{$var};
		}   
		else {
           throw new Exception("No such property");
		} 
}

function __set($var,$value)
{
	if (!property_exists($this,$var) && (strpos($var,"pri_") !== 0) ) {
			throw new Exception("$var: No such property");
	}
	if ( in_array($var, $this->read_only, true)) {
		throw new Exception ("$var: read-only property");
	}	
	else {
		$this->$var=$value;
	}
}



//function db($dbhost,$dbuser,$dbpass,$dbname)
function __construct($dbhost,$dbuser,$dbpass,$dbname)
{
	$this->db = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
	if(mysqli_connect_errno()) {
		throw new Exception (mysqli_connect_error());
	}
	
	$this->db->query("SET NAMES UTF8");
	$this->db->query("SET CHARACTER SET utf8");
	$this->db->query("SET COLLATION_CONNECTION='utf8_general_ci'");	
	$this->db->query("SET sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");

	$this->queryHandler = new ModuleQueryHandler;
	$this->version = $this->db->server_version;
//print 'verzija servera';	
}

public function version() {
	return $this->version;
}

static function getInstance($dbhost,$dbuser,$dbpass,$dbname)
{
	if(!isset(self::$instance))
	{
		self::$instance=new dbClass($dbhost,$dbuser,$dbpass,$dbname);	
	}
	return self::$instance;
}

function executeStrSql($strQuery) {
	$stmt = $this->db->prepare($strQuery);

	if (!$stmt)
	{
		echo '<pre>';
		die ("Greska! ".$this->db->error.'<br/>'.$strQuery);
	}
	$stmt->execute();
	$arrData=$this->fetchQueryResultArray($stmt);
	return $arrData;
}

function insertUpdateStrSql($strQuery) {

	//$stmt = $this->db->prepare($this->db->real_escape_string($strQuery));
	$stmt = $this->db->prepare($strQuery);

	if (!$stmt)
	{
		echo '<pre>';
		die ("Greska! ".$this->db->error.'<br/>'.$strQuery);
	}
	$stmt->execute();
	return $stmt;
}

function prepareSQL($queryArray, $paramValues = false, $limit=false,$offset=false) {

	if (!is_array($queryArray) || count($queryArray)<1)
	{
		throw new Exception ("No query specified");		
	}
	
	if ($paramValues === false && strlen($queryArray['paramType'])>0)
	{
			throw new Exception ("No parameters supplied");
	}
	
	$strLimit=$strOffset="";
	
	if (isset($limit) && $limit!=false && $limit!="" && $limit !=0)
	{
	 	$strLimit=" LIMIT ".$limit;
		$strOffset=" OFFSET 0";
		if (isset($offset) && $offset!=false && $offset!="")
		{
			$strOffset=" OFFSET ".$offset;
		}
	}
	$strQuery=$queryArray['query'] . $strLimit . $strOffset;

	$stmt = $this->db->prepare($strQuery);

	

	if (!$stmt)
	{
		echo '<pre>';
		die ("Greska! ".$this->db->error.'<br/>'.$strQuery);
	}
	
	if ($paramValues !== false) {

		if (is_array($paramValues)) {
			if (count($paramValues) !== strlen($queryArray['paramType']))
			{
				$msgtxt='<pre>Detected error at line '.__LINE__."<br/>";
				$msgtxt.="Description Wrong number of supplied parameter values. Supplied: ".count($paramValues)." Expected: ".strlen($queryArray['paramType']). "Query: {$queryArray['query']}<br/>";
				$msgtxt.="\r\n";
				$msgtxt.='SQL: '.serialize($strQuery);
				$msgtxt.="\r\n";
				$msgtxt.='SQL: '.serialize($paramValues);				
				$msgtxt.="</pre>";		
				throw new Exception ($msgtxt);				
			}
			
			$params = array($queryArray['paramType']);

/*
			foreach ($paramValues as $p)
			{
				$params[] = $p;				
			}
*/			
			foreach ($paramValues as $key => $value)
			{
				$params[] = &$paramValues[$key];				
			}


			call_user_func_array(array($stmt, "bind_param"), $params);
			
		} else {
			if (strlen($queryArray['paramType'])>1)
			{			
				throw new Exception ("Wrong number of supplied parameter values: query expects more then one");				
			}
			//echo " X: ".$paramValues;
			$stmt->bind_param($queryArray['paramType'], $paramValues);
		}
	} 

	$stmt->execute();
	return $stmt;
}


function fetchQueryResult($stmt) {
	$stmt->store_result();
	$result=$stmt->result_metadata();
	$outputRes=array();
	while($column=$result->fetch_field()) {
		$outputRes[]=&$results[$column->name];
	}
	call_user_func_array(array($stmt,'bind_result'),$outputRes);
	$stmt->bind_result($outputRes);
	$stmt->fetch();
	$stmt->close();
	return $outputRes;
}

function fetchQueryResultArray($stmt) {
	$stmt->store_result();
	$result=$stmt->result_metadata();
	$outputRes=array();
	while($column=$result->fetch_field())
	{
		$outputRes[]=&$results[$column->name];
	}
	call_user_func_array(array($stmt,'bind_result'),$outputRes);
	$resultArray = array();
	$count=0;
	while($stmt->fetch()) {
			foreach (array_keys($results) as $c)
				$resultArray[$count][$c] = $results[$c];
			$count++;
	}
	$stmt->close();
	return $resultArray;
}

function updateInsertAffectedRows($stmt)
{
	return $stmt->affected_rows;
}

function updateInsertAffectedRowsGetID($stmt)
{
	$stmt->affected_rows;
	$ret=$stmt->insert_id;
	return $ret;
}


function baci($arg)
{
	$this->logit("Fatal Error: $arg");
	throw new Exception($arg);
}

function commit() 
{
	$this->db->commit();
	$this->db->autocommit(TRUE);
}


function beginTransaction() 
{
	$this->db->autocommit(FALSE);
}


function rollback() 
{
	$this->db->rollback();
	$this->db->autocommit(TRUE);
}



function getRow($sql)
{

	$res=$this->db->query($sql) or $this->baci($this->db->error . " in db.getRow");
	$row=$res->fetch_assoc();
	return $row;
}

function UACheckAndUpdate($UserAgent)
{

	$sql="SELECT IDUserAgent FROM UserAgent WHERE Vrednost=?";

	$stm=$this->db->prepare($sql);
	$stm->bind_param("s",$UserAgent);
	$stm->execute();
	$stm->bind_result($IDUserAgent);
	$stm->fetch();
	$stm->close();

	$this->logit("Checking UA");

	if ($IDUserAgent)
		return $IDUserAgent;

	$this->logit("Couldn't find, put it into UserAgentNew");

	$sql="INSERT INTO UserAgentNew(Vrednost) VALUES (?)";

	$stm=$this->db->prepare($sql) or $this->baci($this->db->error . " in UACheckAndUpdate");
	$stm->bind_param("s",$UserAgent);
	$stm->execute();
	$r=$stm->affected_rows;
	$this->logit("UserAgentNew affected rows: $r");

	//Fallback...
	$uarr=explode("/",$UserAgent);

	// Ako u UA nema slash-a ne mozemo nista vise da uradimo
	if (!(is_array($uarr)))
		return 0;

	// Za sada ne postoji mehanizam po kom bismo prepoznali telefon ciji se browser predstavlja kao
	// Mozilla ili Opera pa odustajemo
	if ($uarr[0] == "Mozilla" or $uarr[0] == "Opera")
		return 0;

	$uar=$uarr[0]."%";

	$sql="SELECT IDUserAgent FROM UserAgent WHERE Vrednost LIKE ?";
	$stm=$this->db->prepare($sql);
	$stm->bind_param("s",$uar);
	$stm->execute();
	$stm->bind_result($IDUserAgent);
	$stm->fetch();
	$stm->close();

	// Ako smo nesto nasli, vraticemo to kao rezultat

	if ($IDUserAgent)
		return $IDUserAgent;


//	if (1 != $stm->affected_rows)
//		return -1;

// Inace i definitivno odustajemo...
// Seti se da ovde sve izlogujes
	return 0;

}




function ret_stm_arr($stm)
{
	$stm->store_result();
	$result=$stm->result_metadata();
	$bindVar=array();

	while ($column = $result->fetch_field())
          $bindVar[] = &$results[$column->name];

	$result->close();

    call_user_func_array(array($stm, 'bind_result'), $bindVar);

	$stm->fetch();

	return ($results);

}

function getFoundRows()
{
	return $this->found_rows;
}

function getPrevOffset($limit,$offset)
{
	$retval=-1;

	$offset=intval($offset);
	$limit=intval($limit);

	if (($offset-$limit) >= 0)
		$retval=$offset-$limit;

	return $retval;
}

function getNextOffset($limit,$offset)
{
	$offset=intval($offset);
	$limit=intval($limit);
	$retval=-1;

	if (($offset+$limit) >= $this->found_rows)
		$retval=-1;
	else
		$retval=$offset+$limit;

	return $retval;
}




}
?>
