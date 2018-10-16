<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class ModelosTextoCertificado extends GetterSetter {
	private $conn;

	const TABELA = 'modelos_texto_certificado';
	const MODULO = 'MODELO DE TEXTO CERTIFICADO';

	public function __construct($codigo = '') {
		$this->conn = new ConexaoMySQL();

		if ($codigo === "") {
			return;
		}

		$result = $this->find($codigo);

		if ($result) {
			$this->setData([$result]);
		}
	}

	public function find($codigo) {
		$sql = "SELECT RECNO         as Codigo,
					   MTC_DESCRICAO as Descricao,
					   MTC_TEXTO     as Texto
				FROM modelos_texto_certificado
				WHERE RECNO = :RECNO";

		$qry = $this->conn->prepare($sql);
		$qry->bindParam(':RECNO', $codigo);
		$qry->execute();

		return $qry->fetch();
	}

	public function list() {
		$sSQL = "SELECT RECNO, MTC_CODIGO, MTC_DESCRICAO
                 FROM modelos_texto_certificado
                 ORDER BY RECNO";

		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function resgataRecno($codigo) {
		$sSQL = "SELECT RECNO
                 FROM modelos_texto_certificado
                 WHERE MTC_CODIGO = :MTC_CODIGO";

		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute([
			':MTC_CODIGO' => $codigo
		]);

		return $sQRY->fetchColumn();
	}
}