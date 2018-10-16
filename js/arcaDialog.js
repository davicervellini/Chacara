jconfirm.defaults = {
    theme: 'modern',
    bgOpacity: 0.8,
};



var ArcaDialog = (function(){
	var instance; 

	function init(){

		function YesNo(text, callback){
			$.confirm({
				content: text,
				typeAnimated: true,
				buttons: {
				    Yes: {
						text: 'Sim',
						keys: ['enter'],
				        btnClass: 'btn background btn-dialog-width',
				        action: function(){
				            (callback) && callback(true);
				        }
					},
				    No: {
				        text: 'Não',
				        btnClass: 'btn background btn-dialog-width',
				        action: function(){
				            (callback) && callback(false);
				        }
				    },
				}
			});
		}

		function YesNoCancel(text, callback){
			$.confirm({
				content: text,
				typeAnimated: true,
				buttons: {
				    Yes: {
						text: 'Sim',
						keys: ['enter'],
				        btnClass: 'btn background btn-dialog-width',
				        action: function(){
				            (callback) && callback(true);
				        }
					},
				    No: {
				        text: 'Não',
				        btnClass: 'btn background btn-dialog-width',
				        action: function(){
				            (callback) && callback(false);
				        }
				    },
				    Cancel: {
				    	text: 'Cancelar',
				        btnClass: 'btn background',
				        action: function(){
				            (callback) && callback('cancel');
				        }
				    },
				}
			});
		}

		function Alert(text, callback){
			$.alert({
				content: text,
				typeAnimated: true,
				buttons: {
				    Ok: {
				    	text: 'Ok',
				    	keys: ['enter'],
				        btnClass: 'btn background btn-dialog-width',
				        action: function(){
							(callback) && callback(true);
				        }
				    }
				},

			});
		}

		return {
			YesNo:       YesNo,
			YesNoCancel: YesNoCancel,
			Alert:       Alert,
		};
	}
	
	return {
		getInstance: function(){
			if(!instance){
				instance = init();
			}

			return instance;
		}
	}
})();

var ArcaDialog = ArcaDialog.getInstance();