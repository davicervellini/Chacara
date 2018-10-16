<?php

	session_start();
	unset($_COOKIE['nome_usuario']);
	unset($_COOKIE['senha_usuario']);
	setcookie('nome_usuario', null, -1, '/');
	setcookie('senha_usuario', null, -1, '/');
	unset($_SESSION['usuario']);
	unset($_SESSION['permissoes']);
	unset($_SESSION['URL_WS']);
	session_destroy();
	header("location: ../login/ ");

?>