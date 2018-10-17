<?php
	class Sistema{

		public function verifica_sessao($NOME,$SENHA){
			if(!isset($NOME) and !isset($SENHA)){
				if(!isset($_SESSION["usuario"]["usuLogin"]) or !isset($_SESSION["usuario"]["usuSenha"])){
					$url = INCLUDE_PATH."/login/";
					header("location: ".$url." ");
					exit;
				}
			}
		}

		function gera_codigo($tabela, $nomeConexao = ''){
			if($nomeConexao == ''){
				$conn = new ConexaoMySQL();
			}else{
				$conn = new ConexaoMySQL($nomeConexao);
			}
			$sSQL = "SELECT RECNO AS CODIGO FROM recnos WHERE TABELA = :sTabela ";
			$sQRY = $conn->prepare($sSQL);
			$sQRY->bindParam(':sTabela', $tabela);
			$sQRY->execute();
			if($sQRY->rowCount() > 0){
				$ln  = $sQRY->fetch(PDO::FETCH_ASSOC);
				$res = $ln["CODIGO"];

				try{
					$sDados = ["RECNO" => ( $res + 1 )];

					$sql = $this->getUpdate("recnos", " TABELA = '".$tabela."' ",  $sDados);
					$qry = $conn->prepare($sql);
					$res = $qry->execute();
					return $sDados['RECNO'];

				}catch(Exception $e){
				    die('Erro ao gerar código:'. $e->getMessage());
				}
			}else{
				$sql = "SELECT MAX(RECNO) AS maxRecno FROM $tabela ";
				$qry = $conn->prepare($sql);
				$qry->execute();
				$resMax = $qry->fetch();
				$novoRecno = $resMax["maxRecno"] + 1;

				$sDados = ["TABELA" => $tabela, "RECNO"  => $novoRecno];

				$sql = $this->getInsert("recnos", $sDados);
				die($sql);
				$qry = $conn->prepare($sql);
				$res = $qry->execute();
				return 1;
			}
		}

		function gera_codigo_sem_tabela($tabela, $nomeConexao = ''){
			if($nomeConexao == ''){
				$conn = new ConexaoMySQL();
			}else{
				$conn = new ConexaoMySQL($nomeConexao);
			}
			$sSQL = "SELECT RECNO AS CODIGO FROM recnos WHERE TABELA = :sTabela ";
			$sQRY = $conn->prepare($sSQL);
			$sQRY->bindParam(':sTabela', $tabela);
			$sQRY->execute();
			if($sQRY->rowCount() > 0){
				$ln  = $sQRY->fetch(PDO::FETCH_ASSOC);
				$res = $ln["CODIGO"];

				try{
					$sDados = ["RECNO" => ( $res + 1 )];

					$sql = $this->getUpdate("recnos", " TABELA = '".$tabela."' ",  $sDados);
					$qry = $conn->prepare($sql);
					$res = $qry->execute();
					return $sDados['RECNO'];

				}catch(Exception $e){
				    die('Erro ao gerar código:'. $e->getMessage());
				}
			}else{
				$novoRecno = 1;

				$sDados = ["TABELA" => $tabela, "RECNO"  => $novoRecno];

				$result = $this->vStrings($sDados);

				$sql = $this->getInsert("recnos", $sDados);
				$qry = $conn->prepare($sql);
				$res = $qry->execute();
				return 1;
			}
		}

		function limpaVars($str){
			$strAux = str_replace("(","",$str);
			$strAux = str_replace(")","",$strAux);
			$strAux = str_replace("-","",$strAux);
			$strAux = str_replace(".","",$strAux);
			$strAux = str_replace("/","",$strAux);
			$strAux = str_replace(" ","",$strAux);
			return $strAux;
		}

		function getInsert($table, array $dados){

			$sql = "INSERT INTO $table ";
			$fields = '';
			$binds  = '';
			foreach ($dados as $key => $value) {
				$fields .= ($fields != "") ? ", " . str_replace(":","",$key) : str_replace(":", "" ,$key) ;
				$binds  .= ($binds != "")  ? ", :" . $key : ":".$key;
			}
			return $sql . " ($fields) VALUES ($binds) ";
		}

		function getUpdate($table,$whereUpdate, $dados ){

			$fields      = "";
			$updateDados = "";
			foreach ($dados as $key => $value) {
				$setUpdate = str_replace(':', "", $key) . ' = :' . $key;
				$updateDados .= ($updateDados != "") ? ",".$setUpdate  : $setUpdate ;
			}

			return "UPDATE $table SET $updateDados WHERE $whereUpdate";
		}
		
		function getDelete($table,$whereDelete){
			return "DELETE FROM $table WHERE $whereDelete";
		}

		function getSelect($table, array $dados, $where){

			$sql = "SELECT";
			$fields = '';
			foreach ($dados as $key => $value) {
				$fields .= ($fields != "") ? ", " . str_replace(":","",$key) : str_replace(":", "" ,$key) ;
			}
			return $sql." ". $fields. " FROM ".$table. " WHERE ". $where ;
		}

		function padroniza_datas_BR($data){
			$aux = explode("-",$data);
			if($data != "" && $data != NULL){
				$result = $aux[2]."/".$aux[1]."/".$aux[0];
				return $result;
			}else{
				return "";
			}
		}

		function padroniza_datas_US($data){
			if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$data)) {
				$aux = explode("/",$data);
				if($data != ""){
					$result = $aux[2]."-".$aux[1]."-".$aux[0];
					return $result;
				}else{
					return "";
				}
			}else{
				return $data;
			}
		}

		function padroniza_datas_BD($data){
			if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$data)) {
				$aux = explode("/",$data);
				if($data != ""){
					$result = $aux[2]."-".$aux[0]."-".$aux[1];
					return $result;
				}else{
					return "";
				}
			}else{
				return $data;
			}
		}

		function padroniza_datas_BDUS($data){
			if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$data)) {
				$aux = explode("-",$data);
				if($data != ""){
					$result = $aux[2]."-".$aux[0]."-".$aux[1];
					return $result;
				}else{
					return "";
				}
			}else{
				return $data;
			}
		}

		function formataReal( $string ){
			return number_format($string, 2, ",", ".");
		}

		function formatarCEP( $string ){
			return $this->mask( $string, "#####-###" );
		}

		function formatarTelefone( $string ){
			return preg_replace('~.*(\d{2})[^\d]{0,7}(\d{5})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $string);
		}

		function formatarMilhar( $string ){
			return number_format( $string, 0, "", ".");
		}

		function higienizarCampo($doc){
			return str_replace(",","", str_replace(".","",str_replace("/","",str_replace("-", "", str_replace("(","",str_replace(")","",str_replace(" ","", trim($doc))))))));
		}

		function getValor($valor){
			if($valor != ''){
				return str_replace(",", ".", str_replace(".", "",$valor));
			}
		}

		function getUfs(){
			return ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
		}

		function historico($modulo,$acao, $options = '', $doaCodigo = null){
			$conn = new ConexaoMySQL();
	        $result = array(
	            'HLO_DATA'      => date('Y-m-d'),
	            'HLO_HORA'      => date('H:i:s'),
	            'USU_CODIGO'    => $_SESSION["usuario"]["usuCodigo"],
	            'HLO_USUARIO'   => strtoupper($_SESSION["usuario"]["usuLogin"]),
	            'HLO_MODULO'    => $modulo,
	            'HLO_ACAO'      => $acao,
	            'HLO_IP'        => $_SERVER["REMOTE_ADDR"]
	        );
	        try{

	        	require_once __DIR__ . "/../config.php";
				$sql = $this->getInsert('historico_log', $result);
				$qry = $conn->prepare($sql);
				$res = $qry->execute();
	        	if($res != ''){
	        		print 'Resposta: ' .$res;
	        	}

	        }catch(Exception $e){
	        	return $e->getMessage();
	        }
	    }

	    public function diferencaDias($dia = "Y-m-d", $nDias, $formato){
	    	$data = new DateTime(date($dia));

			$data->sub(new DateInterval('P'.$nDias.'D'));

			return $data->format($formato);
	    }

		public function identificarCorrecao($vListAntiga, $vListNova, $bind = false, $trigger = true){
	        $resp = "";
	        if($vListAntiga != ""){
	        	foreach($vListAntiga as $key => $value){
		            if(isset($vListNova[$key])){
		                if($vListNova[$key] != $value){
		                	$sKey = $key;
		                	if(!$bind){
		                		$sKey = str_replace(":", "", $key);
		                	}

		                	if(!$trigger){
		                		$sKey = mb_strcut($sKey, $bind == true ? 5 : 4);
		                	}

		                    $resp .= " ".$sKey." de ".$vListAntiga[$key]." para ".$vListNova[$key].";";
		                }
		            }
		        }
	        }

	        return $resp;
	    }

	    public function select($table, $sCampos, $dadosWhere = array(), $bind = false ){
	    	$conn = new ConexaoMySQL();
	    	$where = [];
		    if(count($dadosWhere) > 0){
		        foreach ($dadosWhere as $key => $value) {
		            $bindField = ":".$key;
		            $whereStmt = $key . " = " . $bindField;
		            array_push($where, $whereStmt);
		        }
		        $where = join(" AND ", $where);
		    }else{
		        $where = '';
		    }

		    $campos = '';
		    if(count($sCampos) > 0){
		    	foreach ($sCampos as $key => $value) {
			    	$key = str_replace(":", "" ,$key);
			    	$campos .= $campos != '' ? ",".$key : $key ;
			    }
		    }else{
		    	$campos = '*';
		    }


	    	$sSQL = "SELECT ".$campos." FROM $table WHERE 1 = 1 AND ".$where." ";
		    $query = $conn->prepare($sSQL);
		    if(count($dadosWhere) > 0){
		        foreach ($dadosWhere as $key => &$value) {
		            $query->bindParam(':'.$key, $value);
		        }
		    }
		    $query->execute();

		    $ln = $query->fetch(PDO::FETCH_ASSOC);
		    if($bind){
			    $sDados = [];
			    foreach ($ln as $key => $value) {
			    	$sDados[':'.$key] = $value;
			    }
			    return $sDados;
		    }else{
		    	return $ln;
		    }

	    }

	    public function vStrings(array $sDados){
	        $campos = array();
	        $dados  = array();
	        $result = array();
	        foreach ($sDados as $key => $value) {
	            array_push($campos, $key);
	            array_push($dados, $value);
	        }

	        $sCampo = new vStrings($campos);
	        $sDados = new vStrings($dados);

	        $result['campos'] = $sCampo->getVStrings();
	        $result['dados']  = $sDados->getVStrings();

	        return $result;
	    }

	    public function mesExtenso($nMes){
	        switch($nMes){
	            case "1":
	                return "Janeiro";
	            break;
	            case "2":
	                return "Fevereiro";
	            break;
	            case "3":
	                return "Mar&ccedil;o";
	            break;
	            case "4":
	                return "Abril";
	            break;
	            case "5":
	                return "Maio";
	            break;
	            case "6":
	                return "Junho";
	            break;
	            case "7":
	                return "Julho";
	            break;
	            case "8":
	                return "Agosto";
	            break;
	            case "9":
	                return "Setembro";
	            break;
	            case "10":
	                return "Outubro";
	            break;
	            case "11":
	                return "Novembro";
	            break;
	            case "12":
	                return "Dezembro";
	            break;
	        }
	    }

	    public function mesExtensoAoContrario($nMes){
	        switch($nMes){
	            case "Janeiro":
	                return "01";
	            break;
	            case "Fevereiro":
	                return "02";
	            break;
	            case "Março":
	                return "03";
	            break;
	            case "Abril":
	                return "04";
	            break;
	            case "Maio":
	                return "05";
	            break;
	            case "Junho":
	                return "06";
	            break;
	            case "Julho":
	                return "07";
	            break;
	            case "Agosto":
	                return "08";
	            break;
	            case "Setembro":
	                return "09";
	            break;
	            case "Outubro":
	                return "10";
	            break;
	            case "Novembro":
	                return "11";
	            break;
	            case "Dezembro":
	                return "12";
	            break;
	        }
	    }

		function descStatus($sCodigo, $obj)
		{
			$sql = "select TPS_DESCRICAO from tipo_status where TPS_ID = :TPS_ID";
			$qry = $obj->prepare($sql);
			$qry->bindParam(":TPS_ID",$sCodigo);
			$qry->execute();
			if($qry->rowCount() > 0)
			{
				$result = $qry->fetchAll();
				foreach($result as $lin)
					return $lin["TPS_DESCRICAO"];
			}
		}

		function limpaDoc($doc){
		    return str_replace(",","",str_replace(".","",str_replace("-","",str_replace("/","",$doc))));
		}

		function array_msort($array, $cols)
		{
		    $colarr = array();
		    foreach ($cols as $col => $order) {
		        $colarr[$col] = array();
		        foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
		    }
		    $eval = 'array_multisort(';
		    foreach ($cols as $col => $order) {
		        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
		    }
		    $eval = substr($eval,0,-1).');';
		    eval($eval);
		    $ret = array();
		    foreach ($colarr as $col => $arr) {
		        foreach ($arr as $k => $v) {
		            $k = substr($k,1);
		            if (!isset($ret[$k])) $ret[$k] = $array[$k];
		            $ret[$k][$col] = $array[$k][$col];


		        }
		    }

		    return $ret;

		}

		function formataDoc($sDoc,$iTamanho)
		{
		//    $sDocumento = "";
		    switch($iTamanho)
		    {
		        case 11:
		            $sDocumento = $sDoc[0].$sDoc[1].$sDoc[2].".".
		                $sDoc[3].$sDoc[4].$sDoc[5].".".
		                $sDoc[6].$sDoc[7].$sDoc[8]."-".
		                $sDoc[9].$sDoc[10];
		            break;
		        case 14:
		            $sDocumento = $sDoc[0].$sDoc[1].".".
		                $sDoc[2].$sDoc[3].$sDoc[4].".".
		                $sDoc[5].$sDoc[6].$sDoc[7]."/".
		                $sDoc[8].$sDoc[9].$sDoc[10].$sDoc[11]."-".
		                $sDoc[12].$sDoc[13];
		            break;

		        default:
		            if (strlen($sDoc) < 11) {
			            $sDocumento = @$sDoc[0].@$sDoc[1].@$sDoc[2].".".
						              @$sDoc[3].@$sDoc[4].@$sDoc[5].".".
						              @$sDoc[6].@$sDoc[7].@$sDoc[8]."-".
						              @$sDoc[9].@$sDoc[10];
		            } elseif (strlen($sDoc) > 11 and strlen($sDoc) < 14) {
			            $sDocumento = @$sDoc[0].@$sDoc[1].".".
			               		 	  @$sDoc[2].@$sDoc[3].@$sDoc[4].".".
			                		  @$sDoc[5].@$sDoc[6].@$sDoc[7]."/".
			                		  @$sDoc[8].@$sDoc[9].@$sDoc[10].@$sDoc[11]."-".
			                		  @$sDoc[12].@$sDoc[13];
		            }
		            // $sDocumento = $sDoc;
		            break;
		    }
		    return $sDocumento;
		}

	    function validaCPF($cpf = null) {

	        // Verifica se um número foi informado
	        if(empty($cpf)) {
	            return false;
	        }

	        // Elimina possivel mascara
	        $cpf = preg_replace('[^0-9]', '', $cpf);
	        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

	        // Verifica se o numero de digitos informados é igual a 11
	        if (strlen($cpf) != 11) {
	            return false;
	        }
	        // Verifica se nenhuma das sequências invalidas abaixo
	        // foi digitada. Caso afirmativo, retorna falso
	        else if ($cpf == '00000000000' ||
	            $cpf == '11111111111' ||
	            $cpf == '22222222222' ||
	            $cpf == '33333333333' ||
	            $cpf == '44444444444' ||
	            $cpf == '55555555555' ||
	            $cpf == '66666666666' ||
	            $cpf == '77777777777' ||
	            $cpf == '88888888888' ||
	            $cpf == '99999999999') {
	            return false;
	         // Calcula os digitos verificadores para verificar se o
	         // CPF é válido
	         } else {

	            for ($t = 9; $t < 11; $t++) {

	                for ($d = 0, $c = 0; $c < $t; $c++) {
	                    $d += $cpf{$c} * (($t + 1) - $c);
	                }
	                $d = ((10 * $d) % 11) % 10;
	                if ($cpf{$c} != $d) {
	                    return false;
	                }
	            }

	            return true;
	        }
	    }

	    // VERFICA CNPJ
	    function validaCNPJ($cnpj) {
	        if (strlen($cnpj) <> 14)
	        return false;

	        $soma = 0;

	        $soma += ($cnpj[0] * 5);
	        $soma += ($cnpj[1] * 4);
	        $soma += ($cnpj[2] * 3);
	        $soma += ($cnpj[3] * 2);
	        $soma += ($cnpj[4] * 9);
	        $soma += ($cnpj[5] * 8);
	        $soma += ($cnpj[6] * 7);
	        $soma += ($cnpj[7] * 6);
	        $soma += ($cnpj[8] * 5);
	        $soma += ($cnpj[9] * 4);
	        $soma += ($cnpj[10] * 3);
	        $soma += ($cnpj[11] * 2);

	        $d1 = $soma % 11;
	        $d1 = $d1 < 2 ? 0 : 11 - $d1;

	        $soma = 0;
	        $soma += ($cnpj[0] * 6);
	        $soma += ($cnpj[1] * 5);
	        $soma += ($cnpj[2] * 4);
	        $soma += ($cnpj[3] * 3);
	        $soma += ($cnpj[4] * 2);
	        $soma += ($cnpj[5] * 9);
	        $soma += ($cnpj[6] * 8);
	        $soma += ($cnpj[7] * 7);
	        $soma += ($cnpj[8] * 6);
	        $soma += ($cnpj[9] * 5);
	        $soma += ($cnpj[10] * 4);
	        $soma += ($cnpj[11] * 3);
	        $soma += ($cnpj[12] * 2);


	        $d2 = $soma % 11;
	        $d2 = $d2 < 2 ? 0 : 11 - $d2;

	        if ($cnpj[12] == $d1 && $cnpj[13] == $d2) {
	            return true;
	        }
	        else {
	            return false;
	        }
	    }

		function tipoDocumento($doc){
		    if(strlen($doc) == 9 ){
		        return "RG";
		    }elseif(strlen($doc) == 11){
		        return "CPF";
		    }elseif(strlen($doc) > 11){
		        return "CNPJ";
		    }
		}

		function retiraTagsTinyMce($string) {
		    return ($string != "")? trim(html_entity_decode(htmlspecialchars_decode(strip_tags($string)))) : "";
		}

		function comparaTinymce($antigo, $novo){
			$antigo = $this->retiraTagsTinyMce($antigo);
			$novo   = $this->retiraTagsTinyMce($novo);
			similar_text($antigo, $novo, $porcentagem);
			if ($porcentagem == 100){
				return "";
			}else{
			    $doComeco = strspn($antigo ^ $novo, "\0");
			    $doFinal  = strspn(strrev($antigo) ^ strrev($novo), "\0");

			    $finalAntigo = strlen($antigo) - $doFinal;
			    $finalNovo   = strlen($novo) - $doFinal;

			    $comeco = substr($novo, 0, $doComeco);
			    $final  = substr($novo, $finalNovo);
			    $novo_diff   = substr($novo, $doComeco, $finalNovo - $doComeco);
			    $antigo_diff = substr($antigo, $doComeco, $finalAntigo - $doComeco);

			    $textoFinal = "O TRECHO: \"$antigo_diff\" FOI ALTERADO PARA: \"$novo_diff\". TEXTO ANTIGO: \"$comeco$antigo_diff$final\"";
			    return $textoFinal;
			}
		}

		function mask($val, $mask = null){
			$maskared = '';
			$k = 0;
			if($mask == null || $mask == ""){
				switch (strlen($val)) {
					case 11:
						$mask = "###.###.###-##";
					break;

					case 9:
						$mask = "##.###.###-#";
					break;

					case 14:
						$mask = "##.###.###/####-##";
					break;

					case 8:
						$mask = "#####-###";
					break;

					default:
						return $val;
					break;
				}
			}
			if($val != ""){
				for($i = 0; $i<=strlen($mask)-1; $i++){
					if($mask[$i] == '#'){
						if(isset($val[$k]))
						$maskared .= $val[$k++];
					}else{
						if(isset($mask[$i]))
						$maskared .= $mask[$i];
					}
				}
			}
			return $maskared;
		}

		public function unlinkRecursive($dir, $deleteRootToo)
		{
		    if(!$dh = @opendir($dir))
		    {
		        return;
		    }
		    while (false !== ($obj = readdir($dh)))
		    {
		        if($obj == '.' || $obj == '..')
		        {
		            continue;
		        }

		        if (!@unlink($dir . '/' . $obj))
		        {
		            $this->unlinkRecursive($dir.'/'.$obj, true);
		        }
		    }
		    closedir($dh);
		    if ($deleteRootToo)
		    {
		        @rmdir($dir);
		    }
		    return;
		}

		public function addZeros($iNumero, $qtdAdd)
		{
			$iZeros = "";
			for($i = 0; $i < $qtdAdd; $i++)
			{
				$iZeros.= "0";
			}
			$iZeros = mb_strcut($iZeros,0,strlen($iZeros)-strlen($iNumero));
			$iValor = $iZeros.$iNumero;
			return $iValor;
		}

	    function mysql_escape_mimic($inp) {
	        if(is_array($inp))
	            return array_map(__METHOD__, $inp);

	        if(!empty($inp) && is_string($inp)) {
	            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "''", '\\"', '\\Z'), $inp);
	        }

	        return $inp;
	    }

		function removeAcentos($string, $slug = false) {
			if($this->codificacao($string) === 'UTF-8')
	    		$string = utf8_decode($string);

		  $string = strtolower($string);
		  // Código ASCII das vogais
		  $ascii['a'] = range(224, 230);
		  $ascii['e'] = range(232, 235);
		  $ascii['i'] = range(236, 239);
		  $ascii['o'] = array_merge(range(242, 246), array(240, 248));
		  $ascii['u'] = range(249, 252);
		  // Código ASCII dos outros caracteres
		  $ascii['b'] = array(223);
		  $ascii['c'] = array(231);
		  $ascii['d'] = array(208);
		  $ascii['n'] = array(241);
		  $ascii['y'] = array(253, 255);
		  foreach ($ascii as $key=>$item) {
		    $acentos = '';
		    foreach ($item AS $codigo) $acentos .= chr($codigo);
		    $troca[$key] = '/['.$acentos.']/i';
		  }
		  $string = preg_replace(array_values($troca), array_keys($troca), $string);
		  // Slug?
		  if ($slug) {
		    // Troca tudo que não for letra ou número por um caractere ($slug)
		    $string = preg_replace('/[^a-z0-9]/i', $slug, $string);
		    // Tira os caracteres ($slug) repetidos
		    $string = preg_replace('/' . $slug . '{2,}/i', $slug, $string);
		    $string = trim($string, $slug);
		  }
		  return $string;
		}
		function codificacao($string) {
	        return mb_detect_encoding($string.'x', 'UTF-8, ISO-8859-1');
	    }

    	public function isDiaUtil($data, $nomeConexao = ""){
    		if($nomeConexao == ''){
				$conn = new ConexaoMySQL();
			}else{
				$conn = new ConexaoMySQL($nomeConexao);
			}
		    //Colocamos em um array os dias de fim de semana (sábado e domingo)
		    $fds = array('6', '0');

		    //Verificamos qual é o dia da semana
		    $diaSemana = date('w', strtotime($data));

		    //Fazemos um select com count para saber se existem feriados na data passada no parametro $data
		    $select = "SELECT COUNT(0) as total FROM feriados WHERE data = :DATA";
		    $sQRY = $conn->prepare($select);
			$sQRY->bindParam(':DATA', $data);
			$sQRY->execute();
			$row  = $sQRY->fetch(PDO::FETCH_ASSOC);

		    //Aqui verficamos se é o dia útil
		    if(in_array($diaSemana, $fds) || $row['total'] > 0) {
		        return false;
		    }else{
		        return true;
		    }
		}

		public function tipoAto($tipoAto=''){
			$ato = "";
			switch ($tipoAto) {
				case 'REGISTRO':
					$ato = "R";
				break;

				case 'AVERBACAO':
					$ato = "AV";
				break;

				case 'AVERBAÇÃO':
					$ato = "AV";
				break;

				case 'RETROATIVO':
					$ato = "RE";
				break;

				case 'ABERTURA':
					$ato = "AB";
				break;
			}
			return $ato;
		}

		public function getUsuReciboTalao(){
			$conn = new ConexaoMySQL();
			$select = "SELECT usu.USU_NOME, usu.USU_CODIGO
						FROM usuarios usu
						LEFT JOIN permissao per ON (usu.USU_CODIGO = per.USU_CODIGO)
						WHERE ((per.FORM = 'F_RegAnalise' OR per.FORM = 'F_RegBaixas' OR per.FORM = 'F_RegManutencao' OR per.FORM = 'F_RegManutencao' OR per.FORM = 'F_RegEntrada' OR per.FORM = 'F_RegReentrada') AND per.ACESSO = 1 OR usu.USU_ADMIN = 1)
						GROUP BY usu.USU_NOME, usu.USU_CODIGO
						ORDER BY usu.USU_NOME";
		    $sQRY = $conn->prepare($select);
			$sQRY->execute();
			return $sQRY->fetchAll(PDO::FETCH_ASSOC);
		}

		public function getOrdemOficio($sTipo, $nomeConexao = ''){
			if($nomeConexao == ''){
				$conn = new ConexaoMySQL();
			}else{
				$conn = new ConexaoMySQL($nomeConexao);
			}

			$ws   = new ArcaTDPJ_WS;
			if ($sTipo == "recebidos")
				$sNomeCampo = "ORDEM_RECEBIDOS";
			else
				$sNomeCampo = "ORDEM_EXPEDIDOS";

			$sSQL = "SELECT RECNO AS CODIGO, " . $sNomeCampo . " FROM sequencia";
			$sQRY = $conn->prepare($sSQL);
			$sQRY->execute();

			if($sQRY->rowCount() > 0){
				$ln  = $sQRY->fetch(PDO::FETCH_ASSOC);
				$recno = $ln["CODIGO"];
				$ordem = $ln[$sNomeCampo];

				try{
					$sDados = [$sNomeCampo => ( $ordem + 1 )];

					$result = $this->vStrings($sDados);
					$res = $ws->corrigirRegistro( getToken( $conn->db() ) ,"sequencia", " RECNO = '".$recno."' ",  $result["campos"], $result["dados"]);
					return $sDados[$sNomeCampo];

				}catch(Exception $e){
				    die('Erro ao gerar código:'. $e->getMessage());
				}
			}
		}

		public function diferencaHora($horario1, $horario2){
			$entrada = $horario1;
			$saida = $horario2;
			$hora1 = explode(":",$entrada);
			$hora2 = explode(":",$saida);
			$acumulador1 = ($hora1[0] * 3600) + ($hora1[1] * 60) + $hora1[2];
			$acumulador2 = ($hora2[0] * 3600) + ($hora2[1] * 60) + $hora2[2];
			$resultado = $acumulador2 - $acumulador1;
			$hora_ponto = floor($resultado / 3600);
			$resultado = $resultado - ($hora_ponto * 3600);
			$min_ponto = floor($resultado / 60);
			$resultado = $resultado - ($min_ponto * 60);
			$secs_ponto = $resultado;
			//Grava na variável resultado final
			$tempo = [
				"h"    => $hora_ponto,
				"i"    => $min_ponto,
				"s"    => $secs_ponto
			];
			return $tempo;
		}

		public function dataExtenso( $data ){
			setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
			date_default_timezone_set('America/Sao_Paulo');

			$dateTime = strftime('%d de %B de %Y', strtotime($this->padroniza_datas_US($data)));

			$content = utf8_encode($dateTime);

			mb_internal_encoding('UTF-8');
			if(!mb_check_encoding($content, 'UTF-8')
				OR !($content === mb_convert_encoding(mb_convert_encoding($content, 'UTF-32', 'UTF-8' ), 'UTF-8', 'UTF-32'))) {

				$content = mb_convert_encoding($content, 'UTF-8');
			}

			return utf8_decode(mb_convert_case($content, MB_CASE_UPPER, "UTF-8"));
		}

		public function dinheiroExtenso($valor = 0, $maiusculas = false) {
			if(!$maiusculas){
			$singular = ["centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão"];
			$plural = ["centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões"];
			$u = ["", "um", "dois", "três", "quatro", "cinco", "seis",  "sete", "oito", "nove"];
			}else{
			$singular = ["CENTAVO", "REAL", "MIL", "MILHÃO", "BILHÃO", "TRILHÃO", "QUADRILHÃO"];
			$plural = ["CENTAVOS", "REAIS", "MIL", "MILHÕES", "BILHÕES", "TRILHÕES", "QUADRILHÕES"];
			$u = ["", "um", "dois", "TRÊS", "quatro", "cinco", "seis",  "sete", "oito", "nove"];
			}

			$c = ["", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos"];
			$d = ["", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa"];
			$d10 = ["dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove"];

			$z = 0;
			$rt = "";

			$valor = number_format($valor, 2, ".", ".");
			$inteiro = explode(".", $valor);
			for($i=0;$i<count($inteiro);$i++)
			for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
			$inteiro[$i] = "0".$inteiro[$i];

			$fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);
			for ($i=0;$i<count($inteiro);$i++) {
			$valor = $inteiro[$i];
			$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
			$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
			$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

			$r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd &&
			$ru) ? " e " : "").$ru;
			$t = count($inteiro)-1-$i;
			$r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
			if ($valor == "000")$z++; elseif ($z > 0) $z--;
			if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t];
			if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
			}

			if(!$maiusculas){
			$return = $rt ? $rt : "zero";
			} else {
			if ($rt) $rt = str_replace(" E "," e ",ucwords($rt));
			$return = ($rt) ? ($rt) : "Zero" ;
			}

			if(!$maiusculas){
				return str_replace(" E "," e ",ucwords($return));
			}else{
				return strtoupper($return);
			}
		}

	}
