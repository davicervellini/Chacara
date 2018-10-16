<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class ModelosRecibo extends GetterSetter{
	public $conn;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();

		if($codigo != ""){

			$sql = "SELECT MOD_DESCRICAO       as Descricao,
						   MOD_TEXTO           as Texto
					FROM cadmodelos WHERE RECNO = :RECNO" ;
			$qry = $this->conn->prepare($sql);
			$qry->bindParam(':RECNO', $codigo);
			$qry->execute();

			if($qry->rowCount() > 0){
				$result = $qry->fetchAll(PDO::FETCH_ASSOC);
				$this->setData($result);
			}
		}
	}

	public function listModeloTxt($codigo){
		$sSQL = "SELECT MOD_TEXTO FROM cadmodelos WHERE MOD_CODIGO = :MOD_CODIGO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->bindParam(':MOD_CODIGO', $codigo);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function list(){
		$sSQL = "SELECT RECNO, MOD_CODIGO, MOD_DESCRICAO
                 FROM cadmodelos
                 ORDER BY MOD_DESCRICAO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}
}