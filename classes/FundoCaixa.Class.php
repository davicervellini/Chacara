<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class FundoCaixa extends GetterSetter{
	public $conn;
	public $sys;
	public $ws;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();
		$this->sys  = new Sistema;
		$this->ws   = new ArcaTDPJ_WS;
		if($codigo != ""){
            $sql = "SELECT FDC_DATA    AS FdcData,
                           FDC_USUARIO AS Usuario,
                           FDC_SALDO   AS Saldo
                    FROM caixa_fundo WHERE RECNO = :RECNO" ;
            $qry = $this->conn->prepare($sql);
            $qry->bindParam(':RECNO', $codigo);
            $qry->execute();

            if($qry->rowCount() > 0){
                $result = $qry->fetchAll(PDO::FETCH_ASSOC);
                $this->setData($result);
            }
		}
	}

    public function limparValor($valor){
        return str_replace(",",".",str_replace(".","",$valor));
    }

    public function list(){
        $sSQL = "SELECT RECNO, FDC_DATA, FDC_USUARIO, FDC_SALDO
                 FROM caixa_fundo
                 WHERE FDC_DATA = DATE_FORMAT(NOW(), '%Y-%m-%d')";
        $sQRY = $this->conn->prepare($sSQL);
        $sQRY->execute();        
        return $sQRY;
    }

    public function getUltimaOcorrencia(){

        $sSQL = "SELECT RECNO FROM caixa_fundo ORDER BY RECNO DESC";
        $sQRY = $this->conn->prepare($sSQL);
        $sQRY->execute();
        $ln  = $sQRY->fetch(PDO::FETCH_ASSOC);

        return $ln["RECNO"];
    }

    public function checkLançamentoDiario($usuario, $recno = ""){

        $sqlRecno = ($recno != "")? ' AND RECNO <> :RECNO ' : '';
        $sSQL = "SELECT RECNO
                 FROM caixa_fundo
                 WHERE FDC_USUARIO = :USUARIO AND FDC_DATA = DATE_FORMAT(NOW(), '%Y-%m-%d')".$sqlRecno;
        $sQRY = $this->conn->prepare($sSQL);
        $sQRY->bindParam(':USUARIO', $usuario);
        if ($recno != "") {
            $sQRY->bindParam(':RECNO', $recno);
        }
        $sQRY->execute();
        if($sQRY->rowCount() > 0){
          return false;
        }else{
          return true;
        }
        
    }
}