<?php

require_once __DIR__ ."/GetterSetter.Class.php";
class Recepcao extends GetterSetter{

	public $matricula;
	public $protocolo;
    public $natureza;
    public $usuCodigo;
    public $usuNome;
	public $usuSessao;

    public function __construct(){
        $this->conn = new ConexaoMySQL;
        $this->ws   = new ArcaTDPJ_WS;
        $this->sys  = new Sistema;
    }

    public function getNaturezas($tipo){
        $sql = "SELECT NAT_DESCCODIGO, NAT_DESCRICAO, CUS_TABELA, DIV_CODIGO, NAT_TPATO_REG, NAT_TPATO_CERT, NAT_TPATO_AV, NAT_TPATO_NOT, NAT_TPATO_INT
                FROM  naturezas
                WHERE {$tipo} = 1
                ORDER BY NAT_DESCRICAO ASC";
        $qry = $this->conn->prepare($sql);
        $qry->execute();
        if($qry->rowCount() > 0){

            $result = array();
            $resQuery = $qry->fetchAll();
            foreach ($resQuery as $ln) {
                $ln['NAT_DESCRICAO'] = ($ln['NAT_DESCRICAO']);

                $ln["ATOS_PRATICADOS"] = "";
                $ln["ATOS_PRATICADOS"] .= ($ln["NAT_TPATO_REG"] == 1) ? (($ln["ATOS_PRATICADOS"] != "") ? "-REGISTRO"    : "REGISTRO")    : "";
                $ln["ATOS_PRATICADOS"] .= ($ln["NAT_TPATO_AV"]  == 1) ? (($ln["ATOS_PRATICADOS"] != "") ? "-AVERBACAO"   : "AVERBACAO")   : "";
                $ln["ATOS_PRATICADOS"] .= ($ln["NAT_TPATO_NOT"] == 1) ? (($ln["ATOS_PRATICADOS"] != "") ? "-INTIMACAO"   : "INTIMACAO")   : "";
                $ln["ATOS_PRATICADOS"] .= ($ln["NAT_TPATO_INT"] == 1) ? (($ln["ATOS_PRATICADOS"] != "") ? "-NOTIFICACAO" : "NOTIFICACAO") : "";
                array_push($result, $ln);
            }

            return $result;
        }else{
            return '';
        }
    }

