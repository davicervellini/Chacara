$.fn.loadGif = function(sMessage){

	sMessage = (sMessage !== "") ? "<div style='padding-left:10px;line-height: 32px; height:32px'>"+sMessage+"</div> ": "";
	$(this).html("<div class=\"row\">\
		<div class=\"col l12 center-align\">\
			<div class=\"preloader-wrapper big active\">\
				<div class=\"spinner-layer spinner-green-only\">\
					<div class=\"circle-clipper left\">\
					<div class=\"circle\"></div>\
					</div><div class=\"gap-patch\">\
					<div class=\"circle\"></div>\
					</div><div class=\"circle-clipper right\">\
					<div class=\"circle\"></div>\
					</div>\
				</div>\
			</div>"+sMessage+"\
		</div></div>");
	
}
