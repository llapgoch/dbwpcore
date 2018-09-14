;(function($){
	var DEFAULT_INIT_SELECTOR = '.js-table-updater',
		SORT_DIR_ASC = 'asc',
		SORT_DIR_DESC = 'desc';

	$.widget('dbwpcore.tableUpdater', {
		// TODO: Make options available from Table Definitions file so we're not duplicating
		options: {
			namespace: 'dbwpcoretableupdater',
			jsDataKey: 'jsData',
			endpointDataKey: 'endpoint',
			elementColumnIdDataKey: 'columnId',
			sortableColumnDataKey: 'sortableColumn',
			paginatorSelectorDataKey: 'paginatorSelector',
			columnIdDataKey: 'columnId',
			sortableHeaderSelector: '.js-is-sortable',
			paginatorPreviousSelector: '.js-paginator-previous-button',
			paginatorNextSelector: '.js-paginator-next-button',
			paginatorPageButtonSelector: '.js-paginator-page-button',
			paginatorPageDataKey: 'pageId',
			sortableAscClass: 'sort-asc',
			sortableDescClass: 'sort-desc',
			updateErrorMessage: 'An error occurred in updating the table'
		},

		jsData: null,
		endpoint: '',
		request: null,
		$paginator: null,

		sortColumn: '',
		sortDirection: '',
		pageNumber: 1,

		_create: function () {
			this._super();

			this.jsData = this.element.data(this.options.jsDataKey);

			if(!this.jsData){
				throw 'No js-data params have been set';
			}

			if(!this.jsData[this.options.endpointDataKey]){
				throw 'Table must have an updater endpoint in its data array'
			}

			if(this.jsData['pageNumber']){
				this.pageNumber = parseInt(this.jsData['pageNumber'], 10);
			}

			var orderSettings = this.jsData['order'];

			if(orderSettings['dir']){
				this.sortDirection = orderSettings['dir'];
			}

			if(orderSettings['column']){
				this.sortColumn = orderSettings['column'];
			}

			this.endpoint = this.jsData[this.options.endpointDataKey];
			this.$paginator = $(this.jsData[this.options.paginatorSelectorDataKey]);

			if(!this.$paginator.size()){
				this.$paginator = null;
			}

			this.addEvents();
		},

		/**
		 * @param event
		 * @returns {string}
		 */
		namespaceEvent: function(event)
		{
			return event + "." + event;
		},

		/**
		 * @returns {dbwpcore.tableUpdater}
		 */
		addEvents: function() {
			var events = {},
				self = this;

			events['click ' + this.options.sortableHeaderSelector] = function(ev){
				ev.preventDefault();

				var $target = $(ev.target),
					header = $target.data(self.options.elementColumnIdDataKey);

				this.sortColumn = header;

				var isAsc = $target.hasClass(this.options.sortableAscClass);
				var isDesc = $target.hasClass(this.options.sortableDescClass);

				if(!isAsc || !isDesc){
					self.sortDirection = SORT_DIR_ASC;
					$target.addClass(this.options.sortableAscClass);
				}

				if(isAsc){
					self.sortDirection = SORT_DIR_DESC;
					$target.addClass(this.options.sortableDescClass);
				}

				if(isDesc){
					this.sortColumn = '';
					this.sortDirection = '';
				}else{
					this.sortColumn = header;
				}

				self.update();
			};

			if(this.$paginator){
				$(this.options.paginatorPageButtonSelector, this.$paginator).on(
					this.namespaceEvent('click'), function(ev){
						ev.preventDefault();
						var $this = $(ev.target);
						self.gotoPage($this.data(self.options.paginatorPageDataKey));
					}
				);

				$(this.options.paginatorPreviousSelector, this.$paginator).on(
					this.namespaceEvent('click'), function(ev){
						ev.preventDefault();
						self.gotoPreviousPage();
					}
				);

				$(this.options.paginatorNextSelector, this.$paginator).on(
					this.namespaceEvent('click'), function(ev){
						ev.preventDefault();
						self.gotoNextPage();
					}
				);
			}

			this._on(events);
			return this;
		},

		/**
		 * @param page
		 * @returns {dbwpcore.tableUpdater}
		 */
		gotoPage: function(page) {
			var pageNumber = Math.max(1, page);

			if(pageNumber !== this.pageNumber){
				this.pageNumber = pageNumber;
				this.update();
			}

			return this;
		},

		/**
		 * @returns {dbwpcore.tableUpdater}
		 */
		gotoPreviousPage: function() {
			this.gotoPage(this.pageNumber - 1);
			return this;
		},

		/**
		 * @returns {dbwpcore.tableUpdater}
		 */
		gotoNextPage: function () {
			this.gotoPage(this.pageNumber + 1);
			return this;
		},

		/**
		 *
		 * @returns {{order: {dir: (string|*), column: string, pageNumber: *}}}
		 */
		getUpdateData: function(){
			return {
				'order': {
					'dir' : this.sortDirection,
					'column': this.sortColumn
				},
				'pageNumber': this.pageNumber
			}
		},

		/**
		 * @returns {dbwpcore.tableUpdater}
		 */
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
				this.endpoint, {
					method: 'POST',
					data: this.getUpdateData(),
					complete: function(request){
						if(request.status == 200){
							initialise();
						}
					},
					error: function(request){
						if(request.status !== 0) {
							alert(self.options.updateErrorMessage);
						}
					}
				}
			);

			return this;
		}
	});

	function initialise(){
		$(DEFAULT_INIT_SELECTOR).tableUpdater();
	}

	$(document).on('ready.dbwpcoretableupdater', function(){
		initialise();
	});
}(jQuery));