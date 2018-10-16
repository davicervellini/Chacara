<?php

function autoloadArca_e3692d5885e81184f098cff97fb33b1a($class){
	
	$diretorio = dir(__DIR__);
	while($arquivo = $diretorio -> read()){
		if($arquivo != '.' && $arquivo != '..' && $arquivo != 'GetterSetter.Class.php' && $arquivo != 'autoload.php'){
			if(mb_strpos($arquivo, $class) === 0){
				include_once __DIR__ . DIRECTORY_SEPARATOR . $class . ".Class.php";
			}	
		}
	}
	$diretorio -> close();
		
}

spl_autoload_register('autoloadArca_e3692d5885e81184f098cff97fb33b1a');


?>