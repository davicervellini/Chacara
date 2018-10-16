<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class Ocorrencias extends GetterSetter{
	public $conn;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();
		if($codigo != ""){

			$sql = "SELECT RECNO     	   as Recno,
					       OCO_CODIGO      as Codigo,
						   OCO_DESCRICAO   as Descricao,
						   OCO_CODTRANS    as CodTrans,
						   OCO_CODTRANSATO as CodTransato,
						   OCO_FINALIZADO  as Finalizado
					FROM ocorrencias WHERE OCO_CODIGO = :OCO_CODIGO" ;
			$qry = $this->conn->prepare($sql);
			$qry->bindParam(':OCO_CODIGO', $codigo);
			$qry->execute();

			if($qry->rowCount() > 0){
				$result = $qry->fetchAll(PDO::FETCH_ASSOC);
				$this->setData($result);
			}
		}
	}

	public function getDecOcorrencia($codigo){
		$sql = "SELECT OCO_DESCRICAO 
				FROM ocorrencias WHERE OCO_CODIGO = :OCO_CODIGO" ;
			$qry = $this->conn->prepare($sql);
			$qry->bindParam(':OCO_CODIGO', $codigo);
			$qry->execute();
			$ln  = $qry->fetch(PDO::FETCH_ASSOC);

		return $ln["OCO_DESCRICAO"];
	}

	public function identificaCodPorDesc($desc){
		$sql = "SELECT OCO_CODIGO
				FROM ocorrencias
				WHERE OCO_DESCRICAO = :OCO_DESCRICAO";
		$qry = $this->conn->prepare($sql);
		$qry->bindParam(':OCO_DESCRICAO', $desc);
		$qry->execute();
		$ln  = $qry->fetch(PDO::FETCH_ASSOC);

		return $ln["OCO_CODIGO"];
	}

	public function listarOcorrencias(){
		$sql = "SELECT OCO_CODIGO AS Codigo, 
					   OCO_DESCRICAO AS Descricao
				FROM ocorrencias";
		$qry = $this->conn->prepare($sql);
		$qry->execute();
		$res = $qry->fetchAll();		
		return $res;
	}

	public function excluirOcorrencia($ocoCodigo){
		try{			
			$sql = "DELETE FROM ocorrencias WHERE OCO_CODIGO = :OCO_CODIGO";
			$qry = $this->conn->prepare($sql);
			$qry->bindParam(":OCO_CODIGO", $ocoCodigo);
			$qry->execute();
			return "Ocorrência excluída com sucesso";
		}catch(Exception $e){
			return $e->getMessage();
		}
	}
}
