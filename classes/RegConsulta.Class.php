<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class RegConsulta extends GetterSetter{
	public $conn;

	public function __construct(){
		$this->conn = new ConexaoMySQL();
	}

	public function getMatricula($protocolo){

		$sqlGetMatricula = "SELECT ARS_NUMLIVRO FROM arc_seq_reg WHERE ARS_PROTOCOLO = :ARS_PROTOCOLO ";
		$qryGetMatricula = $this->conn->prepare($sqlGetMatricula);
		$qryGetMatricula->bindParam(':ARS_PROTOCOLO', $protocolo);
		$qryGetMatricula->execute();
		$html = "";
		$cont = 1;
		$total = 0;
		$tresPontos = 1;
		if($qryGetMatricula->rowCount()){
			foreach ($qryGetMatricula as $lnGetMatricula){
				if($cont == 1){
					$html .= $lnGetMatricula['ARS_NUMLIVRO'];
					$cont = 0;
				}else{
					if($total <= 9){
						$html .= ",".$lnGetMatricula['ARS_NUMLIVRO'];
					}else{
						if($tresPontos == 1){
							$html .= "...";
							$tresPontos = 0;
						}
						// $total = 0;
					}
				}
				$total++;
			}
		}
		return $html;
	}

	public function getMatriculaTitle($protocolo){

		$sqlGetMatricula = "SELECT ARS_NUMLIVRO FROM arc_seq_reg WHERE ARS_PROTOCOLO = :ARS_PROTOCOLO ";
		$qryGetMatricula = $this->conn->prepare($sqlGetMatricula);
		$qryGetMatricula->bindParam(':ARS_PROTOCOLO', $protocolo);
		$qryGetMatricula->execute();
		$html = "";
		$cont = 1;
		if($qryGetMatricula->rowCount()){
			foreach ($qryGetMatricula as $lnGetMatricula){
				if($cont == 1){
					$html .= $lnGetMatricula['ARS_NUMLIVRO'];
					$cont = 0;
				}else{
					$html .= ",".$lnGetMatricula['ARS_NUMLIVRO'];
				}
			}
		}
		return $html;
	}
}