<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class ModelosAssinatura extends GetterSetter{
	public $conn;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();

		if($codigo != ""){

			$sql = "SELECT RECNO					  as Recno,
						   MDA_CODIGO 				  as codAssinatura,
						   MDA_DESCRICAO              as Descricao,
						   MDA_IMAGEM                 as Imagem,
						   CLI_CODIGO                 as CliCodigo
					FROM modelos_assinatura WHERE RECNO = :RECNO" ;
			$qry = $this->conn->prepare($sql);
			$qry->bindParam(':RECNO', $codigo);
			$qry->execute();

			if($qry->rowCount() > 0){
				$result = $qry->fetchAll(PDO::FETCH_ASSOC);
				$this->setData($result);
			}
		}
	}

	public function listModeloImg($codigo){
		$sSQL = "SELECT MDA_IMAGEM FROM modelos_assinatura WHERE MDA_CODIGO = :MDA_CODIGO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->bindParam(':MDA_CODIGO', $codigo);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function list(){
		$sSQL = "SELECT RECNO, MDA_CODIGO, MDA_DESCRICAO
                 FROM modelos_assinatura
                 ORDER BY MDA_DESCRICAO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function resgataRecno($codigo){
		$sSQL = "SELECT RECNO
                 FROM modelos_assinatura
                 WHERE MDA_CODIGO = :MDA_CODIGO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->bindParam(":MDA_CODIGO", $codigo);
		$sQRY->execute();

		$resp = $sQRY->fetch();

		return $resp['RECNO'];
	}

	public function getDescricao($codigo){
		$sSQL = "SELECT MDA_DESCRICAO
				 FROM modelos_assinatura
				 WHERE RECNO = :RECNO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->bindParam(':RECNO', $codigo);
		$sQRY->execute();

		$resp = $sQRY->fetch();

		return $resp['MDA_DESCRICAO'];
	}

	public function getImagem($codigo){
		$sSQL = "SELECT MDA_IMAGEM
				 FROM modelos_assinatura
				 WHERE RECNO = :RECNO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->bindParam(':RECNO', $codigo);;
		$sQRY->execute();

		$resp = $sQRY->fetch();

		return $resp['MDA_IMAGEM'];
	}

	public function getCliCodigo($codigo){
		$sSQL = "SELECT CLI_CODIGO
				 FROM modelos_assinatura
				 WHERE RECNO = :RECNO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->bindParam(':RECNO', $codigo);
		$sQRY->execute();

		$resp = $sQRY->fetch();

		return $resp['CLI_CODIGO'];
	}

	public function getCodigo($codigo){
		$sSQL = "SELECT MDA_CODIGO
				 FROM modelos_assinatura
				 WHERE RECNO = :RECNO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->bindParam(':RECNO', $codigo);
		$sQRY->execute();

		$resp = $sQRY->fetch();

		return $resp['MDA_CODIGO'];
	}
}
