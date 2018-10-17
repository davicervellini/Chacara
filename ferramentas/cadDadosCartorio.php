<?php
	require_once "config.php";
	require_once "conexao/ConexaoMySQL.Class.php";
	require_once "conexao/ConexaoMySQL.Class.php";
	require_once "classes/autoload.php";

	$dca = new DadosCartorio();
	
	$recno      = $dca->getCodigo();
	$razao      = $dca->getRazao();
	$complRazao = $dca->getCplRazao();
	$fantasia   = utf8_encode( $dca->getFantasia() );
	$tabelionato= $dca->getTabelionato();
	$endereco   = $dca->getEndereco();
	$bairro     = $dca->getBairro();
	$cep        = $dca->getCep();
	$cidade     = utf8_encode( $dca->getCidade() );
	$uf         = $dca->getEstado();
	$telefone   = $dca->getTelefone();
	$email      = $dca->getEmail();
	$site       = $dca->getSite();
	$substituto = $dca->getNomeSubstituto();
	$cargo      = utf8_encode( $dca->getCargoSubstituto() );
	$cnpj       = $dca->getCnpj();
	$horario    = $dca->getHoraFunc();
	$oficial    = $dca->getOfical();
	$cpf    	= $dca->getCpf();

	if(!verificarPermissoes('F_CadDadosCartorio') ){
		print'<script type="text/javascript">
				Route.href( "bloqueio" );
			  </script>';
	}

	$corrigir = (verificarPermissoes('F_CadDadosCartorio','corrigir'))? "onClick='corrigirDadosCartorio()'": "disabled";
?>
<style type="text/css">
	.padroniza-select{
	    top: -19px!important;
	    color: rgba(0,0,0,0.26)!important;
	}
