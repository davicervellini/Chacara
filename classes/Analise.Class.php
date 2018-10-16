<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class Analise extends GetterSetter{
    public $ws;
	public $conn;
    public $usuCodigo;
    public $usuNome;
    public $usuSessao;
    public $prenota;
    public $matricula;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();
        $this->ws   = new ArcaTDPJ_WS;
        $this->sys  = new Sistema;
	}

    public function listLivros(){

        $sql = "SELECT TPL_CODIGO, TPL_DESCRICAO
                FROM tipo_livro";
        $qry = $this->conn->prepare($sql);
        $qry->execute();

        return $qry->fetchAll(PDO::FETCH_ASSOC);
    }

	public function listStatusPermitidos(){

		$sql = "SELECT oco.OCO_CODIGO, oco.OCO_DESCRICAO
                FROM ocorrencias oco
                WHERE oco.OCO_CODIGO IN (3, 4, 6, 18, 19, 20, 21, 22, 23)
                order by oco.OCO_DESCRICAO";
		$qry = $this->conn->prepare($sql);
		$qry->execute();

		return $qry->fetchAll(PDO::FETCH_ASSOC);
	}

	public function listOcorrencias(){

		$sql = "SELECT oco.OCO_CODIGO, oco.OCO_DESCRICAO
                FROM ocorrencias oco
                WHERE oco.OCO_CODIGO IN (6, 4)
                order by oco.OCO_DESCRICAO";
		$qry = $this->conn->prepare($sql);
		$qry->execute();

		return $qry->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getOcorrencias($descricao){

		$sql = "SELECT OCO_CODIGO FROM ocorrencias WHERE OCO_DESCRICAO LIKE :OCO_DESCRICAO";
		$qry = $this->conn->prepare($sql);
		$descricao = "%".$descricao."%";
		$qry->bindParam(':OCO_DESCRICAO', $descricao);
		$qry->execute();
		$ln = $qry->fetch(PDO::FETCH_ASSOC);
		return $ln['OCO_CODIGO'];
	}

    public function getQualificacao(){
        $sql = 'SELECT QUA_CODIGO, QUA_DESC FROM qualifica ';
        $qry = $this->conn->prepare($sql);

        $qry->execute();
        if($qry->rowCount() > 0){

            $result = array();
            $resQuery = $qry->fetchAll();
            foreach($resQuery as $ln){
                $ln['QUA_DESC'] = ($ln['QUA_DESC']);
                array_push($result, $ln);
            }

            return $result;
        }else{
            return '';
        }
    }

    public function limparValor($valor){
        return str_replace(",",".",str_replace(".","",$valor));
    }

    public function getDate(){
        return date('Y-m-d');
    }

    public function addDados(array $sDados){
        $campos = array();
        $dados  = array();
        $result = array();
        foreach ($sDados as $key => $value) {
            array_push($campos, $key);
            array_push($dados, $value);
        }

        $result['campos'] = $campos;
        $result['dados']  = $dados;

        return $result;
    }

    public function getQualiDesc ($codigo){
    	$sql = 'SELECT QUA_DESC FROM qualifica WHERE QUA_CODIGO = :QUA_CODIGO';
    	$qry = $this->conn->prepare($sql);
    	$qry->bindParam(':QUA_CODIGO', $codigo);
		$qry->execute();
		$ln = $qry->fetch(PDO::FETCH_ASSOC);
		return $ln['QUA_DESC'];
    }

    public function identificarCorrecao($vListAntiga, $vListNova){
        $resp = "";
        foreach($vListAntiga as $key => $value){
            if(isset($vListNova[$key])){
                if($vListNova[$key] != $value){
                    $resp .= " ".$key." de ".$vListAntiga[$key]." para ".$vListNova[$key].";";
                }
            }
        }

        return $resp;
    }

    public function limparTemp($ws, $nomeTabela, $protocolo){

        $res = $ws->deletarRegistro( getToken( $this->conn->db() ), $nomeTabela, " USU_CODIGO =  ".$this->getUsuCodigo()." AND USU_SESSAO = '".$this->getUsuSessao()."' AND ARS_PROTOCOLO =".$protocolo );
        return $res;
    }

    public function inserirRestricoes(array $vRestricoes, $protocolo){

        $resp = ["error"=>"", "restricoes"=>$vRestricoes];

        $restricoesTemporarias = [];
        $sqlVerificarContraditorio = "SELECT INF_DESCRICAO
                                      FROM recepcao_informacoes
                                      WHERE ARS_PROTOCOLO = :ARS_PROTOCOLO";
        $qryContraditorio = $this->conn->prepare($sqlVerificarContraditorio);
        $qryContraditorio->bindParam(":ARS_PROTOCOLO", $protocolo );
        $qryContraditorio->execute();
        if($qryContraditorio->rowCount() > 0){
            $resQuery = $qryContraditorio->fetchAll();
            foreach ($resQuery as $row) {
                $restricoesTemporarias[] = $row["INF_DESCRICAO"];
            }
        }

        foreach ($vRestricoes as $key => $restricoes) {
            $search = array_search( ($restricoes["message"]), $restricoesTemporarias);
            if( !is_numeric( $search ) ){
                $infoRecepcao = $this->addDados(array(
                    'INF_DESCRICAO'           => $restricoes["message"],
                    'INF_RESTRICAO'           => 1,
                    'ARS_PROTOCOLO'           => $protocolo,
                    'INF_PROTOCOLO_RESTRITO'  => $restricoes["ARS_PROTOCOLO"],
                ));

                $res = $this->ws->inserirRegistro(getToken( $this->conn->db() ), 'recepcao_informacoes', $infoRecepcao['campos'], $infoRecepcao['dados']);
                $resp["error"] .= ($res != "") ? "Informacoes:". $res .";" : "";
            }


        }

        return $resp;

    }

    public function getTotalCustas($protocolo, $seq, $temp = 0){
        $tabela = ($temp == 0) ? 'calculo_reg' : 'calculo_reg_temp';
        $sql = 'SELECT SUM(CAL_TOTAL) AS Total
                FROM '.$tabela.'
                WHERE ARS_PROTOCOLO = :ARS_PROTOCOLO and ARS_SEQ = :ARS_SEQ';
        $qry = $this->conn->prepare($sql);
        $qry->bindParam(":ARS_PROTOCOLO", $protocolo);
        $qry->bindParam(":ARS_SEQ",       $seq);
        $qry->execute();
        $ln = $qry->fetch(PDO::FETCH_ASSOC);
        return $ln['Total'];
    }

    public function resgatarDadosAntigos( $vCampos, $sTabela, $recno ){
        $camposAlteracao = "";
        foreach ($vCampos as $key => $value) {
            $camposAlteracao .= ( $camposAlteracao != '' ) ? ",".$key : $key;
        }

        $sqlDadosAntigos = "SELECT $camposAlteracao FROM $sTabela WHERE RECNO = :RECNO";
        $qryDadosAntigos = $this->conn->prepare($sqlDadosAntigos);
        $qryDadosAntigos->bindParam(":RECNO", $recno);
        $qryDadosAntigos->execute();
        return $qryDadosAntigos->fetch();
    }

    public function getTabelas(){
        $sql = 'SELECT CUS_DATA FROM cad_tab_custas GROUP BY CUS_DATA, RECNO ORDER BY RECNO DESC LIMIT 1';
        $qry = $this->conn->prepare($sql);
        $qry->execute();
        $ln = $qry->fetch();
        $cusData  = $ln['CUS_DATA'];

        $sql = "SELECT CUS_TABELA, CUS_LETRA, CUS_DESCRICAO FROM cad_tab_custas WHERE CUS_DATA = '$cusData' GROUP BY CUS_TABELA, CUS_LETRA, CUS_DESCRICAO";
        $qryTabelas = $this->conn->prepare($sql);
        $qryTabelas->execute();
        if($qryTabelas->rowCount() > 0){

            $result = array();
            $result = $qryTabelas->fetchAll();
            // foreach ($result as $ln) {

            //     $ln['CUS_DESCRICAO'] = ($ln['CUS_DESCRICAO']);
            //     array_push($result, $ln);
            // }

            return $result;
        }else{
            return '';
        }
    }

    public $pessoasCampos = [
        "pes.RECNO              ",
        "pes.PES_CODIGO         ",
        "pes.PES_NOME           ",
        "pes.PES_SEXO           ",
        "pes.PES_TIPO_DOC       ",
        "pes.PES_DOCUMENTO      ",
        "pes.PES_TIPO_DOC2      ",
        "pes.PES_DOCUMENTO2     ",
        "pes.PES_ORGAO_DOC2     ",
        "pes.PES_DTNASCIMENTO   ",
        "pes.PES_NATURALIDADE   ",
        "pes.PES_NACIONALIDADE  ",
        "pes.PES_NOME_FONETICO  "
    ];

    public $pessoasDadosCampos = [
        "psd.RECNO                         ",
        "psd.PSD_CODIGO                    ",
        "psd.PSD_APRES_EMAIL               ",
        "psd.PSD_APRES_CONTATO             ",
        "psd.PSD_APRES_TEL                 ",
        "psd.PSD_APRES_CEL                 ",
        "psd.PSD_END_NACIONAL              ",
        "psd.PSD_CEP                       ",
        "psd.PSD_TPLOCALIZACAO             ",
        "psd.PSD_TPIMOVEL                  ",
        "psd.PSD_TPLOGRADOURO              ",
        "psd.PSD_LOGRADOURO                ",
        "psd.PSD_NUMLOGRADOURO             ",
        "psd.PSD_BAIRRO                    ",
        "psd.PSD_CIDADE                    ",
        "psd.PSD_UF                        ",
        "psd.PSD_PROFISSAO                 ",
        "psd.PSD_FILIACAO                  ",
        "psd.PSD_FILIACAO_DOC              ",
        "psd.PSD_CASAMENTO_BRASIL          ",
        "psd.PSD_CASAMENTO_ESTCIVIL        ",
        "psd.PSD_CASAMENTO_DATA            ",
        "psd.PSD_CASAMENTO_REGBENS         ",
        "psd.PSD_CASAMENTO_SITUACAO        ",
        "psd.PSD_CASAMENTO_CONJUGE_DOC     ",
        "psd.PSD_PACANT                    ",
        "psd.PSD_PACANT_ORGAO              ",
        "psd.PSD_PACANT_NLIVRO             ",
        "psd.PSD_PACANT_NLIVRO_COMPL       ",
        "psd.PSD_PACANT_PAGINI             ",
        "psd.PSD_PACANT_PAGINI_COMPL       ",
        "psd.PSD_PACANT_NREGISTRO          ",
        "psd.PSD_PACANT_DTREGISTRO         ",
        "psd.PSD_PACANT_DADOSADICIONAIS    ",
        "psd.PSD_UNIEST                    ",
        "psd.PSD_UNIEST_CONJUGE_DOC        ",
        "psd.PSD_UNIEST_INFORMACOES        ",
        "psd.PSD_CAPCIVIL_INFORMACOES      ",
        "psd.PSD_CAPCIVIL_TPDOC            ",
        "psd.PSD_CAPCIVIL_ORGAO            ",
        "psd.PSD_CAPCIVIL_NLIVRO           ",
        "psd.PSD_CAPCIVIL_NLIVRO_COMPL     ",
        "psd.PSD_CAPCIVIL_PAGINI           ",
        "psd.PSD_CAPCIVIL_PAGINI_COMPL     ",
        "psd.PSD_CAPCIVIL_NREGISTRO        ",
        "psd.PSD_CAPCIVIL_DTREGISTRO       ",
        "psd.PSD_CAPCIVIL_DADOSADICIONAIS  ",
        "psd.PSD_PASTA                     ",
        "psd.PSD_PASTAC                    ",
        "psd.PSD_IMAGEM                    ",
        "psd.PSD_DATA                      ",
        "psd.PSD_HORA                      "
    ];

}
