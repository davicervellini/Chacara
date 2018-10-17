<?php

	require_once "config.php";
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta http-equiv="content-language" content="pt-br" />
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	<title>Sistema</title>
	<base href="<?php print INCLUDE_PATH; ?>/">
	<!-- <link rel="shortcut icon" href="favicon.ico" /> -->
    <link rel="icon" type="image/png" sizes="96x96" href="img/arcaIcon.png">
	<!-- <link rel="stylesheet" href="http://fonts.googleapis.com/icon?family=Material+Icons" > -->
	<link type="text/css" rel="stylesheet" href="css/materialize-fonts.css" media="screen,projection"/>
	<link type="text/css" rel="stylesheet" href="css/materialize-customized.css" media="screen,projection"/>
	<link type="text/css" rel="stylesheet" href="css/style.css"  media="screen,projection"/>
</head>
<body style='background:#FFF !important'>
<div class="row" style='height:150px;'>
	<div class="container" style='margin-top:0; padding-top:0;'>
		<p style='font-size:36px;margin:0;padding-top:80px;color:green'>404!</p>
	</div>
</div>
<div class="row">
	<div class="container">
		<p>Página não encontrada! </p>
		<p>A URL requisitada <b><?php print $_SERVER["REQUEST_URI"]; ?></b> não foi encontrada nesse servidor.</p>
		<p><a href="javascript: history.back()">Voltar para a página anterior</a></p>
	</div>
</div>	
</body>
</html>
