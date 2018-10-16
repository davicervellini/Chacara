<?php


require_once __DIR__ ."/GetterSetter.Class.php";

class Pessoas extends GetterSetter{
	public $conn;
	public $pessoasCampos = [
				"pes.RECNO   	        AS Recno",
			    "pes.PES_CODIGO         AS Codigo",
			    "pes.PES_NOME           AS Nome",
			    "pes.PES_TIPO_DOC       AS TipoDoc",
			    "pes.PES_DOCUMENTO      AS Documento",
			    "pes.PES_TIPO_DOC2      AS TipoDoc2",
			    "pes.PES_DOCUMENTO2     AS Documento2",
			    "pes.PES_ORGAO_DOC2     AS OrgaoDoc2",
			    "pes.PES_DTNASCIMENTO   AS DtNascimento",
			    "pes.PES_SEXO           AS Sexo",
			    "pes.PES_NATURALIDADE   AS Naturalidade",
			    "pes.PES_NACIONALIDADE  AS Nacionalidade",
			    "pes.PES_NOME_FONETICO  AS NomeFonetico"];

	public $pessoasDadosCampos = [
			   "psd.RECNO       	              AS Recno",
		       "psd.PES_CODIGO                    AS PesCodigo",
		       "psd.PSD_CODIGO                    AS PsdCodigo",
		       "psd.PSD_APRES_EMAIL               AS ApresEmail",
		       "psd.PSD_APRES_CONTATO             AS ApresContato",
		       "psd.PSD_APRES_TEL                 AS ApresTel",
		       "psd.PSD_APRES_CEL                 AS ApresCel",
		       "psd.PSD_END_NACIONAL              AS EndNacional",
		       "psd.PSD_CEP                       AS Cep",
		       "psd.PSD_TPLOCALIZACAO             AS Tplocalizacao",
		       "psd.PSD_TPIMOVEL                  AS Tpimovel",
		       "psd.PSD_TPLOGRADOURO              AS Tplogradouro",
		       "psd.PSD_LOGRADOURO                AS Logradouro",
		       "psd.PSD_NUMLOGRADOURO             AS Numlogradouro",
		       "psd.PSD_BAIRRO                    AS Bairro",
		       "psd.PSD_CIDADE                    AS Cidade",
		       "psd.PSD_UF                        AS Uf",
		       "psd.PSD_PROFISSAO                 AS Profissao",
		       "psd.PSD_FILIACAO                  AS Filiacao",
		       "psd.PSD_FILIACAO_DOC              AS FiliacaoDoc",
		       "psd.PSD_CASAMENTO_BRASIL          AS CasamentoBrasil",
		       "psd.PSD_CASAMENTO_ESTCIVIL        AS CasamentoEstcivil",
		       "psd.PSD_CASAMENTO_DATA            AS CasamentoData",
		       "psd.PSD_CASAMENTO_REGBENS         AS CasamentoRegbens",
		       "psd.PSD_CASAMENTO_SITUACAO        AS CasamentoSituacao",
		       "psd.PSD_CASAMENTO_CONJUGE_DOC     AS CasamentoConjugedoc",
		       "psd.PSD_PACANT                    AS Pacant",
		       "psd.PSD_PACANT_ORGAO              AS PacantOrgao",
		       "psd.PSD_PACANT_NLIVRO             AS PacantNlivro",
		       "psd.PSD_PACANT_NLIVRO_COMPL       AS PacantNlivrocompl",
		       "psd.PSD_PACANT_PAGINI             AS PacantPagini",
		       "psd.PSD_PACANT_PAGINI_COMPL       AS PacantPaginiCompl",
		       "psd.PSD_PACANT_NREGISTRO          AS PacantNregistro",
		       "psd.PSD_PACANT_DTREGISTRO         AS PacantDtregistro",
		       "psd.PSD_PACANT_DADOSADICIONAIS    AS PacantDadosadicionais",
		       "psd.PSD_UNIEST                    AS Uniest",
		       "psd.PSD_UNIEST_CONJUGE_DOC        AS UniestConjugedoc",
		       "psd.PSD_UNIEST_INFORMACOES        AS UniestInformacoes",
		       "psd.PSD_CAPCIVIL_INFORMACOES      AS CapcivilInformacoes",
		       "psd.PSD_CAPCIVIL_TPDOC            AS CapcivilTpdoc",
		       "psd.PSD_CAPCIVIL_ORGAO            AS CapcivilOrgao",
		       "psd.PSD_CAPCIVIL_NLIVRO           AS CapcivilNlivro",
		       "psd.PSD_CAPCIVIL_NLIVRO_COMPL     AS CapcivilNlivroCompl",
		       "psd.PSD_CAPCIVIL_PAGINI           AS CapcivilPagini",
		       "psd.PSD_CAPCIVIL_PAGINI_COMPL     AS CapcivilPaginiCompl",
		       "psd.PSD_CAPCIVIL_NREGISTRO        AS CapcivilNregistro",
		       "psd.PSD_CAPCIVIL_DTREGISTRO       AS CapcivilDtregistro",
		       "psd.PSD_CAPCIVIL_DADOSADICIONAIS  AS CapcivilDadosadicionais",
		       "psd.PSD_PASTA                     AS Pasta",
		       "psd.PSD_PASTAC                    AS PastaC",
		       "psd.PSD_IMAGEM                    AS Imagem",
		       "psd.PSD_DATA                      AS DtGeracao",
		       "psd.PSD_HORA                      AS HrGeracao"];

