<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class SequenciaProtocolo extends GetterSetter{
	public $conn;

	public function __construct(){
		$this->conn = new ConexaoMySQL();
		$sql = "SELECT RECNO        AS Recno,
					   PRENOTATD    AS PrenotaTd,
					   PRENOTAPJ    AS PrenotaPj,
					   CERTIDAO     AS Certidao,
					   ID_LOGRA     AS IdLogra,
					   ID_NOME      AS IdNome,
					   PORTA        AS Porta,
					   REGISTROTD   AS RegistroTd,
					   REGISTROPJ   AS RegistroPj,
					   FATURA       AS Fatura,
					   CERTIFICADO  AS Certificado,
					   MATRICULA    AS Matricula
			 	FROM sequencia";
		$qry = $this->conn->prepare($sql);
		// $qry->bindParam(':RECNO', $codigo);
		$qry->execute();
		if($qry->rowCount() > 0){
			$result = $qry->fetchAll(PDO::FETCH_ASSOC);
			$this->setData($result);
		}
	}
}
