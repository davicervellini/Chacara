<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class Divisores extends GetterSetter{
	public $conn;

	public function __construct($recno = ''){
		$this->conn = new ConexaoMySQL();

		// if($recno != ""){

		// 	$sql = "SELECT CUS_DATA     			as Datas,
		// 				   CUS_TABELA     			as Tabela,
		// 			       CUS_DESCRICAO            as Descricao,
		// 				   CUS_LETRA             	as Letra,
		// 				   CUS_DE         		 	as De,
		// 				   CUS_ATE                  as Ate,
		// 				   CUS_OFICIAL              as Oficial,
		// 				   CUS_ESTADO               as Estado,
		// 				   CUS_IPESP                as Ipesp,
		// 				   CUS_REG_CIVIL            as RegCivil,
		// 				   CUS_TJUSTICA             as Tjustica,
		// 				   CUS_MP                  	as Mp,
		// 				   CUS_ISS                  as Iss,
		// 				   CUS_TOTAL                as Total,
		// 				   CUS_DISCRIMINACAO        as Discriminacao,
		// 				   CUS_TEXTO_INFO           as Texto
		// 			FROM cad_tab_custas WHERE RECNO = :RECNO" ;
		// 	$qry = $this->conn->prepare($sql);
		// 	$qry->bindParam(':RECNO', $recno);
		// 	$qry->execute();

		// 	if($qry->rowCount() > 0){
		// 		$result = $qry->fetchAll(PDO::FETCH_ASSOC);
		// 		$this->setData($result);
		// 	}
		// }
	}

	public function listDivisores(){

		$sSQL = "SELECT DIV_CODIGO, DIV_DESCRICAO FROM divisores GROUP BY DIV_CODIGO, DIV_DESCRICAO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}	
}
