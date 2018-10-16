<?php 
	$usuRecno = $_SESSION["usuario"]["usuRecno"];
?>
<style type="text/css">
	body{
		background: url("img/bkg-login.jpg");
		padding:10px;
	}
</style>
<div class="container">
	<div class="row">
		<form class="col l10 white z-depth-1 offset-l1" style='border-top: 1px solid #ccc;min-height:450px;padding:15px'>
			<h5 class="center-align">Alterar Senha</h5>
			<div class="row" style="padding-top:10px">
				<div class="input-field col l8 offset-l2">
					<input id="usuSenhaOld" type="password" data-next='#usuSenha' placeholder="">
					<label for="usuSenhaOld">Senha Antiga</label>
				</div>
			</div>
			<div class="row">
				<div class="input-field col l8 offset-l2">
					<input id="usuSenha" type="password" data-next='#usuNovaSenha' placeholder="">
					<label for="usuSenha">Nova Senha</label>
				</div>
			</div>
			<div class="row">
				<div class="input-field col l8 offset-l2">
					<input id="usuNovaSenha" type="password" placeholder="" data-pesq='#btnCorrigir'>
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
<script src="js/frmAlterarSenha.js?v=<?php print VERSION; ?>" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
	document.title = "Alterar Senha";
</script>