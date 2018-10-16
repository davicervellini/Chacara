<?php

class Usuario{
	public $conn;
	public $usuConectado;
	public $usuLogin;
	public $usuSenha;
	public $usuNome;
	public $usuEmail;
	public $usuRecno;
	public $usuCodigo;
	public $usuTelefone;

	public function __construct($usuCodigo = null){
		$this->conn = new ConexaoMySQL();

		if($usuCodigo !== null) $this->setUsuCodigo($usuCodigo);
	}

	public function login(){

        $resp = [];
        $usuario = $this->getUsuLogin();
		$senha   = $this->getUsuSenha();
		
		if($this->getUsuConectado() == 1){
			setcookie("nome_usuario", $usuario, (time() + (86400 * 7)), '/');
			setcookie("senha_usuario", $senha, (time() + (86400 * 7)), '/');
			if(isset($_COOKIE["nome_usuario"]) and isset($_COOKIE["senha_usuario"])){
				$usuLogin = $_COOKIE["nome_usuario"];
				$usuSenha = $_COOKIE["senha_usuario"];
			}
		}

		try{

            require_once __DIR__ . "/../config.php";
            $sqlUsuarios = "SELECT RECNO              as usuRecno, 
                                   USU_CODIGO         as usuCodigo, 
                                   USU_LOGIN          as usuLogin, 
                                   USU_SENHA          as usuSenha, 
                                   USU_NOME           as usuNome, 
                                   USU_EMAIL          as usuEmail, 
								   USU_CARGO          as usuCargo,
                                   USU_ADMIN          as usuAdmin, 
                                   USU_PRIMEIROACESSO as primeiroAcesso
                            FROM usuarios
                            WHERE USU_LOGIN = :UserLogin AND USU_SENHA = :UserPass ";
            $query = $this->conn->prepare($sqlUsuarios);
			$query->bindParam(':UserLogin', $usuario); 
            $crypSenha = ($senha);
			$query->bindParam(':UserPass',  $crypSenha); 
			$query->execute();
            
			if($query->rowCount() > 0){
				$result = $query->fetch(PDO::FETCH_ASSOC);
                $resp['valid'] = true;
                $_SESSION["usuario"] = $result;

				if($result["primeiroAcesso"] == 0){
                    
					$resp['url'] = './primeiro-acesso/?connect='.$this->getUsuConectado();

				}else if($result["primeiroAcesso"] == 1){

					$this->setUsuCodigo($result['usuCodigo']);
					$this->setUsuEmail( $result['usuEmail']);
					$this->setUsuRecno( $result['usuRecno']);
					$this->setUsuNome(  $result['usuNome']);

                    $_SESSION["timeLogin"] = date('d/m/Y - H:i:s');

                    $_SESSION["permissoes"] = $this->setPermissoes();
					$_SESSION["permissoesNovo"] = [];
                    $this->verificaPermissoesRelatorios();
                    $resp['url']   = './home/';

				}
			}else{
                $resp['valid']   = false;
                $resp['message'] = 'Usuário não encontrado.';
            }

            return $resp;

		}catch(Exception $e){
			
            $resp['valid'] = false;
            $resp['message'] = 'Erro ao realizar o login: '.$e->getMessage();
            return $resp;
		}
	}

	public function logout(){
		session_start();
        unset($_COOKIE['nome_usuario']);
        unset($_COOKIE['senha_usuario']);
        setcookie('nome_usuario', null, -1, '/');
        setcookie('senha_usuario', null, -1, '/');
        unset($_SESSION['usuario']);
        unset($_SESSION['permissoes']);
        unset($_SESSION['URL_WS']);
		session_destroy();
		header("location: ../login/ ");
	}


	public function getUsuarios(array $sCampos, array $dadosWhere){
		$sCampos = join(", ", $sCampos);
		$where = [];
		foreach ($dadosWhere as $key => $value) {
			$bindField = ":".$key;
			$whereStmt = $key . " = " . $bindField;
			array_push($where, $whereStmt);
		}
		$where = join(" AND ", $where);
		$sqlUsuarios = "SELECT " . $sCampos . " FROM usuarios WHERE 1 = 1 AND " . $where ;
		$query = $this->conn->prepare($sqlUsuarios);
		foreach ($dadosWhere as $key => &$value) {
			$query->bindParam(':'.$key, $value);
		}
		$query->execute();
		return $query;
	}

