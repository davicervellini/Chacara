<?php

$routerContent = new AltoRouter();
$routerContent->setBasePath( BASE_ROUTE ."/ferramentas" );

$routerContent->addRoutes(array(

	array('GET','/configuracao-de-menus/',    '/cadConfigMenus.php', 'cadConfigMenus'),
	array('GET','/configuracao-de-menus',     '/cadConfigMenus.php', ''),
	array('GET','/configuracao-de-menus/[*]', '/cadConfigMenus.php', ''),
	
	array('GET','/caminhos/',                 '/cadCaminhos.php', 'cadCaminhos'),
	array('GET','/caminhos',                  '/cadCaminhos.php', ''),
	array('GET','/caminhos/[*]',              '/cadCaminhos.php', ''),

	array('GET','/dados-cartorio/',           '/cadDadosCartorio.php', 'cadDadosCartorio'),
	array('GET','/dados-cartorio',            '/cadDadosCartorio.php', ''),
	array('GET','/dados-cartorio/[*]',        '/cadDadosCartorio.php', ''),

	array('GET','/estorno-de-baixas/',        '/ferEstornoBaixas.php', 'ferEstornoBaixas'),
	array('GET','/estorno-de-baixas',         '/ferEstornoBaixas.php', ''),
	array('GET','/estorno-de-baixas/[*]',     '/ferEstornoBaixas.php', ''),

	array('GET','/sequencia-protocolo/',      '/cadSequenciaProtocolo.php', 'cadSequenciaProtocolo'),
	array('GET','/sequencia-protocolo',       '/cadSequenciaProtocolo.php', ''),
	array('GET','/sequencia-protocolo/[*]',   '/cadSequenciaProtocolo.php', ''),

	array('GET','/variaveis-sistema/',        '/cadVariaveisSistema.php', 'cadVariaveisSistema'),
	array('GET','/variaveis-sistema',         '/cadVariaveisSistema.php', ''),
	array('GET','/variaveis-sistema/[*]',     '/cadVariaveisSistema.php', ''),


));

$matchContent = $routerContent->match();

if( is_array($matchContent)  ) {
	require __DIR__. $matchContent['target'];

}else{
	require "bloqueio.php";
}