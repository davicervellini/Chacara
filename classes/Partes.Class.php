<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class Partes extends GetterSetter{
	public $conn;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();
		if($codigo != ""){

			$sql = "SELECT RECNO     				as Recno,
					       QUA_DESC            		as Descricao,
						   QUA_ATIVO             	as Ativo,
						   QUA_TIPO             	as TipoQualificacao
					FROM qualifica WHERE RECNO = :RECNO" ;
			$qry = $this->conn->prepare($sql);
			$qry->bindParam(':RECNO', $codigo);
			$qry->execute();

			if($qry->rowCount() > 0){
				$result = $qry->fetchAll(PDO::FETCH_ASSOC);
				$this->setData($result);
			}
		}
	}	
}
