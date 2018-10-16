<?php
	session_start();
	require_once "conexao/ConexaoMySQL.Class.php";
	require_once "classes/autoload.php";
	require_once "classes/Config.Class.php";

	$connMYSQL = new ConexaoMySQL();
	$user = new Usuario($connMYSQL);
	
	if(@$_COOKIE["nome_usuario"] != "" && @$_COOKIE["senha_usuario"] != ""){
		$user->loginConectado(@$_COOKIE["nome_usuario"], @$_COOKIE["senha_usuario"]);
	}
	// $cfg = new Config($connMYSQL);
?>
<!DOCTYPE html> 
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<base href="<?php print INCLUDE_PATH; ?>/">
	<title>Login</title>
	<link rel="icon" type="image/png" sizes="96x96" href="img/arcaIcon.png">
	<!--[if IE]><script type="text/javascript">
	    // Fix for IE ignoring relative base tags.
	    (function() {
	        var baseTag = document.getElementsByTagName('base')[0];
	        baseTag.href = baseTag.href;
	    })();
	</script><![endif]-->
	<!-- <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> -->
	<link type="text/css" rel="stylesheet" href="css/materialize-fonts.css" media="screen,projection"/>
	<link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
	<link type="text/css" rel="stylesheet" href="css/style.css"  media="screen,projection"/>
	<script src="js/route.js" type="text/javascript"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

	<style>
		body{
			background: url("img/bkg-login.jpg");
		}

		.main-login{
			height:450px;
			margin-top:50px;
		}

		.no-bottom{
			margin: 0;
		}

		.input-field input{
			margin: 0;
		}

		.center-field{
			margin: 0 auto; 
			
		}
		.padding40{
			padding:0 40px 0 40px;
		}
	</style>
</head>
<body>
<div class="row no-bottom " >
	<div class="col l4 offset-l4 center-align " style='height:100px;text-align:center;padding-top:25px'>
     	<!-- <img src="<?php print 'img/img001.png' ?>" alt="" style='height:100px'> -->
    </div>
</div>
	<div class="row main-login">
		<div id="main" class="co grey lighten-4 z-depth-2" style='border-radius:4px;height:450px;margin-top:10px;width:400px; margin: 0 auto'>
			
			<div class="row no-bottom">
				<div class="col l4 offset-l4 center-align " style='height:175px;text-align:center;padding-top:25px'>
	              <!-- <i class="material-icons" style='font-size:60px'>person_pin</i> -->
	              <img src="<?php print 'img/img001.png' ?>" alt="" style='height:100px'>
	            </div>
			</div>
			<div class="row">
		        <div class="input-field center-field padding40">
		          <input id="usuLogin" type="text" class="login" placeholder=" " onkeypress="next(event,'usuSenha')">
          			<label for="usuLogin" id="lblLogin" class="padding40">Usuário</label>
		        </div>
      		</div>
			<div class="row no-bottom">
		        <div class="input-field center-field padding40">
		          <input id="usuSenha" type="password" class="login" placeholder=" " onkeypress="login(event)">
		          <label for="usuSenha" class="padding40" >Senha</label>
		        </div>
      		</div>
			<div class="row no-bottom" >
		        <div class="input-field col l10 offset-l1"  style='height:40px; margin-left:15px' >
					<input id="manterConectado" type="checkbox" class="filled-in"  style='padding-left: 0'/>
     		 		<label for="manterConectado">Manter conectado</label>
		        </div>
      		</div>
			<div class="row" >
				<div class="col l10 offset-l1" id="msgBox">
				</div>
			</div>					
			<div class="row no-bottom" id='rowBtn'>
		        <div class="col l10 offset-l1">
		        	<a class="waves-effect waves-light btn background" style='width:100%;height:45px;line-height:45px' onClick="iniciarSessao()" onkeypress="login(event)">Entrar</a>
		        </div>
      		</div>
		
		</div>
	</div>


	<!-- <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> -->
	<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<script src="js/materialize.min.js" type="text/javascript" charset="utf-8" ></script>
	<script>
		$(document).ready(function(){
			$.fn.showAlert = function(message){
				$("#main").css('height', '530px');
				$(this).html("<div class=\"card red darken-1 show \" >\
				                        <div class=\"card-content white-text\" >"+message+"</div></div>");
			}
			$("#usuLogin").focus();
			$("#usuLogin").attr("placeholder", "");
			// $("#lblLogin").addClass('active');
		});


		$.fn.loadGif = function(sMessage){

			sMessage = (sMessage !== "") ? "<div style='padding-left:10px;line-height: 32px; height:32px'>"+sMessage+"</div> ": "";
			$(this).html("<div class=\"row\">\
				<div class=\"col l12 center-align\">\
					<div class=\"preloader-wrapper big active\">\
						<div class=\"spinner-layer spinner-green-only\">\
							<div class=\"circle-clipper left\">\
							<div class=\"circle\"></div>\
							</div><div class=\"gap-patch\">\
							<div class=\"circle\"></div>\
							</div><div class=\"circle-clipper right\">\
							<div class=\"circle\"></div>\
							</div>\
						</div>\
					</div>"+sMessage+"\
				</div></div>");
			
		}


		function iniciarSessao(){
		
			$("#divMsg").css("display","none");
			if($("#usuLogin").val() == ""){
				$("#msgBox").showAlert('Por favor, informe o usuário');
				return;
			}
			else if($("#usuSenha").val() == ""){
				$("#msgBox").showAlert('Por favor, informe a senha');
				return;
			}

			var usuConectado = ($("#manterConectado").is(":checked")) ? 1 : 0;

			$.ajax({
				type: "POST",
				url: "php/frmLogin.php",
				data: "processo=login&usuLogin="+$("#usuLogin").val()+"&usuSenha="+$("#usuSenha").val()+"&usuConectado="+usuConectado,
				dataType: "json",
				beforeSend: function(){
					$("#msgBox").loadGif('Autenticando usuário...');
					
					$("#rowBtn").hide();
				},
				success: function(resposta){
					if( resposta.valid === true ){
						// console.log(resposta);
						Route.href( resposta.url );
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

		function next(e,field){
	        var unicode =e.keyCode? e.keyCode : e.charCode
	        if(unicode == 13){
	            $("#"+field).focus();
	            iniciarSessao();
	        }
	    }

	    function login(e){
	        var unicode =e.keyCode? e.keyCode : e.charCode
	        if(unicode == 13){
	            iniciarSessao();
	        }
	    }



	</script>
	
</body>
</html>