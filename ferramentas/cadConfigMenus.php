<?php
	require_once "config.php";
	require_once "conexao/ConexaoMySQL.Class.php";
	require_once "conexao/ConexaoMySQL.Class.php";
	require_once "classes/autoload.php";

	$codigo = (isset($_GET["codigo"]) && $_GET["codigo"] !== "") ? $_GET["codigo"] : "" ;	
	$men    = new ConfigMenus($codigo);

	$menu = $form = $descricao = $grupo = $recno = "";

	if($codigo != ''){
		
		$menu      = $men->getMenu();
		$form      = $men->getForm();
		$descricao = $men->getDescricao();
		$grupo     = $men->getGrupo();
		$recno     = $codigo;
	}

	// if(!verificarPermissoes('F_CadConfigMenus') ){
	// 	print'<script type="text/javascript">
	// 			Route.href( "bloqueio" );
	// 		  </script>';
	// }
	$incluir  = (true) ? "onClick='incluirConfigMenus()'" : "disabled"; 
	$corrigir = (true)? "onClick='corrigirConfigMenus()'": "disabled";
	$excluir  = (true) ? "onClick='excluirConfigMenus()'" : "disabled";

?>
<style>
	@media print{
		table{
			border: 1px solid #666;
			font-family: "Calibri";
			width: 100%
		}
		table th{
			background-color:#e6e6e6;
			border-right:1px solid #666;
			border-bottom:1px solid #666;
		}
		table td{
			border-right:1px solid #666;
			border-bottom:1px solid #666;
			height:25px;
			padding:1px;
		}
		.dt-print-view h1{
			color: #000;
			font-size:22px;
			font-family:"Calibri";
		}
	}
	#frmCadDoadores .row {margin-bottom: 5px;}
	label {font-size:15px !important;}
	#frmCadDoadores .select-dropdown{margin-bottom:5px;}
	input {height:35px !important;}
	.dropdown-content li>a, .dropdown-content li>span {font-size: 14px;}
	.dropdown-content li{min-height:30px;}
	.dropdown-content li > span{padding: 7px 8px;color:#000;}
	.tbl-telefone td { padding: 0 0 0 5px; }
	.input-field label{color: #9e9e9e;}
	#descricao:focus{
		border-bottom: 1px solid #1976d2;
 		box-shadow: 0 1px 0 0 #1976d2;
	}
	.ui-datepicker-year{
    	display:none;
	}
	/*.message-height{
		min-height: 50px;
	}*/
</style>
<div class="container">
	<div class="row" id="ConfigMenus">
		<form id="frmCadConfigMenuss" class="col l12 white z-depth-1" style='padding-top:10px'>
			<div class="row no-bottom center-align">
				<h5>Cadastro de Configuração de Menus</h5>
			</div>
			<div class="row">
				<div class="input-field col l10 offset-l1">
					<input placeholder="" id="menu" type="text" value="<?php print $menu;?>" data-next="#form">
					<label for="menu">Menu <span style="color:#f00;">*</span></label>
				</div>

				<div class="input-field col l10 offset-l1">
					<input placeholder="" id="form" type="text" value="<?php print $form;?>" data-next="#descricao">
					<label for="form">Formulário <span style="color:#f00;">*</span></label>
				</div>

				<div class="input-field col l10 offset-l1">
					<input placeholder="" id="descricao" type="text" value="<?php print $descricao;?>" data-next="#grupo">
					<label for="descricao">Descrição <span style="color:#f00;">*</span></label>
				</div>

				<div class="col l10 offset-l1">
					<label for="grupo">Módulo <span style="color:#f00;">*</span></label>
					<select id="grupo" class="browser-default" data-btn="1">
						<option value="" disabled selected>Escolha a opção</option>
						<?php
							foreach ($men->listGrupos() as $key) {
								$selected = ($grupo == $key['MEN_GRUPO']) ? "selected"  : ""; 
								print "<option value='".$key['MEN_GRUPO']."' data-ordem='".$key['MEN_GRUPO_ORDEM']."' ".$selected."> ".$key['MEN_GRUPO']."</option>";
							}
						?>
					</select>
				</div>
			</div>

			<div class="row">
				<div class="col l10 offset-l1">
					<label style="color: #BBBBBB"><span style="color:#f00;">*</span> Campos obrigatórios</label>
				</div>
			</div>
			<div class="row">
				<div class="col l10 offset-l1 center-align">
		     		<a id="btnIncluir"  name="btnIncluir"  class="waves-effect waves-light btn background btnControl" <?php print $incluir;?> > Incluir</a>
		     		<a id="btnCorrigir" name="btnCorrigir" class="waves-effect waves-light btn background btnControl" <?php print $corrigir;?> style="margin-left:5px">Corrigir</a>
		     		<a id="btnCorrigir" name="btnCorrigir" class="waves-effect waves-light btn background btnControl" <?php print $excluir;?> style="margin-left:5px">Excluir</a>
		     		<a id="btnLimpar"   name="btnLimpar"   class="waves-effect waves-light btn background btnControl" onClick="_redirect()" style="margin-left:5px">Limpar</a>
				</div>
			</div>
			<div class="row no-bottom message-height">
				<div class="col l10 offset-l1" id="message"></div>
			</div>
			<div class="row" style='min-height:250px;margin-bottom:50px'>
				<div class="col l10 offset-l1">
					<table id="gridConfigMenus" class='bordered striped display compact dataTablePrint highlight pointer'></table>
				</div>
			</div>
            <input name='codConfigMenus' id='codConfigMenus' type='hidden' value="<?php print $recno;?>"></input>
		</form>
	</div>
</div>


<!-- Modal Structure -->
  <div id="modalAcesso" class="modal" style='width:500px'>
    <div class="modal-content">
      	<h5>Acesso à tela de configuração</h5>
      	<Br/>
		<div class="input-field col l10 offset-l1">
			<input placeholder="" id="passwordAcesso" type="password" value="">
			<label for="form">Senha de acesso</label>
		</div>
		<div class="row no-bottom">
			<div class="col l12 right-align">
				<a id="btnContinuar"   name="btnContinuar"   class="waves-effect waves-light btn background " onclick="continuarAcesso()" style="margin-left:5px">Continuar</a>		
			</div>
		</div>
		
    </div>
  </div>


<input type="hidden" id="pwm" value="<?php print $_SESSION["AcessoConfigMenus"];?>" >
<script src="ferramentas/js/frmCadConfigMenus.js?v=<?php print VERSION; ?>" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
	document.title = "Cadastro de Configuração de Menus";
</script>