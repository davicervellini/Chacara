<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class ModelosCertidao extends GetterSetter{
	public $conn;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();

		if($codigo != ""){

			$sql = "SELECT MCE_DESCRICAO       as Descricao,
						   MCE_TEXTO           as Texto
					FROM modelos_certidao WHERE RECNO = :RECNO" ;
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
		$sSQL = "SELECT MCE_TEXTO FROM modelos_certidao WHERE MCE_CODIGO = :MCE_CODIGO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->bindParam(':MCE_CODIGO', $codigo);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function list(){
		$sSQL = "SELECT RECNO, MCE_CODIGO, MCE_DESCRICAO
                 FROM modelos_certidao
                 ORDER BY MCE_DESCRICAO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function resgataRecno($codigo){
		$sSQL = "SELECT RECNO
                 FROM modelos_certidao
                 WHERE MCE_CODIGO = :MCE_CODIGO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->bindParam(":MCE_CODIGO", $codigo);
		$sQRY->execute();

		$resp = $sQRY->fetch();

		return $resp['RECNO'];
	}
}