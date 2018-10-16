<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class LinhaDoTempo extends GetterSetter{
	public $conn;

	public function __construct(){
		$this->conn = new ConexaoMySQL();
		$this->sys = new Sistema();
	}

	public function consultarLinhadoTempoProtocolo( $iProtocolo ){
		$resp = array();
		$sql = "SELECT 	dat.REG_PRENOTA, 
					    dat.REG_DTPRENOTA, 
					    oco.OCO_CODIGO    as ocoCodigo, 
					    oco.OCO_DESCRICAO AS Status, 
					    dat.DAT_DTBAIXA   AS DataBaixa, 
					    dat.DAT_HORA      AS HoraBaixa, 
					    dat.DAT_USUARIO   AS Usuario
				FROM arc_registro_datas dat
				INNER JOIN ocorrencias oco ON (oco.OCO_CODIGO = dat.OCO_CODIGO)
				WHERE dat.REG_PRENOTA = :iProtocolo
				ORDER BY dat.DAT_DTBAIXA, dat.DAT_HORA";

		$qry = $this->conn->prepare($sql);
		$qry->bindParam(":iProtocolo", $iProtocolo);
		$qry->execute();
		$resp["total"] = $qry->rowCount();

		$resQuery = $qry->fetchAll();
		$i = 0;
		foreach ($resQuery as $row) {
			$resQuery[$i]["DataBaixa"] = $this->sys->padroniza_datas_BR($row["DataBaixa"]);
			$resQuery[$i]["Usuario"] = ucfirst($row["Usuario"]);
			$i++;
		}

		$resp["results"] = $resQuery;
		return $resp;
	}
}
