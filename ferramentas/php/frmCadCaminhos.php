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
    case 'corrigirCaminhos':
        try{
            $vDados = [
                'CAM_TITULOS'       => $titulos,
                'CAM_CIVIL'         => $civil,
                'CAM_CERTIFICADOS'  => $certificados,
                'CAM_COMPROVANTE'   => $comprovante,
                'CAM_DOCUMENTOS'    => $documentos,
                'CAM_PROTOCOLO'     => $protocolo,
                'CAM_NOTAS'         => $nota,
                'CAM_PRENOTADO'     => $prenotado
            ];
            $regAnterior = $sys->select('caminhos', $vDados, ['RECNO'=>$codCam], false); // Retorna o resultado antes do UPDATE
            $diferenca   = $sys->identificarCorrecao($regAnterior, $vDados, false, true); // Identifica as diferenças entre o resultado antigo e o atual
           
            $result = $sys->vStrings($vDados);
            $res    = $ws->corrigirRegistro(getToken($connMYSQL->db()), "caminhos", "RECNO = ".$codCam, $result['campos'] , $result['dados']);
            if($res != ""){
                $resp['error'] = $res;
            }else{
                $sys->historico("CAMINHOS", "CORRIGIU OS CAMINHOS: ".$diferenca);
                $resp['message'] = 'Caminhos corrigidos com sucesso.';
            }

            print json_encode($resp);

        }catch(Exception $e){
            $resp['error'] = $e->getMessage();
            print json_encode($resp);
        }
    break;

    case 'salvarLogoRelatorio':
        try{
            $target_dir = "../../img/logos/";
            if(!is_dir($target_dir))
                mkdir($target_dir);

            $target_file = $target_dir . basename($_FILES["sArquivoLogoRelatorio"]["name"]);
            $uploadOk = 1;
            $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
                
            $resp = [];
            if(isset($_POST["submit"])){
                $check = getimagesize($_FILES["sArquivoLogoRelatorio"]["tmp_name"]);
                if($check !== false){
                    $uploadOk = 1;
                }else{
                    $resp["message"] = "É necessário que o arquivo seja uma imagem para continuar.";
                    $uploadOk = 0;
                }
            }
            
            if($_FILES["sArquivoLogoRelatorio"]["size"] > 500000){
                $resp["message"] = "Desculpe, seu arquivo é muito grande";
                $uploadOk = 0;
            }

            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                $resp["message"] = "Desculpe, apenas jpg, png e jpeg são permitidos.";
                $uploadOk = 0;
            }

            if($uploadOk == 0) {
                $resp['error'] = $resp['message'];
            }else{
                if(move_uploaded_file($_FILES["sArquivoLogoRelatorio"]["tmp_name"], $target_file)){

                    $urlLogo = str_replace("../../", "",$target_file);
                    $vDados = [
                        'CFG_CAMINHOLOGORELATORIO'       => $urlLogo
                    ];
                    $regAnterior = $sys->select('config', $vDados, ['RECNO'=>1], false); // Retorna o resultado antes do UPDATE
                    $diferenca   = $sys->identificarCorrecao($regAnterior, $vDados, false, true); // Identifica as diferenças entre o resultado antigo e o atual
                   
                    $result = $sys->vStrings($vDados);
                    $res    = $ws->corrigirRegistro(getToken($connMYSQL->db()), "config", "RECNO = 1", $result['campos'] , $result['dados']);

                    if($res != ""){
                        $resp['error'] = $res;
                    }else{
                        $sys->historico("CONFIGURAÇÕES", "ALTEROU O LOGO DE RELATÓRIO: ".$diferenca);
                        $resp["message"] = "O arquivo ". basename( $_FILES["sArquivoLogoRelatorio"]["name"]). " foi carregado com sucesso.";
                    }
                }else{
                    $resp["error"] = "Desculpe, houve um erro no carregamento do seu arquivo.";
                }
            }
            print json_encode($resp);
        }catch(Exception $e){
            $resp['error'] = $e->getMessage();
            print json_encode($resp);
        }
    break;

    case 'salvarLogo':
        try{
            $target_dir = "../../img/logos/";
            if(!is_dir($target_dir))
                mkdir($target_dir);

            $target_file = $target_dir . basename($_FILES["sArquivoLogo"]["name"]);
            $uploadOk = 1;
            $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
                
            $resp = [];
            if(isset($_POST["submit"])){
                $check = getimagesize($_FILES["sArquivoLogo"]["tmp_name"]);
                if($check !== false){
                    $uploadOk = 1;
                }else{
                    $resp["message"] = "É necessário que o arquivo seja uma imagem para continuar.";
                    $uploadOk = 0;
                }
            }
            
            if($_FILES["sArquivoLogo"]["size"] > 500000){
                $resp["message"] = "Desculpe, seu arquivo é muito grande";
                $uploadOk = 0;
            }

            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                $resp["message"] = "Desculpe, apenas jpg, png e jpeg são permitidos.";
                $uploadOk = 0;
            }

            if($uploadOk == 0) {
                $resp['error'] = $resp['message'];
            }else{
                if(move_uploaded_file($_FILES["sArquivoLogo"]["tmp_name"], $target_file)){

                    $urlLogo = str_replace("../../", "",$target_file);
                    $vDados = [
                        'CFG_CAMINHOLOGO'       => $urlLogo
                    ];
                    $regAnterior = $sys->select('config', $vDados, ['RECNO'=>1], false); // Retorna o resultado antes do UPDATE
                    $diferenca   = $sys->identificarCorrecao($regAnterior, $vDados, false, true); // Identifica as diferenças entre o resultado antigo e o atual
                   
                    $result = $sys->vStrings($vDados);
                    $res    = $ws->corrigirRegistro(getToken($connMYSQL->db()), "config", "RECNO = 1", $result['campos'] , $result['dados']);

                    if($res != ""){
                        $resp['error'] = $res;
                    }else{
                        $sys->historico("CONFIGURAÇÕES", "ALTEROU O LOGO: ".$diferenca);
                        $resp["message"] = "O arquivo ". basename( $_FILES["sArquivoLogo"]["name"]). " foi carregado com sucesso.";
                    }
                }else{
                    $resp["error"] = "Desculpe, houve um erro no carregamento do seu arquivo.";
                }
            }
            print json_encode($resp);
        }catch(Exception $e){
            $resp['error'] = $e->getMessage();
            print json_encode($resp);
        }
    break;

    case 'corrigirModelos':
        try{
            $vDados = [
                'CFG_MOD_BALCAO'          => $balcao,                
            ];
            $regAnterior = $sys->select('config', $vDados, ['RECNO'=>1], false); // Retorna o resultado antes do UPDATE
            $diferenca   = $sys->identificarCorrecao($regAnterior, $vDados, false, true); // Identifica as diferenças entre o resultado antigo e o atual
           
            $result = $sys->vStrings($vDados);
            $res    = $ws->corrigirRegistro(getToken($connMYSQL->db()), "config", "RECNO = 1", $result['campos'] , $result['dados']);
            if($res != ""){
                $resp['error'] = $res;
            }else{
                $sys->historico("CONFIGURAÇÕES", "CORRIGIU OS MODELOS: ".$diferenca);
                $resp['message'] = 'Modelos corrigidos com sucesso.';
            }

            print json_encode($resp);

        }catch(Exception $e){
            $resp['error'] = $e->getMessage();
            print json_encode($resp);
        }
    break;

    case 'atualizarRelatorios':
        try{
            $vDados = [
                'REL_ABREVIACAO'         => $abreviacao,
                'REL_LOGO'               => $logo
            ];
            
            $result = $sys->vStrings($vDados);

            $regAnterior = $sys->select('relatorios', $vDados, array('REL_ABREVIACAO'=>$abreviacao), false); // Retorna o resultado antes do UPDATE
            
            $res    = $ws->corrigirRegistro(getToken($connMYSQL->db()), "relatorios",  "REL_ABREVIACAO = '".$abreviacao."' ", $result['campos'] , $result['dados']);

            $diferenca   = $sys->identificarCorrecao($regAnterior, $vDados, false, false); // Identifica as diferenças entre o resultado antigo e o atual
           
            
            if($res != ""){
                $resp['error'] = $res;
            }else{
                $sys->historico("CONFIGURAÇÕES", "RELATÓRIO ATUALIZADO: ".$diferenca);
                $resp['message'] = 'Relatório atualizado com sucesso.';
            }

            print json_encode($resp);

        }catch(Exception $e){
            $resp['error'] = $e->getMessage();
            print json_encode($resp);
        }
    break;

} 

?>