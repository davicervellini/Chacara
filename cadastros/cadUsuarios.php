<?php 
	require_once "conexao/ConexaoMySQL.Class.php";
	require_once "classes/autoload.php";

	$connMySQL = new ConexaoMySQL();
	$sys = new Sistema;
	$usu = new Usuario;
	
	$usuCod = (isset($_GET["codigo"]) && $_GET["codigo"] !== "") ? $_GET["codigo"] : "";
	$resp   = (isset($_GET["msg"])    && $_GET["msg"] !== "")    ? $_GET["msg"]    : "" ;

	$sqlUsuario = "SELECT RECNO, USU_NOME, USU_LOGIN, USU_EMAIL, USU_CARGO, USU_ADMIN FROM usuarios WHERE USU_CODIGO = :USU_CODIGO";
	$qryUsuario = $connMySQL->prepare($sqlUsuario);
	$qryUsuario->bindParam(':USU_CODIGO', $usuCod, PDO::PARAM_STR);      
	$qryUsuario->execute();

	$row = $qryUsuario->fetch(PDO::FETCH_ASSOC);

	$usuNome  = $row["USU_NOME"]   ?? '';
	$usuLogin = $row["USU_LOGIN"]  ?? '';
	$usuEmail = $row["USU_EMAIL"]  ?? '';
	$cargo    = $row["USU_CARGO"]  ?? '';
	$usuAdmin = $row["USU_ADMIN"]  ?? '';
	$usuRecno = $row["RECNO"]      ?? '';

	$displayAdmin = ($usuAdmin == 1) ? "display:none;": "";

	if(!verificarPermissoes('F_CadUsuarios') ){
		print'<script type="text/javascript">
				Route.href( "bloqueio" );
			  </script>';
	}

    $incluir  = (verificarPermissoes('F_CadUsuarios','incluir') )? "onClick='incluirUsuarios(1)'" : "disabled"; 
	$corrigir = (verificarPermissoes('F_CadUsuarios','corrigir'))? "onClick='corrigirUsuarios()'" : "disabled";
	$excluir  = (verificarPermissoes('F_CadUsuarios','excluir') )? "onClick='excluirUsuarios()'" : "disabled";
	$permissoes = ($corrigir == "disabled") ? "disabled" : "onClick='modalPermissoes(0)'";

?>
<div class="container">
	<div class="row">
		<form id="frmCadastros" class="col l12 white z-depth-1" style='padding-top:10px' enctype="multipart/form-data" action="cadastros/php/frmCadUsuarios.php" method="post">
			<div class="row no-bottom center-align">
				<h5>Cadastro de Usuários</h5>
			</div>
			<div class="row">
				<div class="input-field col l10 offset-l1">
					<i class="material-icons prefix">assignment</i>
					<input disabled placeholder="" id="usuNome" type="text" value="<?php print $usuNome; ?>" data-next="#usuLogin">
					<label id="lblNome" for="usuNome">Nome</label>
				</div>
			</div>
			<div class="row">
				<div class="input-field col l10 offset-l1">
					<i class="material-icons prefix">account_circle</i>
					<input disabled placeholder="" id="usuLogin" type="text" value="<?php print $usuLogin; ?>" data-next="#usuEmail">
					<label id="lblLogin" for="usuLogin">Login</label>
				</div>
			</div>
			<div class="row">
				<div class="input-field col l10 offset-l1">
					<i class="material-icons prefix">mail</i>
					<input disabled placeholder="" id="usuEmail" type="text" value="<?php print $usuEmail; ?>" data-next="#cargo" style='text-transform: lowercase'>
					<label id="lblEmail" for="usuEmail">Email</label>
				</div>
			</div>

			<div class="row">
				<div class="col l4 offset-l1" style="padding-left:20px">
					<?php
						$checked = ($usuAdmin != '' && $usuAdmin > 0) ? 'checked' : '' ;
						if( $_SESSION["usuario"]["usuAdmin"] ){
							print"<input ".$checked." disabled type='checkbox' id='usuAdmin' name='usuAdmin' class='checkBtn' />
			    					<label for='usuAdmin'>Administrador</label>";
						}
					?>
				</div>
				<div class="col l6">
					<div id="permissoes">
						<a style="color:#000;line-height:40px; height:40px; <?php print $displayAdmin; ?> " <?php print $permissoes ?> class='right btn white-text background' id="btnModalPermissoes">Permissões de Acesso</a>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col l10 offset-l1">
					<label style="color: #BBBBBB">Obs: No primeiro acesso a senha é o mesmo que o login.</label>
				</div>
			</div>
			<div class="row ">
				<div class="message col l10 offset-l1">
					
				</div>
			</div>
			<div class="row">
				<div class="col l10 offset-l1 center-align">
		     		<input type="button" id="btnIncluir"  name="btnIncluir"  <?php print $incluir ?>  class="btn background btnControl"  value="Incluir">
		     		<input type="button" id="btnCorrigir" name="btnCorrigir" <?php print $corrigir ?> class="btn background btnControl"  style="margin-left:5px" value="Corrigir">	
		     		<input type="button" id="btnExcluir"  name="btnExcluir"  <?php print $excluir ?>  class="btn background btnControl"  style="margin-left:5px" value="Excluir">
		     		<?php 
		     			if($_SESSION["usuario"]["usuAdmin"] == 1){
		     				print "<input type=\"button\" id=\"btnReset\" name=\"btnReset\" class=\"btn background btnControl\" style=\"margin-left:5px;\" onClick=\"resetarSenha()\" value=\"Resetar Senha\" >";
		     			}
		     		?>
				</div>
			</div>
			<div class="row">
				<div class="col l10 offset-l1">
					<table id="gridUsuarios" class='bordered striped display compact dataTablePrint highlight pointer'></table>
				</div>
			</div>
			<!-- Modal Structure -->
			<div id="modalPermissoes" class="modal" style="width:70%;height:100%">
				<div class="modal-content">
					<div class="row">
						<div class="col l12">
							<a class='btn btn-flat right' onclick="fecharModal()"><i class="material-icons">close</i></a>
							<div>
								<h5>Liberar permissões de acessos</h5>
							</div>
							<div class="row">
								<div id="verificaPermissoes" class="input-field col l12" style="padding:10px">		
								</div>
							</div>
							<div class='center-align'><a id="btnFechar" name="btnFechar"   class="waves-effect waves-light btn background" onClick="fecharModal()">Fechar</a></div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<input type="hidden" name="usuRecno"    id="usuRecno"    value="<?php print $usuRecno; ?>">
<input type="hidden" name="usuCodigo"   id="usuCodigo"   value="<?php print $usuCod; ?>">
<input type="hidden" name="hidUsuLogin" id="hidUsuLogin" value="<?php print $usuLogin; ?>">
<input type='hidden' name='msg'    		id='msg'         value="<?php print $resp;?>"></input>
<script src="cadastros/js/frmCadUsuarios.js?v=<?php print VERSION; ?>" type="text/javascript"></script>
<script type="text/javascript">
	document.title = "Cadastro de Usuários";
</script>