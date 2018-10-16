<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class ModelosNotificacao extends GetterSetter{
	public $conn;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();

		if($codigo != ""){

			$sql = "SELECT MDN_DESCRICAO        as Descricao,
						   MDN_TEXTO                  as Texto,
						   CLI_CODIGO                 as CliCodigo
					FROM modelos_notificacao WHERE RECNO = :RECNO" ;
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
		$sSQL = "SELECT MDN_TEXTO FROM modelos_notificacao WHERE MDN_CODIGO = :MDN_CODIGO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->bindParam(':MDN_CODIGO', $codigo);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function list(){
		$sSQL = "SELECT RECNO, MDN_CODIGO, MDN_DESCRICAO
                 FROM modelos_notificacao
                 ORDER BY MDN_DESCRICAO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function resgataRecno($codigo){
		$sSQL = "SELECT RECNO
                 FROM modelos_notificacao
                 WHERE MDN_CODIGO = :MDN_CODIGO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->bindParam(":MDN_CODIGO", $codigo);
		$sQRY->execute();

		$resp = $sQRY->fetch();

		return $resp['RECNO'];
	}
}
