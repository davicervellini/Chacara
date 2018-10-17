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
    case 'incluirConfigMenu':
        
        try{

            $vDados = [
                'MEN_MENU'       => $menu,
                'MEN_FORM'       => $form,
                'MEN_DESCRICAO'  => $descricao,
                'MEN_GRUPO'      => $grupo,
                'MEN_GRUPO_ORDEM'=> $grupoOrdem
            ];

            $result = $sys->vStrings($vDados);

            $res = $ws->inserirRegistro(getToken($connMYSQL->db()), "menus", $result['campos'] , $result['dados'] );
            $men->atualizaPermicoes($menu,$form,$descricao);
            if($res != ""){
              $resp['error'] = $res;
            }else{
              $sys->historico("CONFIGURAÇÃO DE MENUS", "INCLUIU O MENU: ".$descricao." - FORM : ".$form);
              $resp['message'] = 'Menu cadastrado com sucesso.';
              $resp['menCod']  = $men->resgataRecno($menu);
            }
                        
            print json_encode($resp);

        }catch(Exception $e){
            $resp['error'] = $e->getMessage();
            print json_encode($resp);
        }
    break;

    case 'corrigirConfigMenu':
        
        try{

            $vDados = [
                'MEN_MENU'       => $menu,
                'MEN_FORM'       => $form,
                'MEN_GRUPO'      => $grupo,
                'MEN_GRUPO_ORDEM'=> $grupoOrdem,
                'MEN_DESCRICAO'  => $descricao
            ];

            $regAnterior = $sys->select('menus', $vDados, ['RECNO'=>$codMen], false); // Retorna o resultado antes do UPDATE
            $diferenca   = $sys->identificarCorrecao($regAnterior, $vDados, false, false); // Identifica as diferenças entre o resultado antigo e o atual
           
            $result = $sys->vStrings($vDados);
            $res    = $ws->corrigirRegistro(getToken($connMYSQL->db()), "menus", "RECNO = ".$codMen, $result['campos'] , $result['dados']);
            if($res != ""){
                $resp['error'] = $res;
            }else{
                $sys->historico("CONFIGURAÇÃO DE MENUS", "CORRIGIU O MENU: ".$diferenca);
                $resp['message'] = 'Menu corrigido com sucesso.';
            }

            print json_encode($resp);

        }catch(Exception $e){
            $resp['error'] = $e->getMessage();
            print json_encode($resp);
        }
    break;

    case 'excluirConfigMenu':
        
        try{

            $res = $ws->deletarRegistro(getToken($connMYSQL->db()), "menus", "RECNO = ".$codMen);
            $men->deletaPermicoes($form);
            if($res != ""){
                $resp['error'] = $res;
            }else{
                $sys->historico("CONFIGURAÇÃO DE MENUS", "EXCLUIU O MENU: ".$descricao);
                $resp['message'] = 'Menu excluído com sucesso.';
            }
            
            print json_encode($resp);

        }catch(Exception $e){
            $resp['error'] = $e->getMessage();
            print json_encode($resp);
        }
    break;

    case 'listarConfigMenus':

        try{            
            $qry = $men->list();
            if(count($qry) > 0){
                $cont = 0;
                $selected = 0;
                $ativo = "";
                $resp['grid'] = "
                   <thead>
                        <tr align=\"center\">
                          <th>Menu</th>
                          <th>Formulário Extenso</th>
                        </tr>
                  </thead>
                  <tbody>";
                foreach ($qry as $ln) {
                    $cont++;
                    if($ln["RECNO"] == $recno){
                        $ativo = "tr-active";
                        $selected = $cont;
                    }else{
                        $ativo = "";
                    }
                    $resp['grid'] .= "
                        <tr class='trHighlight ".$ativo."' onclick=\"selecionarRegistro('".$ln["RECNO"]."', this)\">
                            <td>".$ln["MEN_MENU"]."</td>
                            <td>".$ln["MEN_FORM"]."</td>
                        </tr>";
                }

                $resp['grid'] .= " </tbody>";
                $resp['pagina'] = (10 * floor(($selected/10)));
            }else{
                $resp['grid'] = "<thead>
                        <th>&nbsp;</th>
                       </thead>
                       <tbody>
                        <tr><td>Nenhum registro encontrado</td></tr>
                       </tbody>";
            }
            print json_encode($resp);
        }catch(Exception $e){
            $resp['error'] = $e->getMessage();
            print json_encode($resp);
        }

    break;

    case 'preencheCampos':

        try{            
            $men = new ConfigMenus($id);
            
            $resp["menu"]      = $men->getMenu();
            $resp["form"]      = $men->getForm();
            $resp["descricao"] = $men->getDescricao();
            $resp["grupo"]     = $men->getGrupo();

            print json_encode($resp);           
        }catch(Exception $e){
            $resp['error'] = $e->getMessage();
            print json_encode($resp);
        }

    break;


    case 'verificarAcesso':

        $password = @$_POST["password"];


        $sql = "SELECT RECNO FROM config WHERE CFG_ACESSO_MENUS = :PASS ";
        $qry = $connMYSQL->prepare($sql);
        $qry->bindParam(':PASS', $password);
        $qry->execute();

        if($qry->rowCount() > 0){
            $_SESSION["AcessoConfigMenus"] = "true";
            print "true";
        }else{
            print "false";
        }

        break;
} 


?>