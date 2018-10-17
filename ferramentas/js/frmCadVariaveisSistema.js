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
		codConfig: 	 $("#codConfig").val(),
		rotaBase: 	 $("#rotaBase").val(),
		ipServer: 	 $("#ipServer").val(),
		urlChat: 	 $("#urlChat").val(),
		wsISAPI: 	 $("#wsISAPI").val(),
		codCliente:  $("#codCliente").val(),
		serventiaRI: $("#serventiaRI").val(),
		serventiaTD: $("#serventiaTD").val(),
		serventiaPJ: $("#serventiaPJ").val(),
		// anoVelho:	 $("#anoVelho").val()
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
	$('input[class=obrigatorio]').each(function(){
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

function corrigirDadosVariaveis(){
	if(!liberarCorrecao){
			habilitarControles(['#btnCorrigir']);
			habilitarCampos(true);
			$("#btnCorrigir").val('Salvar');
			$("#ipServer").focus();
			liberarCorrecao = true;
	}else{
		ArcaDialog.YesNoCancel('Deseja salvar os dados?',function(resConfirm){
			if(resConfirm == true){
				if(verificaCampos()){
					var Dados = getDadosForm();
					Dados.processo = 'corrigirDadosVariaveis';
					$.ajax({
					    type: "POST",
					    url: "ferramentas/php/frmCadVariaveisSistema.php",
					    data: Dados,
					    datatype:'json',
					    beforeSend:function(){
							$("#message").loadGif('Corrigindo as variáveis, por favor aguarde...');
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
			if(resConfirm == 'cancel'){
				habilitarControles();
				habilitarCampos(false);
				$("#btnCorrigir").val('Corrigir');
				$("#rotaBase").focus();
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