	public function __construct( $pesCodigo = null){
		$this->conn = new ConexaoMySQL;
		$this->ws   = new ArcaTDPJ_WS;
		$this->sys  = new Sistema;

		if( $pesCodigo !== null ){

			$this->identificarPessoa( ["PES_CODIGO"=> $pesCodigo] );
		}

	}

	public function identificarPessoa( array $campos, $empty = false): bool{
		if(count( $campos ) == 0)
			throw new Exception("Nenhum parametro encontrado");

		$params = $this->generateBindParams( $campos, $empty );

		$sql = "SELECT " . join(", ", $this->pessoasCampos ) ."
				FROM pessoas pes
				WHERE 1 = 1 ". $params['whereClause']. "
				ORDER BY RECNO DESC";
		$qry = $this->conn->prepare($sql);
		$qry->execute( $params['binds'] );

		if($qry->rowCount() > 0){
			$res = $qry->fetchAll();
			$this->setData($res);
			return true;
		}else{
			return false;
		}
	}

	public function showQuery($query, $params)
    {
        $keys = array();
        $values = array();

        # build a regular expression for each parameter
        foreach ($params as $key=>$value)
        {
            if (is_string($key))
            {
                $keys[] = '/:'.$key.'/';
            }
            else
            {
                $keys[] = '/[?]/';
            }

            if(is_numeric($value))
            {
                $values[] = intval($value);
            }
            else
            {
                $values[] = '"'.$value .'"';
            }
        }

        $query = preg_replace($keys, $values, $query, 1, $count);
        return $query;
    }


	public function identificarPessoasComDados( array $campos): bool{
		if(count( $campos ) == 0)
			throw new Exception("Nenhum parametro encontrado");

		$params = $this->generateBindParams( $campos );

		$sql = "SELECT " . join(", ", $this->pessoasCampos ) .",
						" . join(", ", $this->pessoasDadosCampos ) ."
				FROM pessoas pes
				LEFT JOIN pessoas_dados psd ON ( psd.PES_CODIGO = pes.PES_CODIGO )
				WHERE 1 = 1 ". $params['whereClause']. "
				ORDER BY psd.RECNO DESC LIMIT 1";

		$qry = $this->conn->prepare($sql);

		$qry->execute( $params['binds'] );

		if($qry->rowCount() > 0){
			$res = $qry->fetchAll();

			$this->setData($res);
			return true;
		}else
			return false;

	}

	private function generateSetter( $string, $value ){
		$this->{"set".$string}( strtoupper(  $this->sys->removeAcentos( $value ) ) );
	}

	private function generateBindParams( array $campos , $empty = false): array{

		$whereClause = "";
		foreach ($campos as $key => $value) {

			$key = str_replace(":", "", $key);
			if($value != ""){
				$whereClause .= " AND $key = ? ";
				if($value == ""){
					$value = NULL;
				}
				$bind[]       = $value;
				}
			else{

				$sEmptyClause = ($empty == true) ? " OR $key = '' " : "";

				$whereClause .= " AND ($key IS NULL $sEmptyClause)  ";
			}

		}

		return [
			"whereClause" => @$whereClause,
			"binds"       => @$bind
		];

	}

