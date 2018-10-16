$(document).ready(function(){
    function sessao(){
        $.ajax({
            type: "POST",
            url: "php/frmSessao.php",
            data: "processo=verifica_sessao",
            success: function(resp){  
                if(resp != "" ){
                    alert('Sessão expirada, realize o login novamente.');
                    window.location.href = "login/";
                }
            }
        });
    }
    sessao();
    var varSessao = setInterval(function(){ sessao(); }, 300000);
});