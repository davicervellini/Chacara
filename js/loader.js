!(function(){

	if (typeof(window.loaderID) != "string") {
		window.loaderID = "loader"+(new Date()).getTime();
	};

	var	obj = $(document.createElement("span"))
		.attr("id", loaderID)
		.css({
			display: "block",
			width: "100px",
			height: "100px"//,
			// background: "transparent url(../images/loader.png) 0 0 no-repeat"
		});
	var	tmp = document.createElement("span");
		tmp.id = "temp"+(new Date()).getTime();

	document.write(tmp.outerHTML);
	$("#"+tmp.id).after(obj).remove();

	window[loaderID] = {
		interval: false,
		position: 0,
		remove: function() {
			clearInterval(this.interval);
			obj.remove();
		},
		start: function() {
			var _ = this;
			_.interval = setInterval(function(){
				// console.log(_.position)
				obj.css({
					backgroundPosition: "-"+(_.position++ * 100)+"px 0"
				});
				if (_.position > 75) {
					_.position = 0;
				};
			}, 50);
		}
	};

	return window[loaderID];

})().start();