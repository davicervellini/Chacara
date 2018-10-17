<?php
session_start();
require_once "../../config.php";
require_once "../../conexao/ConexaoMySQL.Class.php";
require_once "../../soap/ArcaTDPJ_WS/ArcaTDPJ_WS.php";
require_once "../../classes/autoload.php";
$sys = new Sistema;
$ws  = new ArcaTDPJ_WS;

$resp    = array();
$vCampos = array();
$vDados  = array();

foreach ($_POST as $key => $value) {
    ${$key} = ($value != "") ? $value : NULL;
}

switch ($processo) {
    case 'corrigirSequenciaProtocolo':
        
        try{

            $vDados = [
                'PRENOTATD'     => $protOficialTd,
                'PRENOTAPJ'     => $protOficialPj,
                'REGISTROTD'    => $registroTd,
                'REGISTROPJ'    => $registroPj,
                'CERTIDAO'      => $pedCertidao,
                'FATURA'        => $fatura,
                'CERTIFICADO'   => $certificado,
                'MATRICULA'     => $matricula
            ];

            $regAnterior = $sys->select('sequencia', $vDados, ['RECNO'=>$codSeq], false); // Retorna o resultado antes do UPDATE
            $diferenca   = $sys->identificarCorrecao($regAnterior, $vDados, false, true); // Identifica as diferenças entre o resultado antigo e o atual
           
            $result = $sys->vStrings($vDados);
            $res    = $ws->corrigirRegistro(getToken($connMYSQL->db()), "sequencia", "RECNO = ".$codSeq, $result['campos'] , $result['dados']);
            if($res != ""){
                $resp['error'] = $res;
            }else{
                $sys->historico("SEQUÊNCIA DE PROTOCOLO", "CORRIGIU A SEQUÊNCIA DE PROTOCOLO: ".$diferenca);
                $resp['message'] = 'Sequência de Protocolo corrigido com sucesso.';
            }

            print json_encode($resp);

        }catch(Exception $e){
            $resp['error'] = $e->getMessage();
            print json_encode($resp);
        }
    break;
} 

?>