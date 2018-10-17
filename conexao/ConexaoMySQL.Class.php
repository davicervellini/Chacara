<?php

	class newPDOMySQL extends PDO{

	    public function prepare($statement, $options = array()){
	        if (empty($options)) $options = array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL);
	        return parent::prepare($statement, $options);
	    }

	}

	class ConexaoMySQL extends newPDOMySQL {
	    protected static $conn;
	    private $amb;
	    private $db;

	    public function __construct($nomeConexao = 'chacara'){

	    	$conexoesXML = __DIR__ . "/conexoes.xml";
			$xml = simplexml_load_file($conexoesXML);
			foreach($xml->children() as $child) {
				$role = strtoupper($child->attributes());
				$nomeConexao = strtoupper($nomeConexao);
				// print_r ($nomeConexao);die;
				if($child->attributes()->type == 'mysql'){
					if($role == $nomeConexao){
						$sHost     = $child->hostname;
						$sUser     = $child->username;
						$sPass     = $child->password;
						$sDataBase = $child->database;
						$sPort     = $child->port;

						$this->amb = 'mysql';
						$db = (array) $sDataBase;
						$this->db = $db[0];


					}
				}
			}

			if(@$sHost == ''){
				throw new Exception("Nenhuma conexao encontrada com esse nome ($nomeConexao)", 1);
				exit;
			}

	        $connMySQL     = "mysql:host=$sHost;dbname=$sDataBase;";
        	parent::__construct($connMySQL, $sUser, $sPass, array(
        			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        		));
			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->exec("set names utf8");

	    }

	    public static function conexao(){
	        if (!self::$conn){
	            new Conexao();
	        }
	        return self::$conn;
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
