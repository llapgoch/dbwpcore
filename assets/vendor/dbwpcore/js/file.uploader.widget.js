;(function($){
	var DEFAULT_INIT_SELECTOR = '.js-file-uploader';

	$.widget('dbwpcore.fileUploader', {
		options: {

		},

		_create: function(){

		}
	});

	function initialise(){
		$(DEFAULT_INIT_SELECTOR).fileUploader();
	}

	$(document).on('ready.dbwpcorefileuploader', function(){
		initialise();
	});
}(jQuery));