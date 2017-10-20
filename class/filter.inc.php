<?php
/*******************************************************************************
*  Function:    
*  Description: Registruje f-je kao filtere
*  Input: 		 
*  Return:      
*******************************************************************************/

FilterClass::registerFilters(array('getEscaped','getStrParam','getStrOne','getStrAny', 'getInt','getIntPos','getFloat','getMsisdn','getPass','getName','getMobileNumber','getPhoneNumber','getEmail','noCheck','getCharOne','getCharTwo','getChar','getNumbers'));

FilterClass::registerParameters(array('username','name','password','email','tos','short','long','quantity','price','metric','currency','post_type','cat_id','limit','offset','categories','category','tip','id','desiredPosts','fcbklist_values','start','remember','user_id','thing','order','thingid','shortdesc','databank_id','address','city','postcode','country','tel','mob','www','adtype','scheme','width','height','page_id','title','text','daily_budget','company','regnumber','taxnumber','user_account','user_type','pgid','activation','idnumber','country_iso','phone_2','contact_person','full_name','domain','key','ad_type','ad_color','ad_width','ad_height','partner_ad_id','month','year','date_from','date_until','tp','bank_account','bank_name','_url','pid','preclickurl','overtext','banners', 'urlparams','pwd1','bonus','keyword','lng','opt','page'));


function noCheck($value) 
{
	return $value;
}

function getEscaped($value) 
{
	return htmlspecialchars($value);	
}

function getInt($value) 
{
	return intval($value);
}

function getIntPos($value) 
{
	$int_value=getInt($value);
	if($int_value<0)
	{
		$int_value*=(-1);
	}
	return $int_value;
}

function getFloat($value) 
{
	return floatval($value);
}

function getName($value)
{
	return 	substr(getEscaped($value),0,30);
	
}

function getCharOne($value)
{
	return substr(getEscaped($value),0,1);
}
function getCharTwo($value)
{
	return substr(getEscaped($value),0,2);
}
function getChar($value)
{
	return substr(getEscaped($value),0,7);
}


function getStrParam($value)
{
	return substr(getEscaped($value),0,50);
}


function getStrAny($value)
{
	return getEscaped($value);	
}

function getMsisdn($value)
{
	if(is_numeric($value) && preg_match('/^3816(4|5)[0-9]{6,7}$/',$value))
 	{
 		$msisdn=$value;
 	}		
	else
	{
		$msisdn=0;
	}
	return $msisdn;	
}

function getMobileNumber($value)
{
	if(is_numeric(trim($value)) && preg_match('/^06(0|1|2|3|4|5|9)[0-9]{6,7}$/',trim($value)))
 	{
 		$phone_number=trim($value);
 	}		
	else
	{
		$phone_number=0;
	}
	return $phone_number;	
}

function getPhoneNumber($value)
{
	if(is_numeric(trim($value)) && preg_match('/^[0-9]{9,11}$/',trim($value)))
 	{
 		$phone_number=trim($value);
 	}		
	else
	{
		$phone_number=0;
	}
	return $phone_number;	
}
function getNumbers($value)
{
	if(is_numeric(trim($value)))
 	{
 		$number=trim($value);
 	}		
	else
	{
		$number=-1;
	}
	return $number;	
}
function getBool($value)
{
	if(is_bool($value))
	{
		return $value;
	}	
	else
	{
		return -1;
	}
}

function getPass($value)
{
	return substr(getEscaped($value),0,8);
}

//check dns lunux/win

if(!function_exists('checkdnsrr'))
{
function checkdnsrr($host,$recType='') 
{
 if(!empty($host)) 
 {
	if($recType=='') $recType="MX";
  	exec("nslookup -type=".$recType." ".$host,$output);
  	foreach($output as $line) 
	{
    	if(preg_match("/^".$host."/", $line)) 
		{
		    return true;
    	}
    }
  return false;
 }
 return false;
}
}

//regex, checkDNS, fsocketopen
function getEmail($email) 
{
 // regex
 //if(preg_match("/^( [a-zA-Z0-9] )+( [a-zA-Z0-9\._-] )*@( [a-zA-Z0-9_-] )+( [a-zA-Z0-9\._-] +)+$/" , $email)) 
 //if (preg_match('/^[a-z0-9][a-z0-9_\.-]{0,}[a-z0-9]@[a-z0-9][a-z0-9_\.-]{0,}[a-z0-9][\.][a-z0-9]{2,4}$/', $email)) //OK
 // if (preg_match('/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i', $email))
 if (preg_match('/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i', $email))
 { 
// echo "email: ".$email;
 /*
  // uzima domain name
  list($username,$domain)=preg_split('/@/',$email);
  // cekira MX records na DNS
  if(!checkdnsrr($domain, 'MX')) 
  {
   return false;
  }
  // socket
  if(!fsockopen($domain,25,$errno,$errstr,30)) 
  {
   return false;
  }
*/  
  //return true;
  return $email;
 }
 else {
	return false;
}
}






?>
