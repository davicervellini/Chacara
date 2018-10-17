var liberarInclusao = false,
    liberarCorrecao = false,
    isInclusao = 0;

function habilitarCampos(sBool){
	$("input[type='text'],select,input[type='hidden']").prop('disabled', sBool == true ? false : true);
}

function _redirect(){
	Route.href( './ferramentas/configuracao-de-menus/' );
}

function limparCampos(){
	$("input[type='text'],select").val('');

}

function selecionarRegistro(cod,elm){
	setDadosForm(cod);
	Route.pushState( '?codigo='+cod);
	$("tr").removeClass("tr-active");
	$(elm).addClass("tr-active");
}

var getDadosForm = function(){
	return {
		menu:      $("#menu").val(),
		form:      $("#form").val(),
		descricao: $("#descricao").val(),
		grupo:     $("#grupo").val(),
		grupoOrdem:$("#grupo option:selected").data('ordem'),
		codMen:    $("#codConfigMenus").val()
	}
}

function setDadosForm(id){
    $.ajax({
        type: "POST",
        url: "ferramentas/php/frmCadConfigMenus.php",
        datatype: "json",
        data: {
            processo: 'preencheCampos',
            id: id
        },
        beforeSend: function(){
        	// $("#message").loadGif('Carregando dados, por favor aguarde...');
        	showLoad(true,'Carregando dados, por favor aguarde...');
        },
        success: function (data){
	    	data = $.parseJSON(data);
	  		if(!data.hasOwnProperty('error')){
				$("#menu").val(data.menu);
				$("#form").val(data.form);
				$("#descricao").val(data.descricao);
				$("#grupo").val(data.grupo);
				$("#codConfigMenus").val(id);
				$('html,body').animate({ scrollTop: 0 }, 'slow');
			}else{
		  		alert(data.error);
	  		}
            // $("#message").html('');
            showLoad(false);
        },
        error: function(data){
            alert('Falha ao carregar os dados, recarregue a página e tente novamente.');
            console.log(data);
        }
    });
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

function incluirConfigMenus(){
	if(!liberarInclusao){
		isInclusao = 1;
		limparCampos();
		habilitarCampos(true);
		habilitarControles(['#btnIncluir']);
		$("#btnIncluir").html('Salvar');
		$("#menu").focus();
		liberarInclusao = true;
	}else{

		if(confirm('Deseja salvar os dados?')){
			var Dados = getDadosForm();
			Dados.processo = 'incluirConfigMenu';

			if(Dados.menu == ""){
				$("#message").showAlert('Preencha o menu antes de continuar'); 
				return;
			}
			if(Dados.form == ""){
				$("#message").showAlert('Preencha o form antes de continuar'); 
				return;
			}
			if(Dados.descricao == ""){
				$("#message").showAlert('Preencha a descrição antes de continuar'); 
				return;
			}
			if(Dados.grupo == "" || Dados.grupo == undefined){
				$("#message").showAlert('Preencha a grupo antes de continuar'); 
				return;
			}

			$.ajax({
			    type: "POST",
			    url: "ferramentas/php/frmCadConfigMenus.php",
			    data: Dados,
			    datatype:'json',
			    beforeSend:function(){
					// $("#message").loadGif('Incluindo o Menu, por favor aguarde...');
					showLoad(true, 'Incluindo o Menu, por favor aguarde...');
			    },
			    success: function (data){
			    	data = $.parseJSON(data);
			    	if(!data.hasOwnProperty('error')){
			    		alert(data.message);
			    		Route.href( './ferramentas/configuracao-de-menus/?codigo='+ data.menCod);
			    	}else{
			    		alert(data.error);
			    	}
			    	showLoad(false);
			    },
			    error:  function(data){
			    	console.log(data.responseText);
			    }
			});

		}else{
			habilitarCampos(false);
			habilitarControles();
			$("#btnIncluir").html('Incluir');
			liberarInclusao = false;
		}
	}
}


function corrigirConfigMenus(){
	var codConfigMenus = $("#codConfigMenus").val();
	if(!liberarCorrecao){
		if(codConfigMenus != ""){
			habilitarControles(['#btnCorrigir']);
			habilitarCampos(true);
			$("#btnCorrigir").html('Salvar');
			$("#menu").focus();
			liberarCorrecao = true;
		}else{
			alert('Por favor, selecione um registro antes de continuar.');
			return false;
		}
	}else{

		if(confirm('Deseja salvar os dados?')){
			var Dados = getDadosForm();
			Dados.processo = 'corrigirConfigMenu';

			if(Dados.menu == ""){
				$("#message").showAlert('Preencha o menu antes de continuar'); 
				return;
			}
			if(Dados.form == ""){
				$("#message").showAlert('Preencha o form antes de continuar'); 
				return;
			}
			if(Dados.descricao == ""){
				$("#message").showAlert('Preencha a descrição antes de continuar'); 
				return;
			}
			if(Dados.grupo == "" || Dados.grupo == undefined){
				$("#message").showAlert('Preencha a grupo antes de continuar'); 
				return;
			}

			$.ajax({
			    type: "POST",
			    url: "ferramentas/php/frmCadConfigMenus.php",
			    data: Dados,
			    datatype:'json',
			    beforeSend:function(){
					// $("#message").loadGif('Corrigindo o Menu, por favor aguarde...');    	
					showLoad(true, 'Corrigindo o Menu, por favor aguarde...');    	
			    },
			    success: function (data){
			    	data = $.parseJSON(data);
			    	if(!data.hasOwnProperty('error')){
			    		alert(data.message);
			    		window.location.reload();
			    	}else{
			    		alert(data.error);
			    	}
			    	showLoad(false);
			    },
			    error:  function(data){
			    	console.log(data.responseText);
			    }
			});	


		}else{

			habilitarControles();
			habilitarCampos(false);
			$("#btnCorrigir").html('Corrigir');
			$("#menu").focus();
			liberarCorrecao = false;
		}
	}
}

function excluirConfigMenus(){
	var codConfigMenus = $("#codConfigMenus").val();
	if(codConfigMenus != ""){
		if(confirm('Deseja excluir esse registro?')){
			var Dados = getDadosForm();
			Dados.processo = 'excluirConfigMenu';

			$.ajax({
			    type: "POST",
			    url: "ferramentas/php/frmCadConfigMenus.php",
			    data: Dados,
			    datatype:'json',
			    beforeSend:function(){
					// $("#message").loadGif('Excluindo o Menu, por favor aguarde...');
					showLoad(true, 'Excluindo o Menu, por favor aguarde...');
			    },
			    success: function (data){
			    	data = $.parseJSON(data);
			    	if(!data.hasOwnProperty('error')){
			    		alert(data.message);
			    		_redirect();
			    	}else{
			    		alert(data.error);
			    	}
			    	showLoad(false);
			    },
			    error:  function(data){
			    	console.log(data.responseText);
			    }
			});	
		}

	}else{
		alert('Por favor, selecione um registro antes de continuar.');
	}
}

function listarConfigMenus(){
	$.ajax({
	    type: "POST",
	    url: "ferramentas/php/frmCadConfigMenus.php",
	    data:{
	    	processo:'listarConfigMenus',
	    	recno:   $("#codConfigMenus").val()
	    },
	    datatype:'json',
	    beforeSend:function(){
			// $("#gridConfigMenus").loadGif('Carregando resultados, por favor aguarde.');
			showLoad(true, 'Carregando resultados, por favor aguarde.');
	    },
	    success: function (data){
	    	data = $.parseJSON(data);	    	
	    	if(!data.hasOwnProperty('error')){
		    	if ($.fn.DataTable.isDataTable("#gridConfigMenus")) {
		    		$('#gridConfigMenus').DataTable().clear().destroy();
				}
		        gridConfigMenus = $("#gridConfigMenus").html(data.grid).DataTable({"iDisplayStart": data.pagina,aaSorting: []});
			}else{
				alert(data.error);
			}
			showLoad(false);
	    }
	});	

}



function continuarAcesso(){
	$password = $("#passwordAcesso");

	if($password.val() != ""){
		$.ajax({
		    type: "POST",
		    url: "ferramentas/php/frmCadConfigMenus.php",
		    data:{
		    	processo:'verificarAcesso',
		    	password:   $password.val()
		    },
		    datatype:'json',
		    success: function (data){
		    	// console.log(data);
		    	if( data == "true"){
		    		$("#modalAcesso").modal('close');
		    	}else{
		    		ArcaDialog.Alert('Senha inválida. Por favor, tente novamente.');
		    	}
		    }
		});	
	}else{
		ArcaDialog.Alert('Digite a senha de acesso antes de continuar.');
	}
}


$("#descricao").on("blur", function(){
	$.ajax({
        type: "POST",
        url: "ferramentas/php/frmCadConfigMenus.php",
        data: {
        	processo: 'verificarConfigMenus',
        	ocoDescricao: $("#descricao").val()
        },
        success: function(resposta){
        	if(parseInt(resposta) > 0){
        		$("#message").showAlert('Este ConfigMenus já foi cadastrado.');
        		$("#descricao").focus();
        		(isInclusao == 1) ? $("#btnIncluir").addClass("disabled") : $("#btnCorrigir").addClass("disabled");
        			
        	}else{
        		$("#message").html('');	
        		(isInclusao == 1) ? $("#btnIncluir").removeClass("disabled") : $("#btnCorrigir").removeClass("disabled");
        	}
        }
    });
});

$(document).ready(function() {
	habilitarCampos(false);
	listarConfigMenus();
	$("input[type='text']").bind('keydown',function(e) {  
		if($(this).data('next'))
			(e.keyCode == '13') && $($(this).data('next')).focus();
    });
    
	$.datepicker.setDefaults({
	    dateFormat: 'dd/mm',
	    dayNames: ['Domingo','Segunda','Ter&ccedil;a','Quarta','Quinta','Sexta','S&aacute;bado','Domingo'],
	    dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
	    dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','S&aacute;b','Dom'],
	    monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
	    monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez']
	});


	if($("#pwm").val() != "true"){
		$("#modalAcesso").modal({
			dismissible: false
		});
		$("#modalAcesso").modal('open');
		$("#passwordAcesso").focus();
	}


	$("#passwordAcesso").keyup(function(event){
		if(event.keyCode == 13){
			continuarAcesso();
		}
	})
});