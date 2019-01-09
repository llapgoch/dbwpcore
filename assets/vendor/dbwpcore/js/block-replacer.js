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

			var newBlock = $(replacers[i]);

			// Trigger an event on the old block, pass in the new block so that any setup can be init'd
			$('[data-' + BLOCK_REPLACERS_DATA_KEY + '="' + i + '"]')
				.trigger('block-replacer-before', {newBlock:newBlock})
				.replaceWith(newBlock);
		}

		$(document).trigger('blockReplacerComplete');
	});
}(jQuery));