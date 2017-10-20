<?php

abstract class QueryHandler {

    final public function getQuery($queryName) {
    	if (!is_array($this->queries[$queryName])) {
			$errtxt="No such query: ".$queryName;
			$errmsg='Detected error at line '.__LINE__."\r\n";
			$errmsg.='Description: '.$errtxt."\r\n";
			mail('bug@adbuka.com','Error in queryHandler.class',$errmsg);	
    		throw new Exception ("No such query: ".$errtxt);
		}	
        $queryArray  = $this->queries[$queryName];
        return $queryArray;
    }
}
?>