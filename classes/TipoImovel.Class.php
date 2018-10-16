<?php

class TipoImovel{
	public $conn;

	public function __construct(){
		$this->conn = new ConexaoMySQL();
	}

	public function listTipoImovel(){

		$sSQL = "SELECT TPI_DESCRICAO FROM tipo_imovel ORDER BY TPI_DESCRICAO ASC ";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();
		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}
	public function optionsListImovel(){

		$resp = "<option value=\"\">Selecione</option>";
        foreach ($this->listTipoImovel() as $dado => $res) {
            $resp .= "<option value='".$res['TPI_DESCRICAO']."' >".$res['TPI_DESCRICAO']."</option>";     
        }
        $resp .= "<option value=\"OUTRO\">OUTRO</option>";
        return $resp;
	}

	public function inserirImovel( $imovel ){
		$ws = new ArcaTDPJ_WS;


		$sql = "SELECT RECNO FROM tipo_imovel WHERE TPI_DESCRICAO = :TPI_DESCRICAO ";
		$qry = $this->conn->prepare($sql);
		$qry->bindParam(":TPI_DESCRICAO", $imovel);
		$qry->execute();

		if( $qry->rowCount() > 0)
			return '';


		$vCampos = ["TPI_DESCRICAO"];
		$vDados  = [$imovel];

		$res = $ws->inserirRegistro( getToken( $this->conn->db() ), "tipo_imovel", $vCampos, $vDados );
		if($res != ""){
			return $res;
		}

		return '';
	}
}