<?php
	session_start();
	// print_r($_SESSION["permissoes"]['F_FerConfiguracoes']['acesso']);
	// print_r($_SESSION["usuario"]["usuAdmin"]);
	// die();
	require_once "config.php";
	require_once "conexao/ConexaoMySQL.Class.php";
	require_once "classes/autoload.php";

	$connMYSQL = new ConexaoMySQL();

	$menu = new Menu;
	$sys  = new Sistema;
	$usu  = new Usuario;
	$ver  = new Versionamento;
	$conf = new Config($connMYSQL);

	$sys->verifica_sessao($_SESSION["usuario"]["usuLogin"],$_SESSION["usuario"]["usuSenha"]);

	$logo = $conf->getLogo();

	$usuario = $_SESSION["usuario"]["usuNome"] ?? "";

	// $cfg = new Config($conn);
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

	<link type="text/css" rel="stylesheet" href="css/font-awesome.min.css">
	<link type="text/css" rel="stylesheet" href="css/dataTables.material.css"/>
	<link type="text/css" rel="stylesheet" href="css/buttons.dataTables.min.css"/>
	<link type="text/css" rel="stylesheet" href="css/datepicker.css"/>
	<link rel="stylesheet" href="css/jquery-confirm.css">
	<!-- <link type="text/css" rel="stylesheet" href="css/bootstrap-tour-standalone.min.css"/> -->
	<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="js/buttons.print.min.js"></script>
	<script type="text/javascript" src="js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/jquery.mask.js"></script>
	<script type="text/javascript" src="js/jquery-maskmoney-v3.0.2.js"></script>
	<!-- <script type="text/javascript" src="js/bootstrap-tour-standalone.min.js"  charset="utf-8" ></script>  -->
	<script type="text/javascript" src="js/materialize.min.js"  charset="utf-8" ></script>
	<script type="text/javascript" src="js/route.js" ></script>
	<script type="text/javascript" src="lib/momentjs/moment.js" ></script>
    <script type="text/javascript" src="lib/momentjs/pt-br.js" ></script>
	<script type="text/javascript" src="js/frmSessao.js" ></script>

	<script src="js/jquery-confirm.js"></script>
	<script src="js/arcaDialog.js"></script>

	<style type="text/css">
		body{
			background: url("img/bkg-login.jpg");
		}

		.btntour{
		    padding: 4px 6px;
		}

		.arca-nav{
			overflow-y: hidden;
			overflow-x: hidden;
		}

		.arca-nav:hover{
			overflow-y: auto;
		}


		.fundoGradiente{
			/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#cdeac7+0,7db9e8+100&1+0,0+100 */
			background: -moz-linear-gradient(top, rgba(205,234,199,1) 0%, rgba(255,255,255,0) 75%); /* FF3.6-15 */
			background: -webkit-linear-gradient(top, rgba(205,234,199,1) 0%,rgba(255,255,255,0) 75%); /* Chrome10-25,Safari5.1-6 */
			background: linear-gradient(to bottom, rgba(205,234,199,1) 0%,rgba(255,255,255,0) 75%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#cdeac7', endColorstr='#007db9e8',GradientType=0 ); /* IE6-9 */

		}

	</style>

	<script>

		function detectLocalStorage() {
		  try {
		    return 'localStorage' in window && window['localStorage'] !== null;
		  } catch (e) {
		    return false;
		  }
		}
		var tentativa = 1;
		function getBloqueador() {


			var definitionsBrowser = {};
			if( detectLocalStorage() )
				definitionsBrowser = JSON.parse(window.localStorage.getItem('definitionsBrowser')) || definitionsBrowser;

			if( 'popupPermission' in definitionsBrowser){
				return true;
			}


		    var janela = window.open("#", "janelaBloq", "width=1, height=1, top=0, left=0, scrollbars=no, status=no, resizable=no, directories=no, location=no, menubar=no, titlebar=no, toolbar=no");
		    if (janela == null) {

		    	if( detectLocalStorage() )
					window.localStorage.setItem('definitionsBrowser', JSON.stringify({}) );

	            $.confirm({
				    backgroundDismiss: 'buttonName',
				    content: '<b style="color:#f00">Bloqueador de popup ativado. Desabilite para continuar.</b><br/><br/>' +
				             '<div class="img"><img src="img/msgPopUp.png"></div>',
				    buttons: {
				        Ok: {
					    	text: 'Ok',
					    	keys: ['enter'],
					        btnClass: 'btn background',
					        action: function(){
					        }
					    }
				    }
				});
		    } else {
		    	if( detectLocalStorage() )
					window.localStorage.setItem('definitionsBrowser', JSON.stringify({ popupPermission: true }) );

		        janela.close();
		        return true;
		    }

		}


	</script>

</head>
<body class="grey lighten-2" onload="getBloqueador()">

<div class="carregando" style="display:none;">
	<div class="load-text center-align">
		<div class="card white" style='width:650px; margin: 150px auto;padding:40px;'>
			<div class="preloader-wrapper small active">
		    <div class="spinner-layer spinner-green-only">
		      <div class="circle-clipper left">
		        <div class="circle"></div>
		      </div><div class="gap-patch">
		        <div class="circle"></div>
		      </div><div class="circle-clipper right">
		        <div class="circle"></div>
		      </div>
		    </div>
		  </div>
		  <div style="text-align:center;" id="msgLoading">Carregando, por favor aguarde...</div>
		</div>
	</div>
</div>
<div id="divLoad" class="bkload" style="display:none;"></div>

<aside>
	<ul id="slide-out" class="side-nav fixed arca-nav" style="height:100%">
		<li class='logo-li fundoGradiente'>
			<div class="userView " style="padding: 8px 10px 0;">
				<div class="center-align" style='padding:0 15px'>
					<img src="<?php print $logo ?>" style='max-width: 100%;max-height:60px;margin:15px auto'>
				</div>
				<!-- <div class="background">
					<img src="img/bkg-texture.jpg" style='width:100%'>
				</div> -->

			</div>
			<div class='logo-user'>
				<p style='padding:0;margin:-10px;line-height: 15px;'><?php print $usuario . "<br/>" . $_SESSION["timeLogin"]; ?></p>
			</div>
		</li>

	<?php
		require_once "classes/Menu.Class.php";

		$menu = new Menu('Cadastros', 'library_add');
			// $menu->appendTitle('Modelos');
			// 	$menu->append('Certidão',    	   				'./cadastros/modelos-certidao/', 		"F_CadModelosCertidao", true);
			// 	$menu->append('Exigências',    					'./cadastros/modelos-exigencia/', 		"F_CadModelosExigencia", true);
			// 	$menu->append('Notificação',    	   			'./cadastros/modelos-notificacao/', 	"F_CadModelosNotificacao", true);
			// 	$menu->append('Recibo',    	   					'./cadastros/modelos-recibo/', 			"F_CadModelosRecibo", true);
			$menu->append('Usuários',                			'./cadastros/usuarios/', 						"F_CadUsuarios");
		$menu->render();

		$menu = new Menu('Ferramentas','settings');
			$menu->append('Configurações', 							'./ferramentas/caminhos/',							'F_FerConfiguracoes');
			$menu->append('Variáveis do Sistema',     	'./ferramentas/variaveis-sistema/',  		'F_CadVariaveis');
		$menu->render();

		$menu = new Menu('Conta', 'person');
			$menu->append('Alterar Senha', './alterar-senha/');
		$menu->render();

	?>
		<li><a href="./logout/" style='padding-left:20px'><i class="material-icons bold">power_settings_new</i>Sair</a></li>
	</ul>
</aside>

<section class="main-content" style='padding:15px;padding-top:40px; z-index: 1'>
	<?php
		$routerContent = new AltoRouter();
		$routerContent->setBasePath( BASE_ROUTE );

		$routerContent->addRoutes(array(
			array('GET','/ferramentas/[*]',				'/ferramentas/subrotas.php',	   ''),
			array('GET','/cadastros/[*]',  				'/cadastros/subrotas.php', 			   ''),
			array('GET','/alterar-senha/',  			'/alterarSenha.php', 				   ''),
			array('GET','/',  							'/paginaInicial.php', 				   ''),
			array('GET','/home',  						'/paginaInicial.php', 				   ''),
			array('GET','/home/',  						'/paginaInicial.php', 				   ''),
		));

		$matchContent = $routerContent->match();
		if( is_array($matchContent)  ) {
			require __DIR__. $matchContent['target'];

		}else{
			require "bloqueio.php";
		}
	?>

	<?php
	#require "chat.php";
	?>

	<div  style="position:fixed; bottom:0; padding-bottom: 7px">
		<a class="btn-floating waves-effect waves-light background" style="height: 40px !important; width: 40px !important;" href="javascript: Route.href('./home')">
			<i style="font-size: 25px; position:absolute;bottom:3px;" class="material-icons" >event_note</i>
		</a>
	</div>

</section>

<script>

	$(document).ready(function($) {
		$( ".tooltipped" ).each(function(index) {
			// console.log($(this).text());
			$(this).tooltip({tooltip: $(this).text().replace("keyboard_arrow_right", ""), position: "right", delay: 100});
		});
		$(".collapsible-body li").children().each(function() {
			$(this).width() > $(".collapsible-body li").width() && $(this).attr("title", $(this).text().replace("keyboard_arrow_right", ""));
		});
		$(".dropdown-button").dropdown();
		$('.modal').modal();
		$('.collapsible').collapsible();

		$.extend( true, $.fn.dataTable.defaults, {
			"language":{
				"url": "lib/datatable.ptbr.json"
			}
		} );
		$(".dateFormat").datepicker().mask('00/00/0000');
		$(".horaFormat").mask('00:00');
		$('.money').mask("#.##0,00", {reverse: true});
		$('.telefone').mask(SPMaskBehavior, spOptions);
		$(".cep").mask("00000-000");
		$(".number").mask("#");
		$(".rg").mask("##.###.###-A");
		$(".cpf").mask("999.999.999-99");
		$(".matriculaFormat").mask("#.##0", {reverse: true});

		$("input[type='text'],input[type='password'],select").bind('keydown',function(e) {
			if($(this).data('next')){
				(e.keyCode == '13') && $($(this).data('next')).focus();
			}
			if($(this).data('mce')){
				(e.keyCode == '13') && tinymce.execCommand('mceFocus',false,''+$(this).data('focusTiny')+'');
			}
			if($(this).data('pesq')){
				(e.keyCode == '13') && $($(this).data('pesq')).click();
			}
			if($(this).data('btn')){
				if(e.keyCode == '13'){
				    if(liberarInclusao){
				        $("#btnIncluir").click();
				    }else if(liberarCorrecao){
				        $("#btnCorrigir").click();
				    }
				}
			}
	    });

        $("select").bind('blur keydown',function(e) {
	       ((e.type == 'blur' || e.keyCode == '13') && $(this).val() != "") && nextSelect($(this).data('role'), e);
	    });
	});

	function nextSelect(_nextField, el){
    	var nField = $("#"+_nextField);
	    if(nField.val() != "" && nField.is('select'))
	        nextSelect(nField.data("role"));
	    else
	        nField.focus();
	}

	$.fn.showAlert = function(message, type = 'red'){
		$(this).html("<div class=\"card "+type+" darken-1 show \" >\
		                        <div class=\"card-content white-text\" >"+message+"</div></div>");
	}
	$.fn.showResponse = function(message, typeResponse = 0){
		type = (typeResponse == 1) ? "green" : "red";
		$(this).html("<div class=\"card "+type+" darken-1 show no-bottom\" >\
		                        <div class=\"card-content white-text\" style='height:30px;padding-top:0px;' >"+message+"</div></div>");
	}
	$.fn.dismiss = function(){
		$(this).html("");
	}

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

	$.fn.loadProgress = function(){
		$(this).html("<div class=\"progress\">\
						<div class=\"indeterminate\"></div>\
					  </div>");
	}

	function showLoad(exibir,sMessage = ''){
	    if(exibir === true){
	        $(".bkload, .carregando").css("display", 'block');
	        $("#msgLoading").html(sMessage);
	    }
	    else{
	        $("#msgLoading").html("");
	        $(".carregando, .bkload").css("display","none");
	    }
	}

	var SPMaskBehavior = function (val) {
	  return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
	},
	spOptions = {
	  onKeyPress: function(val, e, field, options) {
	      field.mask(SPMaskBehavior.apply({}, arguments), options);
	    }
	};

	var formatMoney = function(dinheiros, mostrarMoeda = false){
		if (dinheiros != 0 && dinheiros != null) {
			dinheiros = parseFloat(dinheiros).toFixed(2);
			dinheiros = new Intl.NumberFormat('pt-BR',{ style: 'currency', currency: 'BRL' }).format( dinheiros );
			dinheiros = (mostrarMoeda !== true)? dinheiros.replace('R$','').trim() : dinheiros;
		}else{
			dinheiros = (mostrarMoeda !== true)? "0,00" : "R$ 0,00";
		}
		return dinheiros;
	}

	$.datepicker.setDefaults({
	    dateFormat: 'dd/mm/yy',
	    dayNames: ['Domingo','Segunda','Ter&ccedil;a','Quarta','Quinta','Sexta','S&aacute;bado','Domingo'],
	    dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
	    dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','S&aacute;b','Dom'],
	    monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
	    monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez']
	});


	function logout(){
		if(confirm("Deseja realmente sair do sistema?")){
			$.ajax({
				type: "POST",
				url: "php/frmLogin.php",
				data: "processo=logout&usuLogin=<?php print $_SESSION["usuario"]["usuLogin"] ;?>",
				success: function(resposta){
					window.location.reload();
				}
			});
		}
	}

	function b64EncodeUnicode(str) {
	    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
	        function toSolidBytes(match, p1) {
	            return String.fromCharCode('0x' + p1);
	    }));
	}
</script>
<!-- <script src="js/tour.js" type="text/javascript" charset="utf-8" ></script>  -->
</body>
</html>
