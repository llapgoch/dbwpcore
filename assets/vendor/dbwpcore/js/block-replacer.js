;(function($){
	var BLOCK_REPLACERS_DATA_KEY = '__block__replacers__';

	$(document).on('ajaxComplete', function(event, request){
		var response = request.responseJSON;
		var replacers = response[BLOCK_REPLACERS_DATA_KEY];

		if(!replacers){
			return;
		}

		for(var i in replacers){
			$('[data-' + BLOCK_REPLACERS_DATA_KEY + '="' + i + '"]').replaceWith(replacers[i]);
		}
	});
}(jQuery));