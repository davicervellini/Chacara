<?php

require_once __DIR__ ."/GetterSetter.Class.php";

class Caixa extends GetterSetter{
	public $conn;
	public $sys;
	public $ws;

	public function __construct($codigo = ''){
		$this->conn = new ConexaoMySQL();
		$this->sys  = new Sistema;
		$this->ws   = new ArcaTDPJ_WS;
		if($codigo != ""){
            $sql = "SELECT CAI_DATA            AS DataCai,
                           CAI_HORA            AS Hora,
                           CAI_CODC            AS Codc,
                           CAI_CODH            AS Codh,
                           CAI_OPERACAO        AS Operacao,
                           CAI_HISTORICO       AS Historico,
                           CAI_PROTOCOLO       AS Protocolo,
                           CAI_TP_PROT         AS TpProt,
                           USU_CODIGO          AS Codigo,
                           CAI_USUARIO         AS Usuario,
                           CAI_CHEQUE          AS Cheque,
                           CAI_VALOR_CH        AS ValorCh,
                           CAI_NUMER_CH        AS NumerCh,
                           CAI_BANCO_CH        AS BancoCh,
                           CAI_EMITENTE        AS Emitente,
                           CAI_VALOR_DI        AS ValorDi,
                           CAI_VALOR_CC        AS ValorCc,
                           CAI_BANCO_CC        AS BancoCc,
                           CAI_DEPOSITANTE     AS Depositante,
                           CAI_TP_DOC_APRES    AS TpDocApres,
                           CAI_DOC_APRES       AS DocApres,
                           CAI_APRESENTANTE    AS Apresentante,
                           CAI_QTDE_CER        AS QtdeCer,
                           CAI_VALOR           AS Valor,
                           CAI_OBSERVA         AS Observa,
                           CAI_REEN_SIMULT     AS ReenSimult,
                           CAI_DTOCORRENCIA    AS DtOcorrencia,
                           CAI_BAIXAOCORRENCIA AS BaixaOcorrencia
                    FROM caixa WHERE RECNO = :RECNO" ;
            $qry = $this->conn->prepare($sql);
            $qry->bindParam(':RECNO', $codigo);
            $qry->execute();

            if($qry->rowCount() > 0){
                $result = $qry->fetchAll(PDO::FETCH_ASSOC);
                $this->setData($result);
            }
		}
	}

    public function limparValor($valor){
        return str_replace(",",".",str_replace(".","",$valor));
    }

	public function insertCaixa($options = '', $usuario, $usuCodigo){
        
        $dtAtual = date('Y-m-d');
        $hrAtual = date('H:i:s');

        $vDados = array(
            'CAI_DATA'    => $dtAtual,
            'CAI_HORA'    => $hrAtual,
            'USU_CODIGO'  => $usuCodigo,
            'CAI_USUARIO' => $usuario
        );

        if($options != ""){
            $vDados = array_merge($vDados, $options);
        }
        
        try{
	        $result = $this->sys->vStrings($vDados);
	        
            $res = $this->ws->inserirRegistro(getToken($this->conn->db()), "caixa", $result['campos'] , $result['dados'] );
            if($res != ""){
              return $res;
            }
        }catch(Exception $e){
        	return $e->getMessage();
        }
    }

    public function listarOcorrencias($data, $usuario){
        $campoUsu = ($usuario != "") ? "AND CAI_USUARIO = :USUARIO " : "" ;
        $sSQL = "SELECT RECNO, CAI_HISTORICO ,CAI_DTOCORRENCIA, CAI_VALOR, CAI_OPERACAO 
                 FROM caixa 
                 WHERE CAI_BAIXAOCORRENCIA = 1 AND CAI_DTOCORRENCIA = :DATA ".$campoUsu."
                 ORDER BY CAI_DTOCORRENCIA DESC";
        $sQRY = $this->conn->prepare($sSQL);
        $data = $this->sys->padroniza_datas_US($data);
        if($usuario != ""){
          $sQRY->bindParam(":USUARIO", $usuario);
        }
        $sQRY->bindParam(":DATA", $data);
        // print_r($data);die;
        $sQRY->execute();
        
        return $sQRY;
    }

    public function getUltimaOcorrencia(){

        $sSQL = "SELECT RECNO FROM caixa WHERE CAI_BAIXAOCORRENCIA = 1 ORDER BY RECNO DESC";
        $sQRY = $this->conn->prepare($sSQL);
        $sQRY->execute();
        $ln  = $sQRY->fetch(PDO::FETCH_ASSOC);

        return $ln["RECNO"];
    }

    public function listUsuariosPermitidos( $sForm = "Caixa" ){
        $sSQL = "SELECT usu.USU_LOGIN, usu.USU_NOME
                 FROM usuarios usu
                 LEFT JOIN permissao per on (usu.USU_CODIGO = per.USU_CODIGO)
                 WHERE (per.FORM IN ('$sForm', 'Caixa') AND per.ACESSO = 1) OR usu.USU_ADMIN = 1
                 GROUP BY usu.USU_LOGIN, usu.USU_NOME
                 ORDER BY usu.USU_NOME ASC";
        $sQRY = $this->conn->prepare($sSQL);
        $sQRY->execute();

        return $sQRY->fetchAll();
    }
}