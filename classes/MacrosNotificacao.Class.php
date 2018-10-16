<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class MacrosNotificacao extends GetterSetter{
	public $conn;
	public $tabelas = [
		"arc_recepcao",
		"arc_recepcao_calculo",
		"arc_recepcao_partes",
		"recepcao_informacoes",
		"pessoas",
		"pessoasDados",
    "clientes"
	];

	public function __construct($recno = ''){
		$this->conn = new ConexaoMySQL();

		if($recno != ""){

			$sql = "SELECT MCN_NOME_CAMPO     as Campo,
						   MCN_NOME_TABELA     	    as Tabela,
						   MCN_FORMATO              as Formato,
						   MCN_DESCRICAO     	      as Descricao
					FROM macros_notificacao WHERE RECNO = :RECNO" ;
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

		$sSQL = "SELECT RECNO FROM macros_notificacao ORDER BY RECNO DESC LIMIT 1";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();
		$ln  = $sQRY->fetch(PDO::FETCH_ASSOC);

		return $ln["RECNO"];
	}
}
