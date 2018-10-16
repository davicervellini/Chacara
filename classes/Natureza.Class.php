<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class Natureza extends GetterSetter{
	public $conn;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();

		if($codigo != ""){

			$sql = "SELECT RECNO     					as Recno,
						   NAT_CODIGO  							as Codigo,
						   NAT_DESCRICAO     				as Descricao,
						   NAT_ABREVIADO     				as NatAbreviado,
						   NAT_COBRARPAGINA     	  as CobrPag,
						   NAT_COBRARVIAS     			as CobrVias,
						   NAT_PRAZOREG     				as Prazo,
						   NAT_TIPO_TITULOS     		as Titulo,
						   NAT_TIPO_CERTIDAO     		as Certidao,
						   NAT_TIPO_NOTIFICAO    		as Notificao,
						   NAT_TPATO_REG     				as Treg,
						   NAT_TPATO_AV     				as Tav,
						   NAT_TPATO_CERT     			as Tcert,
						   NAT_ATIVO     						as Ativo,
						   CUS_TABELA     					as CusTabe,
						   DIV_CODIGO     					as DivCod,
						   NAT_DIAS_UTEIS     			as Dias,
						   NAT_TPATO_INT     				as Tint,
						   NAT_TPATO_NOT     				as Tnot,
						   NAT_COBRARANEXOS         as CobrAnex,
						   NAT_TIPO_PESSOAS         as Pessoas,
						   NAT_GARANTIA             as Garantia,
						   NAT_DILIGENCIA           as Diligenci
					FROM naturezas WHERE NAT_CODIGO = :NAT_CODIGO" ;
			$qry = $this->conn->prepare($sql);
			$qry->bindParam(':NAT_CODIGO', $codigo);
			$qry->execute();

			if($qry->rowCount() > 0){
				$result = $qry->fetchAll(PDO::FETCH_ASSOC);
				$this->setData($result);
			}
		}
	}

	public function listNatureza(){

		$sSQL = "SELECT NAT_DESCCODIGO, NAT_DESCRICAO FROM naturezas WHERE NAT_DESCCODIGO IS NOT NULL ORDER BY NAT_DESCRICAO";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function listNaturezaCertidao(){

		$sSQL = "SELECT NAT_DESCRICAO, NAT_DESCCODIGO, NAT_CODIGO from naturezas WHERE NAT_TIPO_CERTIDAO = 1 ORDER BY NAT_DESCRICAO ASC";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getNatureza($natCodigo,$tpWhere = ""){
		$where = ($tpWhere == "")? "NAT_DESCCODIGO" : "NAT_CODIGO";
		$sSQL = "SELECT NAT_DESCRICAO FROM naturezas WHERE ".$where." = :natCodigo";
		$qry = $this->conn->prepare($sSQL);
		$qry->bindParam(':natCodigo', $natCodigo);
		$qry->execute();
		$resp = $qry->fetch();
		return utf8_encode($resp['NAT_DESCRICAO']);
	}

	public function getTabelaCustas($cusTabela, $cusLetra = ''){
		$sSQL = "SELECT CUS_DESCRICAO FROM cad_tab_custas WHERE CUS_TABELA = :cusTabela ";
		if($cusLetra != '')
			$sSQL .= " AND CUS_LETRA = :cusLetra ";

		$sSQL .= " GROUP BY CUS_DESCRICAO";
		$qry = $this->conn->prepare($sSQL);
		$qry->bindParam(':cusTabela', $cusTabela);
		if($cusLetra != '')
			$qry->bindParam(':cusLetra', $cusLetra);

		$qry->execute();
		$resp = $qry->fetch();
		return $resp['CUS_DESCRICAO'];
	}

	// public function getData($natCodigo){

	// 	$sSQL = "SELECT NAT_PRAZOREG FROM naturezas WHERE NAT_CODIGO = :NAT_CODIGO";
	// 	$qry = $this->conn->prepare($sSQL);
	// 	$qry->bindParam(':NAT_CODIGO', $natCodigo);
	// 	$qry->execute();
	// 	$resp = $qry->fetch();
	// 	return $resp['NAT_PRAZOREG'];
	// }

	public function getCustas($natCodigo){

		$sSQL = "SELECT NAT_COBRARPAGINA FROM naturezas WHERE NAT_CODIGO = :NAT_CODIGO";
		$qry = $this->conn->prepare($sSQL);
		$qry->bindParam(':NAT_CODIGO', $natCodigo);
		$qry->execute();
		$resp = $qry->fetch();
		return $resp['NAT_PRAZOREG'];
	}

	public function getUltimoRecno(){

		$sSQL = "SELECT RECNO FROM naturezas ORDER BY RECNO DESC LIMIT 1";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		$ln  = $sQRY->fetch(PDO::FETCH_ASSOC);
		return $ln["RECNO"];
	}
}
