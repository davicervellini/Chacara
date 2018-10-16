<?php

class Restricao {
    use Funcoes;

	public $rec;
    public $conn;
    public $sys;
    private $numProtocolo;
    private $codPosicao            = "1, 2, 3, 5, 6, 8, 10, 11, 13, 18, 20, 22, 23";
    private $codPosicaoFinalizados = "4, 7, 9, 12";

	function __construct(){
		$this->rec  = new Recepcao;
        $this->sys  = new Sistema;
        $this->conn = new ConexaoMySQL;
	}

    private function restricaoPorMatricula( $iMatricula, $livro = 2, $temp = false ){

        if($iMatricula == '' || $livro == '')
            throw new Exception("Não foi possível consultar a restrição, número da matrícula ou tipo do livro vazios.", 99);

        $results = [];

        $sqlProtocolo = "";
        if($this->numProtocolo != "")
            $sqlProtocolo = " AND ase.REG_PRENOTA NOT IN (".$this->numProtocolo.") ";

        if($temp == true){
            $sql = "    SELECT ase.REG_PRENOTA, ase.ARS_RECEPCAO
                        FROM arc_seq_reg ase
                        LEFT JOIN registro_informacoes_tmp rec ON (rec.ARS_NUMLIVRO = ase.ARS_NUMLIVRO AND rec.USU_CODIGO = '".$this->rec->getUsuCodigo()."' AND rec.USU_SESSAO = '".$this->rec->getUsuSessao()."')
                        WHERE ase.ARS_NUMLIVRO = :iMatricula
                            AND ase.ARS_LIVRO  = :iLivro AND rec.ARS_NUMLIVRO is NULL
                            AND ARS_CODPOSICAO NOT IN ( ".$this->codPosicaoFinalizados." )
                            ".$sqlProtocolo."
                        ORDER BY ARS_DTPOSICAO DESC, ARS_HRPOSICAO DESC";
        }else{
            $sql = "    SELECT ase.REG_PRENOTA, ase.ARS_RECEPCAO
                        FROM arc_seq_reg ase
                        WHERE ase.ARS_NUMLIVRO = :iMatricula
                            AND ase.ARS_LIVRO  = :iLivro
                            AND ARS_CODPOSICAO NOT IN ( ".$this->codPosicaoFinalizados." )
                        ORDER BY ARS_DTPOSICAO DESC, ARS_HRPOSICAO DESC";
        }

        $qry = $this->conn->prepare($sql);
        $qry->bindParam(':iMatricula', $iMatricula);
        $qry->bindParam(':iLivro',     $livro);
        $qry->execute();
        if($qry->rowCount() > 0){
            $resQuery = $qry->fetchAll();
            foreach ($resQuery as $row) {
                $row["message"] = "Restrição da matrícula $iMatricula no protocolo ".$row["REG_PRENOTA"]." recepcionado no dia ".$this->sys->padroniza_datas_BR($row["ARS_RECEPCAO"])." ";
                $results[] = $row;
            }
        }
        return $results;
    }

