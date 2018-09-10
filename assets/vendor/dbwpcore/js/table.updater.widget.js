;(function($){
	var DEFAULT_INIT_SELECTOR = '.js-table-updater',
		SORT_DIR_ASC = 'asc',
		SORT_DIR_DESC = 'desc';

	$.widget('dbwpcore.tableUpdater', {
		// TODO: Make options available from Table Definitions file so we're not duplicating
		options: {
			tableUpdaterEndpointDataKey: 'tableUpdaterEndpoint',
			elementColumnIdDataKey: 'columnId',
			sortableColumnDataKey: 'sortableColumn',
			columnIdDataKey: 'columnId',
			sortableHeaderSelector: '.js-is-sortable',
			sortableAscClass: 'sort-asc',
			sortableDescClass: 'sort-desc',
			updateErrorMessage: 'An error occurred in updating the table'
		},

		updateUrl: '',
		request: null,

		sortColumn: '',
		sortDirection: '',

		_create: function () {
			this._super();

			if(!this.element.data(this.options.tableUpdaterEndpointDataKey)){
				throw 'Table must have an updater endpoint in its data array'
			}

			this.updateUrl = this.element.data(this.options.tableUpdaterEndpointDataKey);
			this.addEvents();
		},

		addEvents: function() {
			var events = {},
				self = this;

			events['click ' + this.options.sortableHeaderSelector] = function(ev){
				var $target = $(ev.target),
					header = $target.data(self.options.elementColumnIdDataKey);

				this.sortColumn = header;

				if($target.hasClass(this.options.sortableAscClass)){
					self.sortDirection = SORT_DIR_DESC;
					$target.removeClass(this.options.sortableAscClass).addClass(this.options.sortableDescClass);
				} else {
					self.sortDirection = SORT_DIR_ASC;
					$target.removeClass(this.options.sortableDescClass).addClass(this.options.sortableAscClass);
				}

				if(header){
					this.sortColumn = header;
				}

				self.update();
			};

			this._on(events);
		},

		getUpdateData: function(){
			return {
				'order': {
					'dir' : this.sortDirection,
					'column': this.sortColumn
				}
			}
		},

		update: function() {
			var self = this;

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
					data: this.getUpdateData(),
					complete: function(request){
						if(request.status == 200){
							initialise();
						}
					},
					error: function(){
						alert(self.options.updateErrorMessage);
					}
				}
			)
		}
	});

	function initialise(){
		$(DEFAULT_INIT_SELECTOR).tableUpdater();
	}

	$(document).on('ready', function(){
		initialise();
	});
}(jQuery));