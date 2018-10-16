<?php
session_start();
require_once "../conexao/ConexaoMySQL.Class.php";
require_once "../soap/ArcaTDPJ_WS/ArcaTDPJ_WS.php";
require_once "../classes/autoload.php";
require_once "../config.php";

$sys = new Sistema;

$ws  = new ArcaTDPJ_WS();

$resp    = array();
$vCampos = array();
$vDados  = array();

foreach ($_POST as $key => $value) {
	${$key} = ($value != "")? $value : NULL;
}

$dataPesquisa ? $sys->padroniza_datas_US($dataPesquisa) : "";

switch ($processo) {
	case 'incluirAnotacao':		
		header("Content-Type: application/json;charset=utf-8");
		try{
			$vDados = [
				"USU_CODIGO" => $_SESSION["usuario"]["usuCodigo"],
				"ANO_DATA" => $sys->padroniza_datas_US($dataCadastro),
				"ANO_HORA" => date("H:i:s"),
				"ANO_DESCRICAO" => $descricao,
				"ANO_FAVORITO" => 0,
				"ANO_ARQUIVADO" => 0				
			];

			$result = $sys->vStrings($vDados);

			// $res = $ws->inserirRegistro( getToken(), "anotacoes", $result['campos'], $result['dados'] );
			$insert = $sys->getInsert("anotacoes", $vDados);
			$query  = $connMYSQL->prepare($insert);
			foreach ($vDados as $key => &$value) {
				$query->bindParam($key, $value);
			}   	
			$res = $query->execute();

			if($res != true){
				$resp['error'] = $res;
			}else{
				$sys->historico("PAGINA INICIAL", "INCLUIU A ANOTACAO: ".$descricao);
				$resp['message'] = 'Tarefa cadastrada com sucesso';
			}
			print json_encode($resp);

		}catch(Exception $e){
			$resp['error'] = $e->getMessage();
            print json_encode($resp);
		}

		break;

	case 'corrigirAnotacao':
		header("Content-Type: application/json;charset=utf-8");
		try{


		}catch(Exception $e){
			$resp['error'] = $e->getMessage();
			print json_encode($resp);
		}
		break;

	case 'excluirAnotacao':
		header("Content-Type: application/json;charset=utf-8");
		try{
			// $res = $ws->deletarRegistro(getToken(), "anotacoes", "RECNO = ". $recno);
			$delete = "DELETE FROM anotacoes WHERE RECNO = :RECNO";
			$query = $connMYSQL->prepare($delete);
			$query->bindParam(':RECNO', $recno);       
			$res = $query->execute();

			if($res != true){
                $resp['error'] = $res;
            }else{
                $sys->historico("PAGINA INICIAL", "EXCLUIU A ANOTACAO: ".$descricao);
                $resp['message']   = 'Anotação excluida com sucesso.';
            }
            print json_encode($resp);
		}catch(Exception $e){
			$resp['error'] = $e->getMessage();
			print json_encode($resp);
		}
		break;

	case 'listarAnotacoes':
		header("Content-Type: application/json;charset=utf-8");
		try{			
			$usuario = $_SESSION["usuario"]["usuCodigo"];
			$sql = "SELECT RECNO,ANO_DESCRICAO, ANO_COMPLETO, ANO_FAVORITO, ANO_ARQUIVADO, ANO_DATA
					FROM anotacoes
					WHERE ANO_COMPLETO = 0 AND USU_CODIGO = :USUARIO AND ANO_ARQUIVADO = 0";
			if($dataPesquisa!=""){
				$sql.= " and ANO_DATA = :ANO_DATA ";
			}
			$sql.=" ORDER BY ANO_FAVORITO DESC";

			$qry = $connMYSQL->prepare($sql);
			$qry->bindParam(":USUARIO", $usuario);
			if($dataPesquisa != ""){
				$qry->bindParam(":ANO_DATA", $dataPesquisa);
			}
			$qry->execute();
			
			if($qry->rowCount() > 0 ){
				$ln = $qry->fetchAll();
				$resp["gridPendentes"] = "";
				foreach ($ln as $row) {	
					$dataAnotacao = $sys->padroniza_datas_BR($row["ANO_DATA"]);				
					$star = ($row["ANO_FAVORITO"] == 1) ? "star" : "star_border";					

					$resp['gridPendentes'] .= "
						<li> 
							<div class=\"row\">
									<div class=\"col l9\" >
										<input  type=\"checkbox\" id=".$row["RECNO"]." onChange =\"completarAnotacao(".$row["RECNO"].")\"> 
										
										<label for=".$row["RECNO"]." class=\"red-text\"><span class=\"dateFormat\">".$dataAnotacao."</span> - ".$row["ANO_DESCRICAO"]."</label>						
									</div>

									<div class=\"col l3\">
										<i class=\"material-icons right grey-text\" ><a onClick = \" excluirAnotacao(".$row["RECNO"].")\" style=\"cursor:pointer\">delete</a></i>
										<i class=\"material-icons right grey-text\" ><a onClick = \" arquivarAnotacao(".$row["RECNO"].", '0')\" style=\"cursor:pointer\">folder_open</a></i>
										<i class=\"material-icons right grey-text\"><a onClick =\"favoritarAnotacao(".$row["RECNO"].")\" style=\"cursor:pointer\">".$star."</a></i>
									</div>
							</div>
						</li>";
				}

			}else{
				$resp['gridPendentes'] = "<li>Nenhuma anotação pendente</li>";
			}

			$sql = "SELECT RECNO,ANO_DESCRICAO, ANO_COMPLETO, ANO_FAVORITO, ANO_DATA
					FROM anotacoes
					WHERE ANO_COMPLETO = 1 AND USU_CODIGO = :USUARIO AND ANO_ARQUIVADO = 0";
			if($dataPesquisa!=""){
				$sql.= " AND ANO_DATA = :ANO_DATA ";
			}
			$sql.=" ORDER BY ANO_FAVORITO DESC";

			$qry = $connMYSQL->prepare($sql);
			$qry->bindParam(":USUARIO", $usuario);
			if($dataPesquisa != ""){
				$qry->bindParam(":ANO_DATA", $dataPesquisa);
			}
			$qry->execute();
			
			if($qry->rowCount() > 0){
				$ln = $qry->fetchAll();
				$resp["gridCompletas"] = "";
				foreach ($ln as $row) {		
					$dataAnotacao= $sys->padroniza_datas_BR($row["ANO_DATA"]);			
					$star = ($row["ANO_FAVORITO"] == 1) ? "star" : "star_border";

					$resp['gridCompletas'] .= "
						<li> 
							<div class=\"row\">
									<div class=\"col l9\">
										<input type=\"checkbox\" id=".$row["RECNO"]." onChange =\"completarAnotacao(".$row["RECNO"].")\" checked> 
										<label for=".$row["RECNO"]." class=\"black-text\"><span class=\"dateFormat\">".$dataAnotacao."</span> - ".$row["ANO_DESCRICAO"]."</label>						
									</div>

									<div class=\"col l3\">
										<i class=\"material-icons right grey-text\" ><a onClick = \" excluirAnotacao(".$row["RECNO"].")\" style=\"cursor:pointer\">delete</a></i>
										<i class=\"material-icons right grey-text\" ><a onClick = \" arquivarAnotacao(".$row["RECNO"].", '0')\" style=\"cursor:pointer\">folder_open</a></i>
										<i class=\"material-icons right grey-text\"><a onClick =\"favoritarAnotacao(".$row["RECNO"].")\" style=\"cursor:pointer\">".$star."</a></i>
									</div>
							</div>
						</li>";
				}

			}else{
				$resp['gridCompletas'] = "<li>Nenhuma anotação completa</li>";
			}


			$sql = "SELECT RECNO,ANO_DESCRICAO, ANO_COMPLETO, ANO_FAVORITO, ANO_DATA
					FROM anotacoes
					WHERE USU_CODIGO = :USUARIO AND ANO_ARQUIVADO = 1";
			if($dataPesquisa!=""){
				$sql.= " AND ANO_DATA = :ANO_DATA ";
			}
			$sql.=" ORDER BY ANO_FAVORITO DESC";

			$qry = $connMYSQL->prepare($sql);
			$qry->bindParam(":USUARIO", $usuario);
			if($dataPesquisa != ""){
				$qry->bindParam(":ANO_DATA", $dataPesquisa);
			}
			$qry->execute();
			
			if($qry->rowCount() > 0){
				$ln = $qry->fetchAll();
				$resp["gridArquivadas"] = "";
				foreach ($ln as $row) {		
					$dataAnotacao= $sys->padroniza_datas_BR($row["ANO_DATA"]);			
					$star = ($row["ANO_FAVORITO"] == 1) ? "star" : "star_border";

					$resp['gridArquivadas'] .= "
						<li> 
							<div class=\"row\">
									<div class=\"col l9\">
										<input type=\"checkbox\" id=".$row["RECNO"]." onChange =\"completarAnotacao(".$row["RECNO"].")\" checked> 
										<label for=".$row["RECNO"]." class=\"black-text\"><span class=\"dateFormat\">".$dataAnotacao."</span> - ".$row["ANO_DESCRICAO"]."</label>						
									</div>

									<div class=\"col l3\">
										<i class=\"material-icons right grey-text\" ><a onClick = \" excluirAnotacao(".$row["RECNO"].")\" style=\"cursor:pointer\">delete</a></i>
										<i class=\"material-icons right grey-text\" ><a onClick = \" arquivarAnotacao(".$row["RECNO"].", '1')\" style=\"cursor:pointer\">folder</a></i>
										<i class=\"material-icons right grey-text\"><a onClick =\"favoritarAnotacao(".$row["RECNO"].")\" style=\"cursor:pointer\">".$star."</a></i>
									</div>
							</div>
						</li>";
				}

			}else{
				$resp['gridArquivadas'] = "<li>Nenhuma anotação arquivada</li>";
			}

			print json_encode($resp);

		}catch(Exception $e){
			$resp['error'] = $e->getMessage();
			print json_encode($resp);
		}

	break;	

	case 'favoritarAnotacao':
		header("Content-Type: application/json;charset=utf-8");
		try{
			$sql = "SELECT RECNO, ANO_FAVORITO, ANO_DESCRICAO
					FROM anotacoes
					where RECNO = :RECNO";
			$qry = $connMYSQL->prepare($sql);
			$qry->bindParam(":RECNO", $recno);
			$qry->execute();

			if($qry->rowCount() > 0){
				$ln = $qry->fetch();
			
				if ($ln["ANO_FAVORITO"] == 1) {
					$vDados = [
						"ANO_FAVORITO" => 0
					];
				}else{
					$vDados = [
						"ANO_FAVORITO" => 1
					];
				}
	           
	            $update = $sys->getUpdate("anotacoes", "RECNO = :RECNO", $vDados);
				$query  = $connMYSQL->prepare($update);     
				foreach ($vDados as $key => &$value) {
					$query->bindParam($key, $value);
				} 
				$query->bindParam(':RECNO', $recno);  	
				$res = $query->execute();

	            if($res != true){
	                $resp['error'] = $res;
	            }else{
	                $sys->historico("ANOTACAO", "FAVORITOU A ANOTACAO ".$ln["ANO_DESCRICAO"]);
	                $resp['message']   = 'status alterado com sucesso.';
	            }
            }

			print json_encode($resp);
		}catch(Exception $e){
			$resp['error'] = $e->getMessage();
			print json_encode($resp);
		}
	break;

	case 'arquivarAnotacao':
		try{
			$sql = "SELECT RECNO, ANO_ARQUIVADO, ANO_DESCRICAO
					FROM anotacoes
					where RECNO = :RECNO";
			$qry = $connMYSQL->prepare($sql);
			$qry->bindParam(":RECNO", $recno);
			$qry->execute();

			if($qry->rowCount() > 0){
				$ln = $qry->fetch();
			
				if ($ln["ANO_ARQUIVADO"] == 1) {
					$vDados = [
						"ANO_ARQUIVADO" => 0
					];
				}else{
					$vDados = [
						"ANO_ARQUIVADO" => 1
					];
				}

				$update = $sys->getUpdate("anotacoes", "RECNO = :RECNO", $vDados);
				$query  = $connMYSQL->prepare($update);     
				foreach ($vDados as $key => &$value) {
					$query->bindParam($key, $value);
				} 
				$query->bindParam(':RECNO', $recno);  	
				$res = $query->execute();
	            if(!$res){
	                $resp['error'] = $res;
	            }else{
	                $sys->historico("ANOTACAO", "ARQUIVOU A ANOTACAO ".$ln["ANO_DESCRICAO"]);
	                $resp['message'] = 'Anotação editada com sucesso.';
	            }
            }

			print json_encode($resp);
		}catch(Exception $e){
			$resp['error'] = $e->getMessage();
			print json_encode($resp);
		}

	break;

	case 'completarAnotacao':
		header("Content-Type: application/json;charset=utf-8");
		try{
			$sql = "SELECT RECNO, ANO_COMPLETO, ANO_DESCRICAO
					FROM anotacoes
					where RECNO = :RECNO";
			$qry = $connMYSQL->prepare($sql);
			$qry->bindParam(":RECNO", $recno);
			$qry->execute();

			if($qry->rowCount() > 0){
				$ln = $qry->fetch();

				if($ln["ANO_COMPLETO"] == 1){
					$vDados = [
						"ANO_COMPLETO" => 0
					];
				}else{
					$vDados = [ 
						"ANO_COMPLETO" => 1
					];
				}

	            $update = $sys->getUpdate("anotacoes", "RECNO = :RECNO", $vDados);
				$query  = $connMYSQL->prepare($update);     
				foreach ($vDados as $key => &$value) {
					$query->bindParam($key, $value);
				} 
				$query->bindParam(':RECNO', $recno);  	
				$res = $query->execute();

	            if(!$res){
	                $resp['error'] = $res;
	            }else{
	                $sys->historico("ANOTACAO", "COMPLETOU A ANOTACAO ".$ln["ANO_DESCRICAO"]);
	                $resp['message']   = 'status alterado com sucesso.';
	            }

			}
			print json_encode($resp);

		}catch(Exception $e){
			$resp['error'] = $e->getMessage();
			print json_decode($resp);
		}		
	break;
}

?>