    /**
    *  @param array $params
    *               $params["Nome"]
    *               $params["Documento"]
    */
    private function restricaoDeMatriculaPorIndicadores( array $params, $livro = 2){

        if($livro == '')
            throw new Exception("Não foi possível consultar a restrição, número da matrícula ou tipo do livro vazios.", 99);

        $results = [];
        $sNome      = $params["Nome"];
        $sDocumento = $params["Documento"];

        $sqlProtocolo = "";
        if($this->numProtocolo != "")
            $sqlProtocolo = " AND ase.REG_PRENOTA NOT IN (".$this->numProtocolo.") ";

        $whereDoc = [];
        if($sDocumento != "")
            $whereDoc[] = " OR (pes.PES_DOCUMENTO = '$sDocumento') ";

        if($sNome != "")
            $whereDoc[] = " OR (pes.PES_NOME = '$sNome')  ";

        $sql = "SELECT ase.ARS_NUMLIVRO,ase.REG_PRENOTA, ase.ARS_RECEPCAO,idp.REG_PRENOTA AS pessoaProtocolo, pes.PES_DOCUMENTO
                FROM arc_seq_reg ase
                INNER JOIN indice_pessoal_reg idp ON (idp.REG_PRENOTA = ase.REG_PRENOTA)
                INNER JOIN pessoas pes ON (pes.PES_CODIGO = idp.PES_CODIGO)
                WHERE   (
                            (pes.PES_NOME = '$sNome' AND pes.PES_DOCUMENTO = '$sDocumento')
                            ". join( " ", $whereDoc ) ."
                        )
                    AND ARS_CODPOSICAO NOT IN (".$this->codPosicaoFinalizados.")
                GROUP BY ase.ARS_NUMLIVRO, ase.REG_PRENOTA, ase.ARS_RECEPCAO, idp.REG_PRENOTA, pes.PES_DOCUMENTO ";
        $qry = $this->conn->prepare($sql);
        $qry->execute();
        if($qry->rowCount() > 0){
            $resQuery = $qry->fetchAll();
            foreach ($resQuery as $row) {
                $row["message"] = "Restrição da matrícula ".$row["ARS_NUMLIVRO"]." no protocolo ".$row["REG_PRENOTA"]." recepcionado no dia ".$this->sys->padroniza_datas_BR($row["ARS_RECEPCAO"])." ";
                $results[] = $row;
            }
        }
        return $results;
    }

    /**
    *  @param array $params
    *               $params["Logradouro"]
    *               $params["Numero"]
    *               $params["Contribuinte"]
    *               $params["cadMunicipal"]
    */
    private function restricaoPorIndicadorReal( array $params ){

        if( count($params) == 0)
            throw new Exception("Não foi possível consultar a restrição, nenhum parâmetro encontrado", 1);

        $results = [];
        $where = [];
        $binds = [];
        if(isset($params["cadMunicipal"])){

            $where[] = " AND (idr.IDR_INSC_MUNICIPAL = ?) ";
            $binds[] = $params["cadMunicipal"];

        }else{

            $where[] = "
                AND (idr.IDR_LOGRADOURO = ? AND idr.IDR_NUMLOGRADOURO = ?)
                OR idr.IDR_COMPL_CONTRIBUINTE = ? ";
            $binds[] = $params["Logradouro"];
            $binds[] = $params["Numero"];
            $binds[] = $params["Contribuinte"];
        }

        $sql = "
            SELECT  ars.REG_PRENOTA,
                    ars.ARS_RECEPCAO,
                    idr.IDR_LOGRADOURO,
                    idr.IDR_NUMLOGRADOURO,
                    idr.IDR_COMPL_CONTRIBUINTE
            FROM indice_real_reg idr
            INNER JOIN arc_seq_reg ars ON (ars.REG_PRENOTA = idr.REG_PRENOTA AND ars.ARS_RECEPCAO = idr.ARS_DTRECEPCAO AND ars.ARS_SEQ = idr.ARS_SEQ)
            WHERE ars.ARS_CODPOSICAO NOT IN ( ".$this->codPosicaoFinalizados." ) ".join("", $where)." ";
        $qry = $this->conn->prepare($sql);
        $qry->execute($binds);
        if($qry->rowCount() > 0){
            $resQuery = $qry->fetchAll();
            foreach ($resQuery as $row) {
                $row["message"] = "Restrição do indicador real ".$row["IDR_LOGRADOURO"].",".$row["IDR_NUMLOGRADOURO"]." no protocolo ".$row["REG_PRENOTA"]." recepcionado no dia ".$this->sys->padroniza_datas_BR($row["ARS_RECEPCAO"]);
                $results[] = $row;
            }
        }
        return $results;
    }

