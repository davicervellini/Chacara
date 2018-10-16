<?php


require_once __DIR__ ."/GetterSetter.Class.php";

class Config extends GetterSetter{
	public $conn;

	public function __construct($conn){
		$this->conn = $conn;

		$sql = "SELECT CFG_CAMINHO      			     as Caminho,
				       CFG_CAM_COLETA   			     as CamColeta,
				       CFG_CAM_ARMAZ   				     as CamArmaz,
				       CFG_SEQUENCIA    			     as Sequencia,
				       CFG_CAMINHOLOGO 		 		     as Logo,
				       CFG_CAMINHOLOGORELATORIO 	     as LogoRelatorio,
				       CFG_ROLO 	                     as Rolo,
				       CFG_ROLO_SEQ 	                 as RoloSequencia,
				       CFG_ROLO_LIMITE                   as RoloLimite,
				       CFG_PAGINACAO_ATOS				 as PaginacaoAtos,
					   CFG_MOD_BALCAO 					 as	ModBalcao
				FROM config" ;
			$qry = $this->conn->prepare($sql);
			$qry->execute();

		if($qry->rowCount() > 0){
			$result = $qry->fetchAll(PDO::FETCH_ASSOC);
			$this->setData($result);
		}

	}

	public function gerarSequenciaDoRolo(){
		$sequencia = $this->getRoloSequencia() + 1;
		$rolo      = $this->getRolo();
		$setters   = [];

		if( $sequencia > $this->getRoloLimite() ){
			$sequencia = 1;
			$rolo++;
			$setters[] = " CFG_ROLO = " . $rolo ;
		}

		$setters[] = " CFG_ROLO_SEQ = ". $sequencia;

		$sql = "UPDATE config SET ". join(" , ", $setters );
		$qry = $this->conn->prepare($sql);
		$qry->execute();

		$this->setRoloSequencia( $sequencia );
		return [
			"rolo"=> $rolo,
			"seq" => $sequencia
		];
	}
}