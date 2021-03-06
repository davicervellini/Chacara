<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class MacrosRecibo extends GetterSetter{
	public $conn;
	public $tabelas = [
		"arc_registro",
		"arc_registro_calculo",
		"arc_registro_partes",
		"registro_informacoes",
		"pessoas",
		"pessoas_dados",
	];

	public function __construct($recno = ''){
		$this->conn = new ConexaoMySQL();

		if($recno != ""){

			$sql = "SELECT MCR_NOME_CAMPO     	as Campo,
						   MCR_NOME_TABELA     	as Tabela,
						   MCR_FORMATO          as Formato,
						   MCR_DESCRICAO     	as Descricao
					FROM macros_recibo WHERE RECNO = :RECNO" ;
			$qry = $this->conn->prepare($sql);
			$qry->bindParam(':RECNO', $recno);
			$qry->execute();

			if($qry->rowCount() > 0){
				$result = $qry->fetchAll(PDO::FETCH_ASSOC);
				$this->setData($result);
			}
		}
	}

	public function listTabelas(){
		$sSQL = "SELECT TABLE_NAME AS name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME IN ('".join("','",$this->tabelas)."') ORDER BY name ASC";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function listCampos(){
		$sSQL = "SELECT COLUNAS.NAME AS COLUNA
				FROM SYSOBJECTS AS TABELAS, SYSCOLUMNS AS COLUNAS
				WHERE TABELAS.id = COLUNAS.ID
				GROUP BY COLUNAS.NAME";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function resgataRecno(){

		$sSQL = "SELECT RECNO FROM macros_recibo ORDER BY RECNO DESC LIMIT 1";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();
		$ln  = $sQRY->fetch(PDO::FETCH_ASSOC);

		return $ln["RECNO"];
	}
}