    /**
    *  @param array $params
    *               $params["Nome"]
    *               $params["Documento"]
    */
    private function restricaoPorIndicadorPessoal( array $params ){

        if( count($params) == 0)
            throw new Exception("Não foi possível consultar a restrição, nenhum parâmetro encontrado", 1);

        $results  = [];

        $sNome      = $params["Nome"];
        $sDocumento = $params["Documento"];

        $sql = "
            SELECT ipe.IPE_NOME             AS NOME,
                   ipe.IPE_DOCUMENTO          AS CPFCNPJ ,
                   mov.MOV_NUMERODOPROCESSO AS NUMERODOPROCESSO
            FROM indisp_partes ipa
            INNER JOIN indisp_pessoas ipe   ON (ipa.IPE_CODIGO = ipe.IPE_CODIGO)
            INNER JOIN indisp_movimento mov ON (ipa.IPA_ID_INDISP = mov.RECNO)
            WHERE 1 = 1
            AND (ipa.IPA_CANCELADO = 0)
            AND (
                (ipe.IPE_NOME = '$sNome' AND ipe.IPE_DOCUMENTO = '$sDocumento') OR
                (ipe.IPE_NOME = '$sNome') OR
                (ipe.IPE_DOCUMENTO = '$sDocumento')
            ) GROUP BY ipe.IPE_NOME, ipe.IPE_DOCUMENTO, mov.MOV_NUMERODOPROCESSO";

        $qry = $this->conn->prepare($sql);
        $qry->execute();
        if($qry->rowCount() > 0){
            $resQuery = $qry->fetchAll();
            foreach ($resQuery as $resIndisp) {
                $result = array();
                $result["REG_PRENOTA"] = '';
                $result["ARS_RECEPCAO"]  = '';
                $result["message"]       = "Restrição na indisponibilidade de bens. Número do Processo: ".$resIndisp["NUMERODOPROCESSO"].". ".$resIndisp["NOME"].". Doc: ".$resIndisp["CPFCNPJ"]."  ";
                $result["chave"]         = 1;
                $result["numProcesso"]   = $resIndisp["NUMERODOPROCESSO"];
                $result["documento"]     = $resIndisp["CPFCNPJ"];
                $results[] = $result;
            }
        }
        return $results;
    }

    public function consultarRestricaoTemporario($iMatricula, $livro = 2, $numProtocolo = ''){

        $this->numProtocolo = $numProtocolo;
        $response = $this->restricaoPorMatricula( $iMatricula, $livro, true);
        return $response;
    }

    public function consultarRestricao($iMatricula = "", $documento = "", $nome = "", $cadMunicipal = "", $livro = 2){
        $responseMatricula = [];
        $responsePessoal   = [];
        $responseReal      = [];

        if($iMatricula != ""){
            $responseMatricula = $this->restricaoPorMatricula($iMatricula, $livro);
        }

        if($documento != "" || $nome != ""){
            $responsePessoal = $this->restricaoPorIndicadorPessoal([ "Nome"=> $nome,
                                                                             "Documento" => $documento]);
        }

        if($cadMunicipal != ""){
            $responseReal = $this->restricaoPorIndicadorReal([
                                                                "cadMunicipal" => $cadMunicipal
                                                             ]);
        }

        $response = array_merge($responseMatricula, $responsePessoal, $responseReal);
        return $response;
	}

    public function consultarRestricaoPorIndicadores($iMatricula = "", $documento = "", $nome = "", $cadMunicipal = "", $livro = 2){
        $responseMatricula          = [];
        $responsePessoalIndisp      = [];
        $responsePessoalIndicadores = [];
        $responseReal               = [];

        if($iMatricula != ""){
            $responseMatricula = $this->restricaoPorMatricula($iMatricula, $livro);
        }

        if($documento != "" || $nome != ""){
            $responsePessoalIndisp = $this->restricaoPorIndicadorPessoal([
                                                                           "Nome"=> $nome,
                                                                           "Documento" => $documento
                                                                       ]);

            $responsePessoalIndicadores = $this->restricaoDeMatriculaPorIndicadores([ "Nome"=> $nome,
                                                                                      "Documento" => $documento]);
        }

        if($cadMunicipal != ""){
            $responseReal = $this->restricaoPorIndicadorReal([
                                                                "cadMunicipal" => $cadMunicipal
                                                             ]);
        }

        $response = array_merge($responseMatricula, $responsePessoalIndisp, $responseReal, $responsePessoalIndicadores);
        return $response;
    }

    public function consultarIndisponibilidade( array $params ){

        $response = $this->restricaoPorIndicadorPessoal( $params );
        return $response;
    }

