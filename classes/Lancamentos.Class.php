<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class Lancamentos extends GetterSetter{
	public $conn;

	public function __construct($recno = ''){
		$this->conn = new ConexaoMySQL();

		if($recno != ""){

			$sql = "SELECT LVC_DATA     		as LcvData,
						   LVC_DESCRICAO     	as Dectricao,
						   LVC_VALOR     		as Valor,
						   LVC_CONTA     		as Conta,
						   LVC_OPERACAO     	as Operacao
					FROM livro_caixa WHERE RECNO = :RECNO" ;
			$qry = $this->conn->prepare($sql);
			$qry->bindParam(':RECNO', $recno);
			$qry->execute();

			if($qry->rowCount() > 0){
				$result = $qry->fetchAll(PDO::FETCH_ASSOC);
				$this->setData($result);
			}
		}
	}

	public function getUltimaLancamentos(){

        $sSQL = "SELECT RECNO FROM livro_caixa ORDER BY RECNO DESC";
        $sQRY = $this->conn->prepare($sSQL);
        $sQRY->execute();
        $ln  = $sQRY->fetch(PDO::FETCH_ASSOC);

        return $ln["RECNO"];
    }
}
