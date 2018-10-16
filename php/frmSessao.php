<?php
    session_start();
    ini_set('default_socket_timeout', 5);
    $processo = $_POST["processo"];

    switch($processo){
        case "verifica_sessao":
            if(!isset($_SESSION["usuario"]) || $_SESSION["usuario"] == ""){
                print "1";
                exit;
            }
        break;
    }
?>
