var liberarInclusao = false,
    liberarCorrecao = false,
    isInclusao = 0;

function habilitarCampos(sBool){
	$("input[type='text']:not(.file-path),input[type='hidden']").prop('disabled', sBool == true ? false : true);
}

function habilitarCamposModelos(sBool){
	$("select").prop('disabled', sBool == true ? false : true);
}


function _redirect(){
	Route.href( './ferramentas/caminhos/' );
}

function limparCampos(){
	$("input[type='text']").val('');
}

var getDadosForm = function(){
	return {
		codCam:            	$("#codCam").val(),

        titulos:       	    $("#titulos").val(),
        civil:           	$("#civil").val(),
        certificados:       $("#certificados").val(),
        comprovante:      	$("#comprovante").val(),
        documentos:         $("#documentos").val(),
        protocolo:          $("#protocolo").val(),
        nota:            	$("#nota").val(),
        prenotado: 	        $("#prenotado").val(),

		balcao:  		   	$("#balcao").val(),
		// certidao:  			$("#certidao").val(),
		// exa:  				$("#exa").val(),
		// modAbertura:  		$("#cfgModeloAbertura").val(),
		// modEncerramento:	$("#cfgModeloEncerramento").val(),
	}
}

function habilitarControles(nameButton = null){
	$('.btnControl').addClass('disabled');
	if(nameButton !== null){
		for (var i = 0; i < nameButton.length; i++) {
			$(nameButton[i]).removeClass('disabled');	
		}
	}else{
		$('.btnControl').removeClass('disabled');
	}
}

function verificaCampos(){
	var invalido = "";
	$('input[type=text]').each(function(){
    	if ($(this).val() == "" || $(this).val() == undefined ) {
    		invalido = this.id;
    		return false;
    	};
	})
    if(invalido !== ""){
		$("#message").showAlert('Preencha o campo "'+$("label[for='"+invalido+"']").text().replace(" *","")+'" antes de continuar');
		$("#"+invalido).focus();
		return false
    }else{
    	return true;
    }
}

function corrigirCaminhos(){
	if(!liberarCorrecao){
			habilitarControles(['#btnCorrigir']);
			habilitarCampos(true);
			$("#btnCorrigir").html('Salvar');
			$("#razao").focus();
			liberarCorrecao = true;
	}else{

		ArcaDialog.YesNoCancel('Deseja salvar os dados?', function( resConfirm ){
			if( resConfirm === true ){
				if(verificaCampos()){
					var Dados = getDadosForm();
					Dados.processo = 'corrigirCaminhos';
					$.ajax({
					    type: "POST",
					    url: "ferramentas/php/frmCadCaminhos.php",
					    data: Dados,
					    datatype:'json',
					    beforeSend:function(){
							$("#message").loadGif('Corrigindo os caminhos, por favor aguarde...');
					    },
					    success: function (data){
					    	data = $.parseJSON(data);
					    	if(!data.hasOwnProperty('error')){
					    		ArcaDialog.Alert(data.message, function(){
					    			window.location.reload();	
					    		});
					    	}else{
					    		ArcaDialog.Alert( data.error );
					    	}
					    },
					    error: function(data){
					    	console.log(data.responseText);
					    }
					});	
				}
			}

			if( resConfirm == 'cancel'){
				habilitarControles();
				habilitarCampos(false);
				$("#btnCorrigir").html('Corrigir');
				$("#razao").focus();
				liberarCorrecao = false;
			}

		});
	}
}

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#sPreview').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function readURLRelatorio(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#sPreviewRelatorio').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function salvarImagem(){
	if($("#sArquivoLogo").val() == ""){
		ArcaDialog.Alert("Por favor escolha uma imagem antes de continuar.");
		return;
	}
	ArcaDialog.YesNoCancel('Deseja salvar essa imagem?', function( resConfirm ){
		if( resConfirm === true ){
			form.append('processo', 'salvarLogo');
			$.ajax({
			    url: 'ferramentas/php/frmCadCaminhos.php',
			    cache: false,
			    contentType: false,
			    processData: false,
			    data: form,
			    type: 'post',
			    dataType: 'json',
			    success: function(data) {
			        if(!data.error){
			           	ArcaDialog.Alert(data.message, function(){
			    			window.location.reload();	
			    		});		    		
			        }else{
			            ArcaDialog.Alert( data.error );
			            // console.log(data);
			        }
			    },
			    error: function(data){
			        ArcaDialog.Alert('Erro:' + data.responseText);
			    }
			});
		}
	});
}