    public function listRestricoes($protocolo = '', $tipoRecep = "", $exibirTemp = false, $mostrarDetalhes = false){
        $restricaoTotal= 0;
        $numRows       = 0;
        $reticencias   = $btn = '';
        $identificador = [];

        $resp = "<thead>
                    <tr>
                        <th>Informações</th>
                        <th><a class=\"btn-floating btn-small right waves-effect waves-light background btn-nova-info\" onclick=\"modalInfo(".$protocolo.")\" ><i class=\"material-icons\">add</i></a></th>
                    </tr>
                 </thead>
                 <tbody>";

        if ($protocolo != '') {

            if($exibirTemp === true){
                $identificador[] = ' UNION SELECT INF_DESCRICAO, INF_RESTRICAO FROM registro_informacoes_tmp WHERE REG_PRENOTA = :PROTOCOLO2 ';
            }

            $sql = "SELECT INF_DESCRICAO, INF_RESTRICAO
                    FROM registro_informacoes
                    WHERE REG_PRENOTA = :PROTOCOLO AND REG_TPDOCUMENTO = :REG_TPDOCUMENTO".join(" ", $identificador) . "
                    ORDER BY INF_DESCRICAO ASC";
            $qry = $this->conn->prepare($sql);
            $qry->bindParam(':PROTOCOLO', $protocolo);
            $qry->bindParam(':REG_TPDOCUMENTO', $tipoRecep);
            if($exibirTemp === true){
                $qry->bindParam(':PROTOCOLO2', $protocolo);
            }

            $qry->execute();
            $numRows = $qry->rowCount();
            if($numRows > 0){
                $res = $qry->fetch();
                $restricaoTotal = 0;
                // if ($numRows > 1 || $mostrarDetalhes == true) {
                    $btn = "<td class='right-align'><a class=\"waves-effect waves-light btn background\" id=\"btn-modal-info\" onclick=\"modalInformacoes()\">Detalhes</a></td>";
                    $reticencias = '...';
                // }
                $resp .= "
                    <tr class=\"info-td\" >
                        <td align='left' class='bold'> ".utf8_encode($res["INF_DESCRICAO"])."$reticencias</td>
                        ".$btn."
                    </tr>";
            }
        }

        if($numRows == 0){
            $restricaoTotal = 0;
            $resp .= "
                <tr class=\"info-td\" >
                    <td align='left' >Não há informações neste registro</td>
                </tr>
            ";
        }

        $resp .= "</tbody>";
        $resp .= "<input type='hidden' id='totalInfo' value='".$restricaoTotal."' ";
        return $resp;
    }

