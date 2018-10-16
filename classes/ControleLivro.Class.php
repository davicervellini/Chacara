<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class ControleLivro extends GetterSetter{
	public $conn;
	public $sys;
	public $ws;

	public function __construct($data = ''){
		$this->conn = new ConexaoMySQL();
		$this->sys  = new Sistema;
		$this->ws   = new ArcaTDPJ_WS;
		if($data != ""){
      $sql = "SELECT CLI_DTRECEPCAO AS DtRecepcao,
                     CLI_TPLIVRO    AS TpLivro,
                     CLI_LIVRO      AS Livro,
                     CLI_FOLHA      AS Folha
					    FROM controle_livro WHERE CLI_DTRECEPCAO = :CLI_DTRECEPCAO" ;
			$qry = $this->conn->prepare($sql);
      $data = $this->sys->padroniza_datas_US($data);
			$qry->bindParam(':CLI_DTRECEPCAO', $data);
			$qry->execute();

			if($qry->rowCount() > 0){
				$result = $qry->fetchAll(PDO::FETCH_ASSOC);
				$this->setData($result);
			}
		}
	}

  public function addFolha($tipo, $livro, $folha){

    $vDados = array(
        'CLI_DTRECEPCAO'  => date('Y-m-d'),
        'CLI_TPLIVRO'     => $tipo,
        'CLI_LIVRO'       => $livro,
        'CLI_FOLHA'       => $folha
    );
    try{
      $result = $this->sys->vStrings($vDados);

      $res = $this->ws->inserirRegistro(getToken($this->conn->db()), 'controle_livro', $result['campos'] , $result['dados'] );
      if($res != ""){
        return $res;
      }
    }catch(Exception $e){
      return $e->getMessage();
    }
  }

  public function getAtual($tipo){
    $sql = "SELECT CLI_LIVRO      AS Livro,
                   CLI_FOLHA      AS Folha
            FROM controle_livro WHERE CLI_TPLIVRO = :CLI_TPLIVRO
            ORDER BY RECNO DESC
            LIMIT 1";
    $qry = $this->conn->prepare($sql);
    $qry->bindParam(':CLI_TPLIVRO', $tipo);
    $qry->execute();
    if($qry->rowCount() > 0){
      $result = $qry->fetch();
    }else{
      $result["Livro"] = 1;
      $result["Folha"] = 1;
    }
    return $result;
  }
}
