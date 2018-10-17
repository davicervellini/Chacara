var liberarInclusao = false,
    liberarCorrecao = false,
    isInclusao = 0;

function habilitarCampos(sBool){
	$("input[type='text'],input[type='hidden']").prop('disabled', sBool == true ? false : true);
}


function _redirect(){
	Route.href( './ferramentas/configuracao-de-menus/' );
}

function limparCampos(){
	$("input[type='text']").val('');
}

var getDadosForm = function(){
	return {
		codSeq:     	$("#codSeq").val(),
        protOficialTd:	$("#protOficialTd").val(),
        protOficialPj:	$("#protOficialPj").val(),
        registroTd: 	$("#registroTd").val(),
        registroPj: 	$("#registroPj").val(),
		pedCertidao:	$("#pedCertidao").val(),
        fatura:			$("#fatura").val(),
        certificado:	$("#certificado").val(),
        matricula:		$("#matricula").val()
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

function corrigirSequenciaProtocolo(){
	if(!liberarCorrecao){
			habilitarControles(['#btnCorrigir']);
			habilitarCampos(true);
			$("#btnCorrigir").val('Salvar');
			$("#protOficial").focus();
			liberarCorrecao = true;
	}else{
		ArcaDialog.YesNoCancel('Deseja salvar os dados?',function(respConfirm){
			if(respConfirm == true){
				if(verificaCampos()){
					var Dados = getDadosForm();
					Dados.processo = 'corrigirSequenciaProtocolo';
					$.ajax({
					    type: "POST",
					    url: "ferramentas/php/frmCadSequenciaProtocolo.php",
					    data: Dados,
					    datatype:'json',
					    beforeSend:function(){
							$("#message").loadGif('Corrigindo a sequência de protocolo, por favor aguarde...');
					    },
					    success: function (data){
					    	data = $.parseJSON(data);
					    	if(!data.hasOwnProperty('error')){
					    		ArcaDialog.Alert(data.message,function(){
					    			window.location.reload();
					    		});
					    	}else{
					    		ArcaDialog.Alert(data.error);
					    	}
					    },
					    error: function(data){
					    	console.log(data.responseText);
					    }
					});	
				}
			}
			if(respConfirm == 'cancel'){
				habilitarControles();
				habilitarCampos(false);
				$("#btnCorrigir").val('Corrigir');
				$("#razao").focus();
				liberarCorrecao = false;
			}
		});
	}
}

$(document).ready(function() {
	$("#btnCorrigir").focus();
	habilitarCampos(false);
	$("input[type='text']").bind('keydown',function(e) {  
		if($(this).data('next'))
			(e.keyCode == '13') && $($(this).data('next')).focus();
    });
});