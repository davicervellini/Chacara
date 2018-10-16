<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class ModelosImpressaoTexto extends GetterSetter {
	private $conn;

	const TABELA = 'modelos_impressao_texto';
	const MODULO = 'MODELO DE TEXTO DO CLIENTE';

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
					   MIT_DESCRICAO as Descricao,
					   MIT_TEXTO     as Texto
				FROM modelos_impressao_texto
				WHERE RECNO = :RECNO";

		$qry = $this->conn->prepare($sql);
		$qry->bindParam(':RECNO', $codigo);
		$qry->execute();

		return $qry->fetch();
	}

	public function list() {
		$sSQL = "SELECT RECNO,
						MIT_CODIGO,
						MIT_DESCRICAO
                 FROM modelos_impressao_texto
                 ORDER BY RECNO";

		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function resgataRecno($codigo) {
		$sSQL = "SELECT RECNO
                 FROM modelos_impressao_texto
                 WHERE MIT_CODIGO = :MIT_CODIGO";

		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute([
			':MIT_CODIGO' => $codigo
		]);

		return $sQRY->fetchColumn();
	}
}