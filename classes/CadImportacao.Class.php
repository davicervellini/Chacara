<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class CadImportacao extends GetterSetter{
	public $conn;

	public function __construct($recno = ''){
		$this->conn = new ConexaoMySQL();
		if($recno != ""){

			$sql = "SELECT 	lim.RECNO 			 as Recno,
							lim.LIM_NOMEARQUIVO  as NomeArquivo,
							lim.CLI_CODIGO 	     as CliCodigo,
							lim.LIM_IDLINHA      as IdLinha,
							lim.LIM_DESCRICAO 	 as DescCampo,
							lim.LIM_POSINI 		 as PosInicial, 
							lim.LIM_POSFIM 		 as PosFinal, 
							lim.LIM_TAMANHO      as Tamanho, 
							lim.LIM_TIPOCAMPO    as TipoCampo, 
							lim.LIM_FORMATOGRAVA as DadoGravado, 
							lim.LIM_FORMATOLAY   as ExportarDado, 
							lim.LIM_CAMPOTMP     as CompararCampo, 
							lim.LIM_RESCAMPOTMP  as Resultado,
							lim.LIM_SUBCAMPOTMP  as SelectExecutar, 
							lim.LIM_PREENCHERCOM as CompletarCom, 
							lim.LIM_TABELA 	     as SelectTabela, 
							lim.LIM_CAMPOTABELA  as SelectCampoGravado, 
							lim.LIM_TIPOLINHA 	 as TipoLinha, 
							lim.LIM_IGNORALINHA  as IgnoraLinha
				FROM layout_importacao  lim				
				WHERE lim.RECNO = :RECNO";
			$qry = $this->conn->prepare($sql);
			$qry->bindParam(':RECNO', $recno);			
			$qry->execute();			

			if($qry->rowCount() > 0){
				$result = $qry->fetchAll(PDO::FETCH_ASSOC);
				$this->setData($result);	
			}
		}
	}
}	