	public function identificarDados( array $campos): bool{

		$params = $this->generateBindParams( $campos );

		$sql = "SELECT " . join(", ", $this->pessoasDadosCampos ) ."
				FROM pessoas_dados psd
				WHERE 1 = 1 " . $params['whereClause'] ;
		$qry = $this->conn->prepare($sql);
		$qry->execute( $params['binds'] );

		if($qry->rowCount() > 0){
			$result = $qry->fetchAll(PDO::FETCH_ASSOC);
			$this->setData($result);
			$this->setTotalDados( $qry->rowCount() );
			$this->setFetchAllResults( $result );
			return true;
		}

		return false;

	}


	public function identificarEnderecos( array $campos): bool{

		$params = $this->generateBindParams( $campos );

		$sql = "SELECT PSD_END_NACIONAL as EndNacional , PSD_CEP as Cep, PSD_TPLOCALIZACAO as TpLocalizacao, PSD_TPIMOVEL as Tpimovel, PSD_TPLOGRADOURO as Tplogradouro, PSD_LOGRADOURO as Logradouro, PSD_NUMLOGRADOURO as Numlogradouro, PSD_BAIRRO as Bairro, PSD_CIDADE as Cidade, PSD_UF as Uf
				FROM pessoas_dados psd
				WHERE 1 = 1 " . $params['whereClause'] ."
				GROUP BY PSD_END_NACIONAL, PSD_CEP, PSD_TPLOCALIZACAO, PSD_TPIMOVEL, PSD_TPLOGRADOURO, PSD_LOGRADOURO, PSD_NUMLOGRADOURO, PSD_BAIRRO, PSD_CIDADE, PSD_UF
				" ;
		$qry = $this->conn->prepare($sql);
		$qry->execute( $params['binds'] );

		if($qry->rowCount() > 0){
			$result = $qry->fetchAll(PDO::FETCH_ASSOC);
			$this->setData($result);
			$this->setTotalDados( $qry->rowCount() );
			$this->setFetchAllResults( $result );
			return true;
		}

		return false;

	}

	public function identificaUltimoEstadoCivil( array $campos): bool{

		$params = $this->generateBindParams( $campos );
		$sql = "SELECT psd.PSD_CASAMENTO_ESTCIVIL AS CasamentoEstcivil
				FROM pessoas pes
				INNER JOIN pessoas_dados psd ON (pes.PES_CODIGO = psd.PES_CODIGO)
				WHERE 1 = 1 " . $params['whereClause'] ."
				ORDER BY psd.RECNO DESC LIMIT 1";
		$qry = $this->conn->prepare($sql);
		$qry->execute( $params['binds'] );

		if($qry->rowCount() > 0){
			$result = $qry->fetchAll(PDO::FETCH_ASSOC);

			$this->setData($result);
			$this->setTotalDados( $qry->rowCount() );
			$this->setFetchAllResults( $result );
			return true;
		}

		return false;

	}

