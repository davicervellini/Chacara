<?php

require_once __DIR__ ."/GetterSetter.Class.php";
class Reentrada extends GetterSetter{

	public function __construct(){
        $this->conn = new ConexaoMySQL;
        $this->rec  = new Recepcao;
        $this->ws   = new ArcaTDPJ_WS;

        $this->rec->setUsuCodigo( $_SESSION["usuario"]["usuCodigo"] );
        $this->rec->setUsuNome(   $_SESSION["usuario"]["usuNome"]   );
        $this->rec->setUsuSessao(session_id());

    }

    public function carregarReentrada( $numProtocolo , $tempExportado = '1'){
        
        $resp = ["error"=>""];
        $usuCodigo = $this->rec->getUsuCodigo();
        $usuSessao = $this->rec->getUsuSessao();

        // Se ja tiver sido exportado, nao continua o processo.
        $sqlExportados = "SELECT RECNO 
                          FROM arc_reg_temp 
                          WHERE USU_CODIGO = :USU_CODIGO AND USU_SESSAO = :USU_SESSAO AND (ARG_TEMP_EXPORTADO = 1 OR ARG_TEMP_EXPORTADO = 2) ";
        $qryExportados = $this->conn->prepare($sqlExportados);
        $qryExportados->bindParam( ":USU_CODIGO", $usuCodigo );
        $qryExportados->bindParam( ":USU_SESSAO", $usuSessao );
        $qryExportados->execute();
        if($qryExportados->rowCount() > 0){
            return json_encode($resp);
        }


        $sqlReg = " SELECT ARS_RECEPCAO,ARS_PROTOCOLO,NAT_DESCCODIGO,NAT_DESCRICAO,APR_CODIGO,ARG_APRES_OBS,ARG_QTDEATOS,ARG_CODCALC,ARG_POSICAO,ARG_DATAPOSICAO,ARG_HORAPOSICAO,ARG_LIVRO_ORI,ARG_FOLHA_ORI,ARG_DATA_ORI,ARG_ORIGEM,ARG_DTPREVREG,ARG_VALORCUSTAS,ARG_VALORDEPOSITO, ARG_LIBERAMANUTENCAO, RECNO
                    FROM arc_reg
                    WHERE ARS_PROTOCOLO = :NUM_PROTOCOLO ";
        $qry = $this->conn->prepare($sqlReg);
        $qry->bindParam( ":NUM_PROTOCOLO" , $numProtocolo );
        $qry->execute();
        if($qry->rowCount() > 0){
            $res = $qry->fetch();
            $dadosArcReg = $this->rec->addDados(array(
            'ARS_RECEPCAO'           =>$res["ARS_RECEPCAO"],
            'ARS_PROTOCOLO'          =>$res["ARS_PROTOCOLO"],
            'NAT_DESCCODIGO'         =>$res["NAT_DESCCODIGO"],
            'NAT_DESCRICAO'          =>$res["NAT_DESCRICAO"],
            'APR_CODIGO'             =>$res["APR_CODIGO"],
            'ARG_APRES_OBS'          =>$res["ARG_APRES_OBS"],
            'ARG_QTDEATOS'           =>$res["ARG_QTDEATOS"],
            'ARG_CODCALC'            =>$res["ARG_CODCALC"],
            'ARG_POSICAO'            =>$res["ARG_POSICAO"],
            'ARG_DATAPOSICAO'        =>$res["ARG_DATAPOSICAO"],
            'ARG_HORAPOSICAO'        =>$res["ARG_HORAPOSICAO"],
            'ARG_LIVRO_ORI'          =>$res["ARG_LIVRO_ORI"],
            'ARG_FOLHA_ORI'          =>$res["ARG_FOLHA_ORI"],
            'ARG_DATA_ORI'           =>$res["ARG_DATA_ORI"],
            'ARG_ORIGEM'             =>$res["ARG_ORIGEM"],
            'ARG_DTPREVREG'          =>$res["ARG_DTPREVREG"],
            'ARG_VALORCUSTAS'        =>$res["ARG_VALORCUSTAS"],
            'ARG_VALORDEPOSITO'      =>$res["ARG_VALORDEPOSITO"],
            "USU_CODIGO"             =>$this->rec->getUsuCodigo(),
            "USU_SESSAO"             =>$this->rec->getUsuSessao(),
            "ARG_TEMP_EXPORTADO"     => $tempExportado,
            "ARG_LIBERAMANUTENCAO"   =>$res["ARG_LIBERAMANUTENCAO"],
            "ARG_RECNO_ORIGIN"       =>$res["RECNO"],
            ));
            $res = $this->ws->inserirRegistro( getToken( $this->conn->db() ), 'arc_reg_temp', $dadosArcReg['campos'], $dadosArcReg['dados']);
            $resp["error"] .= ($res != "") ? "Reentrada - arc Reg:". $res .";" : "";
        }else{
            $resp["error"] = "Protocolo não encontrado.";
            return json_encode($resp);
            exit;
        }


        $sqlSeq = "SELECT ARS_RECEPCAO, ARS_PROTOCOLO, ARS_SEQ, ARS_SEQLIVRO, ARS_ATO, ESP_CODIGO, ARS_ESPECIE_ATO, ARS_LIVRO, ARS_NUMLIVRO, ARS_LETRALIVRO, ARS_CODPOSICAO, ARS_DESCPOSICAO, ARS_DTPOSICAO, ARS_HRPOSICAO, ARS_DOI, ARS_ITBI, ARS_DTREGISTRO, ARS_VALORDECLARADO, ARS_VALORTRIBUTADO, ARS_NCERTIDOES, ARS_NVIAS, ARS_VALORCUSTAS, USU_NOME, RECNO
                    FROM arc_seq_reg
                    WHERE ARS_PROTOCOLO = :NUM_PROTOCOLO ";
        $qry = $this->conn->prepare($sqlSeq);
        $qry->bindParam( ":NUM_PROTOCOLO" , $numProtocolo );
        $qry->execute();
        
        if($qry->rowCount() > 0){
            $resQuery = $qry->fetchAll();
            foreach ($resQuery as $res) {

                $dtRecepcao = $res["ARS_RECEPCAO"];
                $sProtocolo = $res["ARS_PROTOCOLO"];
                $iSequencia = $res["ARS_SEQ"];

                $dadosArcSeq = $this->rec->addDados(array(
                'ARS_RECEPCAO'       =>$res["ARS_RECEPCAO"],
                'ARS_PROTOCOLO'      =>$res["ARS_PROTOCOLO"],
                'ARS_SEQ'            =>$res["ARS_SEQ"],
                'ARS_SEQLIVRO'       =>$res["ARS_SEQLIVRO"],
                'ARS_ATO'            =>$res["ARS_ATO"],
                'ESP_CODIGO'         =>$res["ESP_CODIGO"],
                'ARS_ESPECIE_ATO'    =>$res["ARS_ESPECIE_ATO"],
                'ARS_LIVRO'          =>$res["ARS_LIVRO"],
                'ARS_NUMLIVRO'       =>$res["ARS_NUMLIVRO"],
                'ARS_LETRALIVRO'     =>$res["ARS_LETRALIVRO"],
                'ARS_CODPOSICAO'     =>$res["ARS_CODPOSICAO"],
                'ARS_DESCPOSICAO'    =>utf8_encode( $res["ARS_DESCPOSICAO"] ),
                'ARS_DTPOSICAO'      =>$res["ARS_DTPOSICAO"],
                'ARS_HRPOSICAO'      =>$res["ARS_HRPOSICAO"],
                'ARS_DOI'            =>$res["ARS_DOI"],
                'ARS_ITBI'           =>$res["ARS_ITBI"],
                'ARS_DTREGISTRO'     =>$res["ARS_DTREGISTRO"],
                'ARS_VALORDECLARADO' =>$res["ARS_VALORDECLARADO"],
                'ARS_VALORTRIBUTADO' =>$res["ARS_VALORTRIBUTADO"],
                'ARS_NCERTIDOES'     =>$res["ARS_NCERTIDOES"],
                'ARS_NVIAS'          =>$res["ARS_NVIAS"],
                'ARS_VALORCUSTAS'    =>$res["ARS_VALORCUSTAS"],
                'USU_NOME'           =>$res["USU_NOME"],
                "USU_CODIGO"         =>$this->rec->getUsuCodigo(),
                "USU_SESSAO"         =>$this->rec->getUsuSessao(),
                "ARS_TEMP_EXPORTADO" => $tempExportado,
                "ARS_RECNO_ORIGIN"   => $res["RECNO"]
                ));
                $resWS = $this->ws->inserirRegistro( getToken( $this->conn->db() ), 'arc_seq_reg_temp', $dadosArcSeq['campos'], $dadosArcSeq['dados']);
                $resp["error"] .= ($resWS != "") ? "arc seq:". $resWS .";" : "";


                $sqlSeq = " SELECT RECNO, ARS_DTRECEPCAO,ARS_PROTOCOLO,ARS_SEQ,IDR_TIPO,IDR_NUMLIVRO,IDR_CEP,IDR_TPLOCALIZACAO,IDR_TPIMOVEL,IDR_TPLOGRADOURO,IDR_LOGRADOURO,IDR_NUMLOGRADOURO,IDR_BAIRRO,IDR_CIDADE,IDR_UF,IDR_COMPL_TORRE,IDR_COMPL_BLOCO,IDR_COMPL_ANDAR,IDR_COMPL_APTO,IDR_COMPL_SETOR,IDR_COMPL_CONDOMINIO,IDR_COMPL_UNIDADE,IDR_COMPL_TRAVESSA,IDR_COMPL_LOTEAMENTO,IDR_COMPL_QUADRA,IDR_COMPL_LOTE,IDR_COMPL_COMPLEMENTO,IDR_COMPL_CONTRIBUINTE,IDR_AREA,IDR_AREA_CONSTRUIDA,IDR_AREA_PRIVATIVA,IDR_AREA_COMUM,IDR_AREA_TOTAL,IDR_CLASSIMOVEL,IDR_OBSERVACAO,IDR_FORMATO_MEDICAO,IDR_FRACAO_SOLO,IDR_FORMATO_FRACAO_SOLO,IDR_PAVIMENTO,IDR_INSC_MUNICIPAL,IDR_INCRA,IDR_CONJUNTO,IDR_EMPREENDIMENTO,IDR_LETRA,IDR_LIVRO,IDR_BOX,IDR_VAGA,IDR_LOCALIZACAO,IDR_PREDIO,IDR_ITR,IDR_USU,IDR_PASTA,IDR_PASTAC,IDR_IMAGEM,IDR_IMOVEL
                            FROM indice_real_reg
                            WHERE ARS_DTRECEPCAO = :dtRecepcao AND 
                                  ARS_PROTOCOLO  = :sProtocolo AND
                                  ARS_SEQ        = :iSequencia ";
                $qry = $this->conn->prepare($sqlSeq);
                $qry->bindParam( ":dtRecepcao" , $dtRecepcao );
                $qry->bindParam( ":sProtocolo" , $sProtocolo );
                $qry->bindParam( ":iSequencia" , $iSequencia );
                $qry->execute();
                if($qry->rowCount() > 0){                    
                    $resQuery = $qry->fetchAll();
                    foreach ($resQuery as $result) {
                        
                    
                    $dadosindice_real_reg = $this->rec->addDados(array(
                        'ARS_DTRECEPCAO'          =>$result["ARS_DTRECEPCAO"],
                        'ARS_PROTOCOLO'           =>$result["ARS_PROTOCOLO"],
                        'ARS_SEQ'                 =>$result["ARS_SEQ"],
                        'IDR_TIPO'                =>$result["IDR_TIPO"],
                        'IDR_NUMLIVRO'            =>$result["IDR_NUMLIVRO"],
                        'IDR_CEP'                 =>$result["IDR_CEP"],
                        'IDR_TPLOCALIZACAO'       =>$result["IDR_TPLOCALIZACAO"],
                        'IDR_TPIMOVEL'            =>$result["IDR_TPIMOVEL"],
                        'IDR_TPLOGRADOURO'        =>$result["IDR_TPLOGRADOURO"],
                        'IDR_LOGRADOURO'          =>$result["IDR_LOGRADOURO"],
                        'IDR_NUMLOGRADOURO'       =>$result["IDR_NUMLOGRADOURO"],
                        'IDR_BAIRRO'              =>$result["IDR_BAIRRO"],
                        'IDR_CIDADE'              =>$result["IDR_CIDADE"],
                        'IDR_UF'                  =>$result["IDR_UF"],
                        'IDR_COMPL_TORRE'         =>$result["IDR_COMPL_TORRE"],
                        'IDR_COMPL_BLOCO'         =>$result["IDR_COMPL_BLOCO"],
                        'IDR_COMPL_ANDAR'         =>$result["IDR_COMPL_ANDAR"],
                        'IDR_COMPL_APTO'          =>$result["IDR_COMPL_APTO"],
                        'IDR_COMPL_SETOR'         =>$result["IDR_COMPL_SETOR"],
                        'IDR_COMPL_CONDOMINIO'    =>$result["IDR_COMPL_CONDOMINIO"],
                        'IDR_COMPL_UNIDADE'       =>$result["IDR_COMPL_UNIDADE"],
                        'IDR_COMPL_TRAVESSA'      =>$result["IDR_COMPL_TRAVESSA"],
                        'IDR_COMPL_LOTEAMENTO'    =>$result["IDR_COMPL_LOTEAMENTO"],
                        'IDR_COMPL_QUADRA'        =>$result["IDR_COMPL_QUADRA"],
                        'IDR_COMPL_LOTE'          =>$result["IDR_COMPL_LOTE"],
                        'IDR_COMPL_COMPLEMENTO'   =>$result["IDR_COMPL_COMPLEMENTO"],
                        'IDR_COMPL_CONTRIBUINTE'  =>$result["IDR_COMPL_CONTRIBUINTE"],
                        'IDR_AREA'                =>$result["IDR_AREA"],
                        'IDR_AREA_CONSTRUIDA'     =>$result["IDR_AREA_CONSTRUIDA"],
                        'IDR_AREA_PRIVATIVA'      =>$result["IDR_AREA_PRIVATIVA"],
                        'IDR_AREA_COMUM'          =>$result["IDR_AREA_COMUM"],
                        'IDR_AREA_TOTAL'          =>$result["IDR_AREA_TOTAL"],
                        'IDR_CLASSIMOVEL'         =>$result["IDR_CLASSIMOVEL"],
                        'IDR_OBSERVACAO'          =>$result["IDR_OBSERVACAO"],
                        'IDR_FORMATO_MEDICAO'     =>$result["IDR_FORMATO_MEDICAO"],
                        'IDR_FRACAO_SOLO'         =>$result["IDR_FRACAO_SOLO"],
                        'IDR_FORMATO_FRACAO_SOLO' =>$result["IDR_FORMATO_FRACAO_SOLO"],
                        'IDR_PAVIMENTO'           =>$result["IDR_PAVIMENTO"],
                        'IDR_INSC_MUNICIPAL'      =>$result["IDR_INSC_MUNICIPAL"],
                        'IDR_INCRA'               =>$result["IDR_INCRA"],
                        'IDR_CONJUNTO'            =>$result["IDR_CONJUNTO"],
                        'IDR_EMPREENDIMENTO'      =>$result["IDR_EMPREENDIMENTO"],
                        'IDR_LETRA'               =>$result["IDR_LETRA"],
                        'IDR_LIVRO'               =>$result["IDR_LIVRO"],
                        'IDR_BOX'                 =>$result["IDR_BOX"],
                        'IDR_VAGA'                =>$result["IDR_VAGA"],
                        'IDR_LOCALIZACAO'         =>$result["IDR_LOCALIZACAO"],
                        'IDR_PREDIO'              =>$result["IDR_PREDIO"],
                        'IDR_ITR'                 =>$result["IDR_ITR"],
                        'IDR_USU'                 =>$result["IDR_USU"],
                        'IDR_PASTA'               =>$result["IDR_PASTA"],
                        'IDR_PASTAC'              =>$result["IDR_PASTAC"],
                        'IDR_IMAGEM'              =>$result["IDR_IMAGEM"],
                        'IDR_IMOVEL'              =>$result["IDR_IMOVEL"],
                        'IDR_RECNO'               =>$result["RECNO"],
                        "USU_CODIGO"              =>$this->rec->getUsuCodigo(),
                        "USU_SESSAO"              =>$this->rec->getUsuSessao(),
                        "IDR_TEMP_EXPORTADO"      =>$tempExportado
                    ));
                    $resWS = $this->ws->inserirRegistro( getToken( $this->conn->db() ), 'indice_real_reg_temp', $dadosindice_real_reg['campos'], $dadosindice_real_reg['dados']);
                    $resp["error"] .= ($resWS != "") ? "Indice Real:". $resWS .";" : "";
                    }
                }

                $sqlSeq = " SELECT RECNO, IDP_CODIGO, PES_CODIGO, PSD_CODIGO, ARS_RECEPCAO, ARS_PROTOCOLO, ARS_SEQ, QUA_CODIGO, QUA_DESC, IDP_NUMLIVRO, IDP_PORCENTAGEM
                    FROM indice_pessoal_reg
                    WHERE ARS_RECEPCAO = :dtRecepcao AND 
                          ARS_PROTOCOLO  = :sProtocolo AND
                          ARS_SEQ        = :iSequencia ";
                $qry = $this->conn->prepare($sqlSeq);
                $qry->bindParam( ":dtRecepcao" , $dtRecepcao );
                $qry->bindParam( ":sProtocolo" , $sProtocolo );
                $qry->bindParam( ":iSequencia" , $iSequencia );
                $qry->execute();
                if($qry->rowCount() > 0){
                    $resQuery = $qry->fetchAll();                    
                    foreach ($resQuery as $result) {

                        $dadosIndicePessoal = $this->rec->addDados(array(
                            'IDP_CODIGO'         => $result["IDP_CODIGO"],
                            'PES_CODIGO'         => $result["PES_CODIGO"],
                            'PSD_CODIGO'         => $result["PSD_CODIGO"],
                            'ARS_RECEPCAO'       => $result["ARS_RECEPCAO"],
                            'ARS_PROTOCOLO'      => $result["ARS_PROTOCOLO"],
                            'ARS_SEQ'            => $result["ARS_SEQ"],
                            'QUA_CODIGO'         => $result["QUA_CODIGO"],
                            'QUA_DESC'           => $result["QUA_DESC"],
                            'IDP_NUMLIVRO'       => $result["IDP_NUMLIVRO"],
                            'IDP_RECNO'          => $result["RECNO"],
                            'IDP_PORCENTAGEM'    => $result["IDP_PORCENTAGEM"],
                            "USU_CODIGO"         => $this->rec->getUsuCodigo(),
                            "USU_SESSAO"         => $this->rec->getUsuSessao(),
                            "IDP_TEMP_EXPORTADO" => $tempExportado
                        ));
                        $resWS = $this->ws->inserirRegistro( getToken( $this->conn->db() ), 'indice_pessoal_reg_temp', $dadosIndicePessoal['campos'], $dadosIndicePessoal['dados']);
                        $resp["error"] .= ($resWS != "") ? "Indice Pessoal:". $resWS .";" : "";
                    }
                }

            }
        }

        $sqlCalculo = "SELECT RECNO,ARS_RECEPCAO,ARS_PROTOCOLO,ARS_SEQ,CAL_TPCALCULO,CAL_TABELA, CAL_LETRA,CAL_DIVISOR,CAL_QTDE,CAL_VALORDECLARADO,CAL_VALORTRIBUTADO,CAL_EMOLUMENTOS,CAL_ESTADO,CAL_IPESP,CAL_SINOREG,CAL_TRIBUNAL,CAL_ISS,CAL_TOTAL 
                        FROM calculo_reg
                        WHERE ARS_PROTOCOLO = :NUM_PROTOCOLO ";
        $qry = $this->conn->prepare($sqlCalculo);
        $qry->bindParam( ":NUM_PROTOCOLO" , $numProtocolo );
        $qry->execute();
        
        if($qry->rowCount() > 0){
            $resQuery = $qry->fetchAll();
            foreach ($resQuery as $res) {
                $dadosCalculo = $this->rec->addDados(array(
                    "ARS_RECEPCAO"       =>$res["ARS_RECEPCAO"],
                    "ARS_PROTOCOLO"      =>$res["ARS_PROTOCOLO"],
                    "ARS_SEQ"            =>$res["ARS_SEQ"],
                    "CAL_TPCALCULO"      =>$res["CAL_TPCALCULO"],
                    "CAL_TABELA"         =>$res["CAL_TABELA"],
                    "CAL_LETRA"          =>$res["CAL_LETRA"],
                    "CAL_DIVISOR"        =>$res["CAL_DIVISOR"],
                    "CAL_QTDE"           =>$res["CAL_QTDE"],
                    "CAL_VALORDECLARADO" =>$res["CAL_VALORDECLARADO"],
                    "CAL_VALORTRIBUTADO" =>$res["CAL_VALORTRIBUTADO"],
                    "CAL_EMOLUMENTOS"    =>$res["CAL_EMOLUMENTOS"],
                    "CAL_ESTADO"         =>$res["CAL_ESTADO"],
                    "CAL_IPESP"          =>$res["CAL_IPESP"],
                    "CAL_SINOREG"        =>$res["CAL_SINOREG"],
                    "CAL_TRIBUNAL"       =>$res["CAL_TRIBUNAL"],
                    "CAL_ISS"            =>$res["CAL_ISS"],
                    "CAL_TOTAL"          =>$res["CAL_TOTAL"],
                    "USU_CODIGO"         =>$this->rec->getUsuCodigo(),
                    "USU_SESSAO"         =>$this->rec->getUsuSessao(),
                    "CAL_TEMP_EXPORTADO" =>$tempExportado,
                    "CAL_RECNO_ORIGIN"   =>$res["RECNO"]
                ));
                $resWS = $this->ws->inserirRegistro( getToken( $this->conn->db() ),'calculo_reg_temp', $dadosCalculo['campos'], $dadosCalculo['dados']);
                $resp["error"] .= ($resWS != "") ? "Calculo:". $resWS .";" : "";
            }
        }

        $sqlSeq = " SELECT * FROM recepcao_informacoes
                    WHERE ARS_PROTOCOLO = :NUM_PROTOCOLO ";
        $qry = $this->conn->prepare($sqlSeq);
        $qry->bindParam( ":NUM_PROTOCOLO" , $numProtocolo );
        $qry->execute();
        if($qry->rowCount() > 0){
            $resQuery = $qry->fetchAll();            
            foreach ($resQuery as $res) {
                $dadosInformacoes = $this->rec->addDados(array(
                    'INF_CODIGO'             => $res["INF_CODIGO"],
                    'INF_DESCRICAO'          => utf8_decode( $res["INF_DESCRICAO"] ),
                    'INF_CHECKLIST'          => $res["INF_CHECKLIST"],
                    'ARS_NUMLIVRO'           => $res["ARS_NUMLIVRO"],
                    "USU_CODIGO"             => $this->rec->getUsuCodigo(),
                    "USU_SESSAO"             => $this->rec->getUsuSessao(),
                    "INF_TEMP_EXPORTADO"     => $tempExportado,
                    "INF_RESTRICAO"          => $res["INF_RESTRICAO"],
                    "INF_PROTOCOLO_RESTRITO" => $res["INF_PROTOCOLO_RESTRITO"],
                ));

                $resWS = $this->ws->inserirRegistro( getToken( $this->conn->db() ), 'recepcao_informacoes_tmp', $dadosInformacoes['campos'], $dadosInformacoes['dados']);
                $resp["error"] .= ($resWS != "") ? "Real:". $resWS .";" : "";
            
            }
        }
        
        $sqlDadosRegistro  ="SELECT PES_NOME             AS Nome,
                                    PES_TIPO_DOC         AS TipoDoc,
                                    PES_DOCUMENTO        AS Documento,
                                    PSD_CEP              AS Cep,
                                    PSD_LOGRADOURO       AS Logradouro,
                                    PSD_NUMLOGRADOURO    AS Numero,
                                    PSD_BAIRRO           AS Bairro,
                                    PSD_CIDADE           AS Cidade,
                                    PSD_UF               AS Uf,
                                    PSD_APRES_CONTATO    AS Contato,
                                    PSD_APRES_TEL        AS Telefone,
                                    PSD_APRES_EMAIL      AS Email,
                                    APR_CODIGO           AS CodApresentante,
                                    ARG_APRES_OBS        AS ApresObs,
                                    ARG_VALORDEPOSITO    AS ValorDeposito,
                                    CAI_VALOR_CH         AS ValorCheque,
                                    CAI_NUMER_CH         AS NumeroCheque,
                                    CAI_BANCO_CH         AS BancoCheque,
                                    CAI_EMITENTE         AS Emitente,
                                    CAI_VALOR_DI         AS ValorDinheiro,
                                    CAI_VALOR_CC         AS ValorContaCorrente,
                                    CAI_BANCO_CC         AS BancoContaCorrente,
                                    CAI_VALOR            AS Valor, 
                                    CAI_DEPOSITANTE      AS Depositante,
                                    ARG_LIBERAMANUTENCAO AS LibManutencao,
                                    reg.ARG_POSICAO      AS Posicao,
                                    oco.OCO_DESCRICAO    AS PosicaoDescricao,
                                    reg.NAT_DESCCODIGO   AS Natureza
                            FROM arc_reg_temp reg
                            INNER JOIN pessoas_dados psd ON (psd.PSD_CODIGO = reg.APR_CODIGO)
                            INNER JOIN pessoas pes       ON (pes.PES_CODIGO = psd.PES_CODIGO)
                            INNER JOIN caixa cai         ON (cai.CAI_PROTOCOLO = reg.ARS_PROTOCOLO AND cai.CAI_HISTORICO LIKE '%PRENOTAÇÃO%')
                            INNER JOIN ocorrencias oco   ON (reg.ARG_POSICAO = oco.OCO_CODIGO)
                            WHERE  reg.USU_CODIGO = :USU_CODIGO AND USU_SESSAO = :USU_SESSAO  ";
        $qry = $this->conn->prepare($sqlDadosRegistro);
        $qry->bindParam( ":USU_CODIGO", $usuCodigo );
        $qry->bindParam( ":USU_SESSAO", $usuSessao );
        $qry->execute();        
        $resp["dadosReg"] = $qry->fetch();

        $sqlDadosSequencia = "SELECT   seq.ARS_ATO     AS atoPraticado,
                                       cal.CAL_TABELA  AS tabela,
                                       cal.CAL_LETRA   AS tabelaLetra,
                                       cal.CAL_DIVISOR AS redutorValor,
                                       seq.ARS_SEQ     AS primeiroAto
                              FROM arc_seq_reg_temp seq
                              INNER JOIN calculo_reg_temp cal ON (seq.ARS_PROTOCOLO = cal.ARS_PROTOCOLO)
                              WHERE seq.USU_CODIGO = :USU_CODIGO AND seq.USU_SESSAO = :USU_SESSAO LIMIT 1";
        $qrySeq = $this->conn->prepare($sqlDadosSequencia);
        $qrySeq->bindParam( ":USU_CODIGO", $usuCodigo );
        $qrySeq->bindParam( ":USU_SESSAO", $usuSessao );
        $qrySeq->execute();
        
        

        $resp["dadosReg"]["ValorCheque"]        = number_format(@$resp["dadosReg"]["ValorCheque"], 2, ",", ".");
        $resp["dadosReg"]["ValorDinheiro"]      = number_format(@$resp["dadosReg"]["ValorDinheiro"], 2, ",", ".");
        $resp["dadosReg"]["ValorContaCorrente"] = number_format(@$resp["dadosReg"]["ValorContaCorrente"], 2, ",", ".");
        $resp["dadosReg"]["Valor"]              = number_format(@$resp["dadosReg"]["Valor"], 2, ",", ".");
        $resp["dadosReg"]["ValorDeposito"]      = number_format(@$resp["dadosReg"]["ValorDeposito"], 2, ",", ".");
        $resp["dadosReg"]["dadosSeq"]           = $qrySeq->fetch();

        if($resp["error"] == ""){
            $resp["message"] = "Reentrada carregada com sucesso.";
        }

        return json_encode($resp);
	}

    public function getQtdeReentrada( $numProtocolo ){
        $sSQL ="SELECT COUNT(recno) AS Qtde
                FROM datas dat 
                WHERE dat.ARS_PROTOCOLO = :PROTOCOLO AND dat.OCO_CODIGO = 14";
        $sQRY = $this->conn->prepare($sSQL);
        $sQRY->bindParam(":PROTOCOLO", $numProtocolo);
        $sQRY->execute();
        $ln  = $sQRY->fetch(PDO::FETCH_ASSOC);  
        return $ln["Qtde"];
    }

}




// $return = carregarReentrada( 355002 );