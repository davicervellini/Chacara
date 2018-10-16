<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class Guiche extends GetterSetter{
    public $conn;

    public function __construct(){
        $this->conn = new ConexaoMySQL();
        $sql = "SELECT RECNO        as Recno,
                       GCH_USUARIO  as Usuario,
                       USU_CODIGO   as UsuCodigo,
                       GCH_GUICHE   as Guiche,
                       GCH_DATA     as GchData
                FROM guiche WHERE GCH_DATA = :GCH_DATA AND USU_CODIGO = :USU_CODIGO" ;
        $qry = $this->conn->prepare($sql);
        $data = date('Y-m-d');
        $qry->bindParam(":GCH_DATA", $data);
        $qry->bindParam(':USU_CODIGO', $_SESSION["usuario"]['usuCodigo']);
        $qry->execute();

        if($qry->rowCount() > 0){
            $result = $qry->fetchAll(PDO::FETCH_ASSOC);
            $this->setData($result);
        }
    }
}