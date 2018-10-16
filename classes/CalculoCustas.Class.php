<?php
	
	class CalculoCustas {
		public $cusData, $cusTabela, $cusDescricao, $cusLetra, $cusDe, $cusAte, $cusOficial, $cusEstado, $cusIpesp, $cusRegCivil, $cusTJustica, $cusStaCasa, $cusMp, $cusIss, $cusTotal, $cusDiscriminacao, $cusNotasa;
		public $testeDiretorio;
		public $erro;
		public $wsCustas;
		public $xml;

		function realizarCalculo($token ,$sDataCalculo, $iTabela, $iDivisor, $sLetra, $dValorTributado, $dValorDeclarado, $qtde){
			$ws = new ArcaTDPJ_WS;

			return (array) simplexml_load_string($ws->geraCalculo( $token, $sDataCalculo,  $iTabela, $iDivisor, $sLetra, $dValorTributado, $dValorDeclarado, $qtde));
		}

		function transformarXml($sXML){
			$custas = simplexml_load_string($sXML);
			// $this->xml = $sXML;
			// $this->oficial   = $custas->oficial;
			// $this->estado    = $custas->estado;
			// $this->ipesp     = $custas->ipesp;
			// $this->reg_civil = $custas->reg_civil;
			// $this->tjustica  = $custas->tjustica;
			// $this->stacasa   = $custas->stacasa;
			// $this->mp        = $custas->mp;
			// $this->iss       = $custas->iss;
			// $this->total     = $custas->total;
			return (array) $this;
		}

		function setXML($sXml){
			$this->$sXml = $sXml;
		}

		function __construct(){
		}
	}

?>