<?php 

	require "rotas.php";
	require_once "config.php";

	 
	if( $match && is_callable( $match['target'] ) ) {
		call_user_func_array( $match['target'], $match['params'] ); 
	} else {
	  header( $_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	  require '404.php';
	}


?>