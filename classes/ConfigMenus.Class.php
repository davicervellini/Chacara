<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class ConfigMenus extends GetterSetter{
	public $conn;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();
		$this->sys  = new Sistema;

		if($codigo != ""){
			$sql = "SELECT MEN_MENU      AS Menu,
						   MEN_FORM      AS Form,
						   MEN_DESCRICAO AS Descricao,
						   MEN_GRUPO     AS Grupo
				   FROM menus WHERE RECNO = :RECNO";
			$qry = $this->conn->prepare($sql);
			$qry->bindParam(':RECNO', $codigo);
			$qry->execute();
			if($qry->rowCount() > 0){
				$result = $qry->fetchAll(PDO::FETCH_ASSOC);
				$this->setData($result);
			}
		}
	}

	public function list(){

		$sSQL = "SELECT RECNO, MEN_MENU, MEN_FORM FROM menus";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function listGrupos(){

		$sSQL ="SELECT men.MEN_GRUPO, men.MEN_GRUPO_ORDEM
				FROM menus men
				GROUP BY men.MEN_GRUPO, men.MEN_GRUPO_ORDEM
				ORDER BY men.MEN_GRUPO_ORDEM";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->execute();

		return $sQRY->fetchAll(PDO::FETCH_ASSOC);
	}

	public function resgataRecno($menu){

		$sSQL = "SELECT RECNO FROM menus WHERE MEN_MENU = :MEN_MENU ORDER BY RECNO DESC";
		$sQRY = $this->conn->prepare($sSQL);
		$sQRY->bindParam(':MEN_MENU', $menu);
		$sQRY->execute();
		
		$ln  = $sQRY->fetch(PDO::FETCH_ASSOC);

		return $ln["RECNO"];
	}


	public function atualizaPermicoes($menu,$form,$descricao){
		$ws   = new ArcaTDPJ_WS;
		$selectUsu = "SELECT USU_CODIGO FROM usuarios WHERE USU_ADMIN != 1 ORDER BY RECNO";
		$sQRY = $this->conn->prepare($selectUsu);
		$sQRY->execute();
		if(count($sQRY) > 0){
			foreach ($sQRY as $lnUsu) {
				$vDados = [
	                'USU_CODIGO'      	=> $lnUsu['USU_CODIGO'],
	                'FORM'      		=> $form,
	                'MENU' 				=> $menu,
	                'FORMEXTENSO' 		=> $descricao,
	                'ACESSO' 			=> 0,
	                'INCLUIR' 			=> 0,
	                'CORRIGIR' 			=> 0,
	                'EXCLUIR' 			=> 0
	            ];

	            $result = $this->sys->vStrings($vDados);

	            $res = $ws->inserirRegistro(getToken($this->conn->db()), "permissao", $result['campos'] , $result['dados'] );
			}
		}
	}

	public function deletaPermicoes($form){
		$ws   = new ArcaTDPJ_WS;
		$res = $ws->deletarRegistro(getToken($this->conn->db()), "permissao", "FORM = ".$form);
	}
}