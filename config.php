<?php

	require_once __DIR__ . "/conexao/ConexaoMySQL.Class.php";
	$connMYSQL = new ConexaoMySQL();

	if (phpversion() < 7.0) {
    	echo "<p>Ops, sua versão do PHP não é compatível com esse sistema. Por favor, utilize a versão 7.0 ou maior do PHP.</p>";
    	exit;
	}
	$sqlConfig = "SELECT * FROM config";
	$qryConfig = $connMYSQL->prepare($sqlConfig);
	$qryConfig->execute();
	$cfg = $qryConfig->fetch();

	if(!defined("DEBUG"))                 define( "DEBUG", $cfg["CFG_DEBUG"]);

	$ambiente = "";
	if(!DEBUG){

		ini_set("display_errors", 0);
		ini_set("log_errors", true);
		ini_set("error_reporting", E_ALL);
		ini_set("error_log", "logs/error.log");

		$portServer  = $cfg["CFG_PORT"];
	}else{
		$portServer = ':80';
		$versionFull = "v1.0.0-trunk";
		$ambiente    = "Ambiente de Desenvolvimento";
		if(strpos($_SERVER["REQUEST_URI"], "Chacara/dev") > -1){
			$portServer             = ":80";
			$cfg["CFG_BASE_ROUTE"]  = "/Chacara/dev";
			
		}

		if(strpos($_SERVER["REQUEST_URI"], "Chacara/preproducao") > -1){
			$portServer             = ":80";
			$cfg["CFG_BASE_ROUTE"]  = "/Chacara/preproducao";
			$ambiente               = "Ambiente de Pré Produção";
		}
	}

	if(!defined("PORT"))                  define( "PORT", $portServer );

	// // Paths
	if(!defined("_BASE_PATH_"))           define( "_BASE_PATH_", dirname(__DIR__) . DIRECTORY_SEPARATOR);
	if(!defined("_LOG_PATH_"))            define( "_LOG_PATH_", _BASE_PATH_ . 'logs'. DIRECTORY_SEPARATOR);
	if(!defined("REQUIRE_PATH"))          define( "REQUIRE_PATH" , str_replace("\\", "/", __DIR__));

	if(!defined("INCLUDE_PATH"))          define( "INCLUDE_PATH", "http://". $_SERVER["SERVER_NAME"] . PORT . $cfg["CFG_BASE_ROUTE"]);

	// // Urls
	ini_set("display_errors", 1);
	if(!defined("BASE_ROUTE"))            define( "BASE_ROUTE",           $cfg["CFG_BASE_ROUTE"] );
	if(!defined("IP_SERVER"))             define( "IP_SERVER",            $cfg["CFG_IP_SERVER"]);
	if(!defined("VERSION"))               define( "VERSION",              rand());
	
	// Predefinicoes
	if(!defined("AMBIENTE"))              define( "AMBIENTE",             $ambiente );

	set_time_limit(180);
	ini_set("soap.wsdl_cache_enabled", 0);

	function verificarPermissoes($nomeForm, $sTipo = 'acesso'){
		if($nomeForm != ""){
			if(@$_SESSION["usuario"]["usuAdmin"] == 1 || @$_SESSION["usuario"]["usuMaster"] == 1){
				return true;
			}else{
				$formulario = @$_SESSION["permissoes"][$nomeForm];
				return ($formulario[$sTipo] == 1) ? true : false;
			}	
		}
		return false;	
	}

	function getToken( $db = array() ){
		if(!session_id()){
			session_start();
		}
		require_once __DIR__ . "/classes/Token.Class.php";
		$jwt = new Token;
		return $jwt->generate(@$_SESSION["usuario"]["usuLogin"], $db);	
	}

