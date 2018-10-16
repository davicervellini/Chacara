<?php
session_start();
require_once "../conexao/ConexaoMySQL.Class.php";
require_once "../soap/ArcaTDPJ_WS/ArcaTDPJ_WS.php";
require_once "../classes/autoload.php";
require_once "../config.php";

$linha_inicial = filter_input(INPUT_POST, "linha_inicial");
$margem_esq    = filter_input(INPUT_POST, "margem_esq");
$margem_dir    = filter_input(INPUT_POST, "margem_dir");
$fonte_txt     = filter_input(INPUT_POST, "fonte_txt");
$tamanho_txt   = filter_input(INPUT_POST, "tamanho_txt");
$pos_ficha     = filter_input(INPUT_POST, "pos_ficha");
$pos_matricula = filter_input(INPUT_POST, "pos_matricula");
$lado          = filter_input(INPUT_POST, "lado");
$acao          = filter_input(INPUT_POST, "acao");
$resp          = [];
try{
    if($acao == "carregar")
    {
        $sql = "select * from calibragem where CAL_POS_FOLHA = :CAL_POS_FOLHA";
        $qry = $connMYSQL->prepare($sql);
        $qry->bindParam(":CAL_POS_FOLHA", $lado);
        $qry->execute();
        if ($qry->rowCount() > 0)
        {
            $lin = $qry->fetch();
            $resp["linha_inicial"] = $lin["CAL_LINHA_INICIAL"];
            $resp["margem_esq"]    = $lin["CAL_MARGEM_ESQ"];
            $resp["margem_dir"]    = $lin["CAL_MARGEM_DIR"];
            $resp["fonte_txt"]     = $lin["CAL_FONTE_TXT"];
            $resp["tamanho_txt"]   = $lin["CAL_TAMANHO_TXT"];
            $resp["pos_ficha"]     = $lin["CAL_POS_FICHA"];
            $resp["pos_matricula"] = $lin["CAL_POS_MATRICULA"];
            $resp["lado"]          = $lado;
        }
    }
    else
    if($acao == "alterar")
    {
        $connMYSQL->query("UPDATE calibragem SET CAL_LINHA_INICIAL = '".$linha_inicial."',"
            . "                            CAL_MARGEM_ESQ    = '".$margem_esq."',"
            . "                            CAL_MARGEM_DIR    = '".$margem_dir."',"
            . "                            CAL_FONTE_TXT     = '".$fonte_txt."',"
            . "                            CAL_TAMANHO_TXT   = '".$tamanho_txt."',"
            . "                            CAL_POS_FICHA     = '".$pos_ficha."',"
            . "                            CAL_POS_MATRICULA = '".$pos_matricula."' "
            . "WHERE CAL_POS_FOLHA = " . $lado);
    }
    else
    {
        $connMYSQL->query("UPDATE calibragem SET CAL_LINHA_INICIAL = 5,"
            . "                            CAL_MARGEM_ESQ    = 45,"
            . "                            CAL_MARGEM_DIR    = 0,"
            . "                            CAL_FONTE_TXT     = 'Verdana',"
            . "                            CAL_TAMANHO_TXT   = 11,"
            . "                            CAL_POS_FICHA     = 'left',"
            . "                            CAL_POS_MATRICULA = 'left' "
            . "WHERE CAL_POS_FOLHA = " . $lado);
    }
    $resp["resposta"] = "Alteração realizada com sucesso!";
}catch(Exception $e) {
    $resp["resposta"] = $e->getMessage();
}
print json_encode($resp);
