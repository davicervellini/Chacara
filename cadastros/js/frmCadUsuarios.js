var usuLiberado = 0;
var tag = 0;

//Limpar Inputs
function limparInputs(){
	// $("#usuNome, #usuLogin, #usuEmail, #usuTelefone").val("");
	$("input[type='text']").val("");
	$("#usuAdmin, #usuMaster").prop("checked", false);
}

//Habilitar / Desabilitar Inputs
function habilitarCampos(status){
	$("input[type='text']:not(.file-path),input[type='checkbox']").prop('disabled', status);
	$(".file-field").css('display', status == false ? "block" : "none");
}

function habilitarCamposModal(status){
	$(".checkModal").prop('disabled', status);
}

//Habilitar / Desabilitar Buttons
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

function preencheCampos(cod,elm){
	setDadosForm(cod);
	Route.pushState( '?codigo='+cod);
	$("tr").removeClass("tr-active");
	$(elm).addClass("tr-active");
}

function substituirUrl(){
	var href = window.location.href;
	var split = href.split("&msg");
	window.history.pushState( {} , window.location.href, split[0]);
}

function setDadosForm(id){
    $.ajax({
        type: "POST",
        url: "cadastros/php/frmCadUsuarios.php",
        datatype: "json",
        data: {
            processo: 'preencheCampos',
            id: id
        },
        beforeSend: function(){
        	$(".message").loadGif('Carregando dados, por favor aguarde...');
        },
        success: function (data){
	    	data = $.parseJSON(data);
	  		if(!data.hasOwnProperty('error')){
				$("#usuNome").val(data.usuNome);
				$("#usuLogin").val(data.usuLogin);
				$("#usuEmail").val(data.usuEmail);
				$("#usuCodigo").val(data.usuCodigo);
				$("#cargo").val(data.cargo);
				$('#usuAdmin').prop('checked', (data.usuAdmin == 1)? true : false);
				$("#usuRecno").val(data.usuRecno);
				$("#usuCodigo").val(id);
				$('html,body').animate({ scrollTop: 0 }, 'slow');

				if(data.usuAdmin == 1){
					$("#btnModalPermissoes").hide();
				}else{
					$("#btnModalPermissoes").show().attr("onclick", "modalPermissoes(0)" );
				}
			}else{
		  		ArcaDialog.Alert(data.error);
	  		}
            $(".message").html('');
        },
        error: function(data){
            ArcaDialog.Alert('Falha ao carregar os dados, recarregue a página e tente novamente.',function(){
            	console.log(data);
            });
        }
    });
}


function incluirUsuarios(chave){
	if(tag == 0){
		tag = 1;
		habilitarCampos(false);
		habilitarControles(["#btnIncluir"]);
		limparInputs();

		$("#btnModalPermissoes").attr("onclick","modalPermissoes(1)").show();

		$("#btnIncluir").val("Salvar");
		$("#usuAdmin, #usuMaster").prop("checked", false);
		$("#usuNome").focus();
	}else{
		var usuAdmin  = ($("#usuAdmin").is(':checked')) ? 1 : 0;
		// var usuMaster = ($("#usuMaster").is(':checked')) ? 1 : 0; 
		var aprovado = 0;
		if(chave == 1){
			ArcaDialog.YesNoCancel('Deseja incluir o usuário?', function(resConfirm){
				if(resConfirm === true){
					aprovado = 1;

					if(aprovado == 1){
						var Dados = {
							usuNome:  $("#usuNome").val(),
							usuLogin: $("#usuLogin").val(),
							usuEmail: $("#usuEmail").val(),
							usuAdmin: usuAdmin,
							cargo:    $("#cargo").val(),
							processo: "inclusao"
						}

						if(Dados.usuNome == ""){
							$(".message").showAlert('Por favor, é necessário informar um nome');
							$("#usuNome").focus();
							return false;
						}

						if(Dados.usuLogin == ""){
							$(".message").showAlert('Por favor, é necessário informar um login válido');
							$("#usuLogin").focus();
							return false;
						}

						if(Dados.usuEmail == "" || Dados.usuEmail.indexOf('@') ==-1 || Dados.usuEmail.indexOf('.') ==-1){
							$(".message").showAlert('Por favor, informe um email válido');
							$("#usuEmail").focus();
							return false;
						}

						if(Dados.cargo == ""){
							$(".message").showAlert('Por favor, preencha o cargo da assinatura.'); 
							$("#cargo").focus();
							return;
						}

						$(".message").html("");
						$.ajax({
							type: "POST",
							url: "cadastros/php/frmCadUsuarios.php",
							data: Dados,
							dataType: "json",
							success: function(resposta){
								ArcaDialog.Alert(resposta.resposta,function(){
									tag = 0;
									$("#usuRecno").val( resposta.usuRecno );	
									$("#btnIncluir").val("Incluir");
									Route.pushState( '?codigo='+resposta.usuCod);

									if(chave == 1 && resposta.usuAdmin == 0){
										$("#usuCodigo").val(resposta.usuCod);
										modalPermissoes(0, 1);
										atualizarTabelaUsuarios();
									}else{
										preencheCampos(resposta.usuCod);
										habilitarCampos(true);
										habilitarControles();
										limparInputs();
									}

								});
							}
						});

					}
				}
				if(resConfirm === "cancel"){
					$("#btnIncluir").val("Incluir");
					habilitarCampos(true);
					habilitarControles();
					tag = 0;
					limparInputs();
				}
			});
		}
	}	
}

