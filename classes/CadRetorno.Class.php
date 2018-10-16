<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class CadRetorno extends GetterSetter{
	public $conn;

	public function __construct($recno = ''){
		$this->conn = new ConexaoMySQL();
		if($recno != ""){

			$sql = "SELECT 	lre.RECNO 			 as Recno,
							lre.LRE_NOMEARQUIVO  as NomeArquivo,
							lre.CLI_CODIGO 	     as CliCodigo,
							lre.LRE_IDLINHA      as IdLinha,
							lre.LRE_DESCRICAO 	 as DescCampo,
							lre.LRE_POSINI 		 as PosInicial, 
							lre.LRE_POSFIM 		 as PosFinal, 
							lre.LRE_TAMANHO      as Tamanho, 
							lre.LRE_TIPOCAMPO    as TipoCampo, 
							lre.LRE_FORMATOGRAVA as DadoGravado, 
							lre.LRE_FORMATOLAY   as ExportarDado, 
							lre.LRE_CAMPOTMP     as CompararCampo, 
							lre.LRE_RESCAMPOTMP  as Resultado,
							lre.LRE_SUBCAMPOTMP  as SelectExecutar, 
							lre.LRE_PREENCHERCOM as CompletarCom, 
							lre.LRE_TABELA 	     as SelectTabela, 
							lre.LRE_CAMPOTABELA  as SelectCampoGravado, 
							lre.LRE_TIPOLINHA 	 as TipoLinha, 
							lre.LRE_IGNORALINHA  as IgnoraLinha
				FROM layout_retorno  lre				
				WHERE lre.RECNO = :RECNO";
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
			$deleteCad = "DELETE FROM layout_retorno WHERE CLI_CODIGO = ".$cliCodigo." and RECNO = '".$recno."'";
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

	function getUpdateRetorno($table,$whereUpdate, $dados ){
		// str_replace(find,replace,string,count) definição de srt_replace
		$fields      = "";
		$updateDados = "";
		foreach ($dados as $key => $value) {
			$setUpdate = str_replace('', "", $key)."='".$value."'";
			$updateDados .= ($updateDados != "") ? ",".$setUpdate  : $setUpdate ;
		}

		return "UPDATE $table SET $updateDados WHERE $whereUpdate";
	}

	function getInsertRetorno($table, array $dados){

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