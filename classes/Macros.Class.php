<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class Macros extends GetterSetter{
	public $conn;
	public $sys;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();
		$this->sys  = new Sistema;

		if($codigo != ""){

			$sql = "SELECT FER_CODIGO       as Codigo,
						   FER_DATA         as DtFeriado,
						   FER_DESCRICAO    as Descricao
					FROM cadferiado WHERE RECNO = :RECNO" ;
			$qry = $this->conn->prepare($sql);
			$qry->bindParam(':RECNO', $codigo);
			$qry->execute();

			if($qry->rowCount() > 0){
				$result = $qry->fetchAll(PDO::FETCH_ASSOC);
				$this->setData($result);
			}
		}
	}

	public function getMacrosReg($tabela, $tpMacro){
		switch ($tpMacro) {
			case 'cer':
				$sSQL = "SELECT * FROM macros_reg WHERE MCR_NOME_TABELA = :TABELA";
			break;

			case 'exa':
				$sSQL = "SELECT * FROM macros_cer WHERE MCC_NOME_TABELA = :TABELA";
			break;

			case 'reg':
				$sSQL = "SELECT * FROM macros_exa WHERE MEX_NOME_TABELA = :TABELA";
			break;

			default:
				throw new Exception("Não foi possível identificar os macros", 1);
			break;
		}
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->bindParam(':TABELA', $tabela);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function listMacrosRecibo(){
		$sSQL = "SELECT MCR_DESCRICAO FROM macros_recibo ORDER BY MCR_DESCRICAO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function listMacrosExi(){
		$sSQL = "SELECT MCE_DESCRICAO FROM macros_exigencia ORDER BY MCE_DESCRICAO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function listMacrosNot(){
		$sSQL = "SELECT MCN_DESCRICAO FROM macros_notificacao ORDER BY MCN_DESCRICAO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function listMacrosGlobais(){
		$sSQL = "SELECT MCG_DESCRICAO FROM macros_globais ORDER BY MCG_DESCRICAO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function listMacrosCert(){
		$sSQL = "SELECT MCC_DESCRICAO FROM macros_cer ORDER BY MCC_DESCRICAO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function replaceMacroCert($texto,$prenota){
        $qry = $this->getMacros("arc_cer", "cer");

        if(count($qry) > 0){
            foreach ($qry as $ln) {
                if(strpos($texto, htmlentities($ln["MCC_DESCRICAO"]))){

                    $sqlMacro = "SELECT ".$ln["MCC_NOME_CAMPO"]." AS CAMPO FROM ".$ln["MCC_NOME_TABELA"]." WHERE ACE_PROTOCOLO = :ACE_PROTOCOLO";
                    $qryMacro = $this->conn->prepare($sqlMacro);
                    $qryMacro->bindParam(':ACE_PROTOCOLO', $prenota);
                    $qryMacro->execute();
                    $lnMacro = $qryMacro->fetch(PDO::FETCH_ASSOC);
                    $texto = str_replace(htmlentities($ln["MCC_DESCRICAO"]),utf8_encode($lnMacro["CAMPO"]),$texto);

                }
            }
        }
        return $texto;
    }


    public function maskCampos($string, $tipo = 'TEXTO'){
    	if($tipo !== "TEXTO" && $tipo !== "DATA" && $tipo !== "EMAIL" && $tipo != "VALOR")
    		$string = $this->sys->higienizarCampo( $string );

    	switch ($tipo) {
    		case 'TEXTO':
    			return $string;
    			break;
    		case 'NUMERO':
    			return $this->sys->formatarMilhar( $string );
    			break;
    		case 'VALOR':
					return $string;
					// return number_format($string, 2, ",", ".");
    			// return $this->sys->formataReal( $string );
    			break;
    		case 'DATA':

    			if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$string))
    				return $string;
    			else
    				return $this->sys->padroniza_datas_BR( $string );

    			break;
    		case 'DOCUMENTO':
    			return $this->sys->formataDoc( $string, strlen($string) );
    			break;
    		case 'TELEFONE':

    			return $this->sys->mask($string, strlen($string) > 10 ? "(##)#####-####" : "(##)####-####");

    			break;
    		case 'CEP':

    			return $this->sys->formatarCEP( $string );

    			break;
    		case 'EMAIL':

    			return strtolower( $string );

    			break;
    		case 'TIPOVALOR':
					$string = ($string == 1) ? "Com Valor Declarado." : "Sem Valor Declarado.";
    			return  $string ;

    			break;

    		default:
    			return $string;
    			break;
    	}
    }

    public function converterMacrosGerais( $sTexto ){
    	$sql = "SELECT * FROM macros_globais";
    	$qry = $this->conn->prepare($sql);
    	$qry->execute();
    	if($qry->rowCount() > 0){

    		$result = $qry->fetchAll();
    		$row = 0;
    		foreach ($result as $lin) {
    			$row++;
    			if($lin["MCG_CAMPO"] != ""){

    				$sql = "SELECT $lin[MCG_CAMPO] AS CAMPO_TMP FROM $lin[MCG_TABELA] ";

    				$qry = $this->conn->prepare($sql);
    				$qry->execute();


    				$mac = $qry->fetch();


    				$macroSub = $this->maskCampos(strtoupper($mac["CAMPO_TMP"]), $lin[ "MCG_FORMATO"] );

    			}else{

    				$desc = $lin["MCG_DESCRICAO"];
    				switch ( true ) {
    					case strpos( $desc, "GERAL_DIA")         > -1: $macroSub = date("d");                         break;
    					case strpos( $desc, "GERAL_MES_NUMERAL") > -1: $macroSub = date("m");                         break;
    					case strpos( $desc, "GERAL_MES_EXTENSO") > -1: $macroSub = $this->sys->mesExtenso(date("m")); break;
    					case strpos( $desc, "GERAL_ANO")         > -1: $macroSub = date('Y');                         break;
    					case strpos( $desc, "GERAL_HORA")        > -1: $macroSub = date('H:i:s');                     break;
    					case strpos( $desc, "GERAL_USUARIO")     > -1: $macroSub = $_SESSION["usuario"]["usuNome"];   break;
    				}

    			}


    			$sTexto = str_replace(htmlentities($lin["MCG_DESCRICAO"]),  ($macroSub) , $sTexto) ;
    		}

    	}


    	return $sTexto;
    }


    public function converterExigencia($sTexto,$sTipo,$sPrenota, $decoder = true){
    	$vTrig 	      = ["MCC",     "MEX",     "MCR"];
		$vTrigTab     = ["cer",     "exa",     "reg"];
		$vFullTab     = ["arc_cer", "arc_exa", "arc_reg"];
		$vTrigFullTab = ["ACE",     "ASE",     "ARS"];
		$vOptions = [];
		$i        = 0;
		$j 		  = 0;
		foreach ($vTrig as $key) {
			$sqlMacros = "SELECT * FROM macros_" . $vTrigTab[$j] . " WHERE " . $key . "_MOD_EXIGENCIA = 1";
			$qry = $this->conn->query($sqlMacros);

			if($qry->rowCount())  {

				$result = $qry->fetchAll();
				foreach ($result as $lin) {

					$vOptions[$i] = $lin[$key . "_DESCRICAO"];
					$i++;

					$nomeTabela = $lin[ $key . "_NOME_TABELA"];
		            if(strpos($sTexto, htmlentities($lin[ $key ."_DESCRICAO"]))) {

		            	try {
		            		if ($nomeTabela == "pessoas" or $nomeTabela == "pessoas_dados" or $nomeTabela == "qualifica")  {

		            			$inners = [];
		            			switch ($nomeTabela) {
				                	case 'pessoas':
				                			$inners[] = " INNER JOIN pessoas_dados b ON (b.PSD_CODIGO = a.APR_CODIGO)";
				                			$inners[] = " INNER JOIN pessoas       c ON (c.PES_CODIGO = b.PES_CODIGO)";
				                		break;

				                	default:
				                			$inners[] = " INNER JOIN " . $nomeTabela . " b ON (a.APR_CODIGO = b.PSD_CODIGO) ";
				                		break;
				                }

				                $sql = "SELECT " . $lin[$key."_NOME_CAMPO"] . " AS CAMPO_TMP
				                		FROM ".$vFullTab[$j]." a
				                		". join(" ", $inners );


				                $sql.= "WHERE ".$vTrigFullTab[$j]."_PROTOCOLO = :PROTOCOLO; ";
				                $qryReplace = $this->conn->prepare($sql);
				                $qryReplace->bindParam(":PROTOCOLO", $sPrenota);
		            		}
		            		else {

				                $sql = "SELECT " . $lin[$key."_NOME_CAMPO"] . " AS CAMPO_TMP FROM " . $nomeTabela . " WHERE ".$vTrigFullTab[$j]."_PROTOCOLO = :PROTOCOLO; ";

				                $qryReplace = $this->conn->prepare($sql);
				                $qryReplace->bindParam(":PROTOCOLO", $sPrenota);
		            		}

			                $qryReplace->execute();
			                if($qryReplace->rowCount() > 0)
			                {
			                    $mac    = $qryReplace->fetch();
			                    $macroSub = $this->maskCampos(strtoupper($mac["CAMPO_TMP"]), $lin[ $key."_FORMATO"] );
								// if (preg_match('/^\d{4}\-\d{1,2}\-\d{1,2}$/', $mac["CAMPO_TMP"]))
								// 	$macroSub = $this->padroniza_datas_BR($mac["CAMPO_TMP"]);
								// if ($lin[$key."_NOME_TABELA"] == "qualifica")
								// 	$macroSub = strtoupper($macroSub);
								$macroSub = (!$decoder) ? $macroSub : utf8_encode($macroSub);
			                    $sTexto = str_replace(htmlentities($lin[$key . "_DESCRICAO"]), $macroSub , $sTexto);
			                }

		            	} catch (Exception $e) {
		            		continue;
		            	}
		            }
				}
			}
			$j++;
		}
		$sTexto = $this->converterMacrosGerais($sTexto);
		return $sTexto;
    }

	public function removeAdicionais($sString)
	{
		$sString = str_replace("(Transmitente)", "", str_replace("(Adquirente)", "",  str_replace("<-", "",  str_replace("->", "", $sString))));
		$sString = str_replace("(M)","",str_replace("(F)","",str_replace(" (M)","",str_replace(" (F)","",$sString))));
		return "<-" . rtrim($sString) . "->";
	}

	public function getQualificacao($sString)
	{
		if (strpos($sString, "(") !== false and strpos($sString, ")->") !== false)
		{
			if (strpos($sString, "(M)") !== false || strpos($sString, "(F)")) {
				$sString = str_replace("(M)","",str_replace("(F)","",str_replace(" (M)","",str_replace(" (F)","",$sString))));
			}
			$sString = explode("(", $sString);
			$sString = str_replace(")->", "", $sString[1]);
			return $sString;
		}
	}

    public function converteMacros($sTexto, $sTipo, $sPrenota, $sMatric = "", $sAto = "", $trig = "", $decoder = true, $iSeq = "", $tipo){

		$conn = new ConexaoMySQL;
		$sys = new Sistema;
		switch ($sTipo) {

			case "BALCAO":
		        $sql    = "SELECT * FROM macros_recibo";
		        $qry    = $conn->query($sql);
		        $cont   = 0;
		        $result = $qry->fetchAll();
		        foreach($result as $lin){
	            if( strpos( $sTexto, htmlentities( utf8_encode( $lin["MCR_DESCRICAO"] )  ) ) ){

	              if($lin["MCR_NOME_TABELA"] != "arc_registro"){
	               		$sql = "SELECT b.".$lin["MCR_NOME_CAMPO"]." AS CAMPO_TMP ";

	          			if ($lin["MCR_NOME_TABELA"] == "pessoas" or $lin["MCR_NOME_TABELA"] == "pessoas_dados" or $lin["MCR_NOME_TABELA"] == "arc_registro_partes"){

	            			if (strpos($lin["MCR_DESCRICAO"], "QUALIDADE")){
	            				$sql.= "FROM arc_registro a INNER JOIN ".$lin["MCR_NOME_TABELA"]." b ON (a.REG_PRENOTA = b.REG_PRENOTA AND a.REG_TPDOCUMENTO = b.REG_TPDOCUMENTO)";
	            			}else{
											if(strpos($lin["MCR_DESCRICAO"], "APRESENTANTE_NOME")){
												$sql.= "FROM arc_registro a
																INNER JOIN pessoas_dados c ON (a.APR_CODIGO = c.PSD_CODIGO)
																INNER JOIN ".$lin["MCR_NOME_TABELA"]." b ON (c.PES_CODIGO = b.PES_CODIGO)";
											}else{
												if(strpos($lin["MCR_DESCRICAO"], "APRESENTANTE")){
													$sql.= "FROM arc_registro a INNER JOIN ".$lin["MCR_NOME_TABELA"]." b ON (a.APR_CODIGO = b.PSD_CODIGO)";
												}else{
													if(strpos($lin["MCR_DESCRICAO"], "PARTE")){
														$sql.= "FROM arc_registro_partes a INNER JOIN ".$lin["MCR_NOME_TABELA"]." b ON (a.PES_CODIGO = b.PES_CODIGO)";
													}else{
														$sql.= "FROM arc_registro a INNER JOIN ".$lin["MCR_NOME_TABELA"]." b ON (a.PES_CODIGO = b.PES_CODIGO)";
													}
												}
											}
	            			}

	            		}else{
	            		 	$sql.= "FROM arc_registro a INNER JOIN ".$lin["MCR_NOME_TABELA"]." b ON (a.APR_CODIGO = b.REG_PRENOTA)";
	            		}

	          		}else{
									$sql = "SELECT a.".$lin["MCR_NOME_CAMPO"]." AS CAMPO_TMP ";
									$sql .= "FROM ".$lin["MCR_NOME_TABELA"]." a ";
	          		}
	              $sql.= " WHERE a.REG_PRENOTA = :REG_PRENOTA AND a.REG_TPDOCUMENTO = :REG_TPDOCUMENTO";
								// if (strpos($lin["MCR_DESCRICAO"], "PARTE")){
								// 	die($sql);
								// }
	              $qry = $conn->prepare($sql);
	              $qry->bindParam(":REG_PRENOTA", $sPrenota);
	              $qry->bindParam(":REG_TPDOCUMENTO", $tipo);
	              $qry->execute();
	              if($qry->rowCount() > 0){
	                  $mac      = $qry->fetch();
	                  $macroSub = $this->maskCampos(strtoupper($mac["CAMPO_TMP"]), $lin["MCR_FORMATO"] );

	                  $macroSub = (!$decoder) ? $macroSub : utf8_encode($macroSub);

	                  $sTexto   = str_replace(htmlentities($lin["MCR_DESCRICAO"]), $macroSub , $sTexto);
	              }
	            }
		        }
			break;
		}

		$sTexto = $this->converterMacrosGerais($sTexto);
		return $sTexto;
	}

	public function getIndiceByQualifcacao( $conn, $qualificacao, $protocolo, $dtRecepcao){
		$conn = new ConexaoMySQL;
        $sql = "SELECT PES_NOME
                FROM pessoas pes
                INNER JOIN indice_pessoal_reg idp ON (pes.PES_CODIGO = idp.PES_CODIGO )
                WHERE idp.QUA_CODIGO = $qualificacao AND ARS_RECEPCAO = :ARS_RECEPCAO  AND ARS_PROTOCOLO = :ARS_PROTOCOLO
                ORDER BY idp.RECNO DESC LIMIT 1";
        $qry = $conn->prepare($sql);
        $qry->bindParam(":ARS_PROTOCOLO", $protocolo);
        $qry->bindParam(":ARS_RECEPCAO", $dtRecepcao);
        $qry->execute();
        $res = $qry->fetch();
        return $res["PES_NOME"];
    }

    public function getIndiceSemQualificacao($conn, $protocolo, $dtRecepcao){
    	$conn = new ConexaoMySQL;
        $sql = "SELECT PES_NOME
                FROM pessoas pes
                INNER JOIN indice_pessoal_reg idp ON (pes.PES_CODIGO = idp.PES_CODIGO )
                WHERE  ARS_RECEPCAO = :ARS_RECEPCAO  AND ARS_PROTOCOLO = :ARS_PROTOCOLO
                ORDER BY idp.RECNO DESC LIMIT 2";
        $qry = $conn->prepare($sql);
        $qry->bindParam(":ARS_PROTOCOLO", $protocolo);
        $qry->bindParam(":ARS_RECEPCAO", $dtRecepcao);
        $qry->execute();
        $res = $qry->fetchAll();
        return $res;
    }
}
