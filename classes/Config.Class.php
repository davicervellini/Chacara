<?php


require_once __DIR__ ."/GetterSetter.Class.php";

class Config extends GetterSetter{
	public $conn;

	public function __construct($conn){
		$this->conn = $conn;

		$sql = "SELECT CFG_CAMINHOLOGO 		 		     as Logo
				FROM config" ;
			$qry = $this->conn->prepare($sql);
			$qry->execute();

		if($qry->rowCount() > 0){
			$result = $qry->fetchAll(PDO::FETCH_ASSOC);
			$this->setData($result);
		}

	}
}