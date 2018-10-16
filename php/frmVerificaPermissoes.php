<?php 
	session_start();

	require_once "../conexao/ConexaoMySQL.Class.php";
	require_once "../soap/ArcaTDPJ_WS/ArcaTDPJ_WS.php";
	require_once "../classes/autoload.php";
	$sys = new Sistema;
	$ws  = new ArcaTDPJ_WS;

	foreach ($_POST as $key => $value) {
		${$key} = ($value != "") ? $value : NULL;
	}

	try{
		$_SESSION["permissoesNovo"][$sForm] = [
			"ACESSO" 	=> $acesso,
			"INCLUSAO" 	=> $inclusao,
			"CORRECAO" 	=> $correcao,
			"EXCLUSAO" 	=> $exclusao

		];
		print_r($_SESSION["permissoesNovo"]);
		
	}catch(Exception $e){

		print $e->getMessage();
	}
?>