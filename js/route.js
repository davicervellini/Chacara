var Route = {
	href: function(sLink){
		window.location.href = document.getElementsByTagName('base')[0].href + sLink
	},
	open: function(sLink){
		window.open(document.getElementsByTagName('base')[0].href + sLink);
	},
	openRemoteLink: function(sLink){
		window.open(sLink);
	},
	base: document.getElementsByTagName('base')[0].href,
	pushState: function(sLink){
		var targetUrl = window.location.pathname + sLink;
		window.history.pushState({url: "" + targetUrl + ""}, '', targetUrl);
	}
}