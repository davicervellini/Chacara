<?php

function phpmailer_autoload($class){
	
	$diretorio = dir(__DIR__);
	while($arquivo = $diretorio -> read()){
		if($arquivo != '.' && $arquivo != '..' && $arquivo != 'PHPMailerAutoload.php'){
			if(mb_strpos($arquivo, $class) === 0){
				// include_once __DIR__ . DIRECTORY_SEPARATOR . $class . ".php";
				print __DIR__ . DIRECTORY_SEPARATOR . $class . ".php<br>";
			}	
		}
	}
	$diretorio -> close();
		
}

spl_autoload_register('phpmailer_autoload');
die;

?>