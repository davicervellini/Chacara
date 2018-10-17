<?php

$routerContent = new AltoRouter();
$routerContent->setBasePath( BASE_ROUTE ."/cadastros" );

$routerContent->addRoutes(array(

	array('GET','/usuarios/',               '/cadUsuarios.php', 'cadUsuarios'),
	array('GET','/usuarios',                '/cadUsuarios.php', ''),
	array('GET','/usuarios/[*]',            '/cadUsuarios.php', ''),

));

$matchContent = $routerContent->match();

if( is_array($matchContent)  ) {
	require __DIR__. $matchContent['target'];

}else{
	require "bloqueio.php";
}
