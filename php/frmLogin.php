<?php 
	session_start();
	$usuLogin     = filter_input(INPUT_POST, "usuLogin");
	$usuSenha     = filter_input(INPUT_POST, "usuSenha");
	$usuConectado = filter_input(INPUT_POST, "usuConectado");
	$processo     = filter_input(INPUT_POST, "processo");
	$resp         = array();

	require_once "../conexao/ConexaoMySQL.Class.php";
	require_once "../classes/autoload.php";

	$user = new Usuario;
	$sys = new Sistema;

	switch($processo){
		case "login":
			
			$user->setUsuLogin(addslashes(trim($usuLogin)));
			$user->setUsuSenha(addslashes(trim($usuSenha)));
			$user->setUsuConectado($usuConectado);

			$resposta = $user->login();

			if($resposta['valid'] == true){
				$sys->historico('LOGIN', 'O USUARIO '.$usuLogin.' LOGOU NO SISTEMA');
			}

			print json_encode($resposta);
		break;

		case "logout":
			$sys->historico('LOGIN', 'O USUARIO '.$usuLogin.' SAIU DO SISTEMA');
			$user->logout();
		break;
	}

?>