    public function listRestricoesExa($protocolo = '', $exibirTemp = false){

        $numRows       = 0;
        $reticencias   = $btn = '';
        $identificador = [];
        $resp = "<thead>
                    <tr>
                        <th>Informações</th>
                        <th><a class=\"btn-floating btn-small right waves-effect waves-light background btn-nova-info\" onclick=\"modalInfo(".$protocolo.")\" ><i class=\"material-icons\">add</i></a></th>
                    </tr>
                 </thead>
                 <tbody>";

        if ($protocolo != '') {

            if($exibirTemp === true){
                $identificador[] = 'UNION SELECT INF_DESCRICAO, INF_RESTRICAO FROM registro_informacoes_exa_tmp WHERE ASE_PROTOCOLO = :PROTOCOLO2 ';
            }

            $sql = "SELECT INF_DESCRICAO, INF_RESTRICAO
                    FROM registro_informacoes_exa
                    WHERE ASE_PROTOCOLO = :PROTOCOLO ".join(" ", $identificador). "
                    ORDER BY INF_RESTRICAO DESC, INF_DESCRICAO ASC ";
            $qry = $this->conn->prepare($sql);
            $qry->bindParam(':PROTOCOLO', $protocolo);
            if($exibirTemp === true){
                $qry->bindParam(':PROTOCOLO2', $protocolo);
            }
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

    public function listRestricoesChecklist( $protocolo ){
        if($protocolo != ""){
            $sql = "SELECT INF_DESCRICAO as Informacao
                    FROM registro_informacoes
                    WHERE REG_PRENOTA = :PROTOCOLO AND INF_CHECKLIST = 1";
            $qry = $this->conn->prepare($sql);
            $qry->bindParam("PROTOCOLO", $protocolo);
            $qry->execute();

            $resQuery = [];

            if($qry->rowCount() > 0)
                $resQuery = $qry->fetchAll();

            return $resQuery;
        }
    }

    public function listRestricoesChecklistExa( $protocolo ){
        if($protocolo != ""){
            $sql = "SELECT INF_DESCRICAO as Informacao
                    FROM registro_informacoes_exa
                    WHERE ASE_PROTOCOLO = :PROTOCOLO AND INF_CHECKLIST = 1";
            $qry = $this->conn->prepare($sql);
            $qry->bindParam("PROTOCOLO", $protocolo);
            $qry->execute();

            $resQuery = [];

            if($qry->rowCount() > 0)
                $resQuery = $qry->fetchAll();

            return $resQuery;
        }
    }

    public function listModalRestricoes($protocolo = '',$tipoRecep, $restringir = false, $exibirTemp = false, $checklist = false){

        $numRows = 0;
        $identificador = [];
        $sqlRestricao  = "";

        $resp = "<ul class=\"collection with-header no-pointer\">
                    <li class=\"collection-header transparent\"><h4>Informações</h4></li>";

        if ($protocolo != ''){

            if($restringir === true){
                $sqlRestricao    = ' AND INF_RESTRICAO = 1 ';
                $identificador[] = $sqlRestricao;
            }

            if($exibirTemp === true){
                $identificador[] = ' UNION SELECT INF_DESCRICAO, INF_RESTRICAO, INF_PROTOCOLO_RESTRITO FROM registro_informacoes_tmp WHERE REG_PRENOTA = :PROTOCOLO2 ';
            }

            $sql = "SELECT INF_DESCRICAO, INF_RESTRICAO, INF_PROTOCOLO_RESTRITO
                    FROM registro_informacoes
                    WHERE REG_PRENOTA = :PROTOCOLO AND REG_TPDOCUMENTO = :REG_TPDOCUMENTO ".join(" ", $identificador).$sqlRestricao. "
                    ORDER BY INF_RESTRICAO DESC, INF_DESCRICAO ASC";
            $qry = $this->conn->prepare($sql);
            $qry->bindParam(':PROTOCOLO', $protocolo);
            $qry->bindParam(':REG_TPDOCUMENTO', $tipoRecep);
            if($exibirTemp === true){
                $qry->bindParam(':PROTOCOLO2', $protocolo);
            }
            $qry->execute();
            $numRows = $qry->rowCount();
            if($numRows > 0){
                $resQuery = $qry->fetchAll();
                foreach ($resQuery as $res) {
                    $cor = ($res["INF_RESTRICAO"] == 1)? "red" : "black";


                    if($res["INF_PROTOCOLO_RESTRITO"] != "" && $res["INF_PROTOCOLO_RESTRITO"] != "0"){
                        $rowInfo = "<div class=\"col s10 ".$cor."-text\"> ".utf8_encode( $res["INF_DESCRICAO"] )." </div>
                                    <div class=\"col s2 center-align no-padding\">
                                        <a href=\"javascript: Route.open('./balcao/consulta/?protocolo=".$res["INF_PROTOCOLO_RESTRITO"]."');\" class=\"btn background\" style='padding:0 10px'><i class=\"material-icons left\">search</i>".$res["INF_PROTOCOLO_RESTRITO"]."</a>
                                    </div>";
                    }else{
                        $rowInfo = "<div class=\"col s12 ".$cor."-text\"> ".utf8_encode( $res["INF_DESCRICAO"] )." </div>";
                    }

                    $resp .= "  <li class=\"collection-item bold \">
                                    <div class=\"row no-bottom\">
                                        ".$rowInfo."
                                    </div>
                                </li>";
                }
            }
        }

        if($numRows == 0){
            $resp .= "<li class=\"collection-item\">Não há restrições nesse registro</li>";
        }

        $resp .= "</ul>";
        return $resp;
    }

    public function listModalRestricoesExa($protocolo = '', $restringir = false, $exibirTemp = false){
        $numRows = 0;
        $identificador = [];
        $sqlRestricao  = "";

        $resp = "<ul class=\"collection with-header no-pointer\">
                    <li class=\"collection-header transparent\"><h4>Informações</h4></li>";
        if ($protocolo != ''){

            if($restringir === true){
                $sqlRestricao    = ' AND INF_RESTRICAO = 1 ';
                $identificador[] = $sqlRestricao;
            }

            if($exibirTemp === true){
                $identificador[] = 'UNION SELECT INF_DESCRICAO, INF_RESTRICAO, INF_PROTOCOLO_RESTRITO FROM registro_informacoes_exa_tmp WHERE ASE_PROTOCOLO = :PROTOCOLO2 ';
            }

            $sql = "SELECT INF_DESCRICAO, INF_RESTRICAO, INF_PROTOCOLO_RESTRITO
                    FROM registro_informacoes_exa
                    WHERE ASE_PROTOCOLO = :PROTOCOLO ".join(" ", $identificador).$sqlRestricao;

            $qry = $this->conn->prepare($sql);
            $qry->bindParam(':PROTOCOLO', $protocolo);
            if($exibirTemp === true){
                $qry->bindParam(':PROTOCOLO2', $protocolo);
            }
            $qry->execute();
            $numRows = $qry->rowCount();
            if($numRows > 0){
                $resQuery = $qry->fetchAll();
                foreach ($resQuery as $res) {
                    $cor = ($res["INF_RESTRICAO"] == 1)? "red" : "black";

                    if($res["INF_PROTOCOLO_RESTRITO"] != "" && $res["INF_PROTOCOLO_RESTRITO"] != "0"){
                        $rowInfo = "<div class=\"col s10 ".$cor."-text\"> ".($res["INF_DESCRICAO"])." </div>
                                    <div class=\"col s2 center-align no-padding\">
                                        <a href=\"javascript: Route.open('./balcao/consulta/?protocolo=".$res["INF_PROTOCOLO_RESTRITO"]."');\" class=\"btn background\" style='padding:0 10px'><i class=\"material-icons left\">search</i>".$res["INF_PROTOCOLO_RESTRITO"]."</a>
                                    </div>";
                    }else{
                        $rowInfo = "<div class=\"col s12 ".$cor."-text\"> ".($res["INF_DESCRICAO"])." </div>";
                    }

                    $resp .= "  <li class=\"collection-item bold \">
                                    <div class=\"row no-bottom\">
                                        ".$rowInfo."
                                    </div>
                                </li>";
                }
            }
        }

        if($numRows == 0){
            $resp .= "<li class=\"collection-item\">Não há restrições nesse registro</li>";
        }

        $resp .= "</ul>";
        return $resp;
    }

    public function inserirInformacoes($ws, $prenota, $descricao){

        $resp = ["error"=>""];

        $sqlInfo = " SELECT INF_DESCRICAO
                     FROM registro_informacoes
                     WHERE INF_DESCRICAO = :INF_DESCRICAO AND REG_PRENOTA = :REG_PRENOTA AND REG_TPDOCUMENTO = :REG_TPDOCUMENTO";
        $qryInfo = $this->conn->prepare($sqlInfo);
        $qryInfo->bindParam(":INF_DESCRICAO",$descricao);
        $qryInfo->bindParam(":REG_PRENOTA",$prenota);
        $qryInfo->bindParam(":REG_TPDOCUMENTO",$tipoRecep);
        $qryInfo->execute();
        if($qryInfo->rowCount() == 0){
            $infCodigo    = $this->sys->gera_codigo('registro_informacoes');
            $infoRecepcao = $this->addDados(array(
                'INF_DESCRICAO'  => $descricao,
                'REG_PRENOTA'  => $prenota,
                'INF_CODIGO'     => $infCodigo,
                'INF_RESTRICAO'  => 0,
            ));
            $res = $ws->inserirRegistro(getToken( $this->conn->db() ), 'registro_informacoes', $infoRecepcao['campos'], $infoRecepcao['dados']);
        }
        return $resp;
    }

    public function inserirInformacoesExa($ws, $prenota, $descricao){

        $resp = ["error"=>""];

        $sqlInfo = " SELECT INF_DESCRICAO
                     FROM registro_informacoes_exa
                     WHERE INF_DESCRICAO = :INF_DESCRICAO AND ASE_PROTOCOLO = :ASE_PROTOCOLO";
        $qryInfo = $this->conn->prepare($sqlInfo);
        $qryInfo->bindParam(":INF_DESCRICAO",$descricao);
        $qryInfo->bindParam(":ASE_PROTOCOLO",$prenota);
        $qryInfo->execute();
        if($qryInfo->rowCount() == 0){
            $infCodigo    = $this->sys->gera_codigo('registro_informacoes_exa');
            $infoRecepcao = $this->addDados(array(
                'INF_DESCRICAO'  => $descricao,
                'ASE_PROTOCOLO'  => $prenota,
                'INF_CODIGO'     => $infCodigo,
                'INF_RESTRICAO'  => 0
            ));
            $res = $ws->inserirRegistro(getToken( $this->conn->db() ), 'registro_informacoes_exa', $infoRecepcao['campos'], $infoRecepcao['dados']);
        }
        return $resp;

    }

    public function listDetalhes($numProcesso, $numDocumento){
        $sql = "SELECT imo.MOV_PROTOCOLOINDISPONIBILIDADE, MOV_DATAPEDIDO, MOV_NOMEINSTITUICAO, MOV_FORUMVARA, MOV_USUARIO, MOV_EMAIL, MOV_PRENOTACAO
                FROM indisp_partes ipa
                INNER JOIN indisp_pessoas ipe   ON (ipa.IPE_CODIGO = ipe.IPE_CODIGO)
                INNER JOIN indisp_movimento imo ON (ipa.IPA_ID_INDISP = imo.RECNO)
                WHERE imo.MOV_NUMERODOPROCESSO = :NUMERODOPROCESSO and ipe.IPE_DOCUMENTO = :CPFCNPJ
                GROUP BY imo.MOV_PROTOCOLOINDISPONIBILIDADE, MOV_DATAPEDIDO, MOV_NOMEINSTITUICAO, MOV_FORUMVARA, MOV_USUARIO, MOV_EMAIL, MOV_PRENOTACAO";
        $qry = $this->conn->prepare($sql);
        $qry->bindParam(':NUMERODOPROCESSO', $numProcesso);
        $qry->bindParam(':CPFCNPJ', $numDocumento);
        $qry->execute();
        $detalhes = "";
        if($qry->rowCount()){
            foreach ($qry as $ln){
                $detalhes = "
                    <div class='row'>
                        <div class='col l12 center'>
                            <h5>Detalhes</h5>
                        </div>
                    </div>
                    <div class='row b-10'>
                        <div class='col l12'>
                            <b>N° Protocolo</b>
                            <p>".$ln['MOV_PROTOCOLOINDISPONIBILIDADE']."</p>
                        </div>
                    </div>
                    <div class='row b-10'>
                        <div class='col l6'>
                            <b>Data do Pedido</b>
                            <p>".$this->sys->padroniza_datas_BR($ln['MOV_DATAPEDIDO'])."</p>
                        </div>
                        <div class='col l6'>
                            <b>Nome da Instituição</b>
                            <p>".$ln['MOV_NOMEINSTITUICAO']."</p>
                        </div>
                    </div>
                    <div class='row b-10'>
                        <div class='col l6'>
                            <b>Vara</b>
                            <p>".$ln['MOV_FORUMVARA']."</p>
                        </div>
                        <div class='col l6'>
                            <b>Usuário</b>
                            <p>".$ln['MOV_USUARIO']."</p>
                        </div>
                    </div>
                    <div class='row b-10'>
                        <div class='col l6'>
                            <b>E-Mail</b>
                            <p>".$ln['MOV_EMAIL']."</p>
                        </div>
                        <div class='col l6'>
                            <b>N° Protocolo</b>
                            <p>".$ln['MOV_PRENOTACAO']."</p>
                        </div>
                    </div>
                ";
            }
        }else{
            $detalhes = "
                    <div class='row'>
                        <div class='col l12 center'>
                            <h5>Nenhum detalhe disponível.</h5>
                        </div>
                    </div>";
        }
        return $detalhes;
    }
}

?>
