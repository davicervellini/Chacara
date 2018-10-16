<?php 
session_start();

require_once "../conexao/ConexaoMySQL.Class.php";
require_once "../conexao/ConexaoMySQL.Class.php";
require_once "../classes/autoload.php";
require_once "../soap/ArcaTDPJ_WS/ArcaTDPJ_WS.php";
$sys = new Sistema;
$ws  = new ArcaTDPJ_WS;

$usuRecno       = filter_input(INPUT_POST, "usuRecno");
$usuSenha       = filter_input(INPUT_POST, "usuSenha");
$primeiroAcesso = filter_input(INPUT_POST, "primeiroAcesso");
$usuSenhaOld 	= filter_input(INPUT_POST, "usuSenhaOld");
$processo 		= filter_input(INPUT_POST, "processo");
$resp           = array();

switch ($processo) {
	case 'primeiroAcesso':
		try{
			$vDados = [
				"USU_SENHA"=>$usuSenha,
				"USU_PRIMEIROACESSO"=>1
			];

			$result = $sys->vStrings($vDados);
			
			$res = $ws->corrigirRegistro( getToken( $connMYSQL->db() ), "usuarios", "RECNO = ".$usuRecno."" , $result["campos"], $result["dados"]);

			$sys->historico("ALTERAR SENHA", "FOI REALIZADO A ALTERAÇÃO DE SENHA - USU RECNO: ".$usuRecno); // Informa no histórico os campos 

			$resp["message"] = "Senha alterada com sucesso!";
			$resp["redirect"] = ($primeiroAcesso == 1) ? "1" : "0";
			print json_encode($resp);
		}catch(Exception $e){
            $resp['error'] = $e->getMessage();
            print json_encode($resp);
        }
	break;

	case 'alterarSenha':
		try{
			$sql = "SELECT USU_SENHA FROM usuarios WHERE RECNO = :RECNO ";
			$qry = $connMYSQL->prepare($sql);
			$qry->bindParam(':RECNO', $usuRecno);
			$qry->execute();
			$ln = $qry->fetch();
			if($ln['USU_SENHA'] != $usuSenhaOld ){
				$resp["chave"] = 0;
				print json_encode($resp);
				return;
			}
			$vDados = [
				"USU_SENHA"=>$usuSenha,
			];

			$result = $sys->vStrings($vDados);
			
			$res = $ws->corrigirRegistro( getToken( $connMYSQL->db() ), "usuarios", "RECNO = ".$usuRecno."" , $result["campos"], $result["dados"]);

			$sys->historico("ALTERAR SENHA", "FOI REALIZADO A ALTERAÇÃO DE SENHA - USU RECNO: ".$usuRecno); // Informa no histórico os campos 

			$resp["message"] = "Senha alterada com sucesso!";
			$resp["chave"] = 1;
			print json_encode($resp);
		}catch(Exception $e){
            $resp['error'] = $e->getMessage();
            print json_encode($resp);
        }
	break;
}
?>