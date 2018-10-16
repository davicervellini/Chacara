<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class Confirmacao extends GetterSetter{
	public $conn;

	public function __construct($recno = ''){
		$this->conn = new ConexaoMySQL();
		if($recno != ""){

			$sql = "SELECT 	lco.RECNO 			 as Recno,
							lco.LCO_NOMEARQUIVO  as NomeArquivo,
							lco.CLI_CODIGO 	     as CliCodigo,
							lco.LCO_IDLINHA      as IdLinha,
							lco.LCO_DESCRICAO 	 as DescCampo,
							lco.LCO_POSINI 		 as PosInicial, 
							lco.LCO_POSFIM 		 as PosFinal, 
							lco.LCO_TAMANHO      as Tamanho, 
							lco.LCO_TIPOCAMPO    as TipoCampo, 
							lco.LCO_FORMATOGRAVA as DadoGravado, 
							lco.LCO_FORMATOLAY   as ExportarDado, 
							lco.LCO_CAMPOTMP     as CompararCampo, 
							lco.LCO_RESCAMPOTMP  as Resultado,
							lco.LCO_SUBCAMPOTMP  as SelectExecutar, 
							lco.LCO_PREENCHERCOM as CompletarCom, 
							lco.LCO_TABELA 	     as SelectTabela, 
							lco.LCO_CAMPOTABELA  as SelectCampoGravado, 
							lco.LCO_TIPOLINHA 	 as TipoLinha, 
							lco.LCO_IGNORALINHA  as IgnoraLinha
				FROM layout_confirmacao  lco				
				WHERE lco.RECNO = :RECNO";
			$qry = $this->conn->prepare($sql);
			$qry->bindParam(':RECNO', $recno);			
			$qry->execute();			

			if($qry->rowCount() > 0){
				$result = $qry->fetchAll(PDO::FETCH_ASSOC);
				$this->setData($result);	
			}
		}
	}

	public function excluirDados($cliCodigo, $recno){
		try{
			$deleteCad = "DELETE FROM layout_confirmacao WHERE CLI_CODIGO = ".$cliCodigo." and RECNO = '".$recno."'";
			$qryDel = $this->conn->prepare($deleteCad);
			$qryDel->execute();

			$resp["message"] = "Ocorrência excluida com sucesso!";
			$resp["valid"]   = true;

			return $resp;
		}catch(Exception $e){
			$resp["message"] = "Erro ao excluir ocorrência. ".$e->getMessage();
			$resp["valid"] = false;
			return $resp;
		}
	}

	function getUpdateConfirm($table,$whereUpdate, $dados ){
		// str_replace(find,replace,string,count) definição de srt_replace
		$fields      = "";
		$updateDados = "";
		foreach ($dados as $key => $value) {
			$setUpdate = str_replace('', "", $key)."='".$value."'";
			$updateDados .= ($updateDados != "") ? ",".$setUpdate  : $setUpdate ;
		}

		return "UPDATE $table SET $updateDados WHERE $whereUpdate";
	}

	function getInsertConfirm($table, array $dados){

			$sql = "INSERT INTO $table ";
			$fields = '';
			$binds  = '';
			foreach ($dados as $key => $value) {
				$fields .= ($fields != "") ? ", " . str_replace(":","",$key) : str_replace(":", "" ,$key) ;
				$binds  .= ($binds != "")  ? "', '" . $value : "'".$value."";
			}
			return $sql . " ($fields) VALUES ($binds') ";
		}
}