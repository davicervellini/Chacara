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
    case 'corrigirDadosCartorio':
        header("Content-Type: application/json;charset=utf-8");
        try{

            $vDados = [
                'DCA_RAZAO'          => $razao,
                'DCA_CPLRAZAO'       => $complRazao,
                'DCA_FANTASIA'       => $fantasia,
                'DCA_NUM_TABELIONATO'=> $tabelionato,
                'DCA_ENDERECO'       => $endereco,
                'DCA_BAIRRO'         => $bairro,
                'DCA_CEP'            => $cep,
                'DCA_CIDADE'         => $cidade,
                'DCA_ESTADO'         => $uf,
                'DCA_TELEFONE'       => $telefone,
                'DCA_EMAIL'          => strtolower($email),
                'DCA_SITE'           => $site,
                'DCA_NOMESUBSTITUTO' => $substituto,
                'DCA_CSUBSTITUTO'    => $cargo,
                'DCA_CNPJ'           => $sys->limpaVars($cnpj),
                'DCA_HORAFUNC'       => $horario,
                'DCA_CPFOFICIAL'     => $sys->limpaVars($cpf),
                'DCA_NOMEOFICIAL'    => $oficial
            ];
            $regAnterior = $sys->select('dadoscart', $vDados, ['RECNO'=>$codDCA], false); // Retorna o resultado antes do UPDATE
            $diferenca   = $sys->identificarCorrecao($regAnterior, $vDados, false, false); // Identifica as diferenças entre o resultado antigo e o atual
           
            $result = $sys->vStrings($vDados);
            $res    = $ws->corrigirRegistro(getToken($connMYSQL->db()), "dadoscart", "RECNO = ".$codDCA, $result['campos'] , $result['dados']);
            if($res != ""){
                $resp['error'] = $res;
            }else{
                $sys->historico("DADOS DO CARTÓRIO", "CORRIGIU OS DADOS DO CARTÓRIO: ".utf8_encode( $diferenca ));
                $resp['message'] = 'Dados do Cartório corrigidos com sucesso.';
            }

            print json_encode($resp);

        }catch(Exception $e){
            $resp['error'] = $e->getMessage();
            print json_encode($resp);
        }
    break;
} 

?>