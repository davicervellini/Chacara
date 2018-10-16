<?php
session_start();
require_once "../config.php";
require_once "../conexao/ConexaoMySQL.Class.php";
require_once "../soap/ArcaTDPJ_WS/ArcaTDPJ_WS.php";
require_once "../classes/autoload.php";
$sys = new Sistema;
$ws  = new ArcaTDPJ_WS;

$resp    = array();
$vCampos = array();
$vDados  = array();

foreach ($_POST as $key => $value) {
    ${$key} = ($value != "") ? $value : NULL;
}


switch ($processo) {
    case 'excluir':
        
        try{
            $select = "SELECT CHC_REMETENTE FROM chat WHERE CHC_CODIGO = :CHC_CODIGO";
            $qry = $connMYSQL->prepare($select);
            $qry->bindParam(':CHC_CODIGO', $codConversa);
            $qry->execute();
            $ln = $qry->fetch();

            if($ln['CHC_REMETENTE'] == $userId){
                $vDados = [
                    'CHT_INIBEREMETENTE'         => $userId
                ];
                $result = $sys->vStrings($vDados);
                $res    = $ws->corrigirRegistro(getToken($connMYSQL->db()) ,"chat_mensagens", "CHC_CODIGO = ".$codConversa, $result['campos'] , $result['dados']);
            }else{
                $vDados = [
                    'CHT_INIBEDESTINATARIO'         => $userId
                ];
                $result = $sys->vStrings($vDados);
                $res    = $ws->corrigirRegistro(getToken($connMYSQL->db()) ,"chat_mensagens", "CHC_CODIGO = ".$codConversa, $result['campos'] , $result['dados']);
            }

            if($res != "" ){
                $resp['error'] = $res;
            }else{
                $sys->historico("CHAT", "LIMPOU O HISTORICO DA CONVERSA: ".$codConversa);
                $resp['message']   = 'Histórico excluído com sucesso.';
            }
            
            print json_encode($resp);
        }catch(Exception $e){
            $resp['error'] = $e->getMessage();
            print json_encode($resp);
        }
    break;
} 


?>