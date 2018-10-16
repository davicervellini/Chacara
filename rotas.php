<?php
	require_once "classes/AltoRouter.php";
	require_once "config.php";
	$router = new AltoRouter();

	$router->setBasePath(BASE_ROUTE);

	$router->map('GET', '/',		function(){ require __DIR__ . '/home.php';}, 'home');
	$router->map('GET', '/home',	function(){ require __DIR__ . '/home.php';});
	$router->map('GET', '/home/',	function(){ require __DIR__ . '/home.php';});

	$router->map('GET', '/login',	function(){ require __DIR__ .'/login.php';});
	$router->map('GET', '/login/',	function(){ require __DIR__ .'/login.php';});
	$router->map('GET', '/logout/',	function(){ require __DIR__ .'/logout.php';});

	$router->map('GET', '/primeiro-acesso',		function(){ require __DIR__ .'/primeiroAcesso.php';});
	$router->map('GET', '/primeiro-acesso/',	function(){ require __DIR__ .'/primeiroAcesso.php';});

	$router->map('GET|POST', '/atendimento/retirada/',		function(){ require __DIR__ . '/atendimento/senRetirada.php'; });
	$router->map('GET|POST', '/atendimento/chamada/',		function(){ require __DIR__ . '/atendimento/senChamada.php'; });
	$router->map('GET|POST', '/atendimento/pdf-resumo/',	function(){ require __DIR__ . '/atendimento/relatorios/php/frmSenRelatorio.php'; });

	$router->map('GET|POST', '/balcao/pdf-recibo/',			function(){ require __DIR__ . '/balcao/balcaoPDFPedido.php'; });
	$router->map('GET|POST', '/balcao/pedido-de-balcao[*]',	function(){ require __DIR__ .'/balcao/balcaoPDFPedido.php'; });

	$router->map('POST', '/cadastros/relatorios/clientes[*]',		function(){ require __DIR__ .'/cadastros/relatorios/php/relCadClientes.php'; });
	$router->map('POST', '/cadastros/relatorios/ocorrencias[*]',	function(){ require __DIR__ .'/cadastros/relatorios/php/relCadOcorrencias.php'; });

	$router->map('GET', '/consulta/indice[*]',	function(){ require __DIR__ .'/consulta/indice.php'; });

	$router->map('GET', '/gedi/visualizar[*]',	function(){ require __DIR__ .'/gedi/visualizarImagens.php'; });

	$router->map('POST', '/relatorios/etiqueta/pdf_etiquetaPequena',			function(){ require __DIR__ . '/relatorios/pdfEtiquetaPequena.php'; });
	$router->map('POST', '/relatorios/etiqueta/pdf_etiquetaPequena[*]',function(){ require __DIR__ . '/relatorios/pdfEtiquetaPequena.php'; });

	$router->map('POST', '/relatorios/etiqueta/pdf_etiquetaGrande',			function(){ require __DIR__ . '/relatorios/pdfEtiquetaGrande.php'; });
	$router->map('POST', '/relatorios/etiqueta/pdf_etiquetaGrande[*]',function(){ require __DIR__ . '/relatorios/pdfEtiquetaGrande.php'; });

	$router->map('POST', '/financeiro/livro-caixa/pdf-livro-caixa[*]',			function(){ require __DIR__ .'/financeiro/livro-caixa/php/pdfLivroCaixa.php'; });
	$router->map('POST', '/financeiro/livro-caixa/pdf-abertura-encerramento/',	function(){ require __DIR__ .'/financeiro/livro-caixa/php/pdfAberturaEncerramento.php'; });
	$router->map('POST', '/financeiro/livro-caixa/relatorio-de-balanco-anual',	function(){ require __DIR__ .'/financeiro/livro-caixa/php/pdfBalancoAnual.php'; });

	$router->map('GET', '/financeiro/digitalizar-cheques-emissao[*]',	function(){ require __DIR__ .'/financeiro/digitalizarChequesEmissao.php'; });
	$router->map('GET', '/financeiro/digitalizar-cheques-recebidos[*]',	function(){ require __DIR__ .'/financeiro/digitalizarChequesRecebidos.php'; });

	$router->map('POST', '/financeiro/cheques/imprimir-emissao[*]',					function(){ require __DIR__ .'/financeiro/cheques/php/frmImpressaoEmissao.php'; });
	$router->map('POST', '/financeiro/cheques/relatorios/pdf-cheques-emissao[*]',	function(){ require __DIR__ .'/financeiro/cheques/relatorios/php/frmRelChequesEmissao.php'; });
	$router->map('POST', '/financeiro/cheques/relatorios/pdf-cheques-em-aberto[*]',	function(){ require __DIR__ .'/financeiro/cheques/relatorios/php/frmRelChequesEmAberto.php'; });
	$router->map('POST', '/financeiro/cheques/relatorios/pdf-cheques-recebidos[*]',	function(){ require __DIR__ .'/financeiro/cheques/relatorios/php/frmRelChequesRecebidos.php'; });

	$router->map('POST', '/financeiro/pdf-movimento-de-caixa[*]',				function(){ require __DIR__ .'/financeiro/relatorios/pdfMovimentoCaixa.php'; });
	$router->map('POST', '/financeiro/pdf-fechamento-individual[*]',			function(){ require __DIR__ .'/financeiro/relatorios/pdfFechamentoIndividual.php'; });
	$router->map('POST', '/financeiro/impressao-dos-fechamentos-dos-caixas[*]',	function(){ require __DIR__ .'/financeiro/relatorios/pdfFechamentoDosCaixas.php'; });
	$router->map('POST', '/financeiro/fechamento-de-movimento-geral[*]',		function(){ require __DIR__ .'/financeiro/relatorios/pdfFechamentoMovimentoGeral.php'; });
	$router->map('POST', '/financeiro/pdf-credito-em-conta[*]',					function(){ require __DIR__ .'/financeiro/relatorios/pdfCreditoEmConta.php'; });

	$router->map('POST', '/financeiro/cheques-td-pj',			function(){ require __DIR__ .'/financeiro/relatorios/pdfChequesTdPj.php'; });
	$router->map('POST', '/financeiro/pdf-credito-em-conta[*]',	function(){ require __DIR__ .'/financeiro/relatorios/pdfCreditoEmConta.php'; });
	$router->map('POST', '/financeiro/pdf-analitico[*]',		function(){ require __DIR__ .'/financeiro/relatorios/pdfAnalitico.php'; });
	$router->map('POST', '/financeiro/cheques-td-pj',			function(){ require __DIR__ .'/financeiro/relatorios/pdfChequesTdPj.php'; });

	$router->map('GET|POST', '/financeiro/resumo-fechamento-de-caixa-td-pj',	function(){ require __DIR__ .'/financeiro/relatorios/pdfResumoFechamentoTdPj.php'; });

	$router->map('GET|POST', '/gedi/digitalizacao[*]',	function(){ require __DIR__ .'/gedi/digitalizacaoDeDocs.php'; });


	$router->map('POST' ,'/notificacoes/nt-aviso[*]', function(){ require __DIR__ .'/notificacoes/php/frmNtAviso.php'; });

	$router->map('POST', '/relatorios/aviso-de-notificacao[*]', function(){ require __DIR__ . '/relatorios/php/frmNotAviso.php'; });
	$router->map('POST' ,'/relatorios/protocolo-oficial[*]',  		                    		function(){ require __DIR__ .'/relatorios/php/frmProtocoloOficial.php'; });
	$router->map('POST' ,'/relatorios/pj-recibos-registrados[*]',  		                    		function(){ require __DIR__ .'/relatorios/php/frmPjRecibosRegistrados.php'; });
	$router->map('POST' ,'/relatorios/recibo-irregular[*]',  		                    		function(){ require __DIR__ .'/relatorios/php/frmReciboIrregular.php'; });
	$router->map('POST' ,'/relatorios/certidoes-registrados[*]',  		                    		function(){ require __DIR__ .'/relatorios/php/frmCerRegistrados.php'; });
	// Arquivo gerado em Notificação > Arquivo de Confirmação
	$router->map('POST' ,'/notificacoes/arquivo-de-confirmacao[*]',  		                    		function(){ require __DIR__ . '/notificacoes/php/frmNtGerarArquivo.php'; });
	// Arquivo gerado em Notificação > Arquivo de Retorno
	$router->map('POST' ,'/notificacoes/arquivo-de-retorno[*]',  		                    		function(){ require __DIR__ . '/notificacoes/php/frmNtGerarArquivo.php'; });

	$router->map('GET', '/[*:url]',  function($url){ require __DIR__ . '/home.php'; });

	$match = $router->match();
?>