var habilitarCorrecao = 0;
function corrigirUsuarios(){
	if($("#usuRecno").val() == ""){
		ArcaDialog.Alert("Por favor, selecione algum usuário!");
		return;
	}
	if(habilitarCorrecao == 0){
		habilitarCorrecao = 1;
		habilitarCampos(false);
		habilitarControles(["#btnCorrigir"]);
		$("#btnCorrigir").val("Salvar");
		$("#usuNome").focus();
	}else{
		var usuAdmin  = ($("#usuAdmin").is(':checked')) ? 1 : 0;
		// var usuMaster = ($("#usuMaster").is(':checked')) ? 1 : 0; 
		ArcaDialog.YesNoCancel("Deseja corrigir o usuário?",function(resConfirm){
			if (resConfirm == true) {

				var Dados = {
					usuNome:    $("#usuNome").val(),
					usuLogin:   $("#usuLogin").val(),
					usuEmail:   $("#usuEmail").val(),
					usuAdmin: 	usuAdmin,
					cargo:      $("#cargo").val(),
					usuCodigo:  $("#usuCodigo").val(),
					uploadVazio:(document.getElementById("upload").files.length === 0)? 1 : 0,
					processo: 	"correcao"
				}

				if(Dados.usuNome == ""){
					$(".message").showAlert('Por favor, é necessário informar um nome');
					$("#usuNome").focus();
					return false;
				}

				if(Dados.usuLogin == ""){
					$(".message").showAlert('Por favor, é necessário informar um login válido');
					$("#usuLogin").focus();
					return false;
				}

				if(Dados.usuEmail == "" || Dados.usuEmail.indexOf('@') ==-1 || Dados.usuEmail.indexOf('.') ==-1){
					$(".message").showAlert('Por favor, informe um email válido');
					$("#usuEmail").focus();
					return false;
				}

				if(Dados.cargo == ""){
					$(".message").showAlert('Por favor, preencha o cargo da assinatura.'); 
					$("#cargo").focus();
					return;
				}

				$.ajax({
					type: "POST",
					url: "cadastros/php/frmCadUsuarios.php",
					data: Dados,
					dataType: "json",
					success: function(resposta){
						ArcaDialog.Alert(resposta.resposta,function(){
							substituirUrl();
							window.location.reload();            
						});
					},
					error: function(resp){
						console.log(resp);
					}
				});		
						
				$("#btnCorrigir").val("Corrigir");
				habilitarCampos(true);
				habilitarControles();
				habilitarCorrecao = 0;
			}
			if(resConfirm == 'cancel'){
				$("#btnCorrigir").val("Corrigir");
				habilitarCampos(true);
				habilitarControles();
				habilitarCorrecao = 0;
			}
		});		
	}	
}

