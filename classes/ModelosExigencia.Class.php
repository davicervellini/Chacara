<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class ModelosExigencia extends GetterSetter{
	public $conn;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();

		if($codigo != ""){

			$sql = "SELECT MEX_DESCRICAO       as Descricao,
						   MEX_TEXTO           as Texto
					FROM modelos_exigencia WHERE RECNO = :RECNO" ;
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
		$sSQL = "SELECT MEX_TEXTO FROM modelos_exigencia WHERE MEX_CODIGO = :MEX_CODIGO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->bindParam(':MEX_CODIGO', $codigo);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function list(){
		$sSQL = "SELECT RECNO, MEX_CODIGO, MEX_DESCRICAO
                 FROM modelos_exigencia
                 ORDER BY MEX_DESCRICAO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function resgataRecno($codigo){
		$sSQL = "SELECT RECNO
                 FROM modelos_exigencia
                 WHERE MEX_CODIGO = :MEX_CODIGO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->bindParam(":MEX_CODIGO", $codigo);
		$sQRY->execute();

		$resp = $sQRY->fetch();

		return $resp['RECNO'];
	}
}