	public function preencheArrayComDados(){
		return $vDados = [
		       "PSD_APRES_EMAIL"               => strtolower($this->getApresEmail()),
		       "PSD_APRES_CONTATO"             => $this->getApresContato(),
		       "PSD_APRES_TEL"                 => $this->getApresTel(),
		       "PSD_APRES_CEL"                 => $this->getApresCel(),
		       "PSD_END_NACIONAL"              => $this->getEndNacional(),
		       "PSD_CEP"                       => $this->getCep(),
		       "PSD_TPLOCALIZACAO"             => $this->getTplocalizacao(),
		       "PSD_TPIMOVEL"                  => $this->getTpimovel(),
		       "PSD_TPLOGRADOURO"              => $this->getTplogradouro(),
		       "PSD_LOGRADOURO"                => $this->getLogradouro(),
		       "PSD_NUMLOGRADOURO"             => $this->getNumlogradouro(),
		       "PSD_BAIRRO"                    => $this->getBairro(),
		       "PSD_CIDADE"                    => $this->getCidade(),
		       "PSD_UF"                        => $this->getUf(),
		       "PSD_PROFISSAO"                 => $this->getProfissao(),
			   "PSD_FILIACAO"                  => $this->getFiliacao(),
			   "PSD_FILIACAO_DOC"              => $this->getFiliacaoDoc(),
		       "PSD_CASAMENTO_BRASIL"          => $this->getCasamentoBrasil(),
		       "PSD_CASAMENTO_ESTCIVIL"        => $this->getCasamentoEstcivil(),
		       "PSD_CASAMENTO_DATA"            => $this->getCasamentoData(),
		       "PSD_CASAMENTO_REGBENS"         => $this->getCasamentoRegbens(),
		       "PSD_CASAMENTO_SITUACAO"        => $this->getCasamentoSituacao(),
		       "PSD_CASAMENTO_CONJUGE_DOC"     => $this->getCasamentoConjugeDoc(),
		       "PSD_PACANT"                    => $this->getPacant(),
		       "PSD_PACANT_ORGAO"              => $this->getPacantOrgao(),
		       "PSD_PACANT_NLIVRO"             => $this->getPacantNlivro(),
		       "PSD_PACANT_NLIVRO_COMPL"       => $this->getPacantNlivroCompl(),
		       "PSD_PACANT_PAGINI"             => $this->getPacantPagini(),
		       "PSD_PACANT_PAGINI_COMPL"       => $this->getPacantPaginiCompl(),
		       "PSD_PACANT_NREGISTRO"          => $this->getPacantNregistro(),
		       "PSD_PACANT_DTREGISTRO"         => $this->getPacantDtregistro(),
		       "PSD_PACANT_DADOSADICIONAIS"    => $this->getPacantDadosadicionais(),
		       "PSD_UNIEST"                    => $this->getUniest(),
		       "PSD_UNIEST_CONJUGE_DOC"        => $this->getUniestConjugeDoc(),
		       "PSD_UNIEST_INFORMACOES"        => $this->getUniestInformacoes(),
		       "PSD_CAPCIVIL_INFORMACOES"      => $this->getCapcivilInformacoes(),
		       "PSD_CAPCIVIL_TPDOC"            => $this->getCapcivilTpdoc(),
		       "PSD_CAPCIVIL_ORGAO"            => $this->getCapcivilOrgao(),
		       "PSD_CAPCIVIL_NLIVRO"           => $this->getCapcivilNlivro(),
		       "PSD_CAPCIVIL_NLIVRO_COMPL"     => $this->getCapcivilNlivroCompl(),
		       "PSD_CAPCIVIL_PAGINI"           => $this->getCapcivilPagini(),
		       "PSD_CAPCIVIL_PAGINI_COMPL"     => $this->getCapcivilPaginiCompl(),
		       "PSD_CAPCIVIL_NREGISTRO"        => $this->getCapcivilNregistro(),
		       "PSD_CAPCIVIL_DTREGISTRO"       => $this->getCapcivilDtregistro(),
		       "PSD_CAPCIVIL_DADOSADICIONAIS"  => $this->getCapcivilDadosadicionais(),
		       "PSD_PASTA"                     => $this->getPasta(),
		       "PSD_PASTAC"                    => $this->getPastaC(),
		       "PSD_IMAGEM"                    => $this->getImagem(),
		];
	}

	public function setDadosByArray( $vDadosPessoa ){
		foreach ($vDadosPessoa as $key => $value) {

			$posTriggerSeparator = strpos($key,"_");
			$sString = mb_strcut($key, ($posTriggerSeparator+1) );
			$s       = explode("_", $sString);
			$stringCapitalizada = "";
			foreach ($s as $key => $valueString) {
				$stringCapitalizada .= ucfirst(strtolower($valueString));
			}

			$this->generateSetter( $stringCapitalizada, $value );

		}

		return $this;
	}

