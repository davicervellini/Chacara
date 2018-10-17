<?php
	require_once "config.php";
	require_once "soap/ArcaTDPJ_WS/autoload.php";

	if(!verificarPermissoes('ferEstornoBaixas') ){
		print'<script type="text/javascript">
				Route.href( "bloqueio" );
			  </script>';
	}
	$corrigir = (verificarPermissoes('ferEstornoBaixas','corrigir')) ? "onClick='liberarManutencao()'":"disabled";
	$excluir  = (verificarPermissoes('ferEstornoBaixas','excluir'))  ? "onClick='excluirBaixa()'"     :"disabled";
?>
<style>
	.padroniza-select{
	    top: -19px!important;
	    color: rgba(0,0,0,0.26)!important;
	}	
	/*.b-10{
		margin-bottom: 10px;
	}*/
	.b-10{
		min-height: 35px;
	}
	.card{
		margin: 0;
	}
	.b-10 p {
		font-weight:bold;
		margin: 0;
	}
	.b-10 b:not(.title) {
		font-weight: 300;
	}
	.browser-default{
		height: 30px;
	}
</style>
<div class="container">
	<div class="row" id="estornoBaixas">
		<div id="frmFerEstornoBaixas" class="col l10 offset-l1 white z-depth-1" style='padding-top:10px;'>
			<div class="row center-align">
				<h5>Estorno de Baixas</h5>
			</div>
			
			<div class="row no-bottom">
				<div class="col l3 offset-l1">
				    <label for="tpProtocolo">Setor</label>
					<select id="tpProtocolo" class="browser-default">
				      <option value="" disabled selected>Selecione um setor</option>
				      <option value="1">TD</option>
				      <option value="2">PJ</option>
				      <option value="3">Certidão</option>				      
				    </select>
				</div>
				<div class="input-field col l3">
					<input placeholder="" id="protocolo" name="protocolo" type="text" class="center-align validate">
					<label for="protocolo" data-error="Nenhum resultado encontrado">Protocolo</label>
					
				</div>
			</div>
			
			<div class='row b-10'>
				<div class='col l3 offset-l1'>
					<b>Data da Recepção</b>
					<p id="dtRecepcao"></p>
				</div>
				<div class='col l4'>
					<b>Status</b>
					<p id="status"></p>
				</div>
				<div class='col l3' id="infoRegistro">
					<b>Registrado em</b>
					<p id="dtRegistro"></p>
				</div>
				
			</div>

			<div class='row b-10'>
				<div class='col l9 offset-l1'>
					<b>Apresentante</b>
					<p id="apresentante"></p>
				</div>
			</div>

			<div class='row b-10'>
				<div class='col l9 offset-l1'>
					<b>Natureza</b>
					<p id="natureza"></p>
				</div>
			</div>

			<div class='row b-10'>
				<div class='col l10 offset-l1'>
					<b>Última Baixa</b>
					<p id="ultBaixa"></p>
				</div>
			</div>

			<div class="row ">
				<div class="col l10 offset-l1" id="message"></div>
			</div>
			<div class="row">
				<div class="col l10 offset-l1 center-align">
		     		<!-- <input type="button" id="btnManutencao"  name="btnManutencao"  class=" btn background btnControl" value="Liberar Manutenção"   <?php print $corrigir;?>> -->
		     		<input type="button" id="btnUltimaBaixa" name="btnUltimaBaixa" class=" btn background btnControl" value="Remover Última Baixa" <?php print $excluir;?> style="margin-left:5px">

				</div>
			</div>
			<div id="divLoadResultado"></div>
            <input name='recno' id='recno' type='hidden'>
		</div>
	</div>
</div>
<script src="ferramentas/js/frmFerEstornoBaixas.js?v=<?php print VERSION; ?>" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
	document.title = "Estorno de Baixas";
</script>