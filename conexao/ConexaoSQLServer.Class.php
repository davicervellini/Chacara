<?php

	class newPDO extends PDO{

	    public function prepare($statement, $options = array()){
	        if (empty($options)) $options = array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL);
	        return parent::prepare($statement, $options);
	    }

	}

	class ConexaoMySQL extends newPDO {
	    protected static $conn;
	    private $amb;
	    private $db;


	    public function __construct($nomeConexao = 'arcatdpj'){
	        
	        $conexoesXML = __DIR__ . "/conexoes.xml";
			$xml = simplexml_load_file($conexoesXML);
			foreach($xml->children() as $child) {
				$role = strtoupper($child->attributes());
				$nomeConexao = strtoupper($nomeConexao);
				if($child->attributes()->type == 'sqlsrv'){
					if($role == $nomeConexao){
						$sHost     = $child->hostname;
						$sUser     = $child->username;
						$sPass     = $child->password;
						$sDataBase = $child->database;
						$sPort     = $child->port;


						$this->amb = 'sqlsrv';
						$db = (array) $sDataBase;
						$this->db = $db[0];

					}
				}
			}

			if(@$sHost == ''){
				throw new Exception("Nenhuma conexao encontrada com esse nome ($nomeConexao)", 1);
				exit;
			}

	        $connMYSQL = "sqlsrv:Server=$sHost;Database=$sDataBase;";
        	parent::__construct($connMYSQL, $sUser, $sPass, array(
        			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        		));
			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	    }

	    public function db(){
	    	return array(
				'amb'=> $this->amb,
				'db' => $this->db
	    		);
	    }

	}

	try{

		$connMYSQL = new ConexaoMySQL();
		
	}catch(Exception $e){

		print $e->getMessage();
		die;
	}

?>