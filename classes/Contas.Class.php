<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class Contas extends GetterSetter{
	public $conn;
	public $sys;
	public $ws;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();
		$this->sys  = new Sistema;
		$this->ws   = new ArcaTDPJ_WS;
		if($codigo != ""){
            $sql = "SELECT LCC_CONTA            AS Conta,
                           LCC_OPERACAO         AS Operacao
                    FROM livro_caixa_contas WHERE RECNO = :RECNO" ;
            $qry = $this->conn->prepare($sql);
            $qry->bindParam(':RECNO', $codigo);
            $qry->execute();

            if($qry->rowCount() > 0){
                $result = $qry->fetchAll(PDO::FETCH_ASSOC);
                $this->setData($result);
            }
		}
	}

    public function getUltimaConta(){
        $sSQL = "SELECT RECNO FROM livro_caixa_contas ORDER BY RECNO DESC";
        $sQRY = $this->conn->prepare($sSQL);
        $sQRY->execute();
        $ln  = $sQRY->fetch(PDO::FETCH_ASSOC);  
        return $ln["RECNO"];
    }

    public function listContas(){
        $sSQL = "SELECT LCC_CONTA, LCC_OPERACAO FROM livro_caixa_contas ORDER BY LCC_CONTA";
        $sQRY = $this->conn->prepare($sSQL);
        $sQRY->execute();
        return $sQRY->fetchAll(PDO::FETCH_ASSOC);
    }
}