	public function inserirPessoa( $vDadosCorrecao = []) {

		if($this->getNome() == "" && $this->getDocumento() == "" )
			throw new Exception("Nome e Documento vazio.");

		require_once __DIR__ . "/PesquisaFonetica.Class.php";
		$fon = new \PesquisaFonetica\PesquisaFonetica();

		// foreach ($this->settings as $key => $value) {
		// 	$this->generateSetter( $key, $value );
		// }

		$this->setDocumento( $this->sys->limpaVars( $this->getDocumento() ) );

		if($this->getCodigo() == "" && !$this->identificarPessoa( ["PES_DOCUMENTO" => $this->getDocumento() ] )){

			if($this->getTipoDoc() == '') $this->setTipoDoc( strlen( $this->getDocumento() ) == 11 ? 'CPF' : "CNPJ" );
			if($this->getNomeFonetico() == '') $this->setNomeFonetico( $fon->PalavraFonetica( $this->getDocumento() . " " . $this->getNome() ) );
			$this->setCodigo( $this->sys->gera_codigo('pessoas') );

			$vPessoa = [
				"PES_CODIGO"         => $this->getCodigo(),
				"PES_NOME"           => $this->getNome(),
				"PES_TIPO_DOC"       => $this->getTipoDoc(),
				"PES_DOCUMENTO"      => $this->getDocumento(),
				"PES_TIPO_DOC2"      => $this->getTipoDoc2(),
				"PES_DOCUMENTO2"     => $this->getDocumento2(),
				"PES_ORGAO_DOC2"     => $this->getOrgaoDoc2(),
				"PES_DTNASCIMENTO"   => $this->getDtnascimento(),
				"PES_SEXO"   		 => $this->getSexo(),
				"PES_NATURALIDADE"   => $this->getNaturalidade(),
				"PES_NACIONALIDADE"  => $this->getNacionalidade(),
				"PES_NOME_FONETICO"  => $this->getNomeFonetico()
			];

			$p   = $this->sys->vStrings( $vPessoa );
			$res = $this->ws->inserirRegistro( getToken( $this->conn->db() ) ,'pessoas', $p['campos'], $p['dados'] );
			if($res != "")
				throw new Exception( "Erro ao inserir a pessoa. Motivo:" . $res );

		}else{

			if(count($vDadosCorrecao) > 0){
				$this->setDadosByArray($vDadosCorrecao);
			}

			$vPessoa = [
				"PES_NOME"           => $this->getNome(),
				"PES_TIPO_DOC2"      => $this->getTipoDoc2(),
				"PES_DOCUMENTO2"     => $this->getDocumento2(),
				"PES_ORGAO_DOC2"     => $this->getOrgaoDoc2(),
				"PES_DTNASCIMENTO"   => $this->getDtnascimento(),
				"PES_SEXO"   		 => $this->getSexo(),
				"PES_NATURALIDADE"   => $this->getNaturalidade(),
				"PES_NACIONALIDADE"  => $this->getNacionalidade()
			];
			$p   = $this->sys->vStrings( $vPessoa );
			$res = $this->ws->corrigirRegistro( getToken( $this->conn->db() ) ,'pessoas', 'PES_CODIGO = '.$this->getCodigo(), $p['campos'], $p['dados'] );
			if($res != "")
				throw new Exception( "Erro ao corrigir a pessoa. Motivo:" . $res );

		}


		$sDocumento = @$this->getDocumento();
		$pesCodigo  = @$this->getCodigo();


		$sql = "SELECT " . join(", ", $this->pessoasDadosCampos ) ."
				FROM pessoas_dados psd
				INNER JOIN pessoas pes ON (pes.PES_CODIGO = psd.PES_CODIGO)
				WHERE 1 = 1 ";
		if($sDocumento != ""){
			$sql .= " AND pes.PES_DOCUMENTO = :PES_DOCUMENTO ";
		}

		if($pesCodigo != ""){
			$sql .= " AND pes.PES_CODIGO = :PES_CODIGO ";
		}

		$sql .= "ORDER BY psd.RECNO DESC LIMIT 1";

		$qry = $this->conn->prepare($sql);

		if($sDocumento != ""){
			$qry->bindParam(":PES_DOCUMENTO", $sDocumento);
		}

		if($pesCodigo != ""){
			$qry->bindParam(":PES_CODIGO", $pesCodigo);
		}


		$qry->execute();
		if($qry->rowCount() > 0){

			$resQuery = $qry->fetch();
			foreach ($resQuery as $key => $value) {
				if($this->{'get'.$key}() != $value && $this->{'get'.$key}() != ""){

				}else{
					$this->{'set'.$key}( $value );
				}
			}
		}

		$vDados = $this->preencheArrayComDados();

		$this->setPsdCodigo( $this->sys->gera_codigo('pessoas_dados') );
		$insertDados = [
			"PES_CODIGO"        => $this->getCodigo(),
			"PSD_CODIGO"        => $this->getPsdCodigo(),
			"PSD_DATA"          => date('Y-m-d'),
			"PSD_HORA"          => date('H:i:s'),
        ];
        $vDados = array_merge($vDados, $insertDados);

       	$p   = $this->sys->vStrings( $vDados );
		$res = $this->ws->inserirRegistro( getToken( $this->conn->db() ) ,'pessoas_dados', $p['campos'], $p['dados'] );
		if($res != "")
			throw new Exception( "Erro ao inserir os dados da pessoa. Motivo:" . $res );

	}

}
