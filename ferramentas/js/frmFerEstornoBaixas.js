var URL_POST = "ferramentas/php/frmFerEstornoBaixas.php";
function _redirect(){
	Route.href( './ferramentas/estorno-de-baixas/' );
}

function limparCampos(){
	$("input[type='text'],#recno").val('');
}

var getDadosForm = function(){
	return {
		recno:      $("#recno").val(),
		codigo:     $("#codigo").val(),
		tpProtocolo:$("#tpProtocolo").val(),
		protocolo:  $("#protocolo").val(),
		dtRecepcao:  $("#dtRecepcao").html(),
	}
}

function setDadosForm(protocolo){
	var tpProtocolo = $("#tpProtocolo").val();
	if (tpProtocolo == "" || tpProtocolo == undefined) {
		$("#message").showAlert("Selecione o setor antes de continuar");
		return;
	}
	if (protocolo != "") {
	    $.ajax({
	        type: "POST",
	        url: URL_POST,	        
	        data: {
	            processo:   'preencheCampos',
	            protocolo:   protocolo,
	            tpProtocolo: tpProtocolo
	        },
	        dataType: 'JSON',
	        beforeSend: function(){
	        	$("#divLoadResultado").loadProgress();
	        },
	        success: function (data){		    	
		  		if(!data.hasOwnProperty('error')){
	  				$("#recno").val(data.recno);
					$("#dtRecepcao").text(data.dtRecepcao);
					if(data.dtRegistro != "")
						$("#infoRegistro").show();
					else
						$("#infoRegistro").hide();

					$("#dtRegistro").text( data.dtRegistro + " - " + data.hrRegistro );
					$("#status").text(data.status + ((data.DataPosicao != "" && data.HoraPosicao != "") ? " - " + data.DataPosicao + " - " + data.HoraPosicao : "" ));
	  				$("#apresentante").text(data.apresentante);
					$("#natureza").text(data.natureza);
					$("#ultBaixa").text(data.ultBaixa);					
				}else{
			  		alert(data.error);
		  		}
		  		if(data.recno == ""){
		  			$("#protocolo").css('margin-bottom',"35px").addClass('invalid');
		  		}
		  		$("#protocolo").addClass('valid');
	            $("#divLoadResultado").html("");
	        },
	        error: function(data){
	            alert('Falha ao carregar os dados, recarregue a página e tente novamente.');
	            console.log(data);
	        }
	    });
	}
}

function habilitarControles(nameButton = null){
	$('.btnControl').addClass('disabled');
	if(nameButton !== null){
		for (var i = 0; i < nameButton.length; i++) {
			$(nameButton[i]).parent().removeClass('disabled');
		}
	}else{
		$('.btnControl').removeClass('disabled');
	}
}

function liberarManutencao(){
	ArcaDialog.YesNo('Deseja liberar a manutenção?',function(resConfirm){
		if(resConfirm == true){
			var Dados      = getDadosForm();
			Dados.processo = 'liberarManutencao';

			if (Dados.recno != "") {

				$.ajax({
				    type: "POST",
				    url: URL_POST,
				    data: Dados,
				    datatype:'json',
				    beforeSend:function(){
						$("#message").loadGif('Liberando a manutenção, por favor aguarde...');
				    },
				    success: function (data){
				    	data = $.parseJSON(data);
				    	if(!data.hasOwnProperty('error')){
				    		ArcaDialog.Alert(data.message,function(){
				    			_redirect();
				    		});
				    	}else{
				    		ArcaDialog.Alert(data.error);
				    	}
				    },
				    error:  function(data){
				    	console.log(data.responseText);
				    }
				});
			}else{
				$("#message").showAlert("Selecione um registro antes de continuar");
				$("#protocolo").focus();
			}
		}
	});
}

function excluirBaixa(){
	ArcaDialog.YesNo('Deseja remover a última baixa?',function(resConfirm){
		if(resConfirm == true){
			var Dados      = getDadosForm();
			Dados.processo = 'excluirBaixa';

			if (Dados.recno != "") {
				$.ajax({
				    type: "POST",
				    url: URL_POST,
				    data: Dados,
				    datatype:'json',
				    beforeSend:function(){
						$("#message").loadGif('Excluindo baixa, por favor aguarde...');
				    },
				    success: function (data){
				    	data = $.parseJSON(data);
				    	if(!data.hasOwnProperty('error')){
				    		ArcaDialog.Alert(data.message,function(){
				    			setDadosForm($("#protocolo").val());
				    		});
				    	}else{
				    		ArcaDialog.Alert(data.error);
				    	}
				    	$("#message").html("");
				    },
				    error:  function(data){
				    	console.log(data.responseText);
				    }
				});
			}else{
				$("#message").showAlert("Selecione um registro antes de continuar");
				$("#protocolo").focus();
			}	
		}
	});
}

$(document).ready(function() {	
	$('select').material_select();
	$("#protocolo").focus();
	$("#tpProtocolo").change(function() {
		$("input[type='text']:not(#protocolo,#tpProtocolo),#recno").val('');
		$("#protocolo").removeClass('invalid').removeClass('valid');
		$("#message").html("");
		$('select').material_select();
		$('#protocolo').focus();
		setDadosForm($('#protocolo').val());
	});
	// $("#protocolo").blur(function(event) {
	// 	setDadosForm($(this).val());
	// });
	$("#protocolo").keypress(function(e){		
		if(e.keyCode == '13') {						
			setDadosForm( $('#protocolo').val() );
		} 
    });
});