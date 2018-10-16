<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class ModelosAberturaEncerramento extends GetterSetter{
	public $conn;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();
		if($codigo != ""){
			$sql = "SELECT RECNO           AS Recno,
                           MAE_CODIGO      AS Codigo,
                           MAE_NOME_MODELO AS Descricao,
                           MAE_TIPO_ATO    AS TipoAto,
                           MAE_TEXTO       AS Texto
					FROM modelos_abertura_encerramento
					WHERE RECNO = :RECNO" ;
			$qry = $this->conn->prepare($sql);
			$qry->bindParam(':RECNO', $codigo);
			$qry->execute();

			if($qry->rowCount() > 0){
				$result = $qry->fetchAll(PDO::FETCH_ASSOC);
				$this->setData($result);
			}
		}
	}

	public function resgataRecno(){

		$sSQL = "SELECT TOP 1 RECNO FROM modelos_abertura_encerramento ORDER BY RECNO DESC";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();		
		$ln  = $sQRY->fetch(PDO::FETCH_ASSOC);

		return $ln["RECNO"];
	}

	public function list(){

		$sSQL = "SELECT RECNO, MAE_CODIGO, MAE_NOME_MODELO FROM modelos_abertura_encerramento ORDER BY MAE_CODIGO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function listMacrosExig(){

		$sSQL = "SELECT RECNO, MAE_CODIGO, MAE_NOME_MODELO FROM modelos_abertura_encerramento ORDER BY MAE_CODIGO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

}