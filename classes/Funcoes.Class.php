<?php 
	trait Funcoes {

	    public function mysql_escape_mimic($inp) { 
	        if(is_array($inp)) 
	            return array_map(__METHOD__, $inp); 

	        if(!empty($inp) && is_string($inp)) { 
	            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "''", '\\"', '\\Z'), $inp); 
	        } 

	        return $inp; 
	    } 

	    public function addDados(array $sDados){
	        $campos = array();
	        $dados  = array();
	        $result = array();
	        foreach ($sDados as $key => $value) {
	            array_push($campos, $key);
	            array_push($dados, $value);
	        }

	        $result['campos'] = $campos;
	        $result['dados']  = $dados;

	        return $result;
	    } 
	}