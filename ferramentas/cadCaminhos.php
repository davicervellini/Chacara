<?php
	require_once "config.php";
	require_once "conexao/ConexaoMySQL.Class.php";
	require_once "conexao/ConexaoMySQL.Class.php";
	require_once "classes/autoload.php";

	$connMYSQL = new ConexaoMySQL();

	$cam = new Caminhos('SQLServer');
	$mod = new ModelosRecibo();
	$conf = new Config($connMYSQL);

	$recno             	= $cam->getCodigo();
	$titulos       	    = $cam->getTitulos();
	$civil           	= $cam->getCivil();
    $certificados       = $cam->getCertificados();
	$comprovante      	= $cam->getComprovante();
	$documentos         = $cam->getDocumentos();
	$protocolo          = $cam->getProtocolo();
	$nota            	= $cam->getNota();
	$prenotado 	        = $cam->getPrenotado();
	$indicadorReal     	= $cam->getIndicadorReal();
	$indicadorPessoal  	= $cam->getIndicadorPessoal();

	$modMatricula  		= $conf->getModMatricula();
	$modCertidao  		= $conf->getModCertidao();
	$modExame  			= $conf->getModExame();
	$modAbertura  		= $conf->getModAbertura();
	$modEncerramento  	= $conf->getModEncerramento();
	$logo  				= $conf->getLogo();
	$logoRelatorio		= $conf->getLogoRelatorio();

	if(!verificarPermissoes('F_FerConfiguracoes') ){
		print'<script type="text/javascript">
				Route.href( "bloqueio" );
			  </script>';
	}

	$corrigir = (verificarPermissoes('F_FerConfiguracoes','corrigir'))? "": "disabled";
?>
<style>
	.btn-color{
		/*padding:;*/
		text-align: center;
		width:60px;
		padding: 0;
		margin-top:10px;
	}
	.tamanho-fonte{
		font-size: 33px;
	}
