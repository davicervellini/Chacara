<?php 

	require_once __DIR__ . "/../vendor/autoload.php";

	use \Firebase\JWT\JWT;

	class Token extends \Firebase\JWT\JWT{
	
		private $key;

		public function __construct(){
			$this->key = '5epReXASwUze';
		}

		public function generate($user, $db){

			$token = array(
			    "aud" => 'JWT ArcaSistemas',
			    "iss"=> $user,
			    "alg" => "HS256",
			    "exp" => time() + 300,
			    "id" => microtime()
			    );
			$token = array_merge($token, $db);
			return JWT::encode($token, $this->key);

		}

		public function isValid($token){

			try{

				$decoded = JWT::decode($token, $this->key, array('HS256'));
				return true;

			}catch(Exception $e){

				throw new Exception($e->getMessage(), 1);
				return false;

			}
			
		}
	}