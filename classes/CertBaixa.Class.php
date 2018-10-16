<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class CertBaixa extends GetterSetter{
	public $conn;

	public function __construct(){
		$this->conn = new ConexaoMySQL();
	}

	public function getMatricula($protocolo, $tipo, $limite){

		$sqlGetMatricula = "SELECT ACE_NUMLIVRO FROM lnk_dados_certidao WHERE ACE_PROTOCOLO = :ACE_PROTOCOLO AND TPL_CODIGO = :TPL_CODIGO ";
		$qryGetMatricula = $this->conn->prepare($sqlGetMatricula);
		$qryGetMatricula->bindParam(':ACE_PROTOCOLO', $protocolo);
		$qryGetMatricula->bindParam(':TPL_CODIGO', $tipo);
		$qryGetMatricula->execute();
		$html = "";
		$cont = 1;
		$total = 0;
		$tresPontos = 1;
		if($qryGetMatricula->rowCount()){
			foreach ($qryGetMatricula as $lnGetMatricula){
				if($cont == 1){
					$html .= $lnGetMatricula['ACE_NUMLIVRO'];
					$cont = 0;
				}else{
					if($total < $limite){
						$html .= ",".$lnGetMatricula['ACE_NUMLIVRO'];
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

	// public function getDadosCertidao($protocolo, $tipo){

	// 	$sqlGetMatricula = "SELECT ACE_NUMLIVRO AS Livro FROM lnk_dados_certidao WHERE ACE_PROTOCOLO = :ACE_PROTOCOLO AND TPL_CODIGO = :TPL_CODIGO ";
	// 	$qryGetMatricula = $this->conn->prepare($sqlGetMatricula);
	// 	$qryGetMatricula->bindParam(':ACE_PROTOCOLO', $protocolo);
	// 	$qryGetMatricula->bindParam(':TPL_CODIGO', $tipo);
	// 	$qryGetMatricula->execute();
	// 	$html = "";
	// 	$cont = 1;
	// 	$total = 0;
	// 	$tresPontos = 1;
	// 	if($qryGetMatricula->rowCount() > 0){
	// 		$resQuery = $qryGetMatricula->fetchAll();
	// 		$resLivros = [];
	// 		foreach ($resQuery as $row) {
	// 			$resLivros[] = $row["Livro"];
	// 		}
	// 		return [
	// 			"total" => $qryGetMatricula->rowCount(),
	// 			"livros"=> $resLivros
	// 		];
	// 	}else{
	// 		return ["total"=>0];	
	// 	}
		
	// }

	public function collapseDadosCertidao($nome,$qtde,$certidoes ){

		$collapse = "
				<div class=\"col l3\" style='padding:0 0 0 10px'>
					<ul class='collapsible' data-collapsible='accordion' style='margin-bottom: 0'>
						<li>
							<div class='collapsible-header'>
								<div class='row no-bottom'>
									<div class='col l12 center-align'>
										<b class='bold ' style='font-weight:bold'>$nome ($qtde)</b>
									</div>
								</div>
							</div>
							<div class=\"collapsible-body white\" style='padding:5px'>";
							foreach ($certidoes as $key => $value) {
								$collapse.= "<div style='border-bottom:1px solid #CECECE;padding:5px'>$value</div>";
							}
		$collapse .= "
							</div>
						</li>
					</ul>
				</div>";

		return $collapse;
	}

	public function getMatriculaTitle($protocolo, $tipo){

		$sqlGetMatricula = "SELECT ACE_NUMLIVRO FROM lnk_dados_certidao WHERE ACE_PROTOCOLO = :ACE_PROTOCOLO AND TPL_CODIGO = :TPL_CODIGO ";
		$qryGetMatricula = $this->conn->prepare($sqlGetMatricula);
		$qryGetMatricula->bindParam(':ACE_PROTOCOLO', $protocolo);
		$qryGetMatricula->bindParam(':TPL_CODIGO', $tipo);
		$qryGetMatricula->execute();
		$html = "";
		$cont = 1;
		if($qryGetMatricula->rowCount()){
			foreach ($qryGetMatricula as $lnGetMatricula){
				if($cont == 1){
					$html .= $lnGetMatricula['ACE_NUMLIVRO'];
					$cont = 0;
				}else{
					$html .= ",".$lnGetMatricula['ACE_NUMLIVRO'];
				}
			}
		}
		return $html;
	}

	public function getNomesConsultados($protocolo){

		$sqlGetMatricula = "SELECT pes.PES_NOME AS NOME
							FROM arc_registro_partes arp
							LEFT JOIN pessoas pes ON  (pes.PES_CODIGO = arp.PES_CODIGO)
							WHERE arp.REG_PRENOTA = :REG_PRENOTA";
		$qryGetMatricula = $this->conn->prepare($sqlGetMatricula);
		$qryGetMatricula->bindParam(':REG_PRENOTA', $protocolo);
		$qryGetMatricula->execute();
		$html = "";
		$cont = 1;
		$tresPontos = 1;
		if($qryGetMatricula->rowCount()){
			foreach ($qryGetMatricula as $lnGetMatricula){
				if($cont == 1){
					$html .= $lnGetMatricula['NOME'];
					$cont = 0;
				}else{
					$html .= ",&nbsp;".$lnGetMatricula['NOME'];
				}
			}
		}
		return $html;
	}
}