</style>
<div class="container">
	<div class="row" id="DadosCartorio">
		<form id="frmDadosCartorio" class="col l12 white z-depth-1" style='padding-top:10px'>
			<div class="row no-bottom center-align">
				<h5>Cadastro de Dados do Cartório</h5>
			</div>
			
			<div class="row no-bottom" style="margin-bottom: 5px;">
				<div class="input-field col l10 offset-l1">
					<input placeholder="" id="razao" type="text" value="<?php print $razao;?>" data-next="#complRazao">
					<label for="razao">Razão Social <span style="color:#f00;">*</span></label>
				</div>
			</div>			
			<div class="row no-bottom" style="margin-bottom: 5px;">
				<div class="input-field col l10 offset-l1">
					<input placeholder="" id="complRazao" type="text" value="<?php print $complRazao;?>" data-next="#fantasia">
					<label for="complRazao">Complemento da Razão Social <span style="color:#f00;">*</span></label>
				</div>
			</div>
			<div class="row no-bottom" style="margin-bottom: 5px;">
				<div class="input-field col l8 offset-l1">
					<input placeholder="" id="fantasia" type="text" value="<?php print $fantasia;?>" data-next="#tabelionato">
					<label for="fantasia">Fantasia <span style="color:#f00;">*</span></label>
				</div>
				<div class="input-field col l2">
					<input placeholder="" id="tabelionato" type="text" value="<?php print $tabelionato;?>" data-next="#cep">
					<label for="tabelionato">Tabelionato <span style="color:#f00;">*</span></label>
				</div>
			</div>
			<div class="row no-bottom" style="margin-bottom: 5px;">
				<div class="input-field col l2 offset-l1">
					<input placeholder="" id="cep"  class="cep" type="text" value="<?php print $cep;?>" data-next="#endereco">
					<label for="cep">CEP <span style="color:#f00;">*</span></label>
				</div>
				<div class="input-field col l4">
					<input placeholder="" id="endereco" type="text" value="<?php print $endereco;?>" data-next="#bairro">
					<label for="endereco">Endereço <span style="color:#f00;">*</span></label>
				</div>
				<div class="input-field col l4">
					<input placeholder="" id="bairro" type="text" value="<?php print $bairro;?>" data-next="#cidade">
					<label for="bairro">Bairro <span style="color:#f00;">*</span></label>
				</div>
			</div>

			<div class="row no-bottom" style="margin-bottom: 5px;">
				<div class="input-field col l4 offset-l1">
					<input placeholder="" id="cidade" type="text" value="<?php print $cidade;?>" data-next="#uf">
					<label for="cidade">Cidade <span style="color:#f00;">*</span></label>
				</div>
				<div class="input-field col l1">
					<select id="uf">
				      <option value="" disabled selected>UF</option>
					  <?php

					  	foreach ($sys->getUfs() as $key => $value) {

					  		$selected = ($uf == $value) ? "selected"  : ""; 
					  		print "<option value=\"$value\" $selected>$value</option>";
					  	}

					  ?>
				    </select>
				    <label for="uf" class="padroniza-select">Estado <span style="color:#f00;">*</span></label>
				</div>
				<div class="input-field col l2">
					<input placeholder="" id="telefone" class="telefone" type="text" value="<?php print $telefone;?>" data-next="#email">
					<label for="telefone">Telefone <span style="color:#f00;">*</span></label>
				</div>
				<div class="input-field col l3">
					<input placeholder="" id="email" type="text" value="<?php print $email;?>" data-next="#site" style='text-transform: lowercase'>
					<label for="email">Endereço de Email <span style="color:#f00;">*</span></label>
				</div>
			</div>
			<div class="row no-bottom" style="margin-bottom: 5px;">
				<div class="input-field col l10 offset-l1">
					<input placeholder="" id="site" type="text" value="<?php print $site;?>" data-next="#oficial">
					<label for="site">Site</label>
				</div>
			</div>
			<div class="row no-bottom" style="margin-bottom: 5px;">
				<div class="input-field col l7 offset-l1">
					<input placeholder="" id="oficial" type="text" value="<?php print $oficial;?>" data-next="#cpf">
					<label for="oficial">Oficial<span style="color:#f00;">*</span></label>
				</div>
				<div class="input-field col l3">
					<input placeholder="" id="cpf" class="cpf" type="text" value="<?php print $cpf;?>" data-next="#substituto">
					<label for="cpf">CPF Oficial<span style="color:#f00;">*</span></label>
				</div>
			</div>
			<div class="row no-bottom" style="margin-bottom: 5px;">
				<div class="input-field col l10 offset-l1">
					<input placeholder="" id="substituto" type="text" value="<?php print $substituto;?>" data-next="#cargo">
					<label for="substituto">Substituto<span style="color:#f00;">*</span></label>
				</div>
			</div>
			<div class="row no-bottom" style="margin-bottom: 5px;">
				<div class="input-field col l10 offset-l1">
					<input placeholder="" id="cargo" type="text" value="<?php print $cargo;?>" data-next="#cnpj">
					<label for="cargo">Cargo <span style="color:#f00;">*</span></label>
				</div>
			</div>
			<div class="row no-bottom" style="margin-bottom: 5px;">
				<div class="input-field col l3 offset-l1">
					<input placeholder="" id="cnpj" class="cnpj" type="text" value="<?php print $cnpj;?>" data-next="#horario">
					<label for="cnpj">CNPJ <span style="color:#f00;">*</span></label>
				</div>
				<div class="input-field col l4 offset-l1">
					<input placeholder="" id="horario" type="text" value="<?php print $horario;?>" data-btn="1">
					<label for="horario">Horário de Funcionamento</label>
				</div>
			</div>

			<div class="row">
				<div class="col l10 offset-l1">
					<label style="color: #BBBBBB"> <span style="color:#f00;">*</span> Campos obrigatórios</label>
				</div>
			</div>
			<div class="row">
				<div class="col l10 offset-l1" id="message"></div>
			</div>
			<div class="row">
				<div class="col l10 offset-l1 center-align">
		     		<input type="button" id="btnCorrigir" name="btnCorrigir" class="btn background btnControl" <?php print $corrigir;?> style="margin-left:5px" value="Editar">
				</div>
			</div>
            <input name='codDCA' id='codDCA' type='hidden' value="<?php print $recno;?>"></input>
		</form>
	</div>
</div>
<script src="ferramentas/js/frmCadDadosCartorio.js?v=<?php print VERSION; ?>" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
	document.title = "Cadastro de Dados de Cartório";
</script>