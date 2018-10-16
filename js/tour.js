// localStorage.removeItem("Tour_current_step");
// localStorage.removeItem("Tour_redirect_to");

function reiniciaTour (){
  localStorage.removeItem("Tour_end");
  localStorage.removeItem("Tour_current_step");
  localStorage.removeItem("Tour_redirect_to");
  window.location.reload();
}

function cancelaTour (){
  localStorage.Tour_current_step = "1"; 
  localStorage.Tour_end = "yes";
}

var tour = new Tour({
  name: "Tour",
  steps: [
    {
      element: ".right>li",
      title: "Menu",
      content: "Ao clicar aqui você tem acesso as funções relacionadas a conta do seu usuário",
      smartPlacement: true,
      backdrop: false,
      onShow: function(){
        // if (document.location.pathname != "/projeto_crm/home/") {
        //   document.location.href = "/projeto_crm/home/";
        // }        
      },
      onNext: function(){
        if ($(".dropdown-button").data('activates') == "dropdown1") {
          $(".dropdown-button").click();
        } 
      },      
    },
    {
      element: "#dropdown1",
      title: "Menu",
      content: "Você pode realizar ações como a alteração de sua senha e logout do sistema",
      smartPlacement: true,
      backdrop: false,
    },
    {
      element: "#slide-out",
      title: "Menu de Navegação",
      content: "Aqui você tem acesso as páginas presentes no sistema",
      path: "/projeto_crm/cadastros/usuarios/",
    },
    {
      element: ".container",
      title: "Páginas",
      content: "Todas as páginas são por padrão centralizadas como essa",
      placement: "left",
    },
  ],
  container: "body",
  smartPlacement: true,
  keyboard: true,
  backdrop: true,
  backdropContainer: 'body',
  backdropPadding: 0,
  template: "<div class='popover tour'>\
    <div class='arrow'></div>\
    <h3 class='popover-title'></h3>\
    <div class='popover-content'></div>\
    <div class='popover-navigation'>\
        <button class='btntour btntour-default' data-role='prev'>« Voltar</button>\
        <span data-role='separator'>|</span>\
        <button class='btntour btntour-default' data-role='next'>Avançar »</button>\
        <button class='btntour btntour-default' data-role='end'>Finalizar</button>\
    </div>\
  </div>",
});

// Initialize the tour
tour.init();

// Start the tour
tour.start();


var tour2 = new Tour({
  debug: true
});

tour2.init();
tour2.start();