    public function getEspecies(){
        $sql = 'SELECT ESP_CODIGO, ESP_DESCRICAO FROM especies ';
        $res = Conexao::query($sql);
        if(Conexao::rows($res) > 0){

            $result = array();
            while($ln = Conexao::fetchAssoc($res)){
                $ln['ESP_DESCRICAO'] = utf8_encode($ln['ESP_DESCRICAO']);
                array_push($result, $ln);
            }

            return $result;
        }else{
            return '';
        }
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
            foreach ($result as $ln) {

                $ln['CUS_DESCRICAO'] = ($ln['CUS_DESCRICAO']);
                array_push($result, $ln);
            }

            return $result;
        }else{
            return '';
        }
    }

    public function getDivisores(){
        $sql = 'SELECT DIV_CODIGO, DIV_DESCRICAO FROM divisores ';
        $qry = $this->conn->prepare($sql);
        $qry->execute();
        if($qry->rowCount() > 0){

            $result = array();
            $resQuery = $qry->fetchAll();
            foreach ($resQuery as $ln) {

                $ln['DIV_DESCRICAO'] = ($ln['DIV_DESCRICAO']);
                array_push($result, $ln);
            }

            return $result;
        }else{
            return '';
        }
    }



    public function getSeqLivro($iMatricula){

        $sql = "SELECT ARS_SEQLIVRO FROM arc_seq_reg WHERE ARS_NUMLIVRO = :ARS_NUMLIVRO ORDER BY ARS_SEQLIVRO DESC LIMIT 1";
        $res = $this->conn->prepare($sql);
        $res->bindParam(":ARS_NUMLIVRO", $iMatricula);
        $res->execute();

    	$sqlTemp = "SELECT ARS_SEQLIVRO FROM arc_seq_reg_temp WHERE ARS_NUMLIVRO = :ARS_NUMLIVRO ORDER BY ARS_SEQLIVRO DESC LIMIT 1";
    	$qryTemp = $this->conn->prepare($sqlTemp);
        $qryTemp->bindParam(":ARS_NUMLIVRO", $iMatricula);
        $qryTemp->execute();

        $rowsTemp = $qryTemp->rowCount();

    	if($res->rowCount() > 0){


            if($rowsTemp > 0){
                $resTemp = $qryTemp->fetch();
                return $resTemp['ARS_SEQLIVRO'] + 1;
            }else{
                $ln = $res->fetch();
                return $ln['ARS_SEQLIVRO'] + 1;
            }

    	}else{

            if($rowsTemp > 0){
                $resTemp = $qryTemp->fetch();
                return $resTemp['ARS_SEQLIVRO'] + 1;
            }else{
                return 1;
            }

    	}

    }

    public function getDate(){
    	return date('Y-m-d');
    }

    public function getSequencia($iProtocolo){

		$sql = "SELECT ARS_SEQ FROM arc_seq_reg WHERE ARS_PROTOCOLO = '".$iProtocolo."' ORDER BY ARS_SEQ DESC LIMIT 1";
		$res = $this->conn->prepare($sql);
        $res->execute();

        $sqlTemp = "SELECT ARS_SEQ FROM arc_seq_reg_temp WHERE ARS_PROTOCOLO = '".$iProtocolo."' ORDER BY ARS_SEQ DESC LIMIT 1";
        $qryTemp = $this->conn->prepare($sqlTemp);
        $qryTemp->execute();
        $rowsTemp = $qryTemp->rowCount();

		if($res->rowCount() > 0){

            if($rowsTemp > 0){
                $resTemp = $qryTemp->fetch();
                return $resTemp['ARS_SEQ'] + 1;
            }else{
                $ln = $res->fetch();
                return $ln['ARS_SEQ'] + 1;
            }

		}else{

            if($rowsTemp > 0){
                $resTemp = $qryTemp->fetch();
                return $resTemp['ARS_SEQ'] + 1;
            }else{
                return 1;
            }

		}
    }

    public function limparValor($valor){
        return str_replace(",",".",str_replace(".","",$valor));
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

    public function isPrimeiroAto(){
        $sql = "SELECT RECNO FROM arc_reg_temp WHERE USU_CODIGO = '".$this->getUsuCodigo()."' AND USU_SESSAO = '".$this->getUsuSessao()."'";
        $res = $this->conn->prepare($sql);
        $res->execute();
        if($res->rowCount() == 0){
            return true;
        }
        return false;
    }

    public function isPrimeiroAtoManutencao($protocolo){
        $sql = "SELECT RECNO FROM arc_reg_temp WHERE ARS_PROTOCOLO = :ARS_PROTOCOLO";
        $res = $this->conn->prepare($sql);
        $res->bindParam(":ARS_PROTOCOLO", $protocolo);
        $res->execute();
        if($res->rowCount() == 0){
            return true;
        }
        return false;
    }

    public function getTotalAtos(){
        $sql = "SELECT COUNT(RECNO) AS qtde FROM arc_seq_reg_temp WHERE USU_CODIGO = '".$this->getUsuCodigo()."' AND USU_SESSAO = '".$this->getUsuSessao()."'";
        $qry = $this->conn->prepare($sql);
        $qry->execute();
        if($qry->rowCount() > 0){
           $ln = $qry->fetch();
            return $ln['qtde'];
        }
        return 1;
    }

    public function valorTotal(){
        $usuCodigo = $this->getUsuCodigo();
        $usuSessao = $this->getUsuSessao();

        $sql = "SELECT SUM(ARS_VALORCUSTAS) AS ARS_VALORCUSTAS FROM arc_seq_reg_temp WHERE USU_CODIGO = :USU_CODIGO AND USU_SESSAO = :USU_SESSAO ";
        $res = $this->conn->prepare($sql);
        $res->bindParam(":USU_CODIGO", $usuCodigo);
        $res->bindParam(":USU_SESSAO", $usuSessao);
        $res->execute();
        $ln = $res->fetch();
        return ($ln["ARS_VALORCUSTAS"] != 0) ? number_format($ln["ARS_VALORCUSTAS"],2,",",".") : 0;
    }

    public function getTotalAtosManutencao($numProtocolo){
        $sql = "SELECT COUNT(RECNO) AS qtde FROM arc_seq_reg WHERE ARS_PROTOCOLO = :ARS_PROTOCOLO ";
        $qry = $this->conn->prepare($sql);
        $qry->bindParam(":ARS_PROTOCOLO", $numProtocolo);
        $qry->execute();
        if($qry->rowCount() > 0){
           $ln = $qry->fetch();
            return $ln['qtde'];
        }
        return 1;
    }

    public function valorTotalManutencao($numProtocolo){
        $usuCodigo = $this->getUsuCodigo();
        $usuSessao = $this->getUsuSessao();

        $sql = "SELECT SUM(ARS_VALORCUSTAS) AS ARS_VALORCUSTAS FROM arc_seq_reg WHERE ARS_PROTOCOLO = :ARS_PROTOCOLO ";
        $res = $this->conn->prepare($sql);
        $res->bindParam(":ARS_PROTOCOLO", $numProtocolo);
        $res->execute();
        $ln = $res->fetch();
        return ($ln["ARS_VALORCUSTAS"] != 0) ? number_format($ln["ARS_VALORCUSTAS"],2,",",".") : 0;
    }

		public function gerarProtocolo($tipo){
				switch ($tipo) {
					case 'TD':
						$campo = "PRENOTATD";
						break;
					case 'PJ':
						$campo = "PRENOTAPJ";
						break;
					case 'CE':
						$campo = "CERTIFICADO";
						break;
				}
        $usuCodigo = $this->getUsuCodigo();
        $usuSessao = $this->getUsuSessao();

        $sql = "UPDATE sequencia SET {$campo} = {$campo} + 1 WHERE 1 = 1";
        $qryUpdate = $this->conn->prepare($sql);
        $qryUpdate->execute();

				$sql = "SELECT {$campo} FROM sequencia";

				$qryDados = $this->conn->prepare($sql);
        $qryDados->execute();
        $ln = $qryDados->fetch();
        return $ln[$campo];

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

    public function getArcSeq($recno){
        $sql = "SELECT ARS_SEQ FROM arc_seq_reg_temp WHERE RECNO = :RECNO ";
        $res = $this->conn->prepare($sql);
        $res->bindParam(":RECNO", $recno);
        $res->execute();
        if($res->rowCount() > 0){
            $ln = $res->fetch();
            return $ln['ARS_SEQ'];
        }
        return '';
    }

    public function getArcSeqManutencao($recno){
        $sql = "SELECT ARS_SEQ FROM arc_seq_reg WHERE RECNO = :RECNO ";
        $res = $this->conn->prepare($sql);
        $res->bindParam(":RECNO", $recno);
        $res->execute();
        if($res->rowCount() > 0){
            $ln = $res->fetch();
            return $ln['ARS_SEQ'];
        }
        return '';
    }

    public function getNatCodigo(){
        $usuCodigo = $this->getUsuCodigo();
        $usuSessao = $this->getUsuSessao();

        $sql = "SELECT NAT_DESCCODIGO FROM arc_reg_temp WHERE USU_CODIGO = :USU_CODIGO AND USU_SESSAO = :USU_SESSAO ";
        $qry = $this->conn->prepare($sql);

        $qry->bindParam(":USU_CODIGO", $usuCodigo);
        $qry->bindParam(":USU_SESSAO", $usuSessao);
        $qry->execute();
        if($qry->rowCount() > 0){
            $ln = $qry->fetch();
            return $ln['NAT_DESCCODIGO'];
        }else{
            return '';
        }
    }

    function inserirHistorico($ws, $modulo, $acao, $options = ''){


        $result = array(
            'HLO_DATA'    => date('Y-m-d'),
            'HLO_HORA'    => date('H:i:s'),
            'USU_CODIGO'  => $this->getUsuCodigo(),
            'HLO_USUARIO' => strtoupper($this->getUsuLogin()),
            'HLO_MODULO'  => $modulo,
            'HLO_ACAO'    => $acao,
            'HLO_IP'      => $_SERVER["REMOTE_ADDR"],
            'HLO_PROTOCOLO' => $this->getProtocolo()
        );

        if($options != ""){
            $result = array_merge($result, $options);
        }

        $dados = $this->addDados($result);
        $res = $ws->inserirRegistro( getToken( $this->conn->db() ), 'historico_log', $dados['campos'], $dados['dados']);
        if($res != ""){
            return $res;
        }
    }

    public function limparTemp($ws, $nomeTabela){

        $res = $ws->deletarRegistro( getToken( $this->conn->db() ), $nomeTabela, " USU_CODIGO =  ".$this->getUsuCodigo()." AND USU_SESSAO = '".$this->getUsuSessao()."' " );
        return $res;
    }

    public function url_exists($url) {

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($code == 200); // verifica se recebe "status OK"
    }

    public function limparDadosAntigos($ws){
        $res = $ws->deletarRegistro( getToken( $this->conn->db() ), 'arc_seq_reg_temp',  " USU_CODIGO =  ".$this->getUsuCodigo()." AND ARS_RECEPCAO   < '".date('Y-m-d')."' " );
        $res = $ws->deletarRegistro( getToken( $this->conn->db() ), 'arc_reg_temp',      " USU_CODIGO =  ".$this->getUsuCodigo()." AND ARS_RECEPCAO   < '".date('Y-m-d')."' " );
        $res = $ws->deletarRegistro( getToken( $this->conn->db() ), 'indice_real_reg_temp',  " USU_CODIGO =  ".$this->getUsuCodigo()." AND ARS_DTRECEPCAO < '".date('Y-m-d')."' " );
        $res = $ws->deletarRegistro( getToken( $this->conn->db() ), 'indice_pessoal_reg_temp',   " USU_CODIGO =  ".$this->getUsuCodigo()." AND ARS_RECEPCAO   < '".date('Y-m-d')."' " );
        $res = $ws->deletarRegistro( getToken( $this->conn->db() ), 'calculo_reg_temp',      " USU_CODIGO =  ".$this->getUsuCodigo()." AND ARS_RECEPCAO   < '".date('Y-m-d')."' " );

        $res = $ws->deletarRegistro( getToken( $this->conn->db() ), 'recepcao_checklist_tmp',    " USU_CODIGO =  ".$this->getUsuCodigo()." AND USU_SESSAO <> '".$this->getUsuSessao()."' " );
        $res = $ws->deletarRegistro( getToken( $this->conn->db() ), 'recepcao_informacoes_tmp',  " USU_CODIGO =  ".$this->getUsuCodigo()." AND USU_SESSAO <> '".$this->getUsuSessao()."' " );
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

    public static function queryGetProtocolo($iMatricula){
        $sqlGetProtocolo = "SELECT ase.ARS_PROTOCOLO, ase.ARS_RECEPCAO
                            FROM arc_seq_reg ase
                            WHERE ase.ARS_NUMLIVRO = ".$iMatricula." AND ARS_ATO = 'REGISTRO' AND ARS_CODPOSICAO = 4
                            ORDER BY ARS_DTPOSICAO DESC, ARS_HRPOSICAO DESC LIMIT 1";
        return $sqlGetProtocolo;
    }

    public static function queryGetDadosIndicePessoal($indicadorProtocolo, $indicadorDtRecepcao, $sCampos){
        $sqlDados = "SELECT ".$sCampos."
                           FROM indice_pessoal_reg idp
                           LEFT JOIN pessoas pes ON ( pes.PES_CODIGO = idp.PES_CODIGO )
                           LEFT JOIN pessoas_dados psd ON ( pes.PES_CODIGO = psd.PES_CODIGO )
                           WHERE   idp.ARS_PROTOCOLO = '".$indicadorProtocolo."' AND idp.ARS_RECEPCAO = '".$indicadorDtRecepcao."'
                           GROUP BY ".$sCampos;
        return $sqlDados;
    }

    public function limparTabelasTemporarias(){

        $resp = '';
        $resp .= $this->limparTemp($this->ws , 'arc_seq_reg_temp');
        $resp .= $this->limparTemp($this->ws , 'arc_reg_temp');
        $resp .= $this->limparTemp($this->ws , 'indice_real_reg_temp');
        $resp .= $this->limparTemp($this->ws , 'indice_pessoal_reg_temp');
        $resp .= $this->limparTemp($this->ws , 'calculo_reg_temp');
        $resp .= $this->limparTemp($this->ws , 'recepcao_checklist_tmp');
        $resp .= $this->limparTemp($this->ws , 'recepcao_informacoes_tmp');



        return $resp;

    }

    public function listQualificacoes( $natCodigo ){

        $sql = "SELECT QUD_DESCRICAO FROM qualificatit_dados WHERE QUD_CODNAT = :QUD_CODNAT
                ORDER BY QUD_SEQUENCIA ASC";
        $qry = $this->conn->prepare($sql);
        $qry->bindParam(":QUD_CODNAT", $natCodigo);
        $qry->execute();
        $resp = ['html'=>""];
        if($qry->rowCount() > 0 ){
            $row  = 0;
            $resQuery = $qry->fetchAll();
            foreach($resQuery as $res){
                $row++;
                $res['QUD_DESCRICAO'] = ($res["QUD_DESCRICAO"]);

                $valueDescricao = str_replace("<-CHECK->", "", str_replace("<-TEXTO->" , "" , $res["QUD_DESCRICAO"]));

                $res["QUD_DESCRICAO"] = str_replace("<-CHECK->", "<input type=\"checkbox\" class=\"inp-qualificacao filled-in\" style='margin-right:5px' name=\"checkbox".$row."\" id=\"checkbox".$row."\" value=\"".$valueDescricao."\" >", $res["QUD_DESCRICAO"]);
                $res["QUD_DESCRICAO"] = str_replace("<-TEXTO->", "<input type=\"text\" class=\"margin5 inp-qualificacao\" style='min-width:300px' name=\"txt".$row."\" value=\"".$valueDescricao."\" >", $res["QUD_DESCRICAO"]);
                $resp["html"] .= "
                        <p class=\"div-qualificacao\" data-row=".$row." >
                          ".str_replace($valueDescricao,"",$res["QUD_DESCRICAO"])."
                          <label for=\"checkbox".$row."\" data-id=\"for$row\" style='color:#b20000'>$valueDescricao</label>
                        </p>
                ";
            }
            $resp['rows'] = $qry->rowCount();

        }else{
            $resp['rows'] = 0;
             $resp['html'] .= "
                <div class=\"row\" style='height:30px;line-height:30px; font-size:16px; font-weight:bold;'>
                    <div class=\"col-md-12\" >
                        Nenhum resultado encontrado
                    </div>
                </div>";
        }

    }

    public function inserirRestricoes(array $vRestricoes){

        $usuCodigo = $this->getUsuCodigo();
        $usuSessao = $this->getUsuSessao();

        $resp = ["error"=>"", "restricoes"=>$vRestricoes];
        $restricoesTemporarias = [];
        $sqlVerificarContraditorioTemp = " SELECT INF_DESCRICAO
                                           FROM recepcao_informacoes_tmp
                                           WHERE USU_CODIGO = :USU_CODIGO AND USU_SESSAO = :USU_SESSAO
                                           ORDER BY INF_DESCRICAO ";
        $qryContraditorioTemp = $this->conn->prepare($sqlVerificarContraditorioTemp);
        $qryContraditorioTemp->bindParam(":USU_CODIGO",   $usuCodigo );
        $qryContraditorioTemp->bindParam(":USU_SESSAO",   $usuSessao );
        $qryContraditorioTemp->execute();

        if($qryContraditorioTemp->rowCount() > 0){
            $resQuery = $qryContraditorioTemp->fetchAll();
            foreach ($resQuery as $row) {
                $restricoesTemporarias[] = $row["INF_DESCRICAO"];
            }
        }

        $resultRecepcao = [];
        foreach ($vRestricoes as $key => $restricoes) {
            $search = array_search( ($restricoes["message"]), $restricoesTemporarias);
            if( !is_numeric( $search ) ){
                $infoRecepcao = $this->addDados(array(
                    'USU_CODIGO'              => $usuCodigo,
                    'USU_SESSAO'              => $usuSessao,
                    'INF_DESCRICAO'           => $restricoes["message"],
                    'INF_RESTRICAO'           => 1,
                    'INF_PROTOCOLO_RESTRITO'  => $restricoes["ARS_PROTOCOLO"],
                ));
                $res = $this->ws->inserirRegistro(getToken( $this->conn->db() ), 'recepcao_informacoes_tmp', $infoRecepcao['campos'], $infoRecepcao['dados']);
                $resp["error"] .= ($res != "") ? "Informacoes:". $res .";" : "";
            }
        }
        return $resp;

    }

    public function inserirRestricoesManutencao($protocolo, $seqProtocolo){
        $resp['error'] = "";
        $res = $this->ws->deletarRegistro( getToken( $this->conn->db() ), 'recepcao_informacoes', " ARS_PROTOCOLO = '".$protocolo."' ");
        $resp["error"] .= ($res != "") ? "Excluir Informacoes do Checklist:". $res .";" : "";

        $sqlSeq = " SELECT INF_DESCRICAO, INF_CHECKLIST, ARS_NUMLIVRO, INF_RESTRICAO, INF_PROTOCOLO_RESTRITO, INF_NUMATO FROM recepcao_informacoes_tmp
                    WHERE USU_CODIGO = '".$this->getUsuCodigo()."' AND USU_SESSAO = '".$this->getUsuSessao()."' ";
        $qry = $this->conn->prepare($sqlSeq);
        $qry->execute();
        if($qry->rowCount() > 0){
            $resQuery = $qry->fetchAll();

            foreach ($resQuery as $res) {
                $numAto = ($res['INF_NUMATO'] == "") ? $seqProtocolo : $res['INF_NUMATO'];
                $infCodigo = $this->sys->gera_codigo('recepcao_informacoes');
                $dadosInformacoes = $this->addDados(array(
                    'ARS_DTRECEPCAO'          => $this->getDate(),
                    'ARS_PROTOCOLO'           => $protocolo,
                    'INF_CODIGO'              => $infCodigo,
                    'INF_DESCRICAO'           => $res["INF_DESCRICAO"],
                    'INF_CHECKLIST'           => $res["INF_CHECKLIST"],
                    'ARS_NUMLIVRO'            => $res["ARS_NUMLIVRO"],
                    'INF_RESTRICAO'           => $res["INF_RESTRICAO"],
                    'INF_PROTOCOLO_RESTRITO'  => $res["INF_PROTOCOLO_RESTRITO"],
                    'INF_NUMATO'              => $numAto,
                ));

                $res = $this->ws->inserirRegistro( getToken( $this->conn->db() ), 'recepcao_informacoes', $dadosInformacoes['campos'], $dadosInformacoes['dados']);
                $resp["error"] .= ($res != "") ? "Real:". $res .";" : "";

            }
        }

        $res = $this->ws->deletarRegistro( getToken( $this->conn->db() ), 'recepcao_informacoes_tmp', " USU_CODIGO = '".$this->getUsuCodigo()."' AND USU_SESSAO = '".$this->getUsuSessao()."' ");

        $sqlSeq = " SELECT * FROM recepcao_informacoes
                    WHERE ARS_PROTOCOLO = :NUM_PROTOCOLO ";
        $qry = $this->conn->prepare($sqlSeq);
        $qry->bindParam( ":NUM_PROTOCOLO" , $protocolo );
        $qry->execute();
        if($qry->rowCount() > 0){
            $resQuery = $qry->fetchAll();

            foreach ($resQuery as $res) {
                $dadosInformacoes = $this->addDados(array(
                    'INF_CODIGO'             => $res["INF_CODIGO"],
                    'INF_DESCRICAO'          => $res["INF_DESCRICAO"],
                    'INF_CHECKLIST'          => $res["INF_CHECKLIST"],
                    'ARS_NUMLIVRO'           => $res["ARS_NUMLIVRO"],
                    "USU_CODIGO"             => $this->getUsuCodigo(),
                    "USU_SESSAO"             => $this->getUsuSessao(),
                    "INF_TEMP_EXPORTADO"     => 1,
                    "INF_RESTRICAO"          => $res["INF_RESTRICAO"],
                    "INF_PROTOCOLO_RESTRITO" => $res["INF_PROTOCOLO_RESTRITO"],
                    "INF_NUMATO"             => $res["INF_NUMATO"],
                ));

                $resWS = $this->ws->inserirRegistro( getToken( $this->conn->db() ), 'recepcao_informacoes_tmp', $dadosInformacoes['campos'], $dadosInformacoes['dados']);
                $resp["error"] .= ($resWS != "") ? "Real:". $resWS .";" : "";

            }
        }

        return $resp["error"];
    }

    public function listRestricoesTemp($showBtn = false){

        if ($showBtn == true) {
           $btn = "<th><a class=\"btn-floating btn-small right waves-effect waves-light background btn-nova-info\" onclick=\"modalInfo()\" ><i class=\"material-icons\">add</i></a></th>";
        }else{
            $btn = "";
        }

        $resp = "<thead>
                    <tr>
                        <th>Informações</th>
                        $btn
                    </tr>
                 </thead>
                 <tbody>";

        $sql = "SELECT INF_DESCRICAO, INF_RESTRICAO FROM recepcao_informacoes_tmp WHERE USU_CODIGO = '".$this->getUsuCodigo()."' AND USU_SESSAO = '".$this->getUsuSessao()."' ";
        $qry = $this->conn->prepare($sql);
        $qry->execute();
        $numRows = $qry->rowCount();
        if($numRows > 0){
            $res = $qry->fetch();
            $cor = ($res["INF_RESTRICAO"] == 1)? "red" : "black";
            $reticencias = "";
            $btnMais = "";
            // if ($numRows >= 1) {
                $btnMais = "<td class='right-align'><a class=\"waves-effect waves-light btn background\" id=\"btn-modal-info\" onclick=\"modalInformacoes()\">Detalhes</a></td>";
                $reticencias = '...';
            // }

            $resp .= "
                <tr class=\"info-td\" >
                    <td align='left' class='bold ".$cor."-text'> ".($res["INF_DESCRICAO"])."$reticencias</td>
                    ".$btnMais."
                </tr>";
        }

        if($qry->rowCount() == 0){
            $resp .= "
                <tr class=\"info-td\" >
                    <td align='left' >Não há informação neste registro até o presente momento.</td>
                </tr>
            ";
        }

        $resp .= "</tbody>";
        return $resp;
    }

    public function listRestricoesTempMod(){

        $resp = "<ul class=\"collection with-header no-pointer\">
                    <li class=\"collection-header transparent\"><h4>Informações</h4></li>";

        $sql = "SELECT INF_DESCRICAO, INF_RESTRICAO, INF_PROTOCOLO_RESTRITO FROM recepcao_informacoes_tmp WHERE USU_CODIGO = '".$this->getUsuCodigo()."' AND USU_SESSAO = '".$this->getUsuSessao()."' ";
        $qry = $this->conn->prepare($sql);
        $qry->execute();
        if($qry->rowCount() > 0){
            $resQuery = $qry->fetchAll();
            foreach ($resQuery as $res) {
                $red = ($res["INF_RESTRICAO"] == 1) ? "red-text": "";

                if($res["INF_PROTOCOLO_RESTRITO"] != "" && $res["INF_PROTOCOLO_RESTRITO"] != "0"){
                    $rowInfo = "<div class=\"col s10 ".$red."\"> ".($res["INF_DESCRICAO"])." </div>
                                <div class=\"col s2 center-align no-padding\">
                                    <a href=\"javascript: Route.open('./balcao/consulta/?protocolo=".$res["INF_PROTOCOLO_RESTRITO"]."');\" class=\"btn background\" style='padding:0 10px'><i class=\"material-icons left\">search</i>".$res["INF_PROTOCOLO_RESTRITO"]."</a>
                                </div>";
                }else{
                    $rowInfo = "<div class=\"col s12 ".$red."\"> ".($res["INF_DESCRICAO"])." </div>";
                }

                $resp .= "
                    <li class=\"collection-item bold \">
                        <div class=\"row no-bottom\">
                            $rowInfo
                        </div>
                    </li>
                ";
            }
        }

        if($qry->rowCount() == 0){
            $resp .= "
               <li class=\"collection-item\">Não há restrições nesse registro</li>
            ";
        }

        $resp .= "</ul>";
        return $resp;
    }

    public function listRestricoes($protocolo = ''){

        $numRows     = 0;
        $reticencias = $btn = '';
        $resp = "<thead>
                    <tr>
                        <th>Informações</th>
                    </tr>
                 </thead>
                 <tbody>";

        if ($protocolo != '') {
            $sql = "SELECT INF_DESCRICAO, INF_RESTRICAO FROM recepcao_informacoes WHERE ARS_PROTOCOLO = :PROTOCOLO";
            $qry = $this->conn->prepare($sql);
            $qry->bindParam(':PROTOCOLO', $protocolo);
            $qry->execute();
            $numRows = $qry->rowCount();
            if($numRows > 0){
                $res = $qry->fetch();
                $cor = ($res["INF_RESTRICAO"] == 1)? "red" : "black";
                // if ($numRows > 1) {
                    $btn = "<td class='right-align'><a class=\"waves-effect waves-light btn background\" id=\"btn-modal-info\" onclick=\"modalInformacoes()\">Detalhes</a></td>";
                    $reticencias = '...';
                // }
                $resp .= "
                    <tr class=\"info-td\" >
                        <td align='left' class='bold ".$cor."-text'> ".($res["INF_DESCRICAO"])."$reticencias</td>
                        ".$btn."
                    </tr>";
            }
        }

        if($numRows == 0){
            $resp .= "
                <tr class=\"info-td\" >
                    <td align='left' >Não há informações neste registro</td>
                </tr>
            ";
        }

        $resp .= "</tbody>";
        return $resp;
    }

    public function listModalRestricoes($protocolo = '', $restringir = false){
        $numRows = 0;
        $resp = "<ul class=\"collection with-header no-pointer\">
                    <li class=\"collection-header\"><h4>Informações</h4></li>";
        if ($protocolo != ''){
            $sqlRestricao = ($restringir === true)? 'AND INF_RESTRICAO = 1' : '';
            $sql = "SELECT INF_DESCRICAO, INF_RESTRICAO FROM recepcao_informacoes WHERE ARS_PROTOCOLO = :PROTOCOLO ".$sqlRestricao;
            $qry = $this->conn->prepare($sql);
            $qry->bindParam(':PROTOCOLO', $protocolo);
            $qry->execute();
            $numRows = $qry->rowCount();
            if($numRows > 0){
                $resQuery = $qry->fetchAll();
                foreach ($resQuery as $res) {
                    $cor = ($res["INF_RESTRICAO"] == 1)? "red" : "black";
                    $resp .= "<li class=\"collection-item bold ".$cor."-text\">".($res["INF_DESCRICAO"])."</li>";
                }
            }
        }

        if($numRows == 0){
            $resp .= "<li class=\"collection-item\">Não há restrições nesse registro</li>";
        }

        $resp .= "</ul>";
        return $resp;
    }

    public function corrigirArcRegTemp( $vDados ){

        $dadosArcReg = $this->addDados( $vDados );
        $res = $this->ws->corrigirRegistro( getToken( $this->conn->db() ),'arc_reg_temp',"USU_CODIGO = ".$this->getUsuCodigo()." AND USU_SESSAO = '".$this->getUsuSessao()."' ", $dadosArcReg['campos'], $dadosArcReg['dados']);
        return ($res != "") ? "Registro (arc_reg): ".$res.";" : "";
    }

    public function corrigirArcReg( $vDados, $protocolo ){

        $dadosArcReg = $this->addDados( $vDados );
        $res = $this->ws->corrigirRegistro( getToken( $this->conn->db() ),'arc_reg',"ARS_PROTOCOLO = ".$protocolo, $dadosArcReg['campos'], $dadosArcReg['dados']);
        return ($res != "") ? "Registro (arc_reg): ".$res.";" : "";
    }

    public function corrigirArcSeqTemp( $vDados, $where ){

        $dadosArcReg = $this->addDados( $vDados );
        $res = $this->ws->corrigirRegistro( getToken( $this->conn->db() ), 'arc_seq_reg_temp',  $where, $dadosArcReg['campos'], $dadosArcReg['dados']);
        return ($res != "") ? "Registro (arc_seq): ".$res.";" : "";
    }

    public function corrigirArcSeq( $vDados, $where ){

        $dadosArcReg = $this->addDados( $vDados );
        $res = $this->ws->corrigirRegistro( getToken( $this->conn->db() ), 'arc_seq_reg',  $where, $dadosArcReg['campos'], $dadosArcReg['dados']);
        return ($res != "") ? "Registro (arc_seq): ".$res.";" : "";
    }

    public function corrigirCalculoTemp( $vDados, $where ){

        $dadosArcReg = $this->addDados( $vDados );
        $res = $this->ws->corrigirRegistro( getToken( $this->conn->db() ), 'calculo_reg_temp',  $where, $dadosArcReg['campos'], $dadosArcReg['dados']);
        return ($res != "") ? "Registro (calculo_reg): ".$res.";" : "";
    }

    public function corrigirCalculo( $vDados, $where ){

        $dadosArcReg = $this->addDados( $vDados );
        $res = $this->ws->corrigirRegistro( getToken( $this->conn->db() ), 'calculo_reg',  $where, $dadosArcReg['campos'], $dadosArcReg['dados']);
        return ($res != "") ? "Registro (calculo_reg): ".$res.";" : "";
    }

    public function inserirInformacoesExaTmp($prenota, $descricao){

        $usuCodigo = $this->getUsuCodigo();
        $usuSessao = $this->getUsuSessao();

        $resp = ["error"=>""];

        $sqlInfo = " SELECT INF_DESCRICAO
                     FROM recepcao_informacoes_tmp
                     WHERE INF_DESCRICAO = :INF_DESCRICAO AND USU_CODIGO = :USU_CODIGO AND USU_SESSAO = :USU_SESSAO";
        $qryInfo = $this->conn->prepare($sqlInfo);
        $qryInfo->bindParam(":INF_DESCRICAO",$descricao);
        $qryInfo->bindParam(":USU_CODIGO",   $usuCodigo );
        $qryInfo->bindParam(":USU_SESSAO",   $usuSessao );
        $qryInfo->execute();
        if($qryInfo->rowCount() == 0){
            $infCodigo    = $this->sys->gera_codigo('recepcao_informacoes_tmp');
            $infoRecepcao = $this->addDados(array(
                'USU_CODIGO'     => $usuCodigo,
                'USU_SESSAO'     => $usuSessao,
                'INF_DESCRICAO'  => $descricao,
                'INF_CODIGO'     => $infCodigo,
                'INF_RESTRICAO'  => 0
            ));
            $res = $this->ws->inserirRegistro(getToken( $this->conn->db() ), 'recepcao_informacoes_tmp', $infoRecepcao['campos'], $infoRecepcao['dados']);
        }
        return $resp;

    }

    public function inserirInformacoesExaManutencao($prenota, $descricao){

        $usuCodigo = $this->getUsuCodigo();
        $usuSessao = $this->getUsuSessao();
        $protocolo = $this->getProtocolo();

        $resp = ["error"=>""];

        $sqlInfo = " SELECT INF_DESCRICAO
                     FROM recepcao_informacoes
                     WHERE INF_DESCRICAO = :INF_DESCRICAO AND ARS_PROTOCOLO = :ARS_PROTOCOLO";
        $qryInfo = $this->conn->prepare($sqlInfo);
        $qryInfo->bindParam(":INF_DESCRICAO",$descricao);
        $qryInfo->bindParam(":ARS_PROTOCOLO",$protocolo);
        $qryInfo->execute();
        if($qryInfo->rowCount() == 0){
            $infCodigo    = $this->sys->gera_codigo('recepcao_informacoes');
            $infoRecepcao = $this->addDados(array(
                'ARS_PROTOCOLO'  => $protocolo,
                'INF_DESCRICAO'  => $descricao,
                'INF_CODIGO'     => $infCodigo,
                'INF_RESTRICAO'  => 0
            ));
            $res = $this->ws->inserirRegistro(getToken( $this->conn->db() ), 'recepcao_informacoes', $infoRecepcao['campos'], $infoRecepcao['dados']);
        }
        return $resp;

    }

    public function getQualificacaoDesc($quaCodigo)
    {
        $sSQL = "SELECT QUA_DESC
				 FROM qualifica
				 WHERE QUA_CODIGO = :QUA_CODIGO";
        $qry = $this->conn->prepare($sSQL);
        $qry->bindParam(':QUA_CODIGO', $quaCodigo);
        $qry->execute();
        $resp = $qry->fetch();
        return $resp['QUA_DESC'];
    }

}
