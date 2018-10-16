<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class Especies extends GetterSetter{
	public $conn;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();
		if($codigo != ""){

			$sql = "SELECT RECNO     	   as Recno,
					       ESP_CODIGO      as Codigo,
						   ESP_DESCRICAO   as Descricao
					FROM especies WHERE ESP_CODIGO = :ESP_CODIGO" ;
			$qry = $this->conn->prepare($sql);
			$qry->bindParam(':ESP_CODIGO', $codigo);
			$qry->execute();

			if($qry->rowCount() > 0){
				$result = $qry->fetchAll(PDO::FETCH_ASSOC);
				$this->setData($result);
			}
		}
	}

	public function listEspecies(){

		$sSQL = "SELECT ESP_CODIGO, ESP_DESCRICAO FROM especies GROUP BY ESP_CODIGO, ESP_DESCRICAO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}	
}
