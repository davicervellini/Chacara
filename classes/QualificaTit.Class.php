<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class QualificaTit extends GetterSetter{
	public $conn;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();

		if($codigo != ""){

			$sql = "SELECT RECNO     				as Recno,
						   QUT_CODIGO				as Codigo,
						   QUT_CODNAT     			as CodNat,
					       QUT_DESCRICAO            as Descricao,
						   QUT_NATUREZA            	as Natureza,
						   QUT_SEQUENCIA         	as Sequencia,
						   QUT_CODUSUARIO           as CodUsuario
					FROM qualificatit WHERE QUT_CODIGO = :QUT_CODIGO" ;
			$qry = $this->conn->prepare($sql);
			$qry->bindParam(':QUT_CODIGO', $codigo);
			$qry->execute();

			if($qry->rowCount() > 0){
				$result = $qry->fetchAll(PDO::FETCH_ASSOC);
				$this->setData($result);
			}
		}
	}	

	public function listNatureza(){

		$sSQL = "SELECT NAT_DESCCODIGO, NAT_DESCRICAO FROM naturezas WHERE NAT_DESCCODIGO IS NOT NULL ORDER BY NAT_DESCRICAO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function descNatureza($codNat){

		$sSQL = "SELECT NAT_DESCRICAO FROM naturezas WHERE NAT_DESCCODIGO = '".$codNat."' ";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		$nat = $sQRY->fetch();

		return $nat["NAT_DESCRICAO"];
	}


}