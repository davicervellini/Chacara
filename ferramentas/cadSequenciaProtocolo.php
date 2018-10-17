<?php
	require_once "config.php";
	require_once "conexao/ConexaoMySQL.Class.php";
	require_once "conexao/ConexaoMySQL.Class.php";
	require_once "classes/autoload.php";

	$seq = new SequenciaProtocolo();

	$recno          = $seq->getRecno();
    $protOficialTd  = $seq->getPrenotaTd();
    $protOficialPj  = $seq->getPrenotaPj();
    $registroTd     = $seq->getRegistroTd();
    $registroPj     = $seq->getRegistroPj();
    $fatura         = $seq->getFatura();
    $certificado    = $seq->getCertificado();
    $matricula      = $seq->getMatricula();
	$pedCertidao    = $seq->getCertidao();

	$corrigir = (verificarPermissoes('F_CadSequenciaProtocolo','corrigir'))? "onClick='corrigirSequenciaProtocolo()'": "disabled";
?>
<div class="container">
	<div class="row" id="SequenciaProtocolo">
		<form id="frmSequenciaProtocolo" class="col l6 offset-l3 white z-depth-1" style='padding-top:10px;min-height: 300px;'>
			<div class="row no-bottom center-align">
				<h5>Sequência de Protocolo</h5>
			</div>
			<br/>
			
			<div class="row no-bottom" style="margin-bottom: 5px;">
				<div class="input-field col l5 offset-l1">
					<input placeholder="" id="protOficialTd" type="text" value="<?php print $protOficialTd;?>" data-next="#protOficialPj">
					<label for="protOficialTd">Protocolo do TD</label>
				</div>
                <div class="input-field col l5">
					<input placeholder="" id="protOficialPj" type="text" value="<?php print $protOficialPj;?>" data-next="#registroTd">
					<label for="protOficialPj">Protocolo do PJ</label>
				</div>
			</div>
			<div class="row no-bottom" style="margin-bottom: 5px;">
                <div class="input-field col l5 offset-l1">
                    <input placeholder="" id="registroTd" type="text" value="<?php print $registroTd;?>" data-next="#registroPj">
                    <label for="registroTd">Registro do TD</label>
                </div>
                <div class="input-field col l5">
                    <input placeholder="" id="registroPj" type="text" value="<?php print $registroPj;?>" data-next="#pedCertidao">
                    <label for="registroPj">Registro do PJ</label>
                </div>
			</div>
            <div class="row no-bottom" style="margin-bottom: 5px;">
				<div class="input-field col l5 offset-l1">
					<input placeholder="" id="pedCertidao" type="text" value="<?php print $pedCertidao;?>" data-next="#fatura">
					<label for="pedCertidao">Pedido de Certidão</label>
				</div>
                <div class="input-field col l5">
					<input placeholder="" id="fatura" type="text" value="<?php print $fatura;?>" data-next="#certificado">
					<label for="fatura">Fatura</label>
				</div>
			</div>
            <div class="row no-bottom" style="margin-bottom: 5px;">
				<div class="input-field col l5 offset-l1">
					<input placeholder="" id="certificado" type="text" value="<?php print $certificado;?>" data-next="#matricula">
					<label for="certificado">Certificado</label>
				</div>
                <div class="input-field col l5">
					<input placeholder="" id="matricula" type="text" value="<?php print $matricula;?>" data-btn="1">
					<label for="matricula">Registro de Matrícula</label>
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
            <input name='codSeq' id='codSeq' type='hidden' value="<?php print $recno;?>"></input>
		</form>
	</div>
</div>
<script src="ferramentas/js/frmCadSequenciaProtocolo.js?v=<?php print VERSION; ?>" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
	document.title = "Cadastro de Sequência de Protocolo";
</script>