<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class Relatorios extends GetterSetter{
	public $conn;

	public function __construct($codigo = ''){
		if( $codigo == 'mysql'){
			$this->conn = new ConexaoMySQL;
		}else{
			$this->conn = new ConexaoMySQL;	
		}

		if($codigo != ""){

			$sql = "SELECT RECNO          	as Recno,
					       REL_ABREVIACAO	as abreviacao,
						   REL_NOME   		as nome,
						   REL_LOGO       	as logo
					FROM relatorios WHERE REL_ABREVIACAO = :REL_ABREVIACAO" ;
			$qry = $this->conn->prepare($sql);
			$qry->bindParam(':REL_ABREVIACAO', $codigo);
			$qry->execute();

			if($qry->rowCount() > 0){
				$qry = $qry->fetchAll(PDO::FETCH_ASSOC);
				$this->setData($qry);
			}
		}
	}	

	public function listRelatorios(){
		
		$sql = "SELECT RECNO, REL_ABREVIACAO, REL_NOME, REL_LOGO FROM relatorios ORDER BY REL_NOME";
		$qry = $this->conn->prepare($sql);
		$qry->execute();

		return $qry->fetchAll(PDO::FETCH_ASSOC);
	}

	public function listRelatoriosPorAbreviacao( $abreviacao ){
		
		$sql = "SELECT RECNO, REL_ABREVIACAO, REL_NOME, REL_LOGO 
				FROM relatorios
				WHERE REL_ABREVIACAO = :abreviacao
				ORDER BY REL_NOME";
		$qry = $this->conn->prepare($sql);
		$qry->bindParam(":abreviacao", $abreviacao);
		$qry->execute();

		return $qry->fetch();
	}
}
