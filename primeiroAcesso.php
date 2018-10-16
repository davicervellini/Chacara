<?php 
	session_start();

	$usuRecno = $_SESSION["usuario"]["usuRecno"];
?>
<!DOCTYPE html>
<html>
<head>
	<title>Primeiro Acesso</title>
	<meta charset="utf-8">
	<base href="<?php print INCLUDE_PATH; ?>/arcari">

	<link rel="icon" type="image/png" sizes="96x96" href="img/arcaIcon.png">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
	<link type="text/css" rel="stylesheet" href="css/style.css"  media="screen,projection"/>
	<link rel="stylesheet" href="css/jquery-confirm.css">
	<script src="js/jquery-3.2.1.min.js"></script>

	<script src="js/jquery.maskedinput.js"></script>
	<script src="js/materialize.min.js" type="text/javascript" charset="utf-8" ></script> 
	<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
	<script src="js/jquery-confirm.js"></script>
	<script src="js/arcaDialog.js"></script>
</head>
<style type="text/css">
	body{
		background: url("img/bkg-login.jpg");
		padding:10px;
	}
</style>
<body class="grey lighten-1">
	<div class="container">
		<div class="row">
			<form class="col l10 white z-depth-1 offset-l1" style='border-top: 1px solid #ccc;min-height:450px;padding:15px'>
				<h5 class="center-align">Primeiro Acesso</h5>
				<p class="center-align">Para concluir seu cadastro, defina uma senha para seu usuário.</p>	
				<div class="row" style="padding-top:10px">
					<div class="input-field col l8 offset-l2">
						<input id="usuSenha" type="password" onkeypress="next(event,'usuNovaSenha')" placeholder=" ">
						<label for="usuSenha">Nova Senha</label>
					</div>
				</div>
				<div class="row">
					<div class="input-field col l8 offset-l2">
						<input id="usuNovaSenha" type="password" placeholder=" " onkeypress="primeiroAcesso(event)">
						<label for="usuNovaSenha">Confirmar Senha</label>
					</div>
				</div>
				<div id="divMsg" class="row" style="display:none">
					<div class="col l8 offset-l2 center-align">
						<div class="card red lighten-1">
							<div class="card-content white-text">
								<p id="msgBox" class="center-align"></p>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col l10 offset-l1 center-align">
			     		<a id="btnCorrigir" name="btnCorrigir" class="waves-effect waves-light btn background" onClick="salvarSenha()">Salvar</a>
					</div>
				</div>
			</form>
		</div>
	</div>
<input type="hidden" name="usuRecno" id="usuRecno" value="<?php print $usuRecno; ?>">
<input type="hidden" name="usuLogin" id="usuLogin" value="<?php print $_SESSION['usuario']['usuLogin']; ?>">
<input type="hidden" name="usuConectado" id="usuConectado" value="<?php print $_GET['conect']; ?>">
</body>


<script>

function exibeMsg(_id, desc){
	$("#divMsg").css("display","block");
	$("#msgBox").html(desc);
	$("#"+_id).focus();
}

function salvarSenha(){
	
	$("#divMsg").css("display","none");
	if($("#usuSenha").val() == ""){
		exibeMsg("usuSenha"," Por favor, informe a senha desejada");
		return;
	}else if($("#usuNovaSenha").val() == ""){
		exibeMsg("usuNovaSenha"," Por favor, confirme a senha desejada");
		return;
	}else if($("#usuSenha").val() != $("#usuNovaSenha").val()){
		exibeMsg("usuNovaSenha"," As senhas não são idênticas");
		return;
	}

	ArcaDialog.YesNoCancel('Deseja salvar a senha?', function( responseConfirm ){
		if( responseConfirm === true){
			var campos = { usuSenha: $("#usuNovaSenha").val(), usuRecno: $("#usuRecno").val(), primeiroAcesso: "1", processo: "primeiroAcesso" }
			$.ajax({
	            type: "POST",
	            url: "php/frmAlterarSenha.php",
	            data: campos,
	            dataType: "json",
	            success: function(resp){
	            	ArcaDialog.Alert( resp.message);
	            	if(resp.redirect == 1){
	            		$.ajax({
							type: "POST",
							url: "php/frmLogin.php",
							data: "processo=login&usuLogin="+$("#usuLogin").val()+"&usuSenha="+$("#usuSenha").val()+"&usuConectado="+usuConectado,
							dataType: "json",
							success: function(resposta){
								if( resposta.valid === true ){
									window.location.href = "home/";
								}else{
									$("#msgBox").showAlert(resposta.message);
									$("#rowBtn").show();
									return false;
								}
							},
							error: function(info){
								console.log(info.responseText);
							}
						});
	            	}         
	            }
	        });
        }

        if( responseConfirm === "cancel"){
        	window.location.reload();	
        }

	});

}

function next(e,field){
    var unicode =e.keyCode? e.keyCode : e.charCode
    if(unicode == 13){
        $("#"+field).focus();
        salvarSenha();
    }
}

function primeiroAcesso(e){
    var unicode =e.keyCode? e.keyCode : e.charCode
    if(unicode == 13){
        salvarSenha();
    }
}

$(document).ready(function(){
	$("#usuSenha").focus();
});

</script>
