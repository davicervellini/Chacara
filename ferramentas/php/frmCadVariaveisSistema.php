<?php
session_start();
require_once "../../config.php";
require_once "../../conexao/ConexaoMySQL.Class.php";
require_once "../../soap/ArcaTDPJ_WS/ArcaTDPJ_WS.php";
require_once "../../classes/autoload.php";
$sys = new Sistema;
$men = new ConfigMenus;
$ws  = new ArcaTDPJ_WS;

$resp    = array();
$vCampos = array();
$vDados  = array();

foreach ($_POST as $key => $value) {
    ${$key} = ($value != "") ? $value : NULL;
}

switch ($processo) {
    case 'corrigirDadosVariaveis':
        
        try{
            // if($anoVelho != $ano){
            //     $sDados = ["RECNO" => 0];
            //     $result = $sys->vStrings($sDados);
            //     $res = $ws->corrigirRegistro( getToken( $connMYSQL->db() ) ,"recnos", " TABELA = 'movimento_oficio' ",  $result["campos"], $result["dados"]);
            //     $res = $ws->corrigirRegistro( getToken( $connMYSQL->db() ) ,"recnos", " TABELA = 'movimento_oficio_resp' ",  $result["campos"], $result["dados"]);
            // }
            $vDados = [                 
                 "CFG_IP_SERVER"            => $ipServer,
                 "CFG_SOCKET_CHAT_URL"      => $urlChat,
                 "CFG_URL_WS"               => $wsISAPI,
                 "CFG_COD_CLIENTE"          => $codCliente,
                 "CFG_SERVENTIA_RI"         => $serventiaRI,
                 "CFG_SERVENTIA_TD"         => $serventiaTD,
                 "CFG_SERVENTIA_PJ"         => $serventiaPJ, 
            ];

            $regAnterior = $sys->select('config', $vDados, ['RECNO'=>$codConfig], false); // Retorna o resultado antes do UPDATE
            $diferenca   = $sys->identificarCorrecao($regAnterior, $vDados, false, false); // Identifica as diferenças entre o resultado antigo e o atual
           
            $result = $sys->vStrings($vDados);
            $res    = $ws->corrigirRegistro(getToken($connMYSQL->db()), "config", "RECNO = ".$codConfig, $result['campos'] , $result['dados']);
            if($res != ""){
                $resp['error'] = $res;
            }else{
                $sys->historico("VARIÁVEIS DO SISTEMA", "CORRIGIU AS VARIÁVEIS: ".$diferenca);
                $resp['message'] = 'Variáveis corrigidas com sucesso.';
            }

            print json_encode($resp);

        }catch(Exception $e){
            $resp['error'] = $e->getMessage();
            print json_encode($resp);
        }
    break;
} 

?>