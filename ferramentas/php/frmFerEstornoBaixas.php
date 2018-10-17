<?php
session_start();
require_once "../../config.php";
require_once "../../conexao/ConexaoMySQL.Class.php";
require_once "../../soap/ArcaTDPJ_WS/ArcaTDPJ_WS.php";
require_once "../../classes/autoload.php";
$sys = new Sistema;
$ws  = new ArcaTDPJ_WS;

$resp    = array();
$vCampos = array();
$vDados  = array();

header('Content-Type: text/html; charset=utf-8');
foreach ($_POST as $key => $value) {
    ${$key} = ($value != "") ? $value : NULL;
}

function limparValor($valor){
    return str_replace(",",".",str_replace(".","",$valor));
}

switch ($processo) {

    case 'preencheCampos':
        try{        	
        	$dtRecepcao = "";
			$dtRegistro = "";
			$dtUltBaixa = "";

			switch ($tpProtocolo) {
				case '1':
					$tpDocumento = "TD";					
				break;				
				case '2':
					$tpDocumento = "PJ";
				break;
				case '3':
					$tpDocumento = "CE";
				break;
			}

           	$sql = "SELECT 	reg.RECNO           		AS recno,
							reg.REG_DTPRENOTA 			AS dtRecepcao,
							oco.OCO_DESCRICAO 			AS STATUS,
							pes.PES_NOME 				AS apresentante,
							reg.NAT_DESCRICAO 			AS natureza,	
							reg.REG_DATAPOSICAO 		AS DataPosicao,
							reg.REG_HORAPOSICAO 		AS HoraPosicao
					FROM arc_registro reg
					LEFT JOIN pessoas_dados psd ON (reg.APR_CODIGO = psd.PSD_CODIGO)
					LEFT JOIN pessoas pes ON (psd.PES_CODIGO = pes.PES_CODIGO)
					LEFT JOIN ocorrencias oco ON (reg.REG_CODPOSICAO = oco.OCO_CODIGO)
					WHERE reg.REG_PRENOTA = :REG_PRENOTA AND reg.REG_TPDOCUMENTO = :REG_TPDOCUMENTO";
			$qry = $connMYSQL->prepare($sql);
			$qry->bindParam(":REG_PRENOTA", $protocolo);
			$qry->bindParam(":REG_TPDOCUMENTO", $tpDocumento);
			$qry->execute();			
			$hrRegistro = "";
			if($qry->rowCount() > 0){
				$ln  = $qry->fetch(PDO::FETCH_ASSOC);
				// print_r($ln);die;
				$dtRecepcao = $sys->padroniza_datas_BR(@$ln["dtRecepcao"]);
				$dtRegistro = $sys->padroniza_datas_BR(@$ln["dtRegistro"]);
				$hrRegistro = @$ln["hrRegistro"];
			
				$sqlUltBaixa = "
					SELECT oco.OCO_DESCRICAO, dat.DAT_DTBAIXA, dat.DAT_HORA
					FROM arc_registro_datas dat
					INNER JOIN ocorrencias oco ON (dat.OCO_CODIGO = oco.OCO_CODIGO)
					WHERE dat.REG_PRENOTA = :REG_PRENOTA
					ORDER BY dat.RECNO DESC LIMIT 2";
				$qryUltBaixa = $connMYSQL->prepare($sqlUltBaixa);
				$qryUltBaixa->bindParam(":REG_PRENOTA", $protocolo);
				$qryUltBaixa->execute();
				$rowBaixas = $qryUltBaixa->rowCount();
				if($rowBaixas > 0){
					$lnUlt = $qryUltBaixa->fetchAll();
					
					if( $rowBaixas > 1 )
						$ultBaixa = utf8_encode( $lnUlt[1]["OCO_DESCRICAO"] )." - ".$sys->padroniza_datas_BR($lnUlt[1]["DAT_DTBAIXA"])." - ".$lnUlt[1]["DAT_HORA"];
					else
						$ultBaixa = utf8_encode( $lnUlt[0]["OCO_DESCRICAO"] )." - ".$sys->padroniza_datas_BR($lnUlt[0]["DAT_DTBAIXA"])." - ".$lnUlt[0]["DAT_HORA"];				

	            }

            }

			$resp["recno"]        = @$ln["recno"]        ?? "";
			$resp["dtRecepcao"]   = @$dtRecepcao         ?? "";
			$resp["dtRegistro"]   = @$dtRegistro         ?? "";
			$resp["hrRegistro"]   = @$hrRegistro         ?? "";
			$resp["status"]       = @$ln["STATUS"]       ?? "";
			$resp["apresentante"] = @$ln["apresentante"] ?? "";
			$resp["natureza"]     = @$ln["natureza"]     ?? "";
			$resp["ultBaixa"]     = @$ultBaixa           ?? "";
			$resp["DataPosicao"]  = @$sys->padroniza_datas_BR($ln["DataPosicao"])  ?? "";
			$resp["HoraPosicao"]  = @$ln["HoraPosicao"]  ?? "";			
            print json_encode($resp);
            
        }catch(Exception $e){
            $resp['error'] = $e->getMessage();
            print json_encode($resp);
        }
    break;

    case 'liberarManutencao':
        try{
			switch ($tpProtocolo) {
	            case '1':
	                $prefixo = "ARG";
	                $tabelas = "arc_reg";
	                $hist    = "Registro";
	                $prefixoData = "ARS_";
	                $tabelasData = "datas";
	            break;
	            case '2':
	                $prefixo = "CER";
	                $tabelas = "arc_cer";
	                $hist    = "Certidão";
	                $prefixoData = "ACE_";
	                $tabelasData = "datas_cer";
	            break;            
	            case '3':
	                $prefixo = "ARE";
	                $tabelas = "arc_exa";
	                $hist    = "Exame e Cálculo";
	                $prefixoData = "ASE_";
	                $tabelasData = "datas_exa";
	            break;
			}

			$vDados = [
				$prefixo.'_LIBERAMANUTENCAO' => 1,
				$prefixo.'_POSICAO' => 30
			];
            $result = $sys->vStrings($vDados);
            if($tpProtocolo == '1'){
            	$result['campos'][2] = "ARG_MANUTENCAOFINALIZADA";
            	$result['dados'][2] = 0;
            }
            $res    = $ws->corrigirRegistro(getToken($connMYSQL->db()), $tabelas, "RECNO = $recno", $result['campos'] , $result['dados']);

            $vDados = [
                $prefixoData.'RECEPCAO'   => $sys->padroniza_datas_US($dtRecepcao),
                $prefixoData.'PROTOCOLO'  => $protocolo,
                'OCO_CODIGO'           	  => 30,
                'DAT_DTBAIXA'          	  => date('Y-m-d'),
                'DAT_HORA'          	  => date('H:i:s'),
                'DAT_USUARIO'          	  => $_SESSION["usuario"]["usuLogin"]
            ];
            $result = $sys->vStrings($vDados);
            $res 	.= $ws->inserirRegistro( getToken($connMYSQL->db()) , $tabelasData, $result['campos'] , $result['dados'] );

            if($res != ""){
                $resp['error'] = $res;
            }else{
            	if($hist == "Registro")
	        		$sys->historico("ESTORNO DE BAIXAS", "Liberou para manutenção - ".$hist." Nº: ".$protocolo, ["HLO_PROTOCOLO"=> $protocolo]);
	        	else
	        		$sys->historico("ESTORNO DE BAIXAS", "Liberou para manutenção - ".$hist." Nº: ".$protocolo);

	            $resp['message'] = 'Manutenção liberada com sucesso.';
            }

            print json_encode($resp);
        }catch(Exception $e){
            $resp['error'] = $e->getMessage();
            print json_encode($resp);
        }
    break;

    case 'excluirBaixa':
        try{
		          	
           	$sql = "SELECT dat.RECNO, oco.OCO_DESCRICAO, dat.DAT_DTBAIXA, dat.DAT_HORA
					FROM arc_registro_datas dat
					INNER JOIN ocorrencias oco ON (dat.OCO_CODIGO = oco.OCO_CODIGO)
					WHERE dat.REG_PRENOTA = :REG_PRENOTA
					ORDER BY dat.RECNO DESC LIMIT 2";
			$qry = $connMYSQL->prepare($sql);
			$qry->bindParam(":REG_PRENOTA", $protocolo);
			$qry->execute();			
			if($qry->rowCount() > 1){
				$ln = $qry->fetch(PDO::FETCH_ASSOC);
            }else{
				throw new Exception("Não há outra baixa disponível para remover.", 1);
            }

            $res = $ws->deletarRegistro(getToken($connMYSQL->db()), "arc_registro_datas", "RECNO = ".$ln["RECNO"]);

            $sql = "SELECT dat.RECNO, oco.OCO_DESCRICAO, dat.OCO_CODIGO, dat.DAT_DTBAIXA, dat.DAT_HORA
					FROM arc_registro_datas dat
					INNER JOIN ocorrencias oco ON (dat.OCO_CODIGO = oco.OCO_CODIGO)
					WHERE dat.REG_PRENOTA = :REG_PRENOTA 
					ORDER BY dat.RECNO DESC LIMIT 1";
			$qry = $connMYSQL->prepare($sql);
			$qry->bindParam(":REG_PRENOTA", $protocolo);
			$qry->execute();
			$lnNovo = $qry->fetch(PDO::FETCH_ASSOC);
            
        	$vDados = [
               'REG_CODPOSICAO'      => $lnNovo["OCO_CODIGO"],
               'REG_DATAPOSICAO'     => $lnNovo["DAT_DTBAIXA"],
               'REG_HORAPOSICAO'     => $lnNovo["DAT_HORA"],               
            ];

            $result = $sys->vStrings($vDados);
            $res    = $ws->corrigirRegistro(getToken($connMYSQL->db()) ,'arc_registro', "REG_PRENOTA = ".$protocolo, $result['campos'] , $result['dados']);
            
            if($res != ""){
                $resp['error'] = $res;
            }else{
        		$sys->historico("ESTORNO DE BAIXAS", "Removeu a baixa de ".$ln["OCO_DESCRICAO"]." - do protocolo Nº: ".$protocolo);
            	$resp['message'] = 'Baixa removida com sucesso.';
            }

            print json_encode($resp);
        }catch(Exception $e){
            $resp['error'] = $e->getMessage();
            print json_encode($resp);
        }
    break;

}

?>