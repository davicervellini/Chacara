<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class DadosCartorio extends GetterSetter{
	public $conn;

	public function __construct(){
		$this->conn = new ConexaoMySQL();
		$sql = "SELECT RECNO             	 AS Codigo,
					   DCA_RAZAO             AS Razao,
					   DCA_CPLRAZAO          AS CplRazao,
					   DCA_FANTASIA          AS Fantasia,
					   DCA_NUM_TABELIONATO   AS Tabelionato,
					   DCA_ENDERECO          AS Endereco,
					   DCA_BAIRRO            AS Bairro,
					   DCA_CEP               AS Cep,
					   DCA_CIDADE            AS Cidade,
					   DCA_ESTADO            AS Estado,
					   DCA_TELEFONE          AS Telefone,
					   DCA_EMAIL             AS Email,
					   DCA_SITE              AS Site,
					   DCA_NOMESUBSTITUTO    AS NomeSubstituto,
					   DCA_CSUBSTITUTO       AS CargoSubstituto,
					   DCA_CNPJ              AS Cnpj,
					   DCA_HORAFUNC          AS HoraFunc,
					   DCA_CAM_EXIGENCIAS    AS CamExigencias,
					   DCA_CAM_IMGURL        AS CamImgurl,
					   DCA_CAM_REPELETRONICO AS CamRepeletronico,
					   DCA_CAM_ARQINTERNET   AS CamArqinternet,
					   DCA_CAM_SYSLOCAL      AS CamSyslocal,
					   DCA_CAM_SYSREDE       AS CamSysrede,
					   DCA_FANTASIA          AS Fantasia,
					   DCA_CPFOFICIAL        AS Cpf,
					   DCA_NOMEOFICIAL       AS Ofical
			 	FROM dadoscart";
		$qry = $this->conn->prepare($sql);
		// $qry->bindParam(':RECNO', $codigo);
		$qry->execute();
		if($qry->rowCount() > 0){
			$result = $qry->fetchAll(PDO::FETCH_ASSOC);
			$this->setData($result);
		}
	}
}