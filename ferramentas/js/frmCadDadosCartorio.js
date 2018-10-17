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
		codDCA:     $("#codDCA").val(),
		razao:      $("#razao").val(),
		complRazao: $("#complRazao").val(),
		fantasia:   $("#fantasia").val(),
		tabelionato:$("#tabelionato").val(),
		cep:        $("#cep").val(),
		endereco:   $("#endereco").val(),
		bairro:     $("#bairro").val(),
		cidade:     $("#cidade").val(),
		uf:         $("#uf").val(),
		telefone:   $("#telefone").val(),
		email:      $("#email").val(),
		site:       $("#site").val(),
		oficial:    $("#oficial").val(),
		cargo:      $("#cargo").val(),
		cnpj:       $("#cnpj").val(),
		horario:    $("#horario").val(),
		cpf:    	$("#cpf").val(),
		substituto: $("#substituto").val(),
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
	$('input[type=text]:not(#site):not(#horario)').each(function(){
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

function corrigirDadosCartorio(){
	if(!liberarCorrecao){
			habilitarControles(['#btnCorrigir']);
			habilitarCampos(true);
			$("#btnCorrigir").html('Salvar');
			$("#razao").focus();
			liberarCorrecao = true;
	}else{
		ArcaDialog.YesNoCancel('Deseja salvar os dados?',function(resConfirm){
			if (resConfirm == true) {
				if(verificaCampos()){
					var Dados = getDadosForm();
					Dados.processo = 'corrigirDadosCartorio';
					$.ajax({
					    type: "POST",
					    url: "ferramentas/php/frmCadDadosCartorio.php",
					    data: Dados,
					    datatype:'json',
					    beforeSend:function(){
							$("#message").loadGif('Corrigindo os Dados do Cartório, por favor aguarde...');    	
					    },
					    success: function (data){					    	
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

			else if(resConfirm == 'cancel'){
				habilitarControles();
				habilitarCampos(false);
				$("#btnCorrigir").html('Corrigir');
				$("#razao").focus();
				liberarCorrecao = false;
			}

		});
	}
}

$(document).ready(function() {
	$("#btnCorrigir").focus();
	$('select').material_select();
	$(".cnpj").mask("99.999.999/9999-99");

	habilitarCampos(false);
	$("input[type='text']").bind('keydown',function(e) {  
		if($(this).data('next'))
			(e.keyCode == '13') && $($(this).data('next')).focus();
    });
});