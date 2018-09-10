;(function($){
	var DEFAULT_INIT_SELECTOR = '.js-table-updater';

	$.widget('dbwpcore.tableUpdater', {
		// TODO: Make options available from Table Definitions file so we're not duplicating
		options: {
			tableUpdaterEndpointKey: 'tableUpdaterEndpoint',
			sortableHeaderSelector: '.js-is-sortable',
			sortableColumnDataKey: 'sortableColumn',
			sortableAscClass: 'sort-asc',
			sortableDescClass: 'sort-desc'
		},

		updateUrl: '',
		request: null,


		_create: function () {
			this._super();

			if(!this.element.data(this.options.tableUpdaterEndpointKey)){
				throw 'Table must have an updater endpoint in its data array'
			}

			this.updateUrl = this.element.data(this.options.tableUpdaterEndpointKey);
			this.addEvents();
		},

		addEvents: function() {
			var events = {},
				self = this;

			events['click ' + this.options.sortableHeaderSelector] = function(ev){
				self.update();
			};

			this._on(events);
		},

		update: function() {
			if(this.request){
				try {
					this.request.abort();
				} catch (e){
					// For darling IE
				}
			}

			this.request = $.ajax(
				this.updateUrl, {
					method: 'POST',
					data: {'horse':'morse'}
				}
			)
		}


	});

	$(document).on('ready', function(){
		$(DEFAULT_INIT_SELECTOR).tableUpdater();
	});
}(jQuery));