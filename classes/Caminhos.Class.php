<?php


require_once __DIR__ ."/GetterSetter.Class.php";

class Caminhos extends GetterSetter{
	public $conn;

	public function __construct($tp = 'mysql'){
		if( $tp == 'mysql'){
			$this->conn = new ConexaoMySQL;
		}else{
			$this->conn = new ConexaoMySQL;	
		}
		

		$sql = "SELECT RECNO       			 		as Codigo,
                       CAM_TITULOS      		    as Titulos,
				       CAM_CIVIL           		    as Civil,
				       CAM_CERTIFICADOS        		as Certificados,
				       CAM_DOCUMENTOS_TD     		as DocumentosTD,
				       CAM_DOCUMENTOS_PJ     		as DocumentosPJ,
				       CAM_DOCUMENTOS_DEVOLUCAO 	as DocumentosDevolucao,
				       CAM_DOCUMENTOS_IRREGULARES 	as DocumentosIrregulares,
				       CAM_RETROATIVO	     		as Retroativo,
				       CAM_COMPROVANTE     		    as Comprovante,
				       CAM_MATRICULAS        		as Matriculas,
				       CAM_PROTOCOLO            	as Protocolo,
				       CAM_DOCUMENTOS            	as Documentos,
				       CAM_NOTAS            		as Nota,
				       CAM_INDICADORES       		as Indicadores,
				       CAM_PRENOTADO         		as Prenotado,
				       CAM_CHEQUES_EMITIDOS 		as ChequesEmitidos,
				       CAM_CHEQUES_RECEBIDOS 		as ChequesRecebidos,
				       CAM_CERTIDAO 				as Certidao,
				       CAM_EXTRAS 		 	 		as Extras,	
				       CAM_EXTRAS_TD 		 		as ExtrasTD,
				       CAM_EXTRAS_PJ 		 		as ExtrasPJ,
				       CAM_IMAGENS_NFSE 			as ImagensNfse,
				       CAM_RECIBO_ENTREGA 			as ReciboEntrega
				FROM caminhos" ;
			$qry = $this->conn->prepare($sql);
			$qry->execute();

		if($qry->rowCount() > 0){
			$result = $qry->fetchAll(PDO::FETCH_ASSOC);
			$this->setData($result);
		}

	}

	public function getExtras($sTipo = "")
	{
		if ($sTipo != "" and $sTipo != SERVENTIA_RI)
			$sTipo = "_" . $sTipo;
		else
			$sTipo = "";
		$sql = "SELECT CAM_EXTRAS_TD" . strtoupper($sTipo) . " from caminhos";
		$qry = $this->conn->prepare($sql);
		$qry->execute();
		$lin = $qry->fetch();
		return $lin["CAM_EXTRAS" . strtoupper($sTipo)];
	}	
}