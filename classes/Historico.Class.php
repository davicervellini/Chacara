<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class Historico extends GetterSetter{
	public $conn;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();

		if($codigo != ""){

			$sql = "SELECT HLO_DATA       	as HloData,
						   HLO_HORA       	as Hora,
						   USU_CODIGO    	as UsuCodigo,
						   HLO_MODULO    	as Modulo,
						   HLO_IP    		as Ip
					FROM historico_log WHERE RECNO = :RECNO" ;
			$qry = $this->conn->prepare($sql);
			$qry->bindParam(':RECNO', $codigo);
			$qry->execute();

			if($qry->rowCount() > 0){
				$result = $qry->fetchAll(PDO::FETCH_ASSOC);
				$this->setData($result);
			}
		}
	}

	public function listModulos(){

		$sSQL = "SELECT HLO_MODULO FROM historico_log GROUP BY HLO_MODULO ORDER BY HLO_MODULO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}
}