function salvarImagemRelatorio(){
	if($("#sArquivoLogoRelatorio").val() == ""){
		ArcaDialog.Alert("Por favor escolha uma imagem antes de continuar.");
		return;
	}
	ArcaDialog.YesNoCancel('Deseja salvar essa imagem?', function( resConfirm ){
		if( resConfirm === true ){
			formRelatorio.append('processo', 'salvarLogoRelatorio');
			$.ajax({
			    url: 'ferramentas/php/frmCadCaminhos.php',
			    cache: false,
			    contentType: false,
			    processData: false,
			    data: formRelatorio,
			    type: 'post',
			    dataType: 'json',
			    success: function(data) {
			        if(!data.error){
			            ArcaDialog.Alert(data.message, function(){
			    			window.location.reload();	
			    		});
			        }else{
			           	ArcaDialog.Alert( data.error );
			           	// console.log(data);
			        }
			    },
			    error: function(data){
			        alert('Erro:' + data.responseText);
			    }
			});
		}
	});
}

function atualizaCheckbox(element){
	
	if($(element).is(':checked')) {
		var Dados = {};
		Dados.abreviacao = $(element).val();
		Dados.logo = '1';
		Dados.processo = 'atualizarRelatorios';
		$.ajax({
		    type: "POST",
		    url: "ferramentas/php/frmCadCaminhos.php",
		    data: Dados,
		    datatype:'json',
		    success: function (data){
		    	data = $.parseJSON(data);
		    	if(!data.hasOwnProperty('error')){
		    		
		    	}else{
		    		ArcaDialog.Alert( data.error );
		    	}
		    },
		    error: function(data){
		    	console.log(data.responseText);
		    }
		});
	} else {
	    var Dados = {};
		Dados.abreviacao = $(element).val();
		Dados.logo = '0';
		Dados.processo = 'atualizarRelatorios';
		$.ajax({
		    type: "POST",
		    url: "ferramentas/php/frmCadCaminhos.php",
		    data: Dados,
		    datatype:'json',
		    success: function (data){
		    	data = $.parseJSON(data);
		    	if(!data.hasOwnProperty('error')){
		    	}else{
		    		ArcaDialog.Alert( data.error );
		    	}
		    },
		    error: function(data){
		    	console.log(data.responseText);
		    }
		});
	}
	
}

function corrigirModelos() {
	if(!liberarCorrecao){
			habilitarControles(['#btnCorrigirMod']);
			habilitarCamposModelos(true);
			$("#btnCorrigirMod").html('Salvar');
			$("#razao").focus();
			liberarCorrecao = true;
	}else{
		ArcaDialog.YesNoCancel('Deseja salvar os dados?', function( resConfirm ){
			if( resConfirm === true ){
				if(verificaCampos()){
					var Dados = getDadosForm();
					Dados.processo = 'corrigirModelos';
					$.ajax({
					    type: "POST",
					    url: "ferramentas/php/frmCadCaminhos.php",
					    data: Dados,
					    datatype:'json',
					    beforeSend:function(){
							$("#message").loadGif('Corrigindo os modelos, por favor aguarde...');
					    },
					    success: function (data){
					    	data = $.parseJSON(data);
					    	if(!data.hasOwnProperty('error')){
					    		ArcaDialog.Alert(data.message, function(){
					    			window.location.reload();	
					    		});
					    	}else{
					    		ArcaDialog.Alert( data.error );
					    	}
					    },
					    error: function(data){
					    	console.log(data.responseText);
					    }
					});	
				}
			}

			if( resConfirm == 'cancel'){
				habilitarControles();
				habilitarCamposModelos(false);
				$("#btnCorrigirMod").html('Corrigir');
				$("#razao").focus();
				liberarCorrecao = false;
			}
		});
	}
}

var form = new FormData();
var formRelatorio = new FormData();
$(document).ready(function() {
	habilitarCampos(false);
	habilitarCamposModelos(false);
	$("input[type='text']").bind('keydown',function(e) {  
		if($(this).data('next'))
			(e.keyCode == '13') && $($(this).data('next')).focus();
    });

    $("#sArquivoLogo").change(function(event){
		form.append('sArquivoLogo', event.target.files[0]);
		readURL(this);
		return;
    });

    $("#sArquivoLogoRelatorio").change(function(event){
		formRelatorio.append('sArquivoLogoRelatorio', event.target.files[0]);
		readURLRelatorio(this);
		return;
    });


    var painelCaminhos 	= $("#painelCaminhos");
    var painelModelo 	= $("#painelModelo");
    var	painelLogo 		= $("#painelLogo");
    var	painelDireito 		= $("#painelDireito");

    // painelCaminhos.outerHeight(painelDireito.outerHeight());
});