	public function setPermissoes(){

		try{

			$vPermissoes = [];
			$sqlPermissoes = "SELECT FORM         as formulario, 
                                     MENU         as menu, 
                                     FORMEXTENSO  as formExtenso, 
									 ACESSO       as acesso, 
									 INCLUIR      as incluir, 
									 CORRIGIR     as corrigir, 
                                     EXCLUIR      as excluir
							  FROM permissao WHERE USU_CODIGO = :usuCodigo ";
			$query = $this->conn->prepare($sqlPermissoes);
			$usuCodigo = $this->getUsuCodigo();
			$query->bindParam(':usuCodigo', $usuCodigo);
			$query->execute();

			if($query->rowCount() > 0){
				$result = $query->fetchAll(PDO::FETCH_ASSOC);
				foreach ($result as $row) {
					$vPermissoes[$row['formulario']] = $row;
				}
				return $vPermissoes;
			};

		}catch(Exception $e){
			print "Error: ". $e->getMessage();
		}

	}

	public function inserirPermissoes($usuCodigo){

		$sqlMenus = "SELECT MEN_FORM, MEN_MENU, MEN_DESCRICAO FROM  menus";
		$qry = $this->conn->query($sqlMenus);

		$result = $qry->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result as $row) {

			$insertPermissoes = "INSERT INTO permissao (USU_CODIGO, FORM, MENU, FORMEXTENSO, ACESSO, INCLUIR, CORRIGIR, EXCLUIR)
								VALUES (:USU_CODIGO, :FORM, :MENU, :FORMEXTENSO, 0,0,0,0)";
			$qryPermissoes = $this->conn->prepare($insertPermissoes);
			$qryPermissoes->bindParam(':USU_CODIGO',     $usuCodigo);
            $qryPermissoes->bindParam(':FORM',           $row['MEN_FORM']);
            $qryPermissoes->bindParam(':MENU',           $row['MEN_MENU']);
			$qryPermissoes->bindParam(':FORMEXTENSO',    $row['MEN_DESCRICAO']);
			$qryPermissoes->execute();

		}
	}

    public function verificaPermissoesRelatorios(){
        $tipoRelatorio = ["Registro","Certidão","Exame e Cálculo", "Financeiro"];

        for($i=0; $i < count($tipoRelatorio); $i++) { 
            $sqlRelatorios = "SELECT ACESSO FROM permissao
                              INNER JOIN menus ON (menus.MEN_FORM = permissao.FORM)
                              WHERE USU_CODIGO = :USU_CODIGO AND MEN_GRUPO = '".$tipoRelatorio[$i]." Relatórios'";
            $qryRelatorio  = $this->conn->prepare($sqlRelatorios);
            $qryRelatorio->bindParam(':USU_CODIGO', $_SESSION["usuario"]["usuCodigo"]);
            $qryRelatorio->execute();

            if($qryRelatorio->rowCount() > 0){
                $result = $qryRelatorio->fetchAll(PDO::FETCH_ASSOC);
                foreach ($result as $row) {
                    if($row["ACESSO"] == 1){
                        $updatePermissoes = "UPDATE permissao 
                                             SET permissao.ACESSO = 1
                                             FROM permissao
                                             INNER JOIN menus ON (menus.MEN_FORM = permissao.FORM)
                                             WHERE permissao.USU_CODIGO = ".$_SESSION["usuario"]["usuCodigo"]." AND menus.MEN_SUBMENU = '".$tipoRelatorio[$i]."' AND menus.MEN_DESCRICAO = '".$tipoRelatorio[$i]." - Relatórios'";
                        $qryUpdate = $this->conn->query($updatePermissoes);
                    }else{
                        $updatePermissoes = "UPDATE permissao 
                                             SET permissao.ACESSO = 0
                                             FROM permissao
                                             INNER JOIN menus ON (menus.MEN_FORM = permissao.FORM)
                                             WHERE permissao.USU_CODIGO = ".$_SESSION["usuario"]["usuCodigo"]." AND menus.MEN_SUBMENU = '".$tipoRelatorio[$i]."' AND menus.MEN_DESCRICAO = '".$tipoRelatorio[$i]." - Relatórios'";
                        $qryUpdate = $this->conn->query($updatePermissoes);
                    }
                }
            }   
        }
    }

    public function inserirPermissoesCopia($usuCodigo,$usuCodigoCopia){   

        $sql = "SELECT FORM, ACESSO, INCLUIR, CORRIGIR, EXCLUIR FROM permissao WHERE USU_CODIGO = ".$usuCodigo;
        $qry = $this->conn->query($sql);

        $result = $qry->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {

            $insertPermissoes = "UPDATE permissao SET  ACESSO   = :ACESSO, 
                                                        INCLUIR = :INCLUIR, 
                                                        CORRIGIR = :CORRIGIR, 
                                                        EXCLUIR = :EXCLUIR
                                WHERE USU_CODIGO = :USU_CODIGO AND FORM = :FORM";
            $qryPermissoes = $this->conn->prepare($insertPermissoes);
            $qryPermissoes->bindParam(':USU_CODIGO',    $usuCodigoCopia, PDO::PARAM_STR);
            $qryPermissoes->bindParam(':FORM',          $row['FORM'], PDO::PARAM_STR);
            $qryPermissoes->bindParam(':ACESSO',        $row['ACESSO'], PDO::PARAM_STR);
            $qryPermissoes->bindParam(':INCLUIR',       $row['INCLUIR'], PDO::PARAM_STR);
            $qryPermissoes->bindParam(':CORRIGIR',      $row['CORRIGIR'], PDO::PARAM_STR);
            $qryPermissoes->bindParam(':EXCLUIR',       $row['EXCLUIR'], PDO::PARAM_STR);
            $qryPermissoes->execute();
        }
    }

    public function loginConectado($usuLogin, $usuSenha){
        if(isset($_COOKIE['nome_usuario']) and $_COOKIE['nome_usuario'] != "" and isset($_COOKIE['senha_usuario']) and $_COOKIE['senha_usuario'] != ""){

            require_once __DIR__ . "/../config.php";
            $sqlUsuarios = "SELECT RECNO              as usuRecno, 
                                   USU_CODIGO         as usuCodigo, 
                                   USU_NOME           as usuNome, 
                                   USU_LOGIN          as usuLogin, 
                                   USU_SENHA          as usuSenha, 
                                   USU_EMAIL          as usuEmail, 
                                   USU_ADMIN          as usuAdmin, 
                                   USU_PRIMEIROACESSO as primeiroAcesso
                            FROM usuarios
                            WHERE USU_LOGIN = :UserLogin AND USU_SENHA = :UserPass ";
            $query = $this->conn->prepare($sqlUsuarios);
            $query->bindParam(':UserLogin', $usuLogin); 
            $crypSenha = ($usuSenha);
            $query->bindParam(':UserPass',  $crypSenha); 
            $query->execute();
            
            if($query->rowCount() > 0){
                $result = $query->fetch(PDO::FETCH_ASSOC);

                $_SESSION["usuario"] = $result;

                if($result["primeiroAcesso"] == 0){
                    
                    header("Location: ".INCLUDE_PATH."/primeiro-acesso/?connect=".$this->getUsuConectado()."");

                }else if($result["primeiroAcesso"] == 1){

                    $this->setUsuCodigo($result['usuCodigo']);
                    $this->setUsuEmail( $result['usuEmail']);
                    $this->setUsuRecno( $result['usuRecno']);
                    $this->setUsuNome(  $result['usuNome']);

                    $_SESSION["timeLogin"] = date('d/m/Y - H:i:s');

                    $_SESSION["permissoes"] = $this->setPermissoes();
                    $_SESSION["permissoesNovo"] = [];
                    
                    header("Location: ".INCLUDE_PATH."/home/");
                }
            }else{
                $resp['valid']   = false;
                $resp['message'] = 'Usuário não encontrado.';
            }

            return $resp;
        }
    }

    public function listUsuarios(){
         $sSQL =   "SELECT RECNO, USU_CODIGO, USU_NOME, USU_EMAIL, USU_LOGIN
                    FROM usuarios
                    WHERE USU_LOGIN != ''
                    ORDER BY USU_NOME ASC";
        $sQRY = $this->conn->prepare($sSQL);
        $sQRY->execute();

        return $sQRY->fetchAll();
    }

    public function verificaLogin($sLogin){
        $sSQL = "SELECT USU_LOGIN FROM usuarios WHERE USU_LOGIN = :USU_LOGIN";
        $sQRY = $this->conn->prepare($sSQL);
        $sQRY->bindParam(":USU_LOGIN",$sLogin);
        $sQRY->execute();

        $valid = ($sQRY->rowCount() > 0) ? "0" : "1";

        return $valid;
    }

    public function listPermissoes($usuCodigo){
        $sSQL = "SELECT USU_CODIGO, MENU, FORMEXTENSO, ACESSO, INCLUIR, CORRIGIR, EXCLUIR FROM usuarios WHERE USU_CODIGO = :USU_CODIGO";
        $sQRY = $this->conn->prepare($sSQL);
        $sQRY->bindParam(":USU_CODIGO",$usuCodigo);
        $sQRY->execute();

        $ln = $sQRY->fetchAll();

        return $ln;
    }

    public function validarSenha($user, $password){
        $valido = false;
        $sql = "SELECT RECNO 
                FROM usuarios 
                WHERE USU_LOGIN = :UserLogin AND USU_SENHA = :UserPass ";
        $query = $this->conn->prepare($sql);
        $query->bindParam(':UserLogin', $user);
        $crypSenha = ($password);
        $query->bindParam(':UserPass',  $crypSenha); 
        $query->execute();

        if($query->rowCount() > 0){
            $valido = true;
        }

        return $valido;
    }

    /**
     * Gets the value of usuario.
     *
     * @return mixed
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Sets the value of usuario.
     *
     * @param mixed $usuario the usuario
     *
     * @return self
     */
    private function _setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Gets the value of senha.
     *
     * @return mixed
     */
    public function getSenha()
    {
        return $this->senha;
    }

    /**
     * Sets the value of senha.
     *
     * @param mixed $senha the senha
     *
     * @return self
     */
    private function _setSenha($senha)
    {
        $this->senha = $senha;

        return $this;
    }

    /**
     * Gets the value of conn.
     *
     * @return mixed
     */
    public function getConn()
    {
        return $this->conn;
    }

    /**
     * Sets the value of conn.
     *
     * @param mixed $conn the conn
     *
     * @return self
     */
    public function setConn($conn)
    {
        $this->conn = $conn;

        return $this;
    }

    /**
     * Gets the value of usuConectado.
     *
     * @return mixed
     */
    public function getUsuConectado()
    {
        return $this->usuConectado;
    }

    /**
     * Sets the value of usuConectado.
     *
     * @param mixed $usuConectado the usu conectado
     *
     * @return self
     */
    public function setUsuConectado($usuConectado)
    {
        $this->usuConectado = $usuConectado;

        return $this;
    }

    /**
     * Gets the value of usuLogin.
     *
     * @return mixed
     */
    public function getUsuLogin()
    {
        return $this->usuLogin;
    }

    /**
     * Sets the value of usuLogin.
     *
     * @param mixed $usuLogin the usu login
     *
     * @return self
     */
    public function setUsuLogin($usuLogin)
    {
        $this->usuLogin = $usuLogin;

        return $this;
    }

    /**
     * Gets the value of usuSenha.
     *
     * @return mixed
     */
    public function getUsuSenha()
    {
        return $this->usuSenha;
    }

    /**
     * Sets the value of usuSenha.
     *
     * @param mixed $usuSenha the usu senha
     *
     * @return self
     */
    public function setUsuSenha($usuSenha)
    {
        $this->usuSenha = $usuSenha;

        return $this;
    }

    /**
     * Gets the value of usuNome.
     *
     * @return mixed
     */
    public function getUsuNome()
    {
        return $this->usuNome;
    }

    /**
     * Sets the value of usuNome.
     *
     * @param mixed $usuNome the usu nome
     *
     * @return self
     */
    public function setUsuNome($usuNome)
    {
        $this->usuNome = $usuNome;

        return $this;
    }

    /**
     * Gets the value of usuEmail.
     *
     * @return mixed
     */
    public function getUsuEmail()
    {
        return $this->usuEmail;
    }

    /**
     * Sets the value of usuEmail.
     *
     * @param mixed $usuEmail the usu email
     *
     * @return self
     */
    public function setUsuEmail($usuEmail)
    {
        $this->usuEmail = $usuEmail;

        return $this;
    }

    /**
     * Gets the value of usuRecno.
     *
     * @return mixed
     */
    public function getUsuRecno()
    {
        return $this->usuRecno;
    }

    /**
     * Sets the value of usuRecno.
     *
     * @param mixed $usuRecno the usu recno
     *
     * @return self
     */
    public function setUsuRecno($usuRecno)
    {
        $this->usuRecno = $usuRecno;

        return $this;
    }

    /**
     * Gets the value of usuCodigo.
     *
     * @return mixed
     */
    public function getUsuCodigo()
    {
        return $this->usuCodigo;
    }

    /**
     * Sets the value of usuCodigo.
     *
     * @param mixed $usuCodigo the usu codigo
     *
     * @return self
     */
    public function setUsuCodigo($usuCodigo)
    {
        $this->usuCodigo = $usuCodigo;

        return $this;
    }

    /**
     * Gets the value of usuTelefone.
     *
     * @return mixed
     */
    public function getUsuTelefone()
    {
        return $this->usuTelefone;
    }

    /**
     * Sets the value of usuTelefone.
     *
     * @param mixed $usuTelefone the usu telefone
     *
     * @return self
     */
    public function setUsuTelefone($usuTelefone)
    {
        $this->usuTelefone = $usuTelefone;

        return $this;
    }
}
?>