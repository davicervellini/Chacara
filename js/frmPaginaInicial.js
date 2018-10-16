$(document).ready(function() {
		$('.collapsible').collapsible();
});

var getDadosForm = function(){
	return{
		descricao: 			$("#descricao").val(),
		recno: 	   			$("#recno").val(),
		dataCadastro: 	   	$("#data").val(),
		dataPesquisa: 		$("#dataPesquisa").val()
	}	
}

function verificaCampos(){
    if($("#descricao").val() == "" ){
		$("#message").showAlert('Preencha o campo descricao antes de continuar');
		return false
    }else{
    	return true;
    }
}
function abrirModal(){
	$("#incluirAnotacao").modal("open");
	$("#descricao").focus();
}
function incluirAnotacao(){
	ArcaDialog.YesNo("Deseja salvar a anotação?",function(resConfirm){
		if(resConfirm == true){
			if(verificaCampos()){
				var Dados = getDadosForm();
				Dados.processo = "incluirAnotacao";
				$.ajax({
					type: "POST",
					url: "php/frmPaginaInicial.php",
					data: Dados,
					dataType: 'json',
					beforeSend: function(){
						$("#message").loadGif('Incluindo anotação, por favor aguarde...');
					},
					success: function(data){
						console.log(data);
						if(!data.hasOwnProperty('error')){			    		
				    		$("#message").html("");
				    		Route.href( './home');
				    		return;					    			
						}
						else{
							ArcaDialog.Alert(data.error);
						}
					},
					error:  function(data){
					    console.log(data.responseText);
					}
				});

			}
		}
	});
}

function corrigirAnotacao(recno){
	ArcaDialog.YesNo("Deseja salvar os daods",function(resConfirm){
		if(resConfirm == true){
			if(verificaCampos()){
				var Dados = getDadosForm();
				Dados.processo = "corrigirTarefas";
				Dados.recno = recno;
				$.ajax({
					type:"POST",
					url:"php/frmPaginaInicial.php",
					data: Dados,
					dataType: "json",
					beforeSend: function(){
						$("#message").loadGif('Corrigindo anotação, por favor aguarde...')
					},
					success:function(data){
						if(!data.hasOwnProperty('error')){
							ArcaDialog.Alert(data.message, function(){					    		
					    		$("#message").html("");
					    		Route.href( './home');
					    		return;					    			
					    	});
						}
						else{
							ArcaDialog.Alert(data.error);
						}
					},
					error:  function(data){
					    console.log(data.responseText);
					}
				});
			}
		}
	});
}

function excluirAnotacao(recno){
	ArcaDialog.YesNo("Deseja excluir a anotação? ",function(resConfirm){
		if(resConfirm == true){
			console.log(recno);
			var Dados = getDadosForm();
			Dados.processo = "excluirAnotacao";
			Dados.recno = recno;
			$.ajax({
				type: "POST",
				url: "php/frmPaginaInicial.php",
				data: Dados,
				dataType: "json",
				beforeSend: function(){
					$("#message").loadGif('Excluindo anotação, por favor aguarde...');
				},
				success:function(data){
					if(!data.hasOwnProperty('error')){
				    		Route.href( './home');			    			
				    }else{
				    		ArcaDialog.Alert( data.error );
				    }
				},
				error:  function(data){
				   	console.log(data.responseText);
				}				
			});
		}
	});
}

function favoritarAnotacao(recno){

	var Dados = getDadosForm();
	Dados.processo = "favoritarAnotacao";
	Dados.recno = recno;
	$.ajax({
		type: "POST",
		url: "php/frmPaginaInicial.php",
		data: Dados,
		dataType: "json",
		success: function(data){
			if(!data.hasOwnProperty('error')){
	    		listarAnotacoes();
	    	}else{
	    		ArcaDialog.Alert( data.error );
	    	}
		},
		error:  function(data){
		   	console.log(data.responseText);
		}				
	});
}

function arquivarAnotacao(recno, arquivado ){

	var msgArquiva = (arquivado == 1) ? "desarquivar" :  "arquivar";
	var Dados = getDadosForm();
	Dados.processo = 'arquivarAnotacao',
	Dados.recno = recno,
	ArcaDialog.YesNo("Deseja "+msgArquiva+" a anotação?", function(resConfirm){
		if(resConfirm === true){
			$.ajax({
				type: "POST",
				url: "php/frmPaginaInicial.php",
				data: Dados,
				dataType: "json",
				success: function(data){
					if(!data.hasOwnProperty('error')){
			    		listarAnotacoes();
			    	}else{
			    		ArcaDialog.Alert( data.error );
			    	}
				},
				error:  function(data){
				   	console.log(data.responseText);
				}	
			});
		}
	});
}

function completarAnotacao(recno){
	var Dados = getDadosForm();
	Dados.processo = "completarAnotacao";
	Dados.recno = recno;
	$.ajax({
		type: "POST",
		url: "php/frmPaginaInicial.php",
		data: Dados,
		dataType: "json",
		success: function(data){
			if(!data.hasOwnProperty('error')){
				listarAnotacoes();
			}else{
				ArcaDialog.Alert( data.error );
			}
		},
		error: function(data){
			console.log(data.responseText);
		}
	});
}

function listarAnotacoes(){
	var Dados = getDadosForm();
		Dados.processo = "listarAnotacoes"
	$.ajax({
		type: "POST",
		url:  "php/frmPaginaInicial.php",
		data: Dados,

		dataType: "json",
		beforeSend:function(){
			$("#listaAnotacoesPendentes").loadGif('Carregando resultados, por favor aguarde.');
		},
		success: function(data){
			$("#listaAnotacoesPendentes").html(data.gridPendentes);
			$("#listaAnotacoesCompletas").html(data.gridCompletas);
			$("#listaAnotacoesArquivadas").html(data.gridArquivadas);			
		},

	});
}

function realizaAcao(e){
    var unicode =e.keyCode? e.keyCode : e.charCode
    if(unicode == 13){     
        incluirAnotacao();
    }
}

$(document).ready(function() {	
	listarAnotacoes();
});