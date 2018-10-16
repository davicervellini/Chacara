
function exibeMsg(_id, desc){
	$("#divMsg").css("display","block");
	$("#msgBox").html(desc);
	$("#"+_id).focus();
}

function salvarSenha(){
	
	$("#divMsg").css("display","none");
	if($("#usuSenhaOld").val() == ""){
		exibeMsg("usuSenhaOld"," Por favor, informe a senha antiga");
		return;
	}
	if($("#usuSenha").val() == ""){
		exibeMsg("usuSenha"," Por favor, informe a senha desejada");
		return;
	}else if($("#usuNovaSenha").val() == ""){
		exibeMsg("usuNovaSenha"," Por favor, confirme a senha desejada");
		return;
	}else if($("#usuSenha").val() != $("#usuNovaSenha").val()){
		exibeMsg("usuNovaSenha"," As senhas não são idênticas");
		return;
	}

	ArcaDialog.YesNoCancel('Deseja salvar os dados?', function( responseConfirm ){
		if( responseConfirm === true){
			var campos = { usuSenha: $("#usuNovaSenha").val(), usuRecno: $("#usuRecno").val(), usuSenhaOld: $("#usuSenhaOld").val() , processo: "alterarSenha" }
			$.ajax({
	            type: "POST",
	            url: "php/frmAlterarSenha.php",
	            data: campos,
	            dataType: "json",
	            success: function(resp){
	            	if(resp.chave == 0){
	            		exibeMsg("usuSenhaOld"," Senha antiga incorreta");
						return;
	            	}
	            	ArcaDialog.Alert( resp.message, function(){
	            		window.location.reload();      	
	            	});
	            	
	            }
	        });
        }

        if( responseConfirm == "cancel"){
        	window.location.reload();	
        }

	});

}

function next(e,field){
    var unicode =e.keyCode? e.keyCode : e.charCode
    if(unicode == 13){
        $("#"+field).focus();
        salvarSenha();
    }
}

function primeiroAcesso(e){
    var unicode =e.keyCode? e.keyCode : e.charCode
    if(unicode == 13){
        salvarSenha();
    }
}

$(document).ready(function(){
	$("#usuSenhaOld").focus();
});