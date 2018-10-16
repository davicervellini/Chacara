<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class Diligencia extends GetterSetter{
    public $conn;

    public function __construct($codigo = ''){
        $this->conn = new ConexaoMySQL();

        if($codigo != ""){

            $sql = "SELECT DIL_CODIGO       as Codigo,
						   DIL_VALOR        as Valor,
						   DIL_DESCRICAO    as Descricao
					FROM diligencia WHERE RECNO = :RECNO" ;
            $qry = $this->conn->prepare($sql);
            $qry->bindParam(':RECNO', $codigo);
            $qry->execute();

            if($qry->rowCount() > 0){
                $result = $qry->fetchAll(PDO::FETCH_ASSOC);
                $this->setData($result);
            }
        }
    }

    public function list(){

        $sSQL = "SELECT RECNO, DIL_CODIGO, DIL_VALOR, DIL_DESCRICAO FROM diligencia";
        $sQRY = $this->conn->prepare($sSQL);
        $sQRY->execute();

        return $sQRY->fetchAll(PDO::FETCH_ASSOC);
    }

    public function resgataRecno($ferCodigo){

        $sSQL = "SELECT RECNO FROM diligencia WHERE DIL_CODIGO = :DIL_CODIGO" ;
        $sQRY = $this->conn->prepare($sSQL);
        $sQRY->bindParam(':DIL_CODIGO', $ferCodigo);
        $sQRY->execute();

        $ln  = $sQRY->fetch(PDO::FETCH_ASSOC);

        return $ln["RECNO"];
    }
}