<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class Clientes extends GetterSetter{
	public $conn;

	public function __construct($recno = ''){
		$this->conn = new ConexaoMySQL();
		if($recno != ""){

			$sql = "SELECT 	cli.RECNO 				AS 	Recno,
							cli.CLI_DATA 			AS	DataCad, 
							cli.CLI_CODIGO 			AS	Codigo, 
							cli.CLI_DEPARTAMENTO 	AS	Departamento, 
							cli.CLI_CONV_ARQUIVO 	AS	Arquivo, 
							cli.CLI_FATURAMENTO 	AS	Faturamento, 
							cli.CLI_ONLINE 			AS	Online,
							pes.PES_NOME 			AS	Nome,
							pes.PES_TIPO_DOC 		AS	TipoDoc,
							pes.PES_DOCUMENTO 		AS	Documento,
							psd.PSD_APRES_EMAIL 	AS	Email,
							psd.PSD_APRES_CONTATO 	AS	Contato,
							psd.PSD_APRES_TEL 		AS	Telefone,
							psd.PSD_CEP 			AS	Cep,
							psd.PSD_LOGRADOURO 		AS	Logradouro,
							psd.PSD_NUMLOGRADOURO 	AS	Numero,
							psd.PSD_BAIRRO 			AS	Bairro,
							psd.PSD_CIDADE 			AS	Cidade,
							psd.PSD_UF              AS	Uf
				FROM clientes  cli
				INNER JOIN pessoas pes ON (cli.PES_CODIGO = pes.PES_CODIGO)
				INNER JOIN pessoas_dados psd ON (cli.PSD_CODIGO = psd.PSD_CODIGO)
				WHERE cli.RECNO = :RECNO" ;
			$qry = $this->conn->prepare($sql);
			$qry->bindParam(':RECNO', $recno);			
			$qry->execute();

			if($qry->rowCount() > 0){
				$result = $qry->fetchAll(PDO::FETCH_ASSOC);
				$this->setData($result);
			}
		}
	}

	public function listarClientes(){
		$sql = "SELECT 	cli.RECNO 				AS Recno,
						cli.CLI_CODIGO 			AS Codigo, 
						cli.CLI_DEPARTAMENTO 	AS Departamento, 
						pes.PES_NOME 			AS Nome
				FROM clientes cli
				INNER JOIN pessoas pes ON (cli.PES_CODIGO = pes.PES_CODIGO)
				ORDER BY Codigo";
		$qry = $this->conn->prepare($sql);				
		$qry->execute();
		$res = $qry->fetchAll();

		return $res;
	}

	public function resgataRecno($cliCodigo){
		$sql = "SELECT RECNO 
				FROM clientes cli 
				WHERE CLI_CODIGO = :CLI_CODIGO";
		$qry = $this->conn->prepare($sql);
		$qry->bindParam(":CLI_CODIGO", $cliCodigo);
		$qry->execute();

		$res = $qry->fetch();

		return $res['RECNO'];
	}

}