function excluirUsuarios(){
	if($("#usuCodigo").val() == ""){
		ArcaDialog.alert("Por favor, selecione algum usuário!");
		return;
	}
	ArcaDialog.YesNo("Deseja excluir o usuário?",function(resConfirm){
		if(resConfirm == true){
			var campos = {
				usuCodigo: $("#usuCodigo").val(),
				usuNome: $("#usuNome").val(),
				processo: "exclusao"
			}
			$.ajax({
		        type: "POST",
		        url: "cadastros/php/frmCadUsuarios.php",
		        data: campos,
		        dataType: "json",
		        success: function(resposta){
		        	ArcaDialog.Alert(resposta.resposta,function(){
		        		window.location.href = "cadastros/usuarios/"; 
		        	});           
		        }
		    });	
		}
	});
}

function resetarSenha(){
	if($("#usuRecno").val() == ""){
		ArcaDialog.Alert("Por favor, selecione algum usuário para prosseguir!");
		return;
	}

	ArcaDialog.YesNo("Deseja resetar a senha do usuário?",function(resConfirm){
		if(resConfirm == true){
			$.ajax({
			    type: "POST",
			    url: "cadastros/php/frmCadUsuarios.php",
			    data:{
			    	processo: 'resetarSenha',
			    	recno: $("#usuRecno").val(),
			    	usuLogin: $("#usuLogin").val()
			    },
			    dataType: "json",
			    success: function(data){
			   		ArcaDialog.Alert(data.message,function(){
			   			window.location.reload();
			   		});
			    }
			});	
		}
	});
}

function exibeResposta(){
	var mensagem = $("#msg").val();
	if (mensagem != "") {
		switch (mensagem) {
			case "0":
				$(".message").showAlert("Não foi possível realizar o processo.");
				return;
			break;			
			case "1":
				$(".message").showAlert("Inclusão realizada com sucesso", "green");
				return;
			break;			
			case "2":
				$(".message").showAlert("Correção realizada com sucesso", "green");
				return;
			break;			
			case "3":
				$(".message").showAlert("Erro ao realizar a inclusão");
				return;
			break;			
			case "4":
				$(".message").showAlert("Erro ao realizar a correção");
				return;
			break;						
			case "5":
				$(".message").showAlert("O tamanho do arquivo é maior do que o limite definido.");
				return;
			break;						
			case "6":
				$(".message").showAlert("O arquivo ultrapassa o limite de tamanho que foi especificado.");
				return;
			break;						
			case "7":
				$(".message").showAlert("O upload do arquivo foi feito parcialmente.");
				return;
			break;						
			case "8":
				$(".message").showAlert("Não foi realizado o upload do arquivo.");
				return;
			break;
			default:
				$(".message").showAlert("Código de erro não especificado.");
				return;
			break;
		}
	}
}

function atualizarTabelaUsuarios(){

	$.ajax({
	    type: "POST",
	    url: "cadastros/php/frmCadUsuarios.php",
	    data:{
	    	processo: 'atualizarTabelaUsuarios',
	    	recno: $("#usuRecno").val()	    	
	    },
	    datatype:'json',
	    beforeSend:function(){
			$("#gridUsuarios").loadGif('Carregando resultados, por favor aguarde.');    	
	    },
	    success: function (data){
	    	data = $.parseJSON(data);	    	
	    	if ($.fn.DataTable.isDataTable("#gridUsuarios")) {
			  $('#gridUsuarios').DataTable().clear().destroy();
			}
	        gridUsuarios = $("#gridUsuarios").html(data.grid).DataTable({"iDisplayStart": data.pagina,aaSorting: []});
	    }
	});	

}

