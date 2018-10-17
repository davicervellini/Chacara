<?php
	require_once "config.php";
	require_once "conexao/ConexaoMySQL.Class.php";
	require_once "conexao/ConexaoMySQL.Class.php";
	require_once "classes/autoload.php";

	$conn = new ConexaoMySQL("arcatdpj");
	$sql = "select * from config";
	$qry = $conn->prepare($sql);
	$qry->execute();

	if ($qry->rowCount() > 0)
	{
		$lin = $qry->fetch();
		$recno 	 	 = $lin["RECNO"];
		$rotaBase 	 = $lin["CFG_BASE_ROUTE"];
		$ipServer 	 = $lin["CFG_IP_SERVER"];
		$urlChat 	 = $lin["CFG_SOCKET_CHAT_URL"];
		$wsISAPI 	 = $lin["CFG_URL_WS"];
		$codCliente	 = $lin["CFG_COD_CLIENTE"];
		$serventiaRI = $lin["CFG_SERVENTIA_RI"];
		$serventiaTD = $lin["CFG_SERVENTIA_TD"];
		$serventiaPJ = $lin["CFG_SERVENTIA_PJ"];
	}

	if(!verificarPermissoes('F_CadVariaveis') ){
		print'<script type="text/javascript">
				Route.href( "bloqueio" );
			  </script>';
	}

	$corrigir = (verificarPermissoes('F_CadVariaveis','corrigir'))? "onClick='corrigirDadosVariaveis()'": "disabled";
?>
<style type="text/css">
	.padroniza-select{
	    top: -19px!important;
	    color: rgba(0,0,0,0.26)!important;
	}
</style>
<div class="container">
	<div class="row" id="VariaveisSistema">
		<form id="frmVariaveisSistema" class="col l9 offset-l1 white z-depth-1" style='padding-top:10px'>
			<div class="row no-bottom center-align">
				<h5>Cadastro de Variáveis do Sistema</h5>
			</div>
			
			<div class="row no-bottom" style="margin-bottom: 5px; ">				
				<div class="input-field col l2 offset-l1">
					<input placeholder="" id="ipServer" type="text" value="<?php print $ipServer;?>" data-next="#ano" class="center-align obrigatorio">
					<label for="ipServer">IP Servidor <span style="color:#f00;">*</span></label>
				</div>						
			</div>					
			<div class="row no-bottom" style="margin-bottom: 5px;">
				<div class="input-field col l10 offset-l1">
					<input placeholder="" id="wsISAPI"  class="wsISAPI obrigatorio" type="text" value="<?php print $wsISAPI;?>" data-next="#wsRegEletr">
					<label for="wsISAPI">Web Service ISAPI <span style="color:#f00;">*</span></label>
				</div>
			</div>
			<div class="row no-bottom" style="margin-bottom: 5px;">
				<div class="input-field col l10 offset-l1">
					<input placeholder="" id="urlChat" type="text" value="<?php print $urlChat;?>" data-next="#wsISAPI" class="obrigatorio">
					<label for="urlChat">URL Chat <span style="color:#f00;">*</span></label>
				</div>
			</div>
			<div class="row no-bottom" style="margin-bottom: 5px;">
				<div class="input-field col l2 offset-l1">
					<input placeholder="" id="codCliente" type="text" value="<?php print $codCliente;?>" data-next="#serventiaRI">
					<label for="codCliente">Código do Cliente </label>
				</div>
				<div class="input-field col l2">
					<input placeholder="" id="serventiaRI" type="text" value="<?php print $serventiaRI;?>" data-next="#serventiaTD">
					<label for="serventiaRI">Serventia RI </label>
				</div>
				<div class="input-field col l2">
					<input placeholder="" id="serventiaTD" type="text" value="<?php print $serventiaTD;?>" data-next="#serventiaPJ">
					<label for="serventiaTD">Serventia TD </label>
				</div>
				<div class="input-field col l2">
					<input placeholder="" id="serventiaPJ" type="text" value="<?php print $serventiaPJ;?>">
					<label for="serventiaPJ">Serventia PJ </label>
				</div>
			</div>			

			<div class="row">
				<div class="col l10 offset-l1">
					<label style="color: #BBBBBB"> <span style="color:#f00;">*</span> Campos obrigatórios</label>
				</div>
			</div>
			<div class="row">
				<div class="col l9 offset-l1" id="message"></div>
			</div>
			<div class="row">
				<div class="col l9 offset-l1 center-align">
		     		<input type="button" id="btnCorrigir" name="btnCorrigir" class="btn background btnControl" <?php print $corrigir;?> style="margin-left:5px" value="Editar">
				</div>
			</div>
            <input name='codConfig' id='codConfig' type='hidden' value="<?php print $recno;?>"></input>
		</form>
	</div>
</div>
<!-- <input type="hidden" name="anoVelho" id="anoVelho" value="<?php print $ano;?>"> -->
<script src="ferramentas/js/frmCadVariaveisSistema.js?v=<?php print VERSION; ?>" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
	document.title = "Cadastro de Variáveis do Sistema";
</script>