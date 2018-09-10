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


		_create: function () {
			this._super();

			if(!this.element.data(this.options.tableUpdaterEndpointKey)){
				throw 'Table must have an updater endpoint in its data array'
			}
			this.addEvents();

		},

		addEvents: function() {
			var events = {};

			events['click ' + this.options.sortableHeaderSelector] = function(ev){
				alert("update");
			};

			this._on(events);
		}


	});

	$(document).on('ready', function(){
		$(DEFAULT_INIT_SELECTOR).tableUpdater();
	});
}(jQuery));