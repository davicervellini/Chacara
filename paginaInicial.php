<?php 
	require_once "conexao/ConexaoMySQL.Class.php";
	require_once "classes/autoload.php";

	$usuRecno = $_SESSION["usuario"]["usuRecno"];
	$usuNome  = $_SESSION["usuario"]["usuNome"];
	$recno = (isset($_GET["codigo"]) && $_GET["codigo"] !== "") ? $_GET["codigo"] : "";
	$data = date("d-m-Y");
?>


<style type="text/css">
	body{
		background: url("img/bkg-login.jpg");
		padding:10px;
	}

	.tachado{text-decoration:line-through;}
	.max-height{
		max-height: 200px;
		overflow-x: hidden;
		overflow-y:  auto;
	}
</style>
<div class="container">
	<div class="row">
		<div class="col l12">
			<h5>Seja bem-vindo(a), <b><?php print $usuNome; ?></b>!</h5>

			<?php 

				setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
				date_default_timezone_set('America/Sao_Paulo');
				echo (ucfirst(strftime('%A, %d de %B de %Y', strtotime('today'))));

			?>		
		</div>

	</div>
</div>
<input type="hidden" name="recno" id="recno"   value="<?php print $recno; ?>">
<script src="js/frmPaginaInicial.js"></script>