<?php


class Versionamento{
	public $conn;

	public function getVersao( $codCliente ){
		$conn = new ConexaoMySQL("versionamento"); 
		$sql = "SELECT CONCAT(VERSAO, \"-r.\", VERSAO_SVN) AS VERSAO 
				FROM controle_versao A 
				WHERE A.COD_CLIENTE = :COD_CLIENTE" ;
		$qry = $conn->prepare($sql);
		$qry->bindParam(":COD_CLIENTE", $codCliente );
		$qry->execute();
		$res = $qry->fetch();

		return ($res["VERSAO"] != "") ? "v".$res["VERSAO"] : "";
	}
}