</style>
<div class="row">
	<div class="col m5 xl6">
		<div>
			<div class="row" >
				<div class="card-panel white z-depth-1 " id="painelCaminhos" style='min-height: 900px'>
					<div class="row">
						<div class="col l12  xl12" style='padding-top:10px;min-height:470px'>
							<div class="row center-align">
								<h5>Caminhos</h5>
							</div>
							<div class="row no-bottom">
								<div class="input-field col l12 xl10 offset-xl1 ">
									<input placeholder="" id="titulos" type="text" value="<?php print $titulos;?>" data-next="#oficios">
									<label for="titulos">Títulos e Documentos</label>
								</div>
							</div>			
							<div class="row no-bottom">
								<div class="input-field col l12 xl10 offset-xl1 ">
									<input placeholder="" id="civil" type="text" value="<?php print $civil;?>" data-next="#documentos">
									<label for="civil">Cilvil de Pessoas Jurídicas</label>
								</div>
							</div>			
							<div class="row no-bottom">
								<div class="input-field col l12 xl10 offset-xl1 ">
									<input placeholder="" id="certificados" type="text" value="<?php print $certificados;?>" data-next="#sinalPublico">
									<label for="certificados">Certificados de Notificação</label>
								</div>
							</div>			
							<div class="row no-bottom">
								<div class="input-field col l12 xl10 offset-xl1 ">
									<input placeholder="" id="comprovante" type="text" value="<?php print $comprovante;?>" data-next="#livro2">
									<label for="comprovante">Comprovante da AR</label>
								</div>
							</div>			
							<div class="row no-bottom">
								<div class="input-field col l12 xl10 offset-xl1 ">
									<input placeholder="" id="documentos" type="text" value="<?php print $documentos;?>" data-next="#livro3">
									<label for="documentos">Documentos Devolvidos</label>
								</div>
							</div>			
							<div class="row no-bottom">
								<div class="input-field col l12 xl10 offset-xl1 ">
									<input placeholder="" id="protocolo" type="text" value="<?php print $protocolo;?>" data-next="#fichas">
									<label for="protocolo">Protocolo Oficial</label>
								</div>
							</div>	
							<div class="row no-bottom">
								<div class="input-field col l12 xl10 offset-xl1 ">
									<input placeholder="" id="nota" type="text" value="<?php print $nota;?>" data-next="#indisponibilidade">
									<label for="nota">Nota Devolutiva</label>
								</div>
							</div>			
							<div class="row no-bottom">
								<div class="input-field col l12 xl10 offset-xl1 ">
									<input placeholder="" id="prenotado" type="text" value="<?php print $prenotado;?>" data-next="#indicadorReal">
									<label for="prenotado">PJ Prenotado</label>
								</div>
							</div>			
							<div class="row no-bottom">
								<div class="col l10 offset-l1" id="message"></div>
							</div>
							<div class="row no-bottom">
								<div class="col l10 offset-l1 center-align">
						     		<a id="btnCorrigir" name="btnCorrigir" class="waves-effect waves-light btn background btnControl" onClick='corrigirCaminhos()' <?php print $corrigir;?> style="margin-left:5px">Editar</a>
								</div>
							</div>
				            <input name='codCam' id='codCam' type='hidden' value="<?php print $recno;?>"></input>
						</div>
					</div>
				</div>
			</div>
		</div>
				
	</div>
	<div class="col m7 xl6">
		<div>
			<div class="row" id="painelDireito">
				<div class="col l12 card-panel white z-depth-1" id="painelModelo" style='padding-top:10px;'>
					<div class="row no-bottom center-align">
						<h5>Modelos padrão de recibo</h5>
					</div>
					<div class="row">
						<div class="col l9 offset-l1">
							<label for="balcao">Balcao</label>
							<select id="balcao" class="browser-default">
								<option value="" disabled selected>Escolha a opção</option>
								<?php
									foreach ($mod->list() as $key) {
										$selected = ($modMatricula == $key['MOD_CODIGO']) ? "selected"  : ""; 
										print "<option value='".$key['MOD_CODIGO']."' ".$selected."> ".utf8_encode( $key['MOD_DESCRICAO'] )."</option>";
									}
								?>
							</select>
						</div>
					</div>					
					
					<div class="row">
						<div class="col l10 offset-l1 center-align">
				     		<a id="btnCorrigirMod" name="btnCorrigirMod" class="waves-effect waves-light btn background btnControl" <?php print $corrigir;?> onClick='corrigirModelos()' style="margin-left:5px">Editar</a>
						</div>
					</div>
				</div> 

				<div class="col l12 card-panel white z-depth-1" id="painelLogo" style='padding-top:10px;min-height: 476px;'>
					<div class="row ">
						<div class="row no-bottom center-align">
							<h5>Logos do sistema</h5>
						</div>
						<div class="col l12">
							<p>Clique em arquivo para escolher o logo.</p>
						</div>
						<div class="col l12">
							<div class="row no-bottom valign-wrapper">
								<div class="col l6">
									
									<form action="#" method="POST" enctype="multipart/form-data">
									    <div class="file-field input-field ">
									      	<div class="btn background">
									      	  	<span>Arquivo</span>
									      	  	<input type="file" id="sArquivoLogo" name="sArquivoLogo" >
									      	</div>
									      	<div class="file-path-wrapper">
									      	  	<input class="file-path validate" type="text">
									      	</div>
									    </div>
									</form>
								</div>
								<div class="col l6 center-align">
									<div style='border:1px solid #CCC;height:90px; width:100%;' class="valign-wrapper center-align">
										<img src="<?php print $logo ;?>" id="sPreview" name="sPreview" alt="Preview" style='margin:0 auto; max-height:64px; max-width: 100%'>		
									</div>		
								</div>
							</div>
						</div>
					</div>
					<div class="row" style="border-bottom: 15px;">
						<div class="col l12 center-align">
							<a id="btnIncluir" class="waves-effect waves-light btn background btnControl" onClick="salvarImagem()">Salvar Imagem</a>	
						</div>
					</div>

					<div class="row">
						<div class="row no-bottom center-align">
							<h5>Logos dos relatórios</h5>
						</div>
						<div class="col l12">
							<p>Clique em arquivo para escolher o logo de relatório.</p>							
						</div>
						<div class="col l12">
							<div class="row no-bottom valign-wrapper">
								<div class="col l6">
									<form action="#" method="POST" enctype="multipart/form-data">
									    <div class="file-field input-field ">
									      	<div class="btn background">
									      	  	<span>Arquivo</span>
									      	  	<input type="file" id="sArquivoLogoRelatorio" name="sArquivoLogoRelatorio" >
									      	</div>
									      	<div class="file-path-wrapper ">
									      	  	<input class="file-path validate" type="text">
									      	</div>
									    </div>
									</form>
								</div>
								<div class="col l6 center-align">
									<div style='border:1px solid #CCC;height:90px; width:100%;' class="valign-wrapper">
										<img src="<?php print $logoRelatorio ;?>" id="sPreviewRelatorio" name="sPreviewRelatorio" alt="Preview" style='margin:0 auto; max-height:64px; max-width: 100%;'>	
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row" style="margin-top: 5px;">
						<div class="col l12 center-align">
							<a id="btnIncluirRelatoio" class="waves-effect waves-light btn background btnControl" onClick="salvarImagemRelatorio()">Salvar Imagem</a>	
						</div>
					</div>



					<div>
						<div class="row no-bottom left-align">
							Exibir o logo do cartório nos relatórios:
						</div>
					</div>
					<div class="row" style="margin-top: 5px;">
						<?php 
							$rel = new Relatorios();
							$result = $rel->listRelatorios();
							foreach($result as $row){
								$id = "chkRelatorio".$row["REL_ABREVIACAO"];
								if($row["REL_LOGO"]==1){
									print "<input type='checkbox' name='Relatorios' id='".$id."' value=\"".$row["REL_ABREVIACAO"]."\" checked onChange='atualizaCheckbox(this)'> <label for='".$id."'>".utf8_encode( $row["REL_NOME"] )."</label><br>";
								}else{
									print "<input type='checkbox' name='Relatorios' id='".$id."' value=\"".$row["REL_ABREVIACAO"]."\"  onChange='atualizaCheckbox(this)'> <label for='".$id."'>".utf8_encode( $row["REL_NOME"] )."</label><br>";
								}
								
							}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="ferramentas/js/frmCadCaminhos.js?v=<?php print VERSION; ?>" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
	document.title = "Cadastro de Caminhos";
</script>