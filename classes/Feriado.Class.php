<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class Feriado extends GetterSetter{
	public $conn;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();

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

	public function inverteDtFeriado($data){
		if($data != ""){
		    $aux = explode("-",$data);
		    $result = $aux[1]."-".$aux[0];
			return $result;
		}else{
			return "";
		}
	}

	public function list(){

		$sSQL = "SELECT RECNO, FER_DATA, FER_DESCRICAO FROM cadferiado ORDER BY FER_DATA ASC";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function resgataRecno($ferCodigo){

		$sSQL = "SELECT RECNO FROM cadferiado WHERE FER_CODIGO = :FER_CODIGO" ;
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->bindParam(':FER_CODIGO', $ferCodigo);
		$sQRY->execute();
		
		$ln  = $sQRY->fetch(PDO::FETCH_ASSOC);

		return $ln["RECNO"];
	}
}