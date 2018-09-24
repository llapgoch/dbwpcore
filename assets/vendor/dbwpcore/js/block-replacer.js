;(function($){
	var BLOCK_REPLACERS_DATA_KEY = '__block__replacers__';

	$(document).on('ajaxSuccess', function(event, request){
		var response = request.responseJSON;

		if(!response || !response[BLOCK_REPLACERS_DATA_KEY]){
			return;
		}

		var replacers = response[BLOCK_REPLACERS_DATA_KEY];

		for(var i in replacers){
			if(!replacers.hasOwnProperty(i)){
				continue;
			}

			$('[data-' + BLOCK_REPLACERS_DATA_KEY + '="' + i + '"]')
				.trigger('block-replacer-before')
				.replaceWith(replacers[i])
				.trigger('block-replacer-after');
		}

		$(document.trigger('blockReplacerComplete'));
	});
}(jQuery));