// PERMISSÕES
function modalPermissoes(chave, incluir = 0){
	if(chave == 1){
		ArcaDialog.YesNo("Deseja incluir o usuário para alterar as permissões de acesso?",function(resConfirm){
			if(resConfirm == true){
				incluirUsuarios(1);	
			}
		});
	}else{
		if(incluir == 1){
			habilitarCorrecao = 1;
			habilitarCampos(false);
			habilitarControles(["#btnCorrigir"]);
			$("#btnCorrigir").val("Salvar");
		}
		$.ajax({
			url: "cadastros/php/frmCadUsuarios.php",
			type: "POST",
			data: {
				usuCodigo: $("#usuCodigo").val(),
				processo: "verificaPermissoes",
				chave: chave
			},
			success: function(resposta){
				$('#modalPermissoes').modal('open');
				$("#verificaPermissoes").html(resposta);
				if(habilitarCorrecao == 1 || tag == 1){
                    habilitarCamposModal(false);
                }else{
                    habilitarCamposModal(true);
                }
				$('.collapsible').collapsible();
			},
			error: function(resposta){
				console.log();
			}
		});
	}
}

function fecharModal(){
	$('#modalPermissoes').modal('close');
    habilitarCamposModal(true);
}

function alterarPermissoes(campo,sForm){

	var inclusao = ($("#inclusao"+sForm).is(":checked")) ? 1 : 0;
	var correcao = ($("#correcao"+sForm).is(":checked")) ? 1 : 0;
	var exclusao = ($("#exclusao"+sForm).is(":checked")) ? 1 : 0;

	var valorAcesso = ($("#acesso"+sForm).is(":checked")) ? 1 : 0;

	if(campo != "ACESSO" && (inclusao == 1 || correcao == 1 || exclusao == 1) && valorAcesso == 0){
		$("#acesso"+sForm).prop('checked', true);
		valorAcesso = 1;
	}

	$.ajax({
		url: "php/frmVerificaPermissoes.php",
		type: "POST",
		dataType: "json",
		data: {
			sForm: sForm,
			usuCodigo: $("#usuCodigo").val(),
			acesso   : valorAcesso,
			inclusao : inclusao,
			correcao : correcao,
			exclusao : exclusao,
		},
		success: function(resposta){
			// alert(resposta);
			console.log(resposta);
		}
	});
}

function salvarPermissoes(){
	$('#modalPermissoes').modal('close');
}

function selectAllPermissoes(modulo, menu, elem){
	// modulo: acesso menu: Registro
	$("#verificaPermissoes input:checkbox").filter(function() {
    	if($(this).data('menu') == modulo+menu){
    		if( $(elem).prop('checked') ){
    			if(!$(this).prop('checked')){
					$(this).click();
    			}
    		}else{
    			if($(this).prop('checked')){
					$(this).click();
    			}
    		}
		}	    
	});
}

$(document).ready(function(){
	$("#btnIncluir").focus();
	atualizarTabelaUsuarios();
	habilitarCampos(true);
	exibeResposta();
	$('.collapsible').collapsible();
	$("#usuTelefone").mask("(99) 99999-9999");
	$("#usuTelefone").on("blur", function(){
        var last = $(this).val().substr( $(this).val().indexOf("-") + 1 );

        if( last.length == 3 ) {
            var move = $(this).val().substr( $(this).val().indexOf("-") - 1, 1 );
            var lastfour = move + last;
            var first = $(this).val().substr( 0, 9 );

            $(this).val( first + '-' + lastfour );
        }
    })

    $("#usuLogin").on("blur", function(){
    	if($(this).val() != ""){
    		$.ajax({
	            type: "POST",
	            url: "cadastros/php/frmCadUsuarios.php",
	            data: {
	            	processo : 'verificarLogin',
	            	usuLogin : $(this).val(),
	            	habilita : habilitarCorrecao,
	            	usuCodigo : $("#usuCodigo").val(),
	            	usuAntigo: $("#hidUsuLogin").val()
	            },
	            success: function(resposta){
	        		if(resposta == 0){
						$(".message").showAlert('Esse login já está sendo utilizado.');
						$("#usuLogin").focus();
						return;	
	            	}else{
	            		$(".message").html("");	
	            		usuLiberado = 1;
	            	}
	            }
	        });
    	}
    });

    $("#frmCadUsuarios").on('submit', function(e){
    	e.preventDefault();
    });
});