; (function ($) {
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
            updateErrorMessage: 'An error occurred in updating the table',
            loaderClass: 'js-loader',
            loaderStandaloneClass: 'js-loader-standalone',
            loaderOnClass: 'js-show-loader',
            loaderContainerSelector: '.table-responsive',
            doScrollEvent: true,
            scrollUpdateInt: 100,
            updateDebounceInt: 400
        },

        jsData: null,
        endpoint: '',
        request: null,
        $paginator: null,
        updateInt: null,

        sortColumn: '',
        sortDirection: '',
        pageNumber: 1,
        customData: {},


        _create: function () {
            this._super();
            this.showLoader();

            this.jsData = this.element.data(this.options.jsDataKey);

            if (!this.jsData) {
                throw 'No js-data params have been set';
            }

            if (!this.jsData[this.options.endpointDataKey]) {
                throw 'Table must have an updater endpoint in its data array'
            }

            if (this.jsData['pageNumber']) {
                this.pageNumber = parseInt(this.jsData['pageNumber'], 10);
            }

            var orderSettings = this.jsData['order'] || {};

            if (orderSettings['dir']) {
                this.sortDirection = orderSettings['dir'];
            }

            if (orderSettings['column']) {
                this.sortColumn = orderSettings['column'];
            }

            this.endpoint = this.jsData[this.options.endpointDataKey];
            this.$paginator = $(this.jsData[this.options.paginatorSelectorDataKey]);

            if (!this.$paginator.length) {
                this.$paginator = null;
            }

            this.addEvents();
            this.hideLoader();

            if (this.options.doScrollEvent) {
                this.updateLoaderPosition();
            }
        },

        namespaceEvent: function (event) {
            return event + "." + event;
        },

        addEvents: function () {
            var events = {},
                self = this;

            events['click ' + this.options.sortableHeaderSelector] = function (ev) {
                ev.preventDefault();

                var $target = $(ev.target),
                    header = $target.data(self.options.elementColumnIdDataKey);

                self.sortColumn = header;

                var isAsc = $target.hasClass(self.options.sortableAscClass);
                var isDesc = $target.hasClass(self.options.sortableDescClass);

                if (!isAsc || !isDesc) {
                    self.sortDirection = SORT_DIR_ASC;
                    $target.addClass(self.options.sortableAscClass);
                }

                if (isAsc) {
                    self.sortDirection = SORT_DIR_DESC;
                    $target.addClass(self.options.sortableDescClass);
                }

                if (isDesc) {
                    self.sortColumn = '';
                    self.sortDirection = '';
                } else {
                    self.sortColumn = header;
                }

                self.update();
            };

            if (this.$paginator) {
                $(this.options.paginatorPageButtonSelector, this.$paginator).on(
                    this.namespaceEvent('click'), function (ev) {
                        ev.preventDefault();
                        var $this = $(ev.target);
                        self.gotoPage($this.data(self.options.paginatorPageDataKey));
                    }
                );

                $(this.options.paginatorPreviousSelector, this.$paginator).on(
                    this.namespaceEvent('click'), function (ev) {
                        ev.preventDefault();
                        self.gotoPreviousPage();
                    }
                );

                $(this.options.paginatorNextSelector, this.$paginator).on(
                    this.namespaceEvent('click'), function (ev) {
                        ev.preventDefault();
                        self.gotoNextPage();
                    }
                );
            }

            if (this.options.doScrollEvent) {
                var timeout;

                this.getLoaderContainer().on('scroll.impresariotableupdater', function (ev) {
                    if (!timeout) {
                        timeout = window.setTimeout(function () {
                            self.updateLoaderPosition();
                            window.clearTimeout(timeout);
                            timeout = null;
                        }, 5);
                    }
                });
            }

            this._on(events);
            return this;
        },

        addCustomData: function (key, data) {
            this.customData[key] = data;
            this.debounceUpdate();
        },

        updateLoaderPosition: function () {
            var $loaderContainer = this.getLoaderContainer(),
                $loaderElement = this.getLoaderElement();

            if (!$loaderContainer.length || !$loaderElement.length) {
                return;
            }

        },

        showLoader: function () {
            this.createLoader();
            this.getLoaderElement().addClass(this.options.loaderOnClass);

            this._trigger('loader-added');
        },

        hideLoader: function () {
            this.getLoaderElement().removeClass(this.options.loaderOnClass);
        },

        createLoader: function () {
            var $loaderElement = $("." + this.options.loaderClass, this.getLoaderContainer());

            if ($loaderElement.length) {
                return;
            }

            $loaderElement = $('<div></div>');
            $loaderElement.addClass(this.options.loaderClass).addClass(this.options.loaderStandaloneClass);

            this.getLoaderContainer().append($loaderElement);

            return $loaderElement;
        },

        /**
		 * @param page
		 * @returns {dbwpcore.tableUpdater}
		 */
        gotoPage: function (page, force) {
            var pageNumber = Math.max(1, page);

            if (pageNumber !== this.pageNumber) {
                this.pageNumber = pageNumber;
                this.update();
            }

            return this;
        },

        getLoaderElement: function () {
            var $loaderElement = $("." + this.options.loaderClass, this.getLoaderContainer());

            if ($loaderElement.length) {
                return $loaderElement;
            }

            return this.createLoader();
        },

        getLoaderContainer: function () {
            return this.element.closest(this.options.loaderContainerSelector);
        },

        /**
		 * @returns {dbwpcore.tableUpdater}
		 */
        gotoPreviousPage: function () {
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
        getUpdateData: function () {
            return {
                'order': {
                    'dir': this.sortDirection,
                    'column': this.sortColumn
                },
                'pageNumber': this.pageNumber,
                'customData': this.customData
            }
        },

        debounceUpdate: function () {
            var self = this;
            
            if (this.updateInt) {
                window.clearTimeout(this.updateInt);
            }

            this.updateInt = window.setTimeout(function () {
                self.update();
                window.clearTimeout(self.updateInt);
                self.updateInt = null;
            }, this.options.updateDebounceInt);

        },

        /**
		 * @returns {dbwpcore.tableUpdater}
		 */
        update: function () {
            var self = this;

            this.showLoader();
            console.log("UPDATE");

            if (this.request) {
                try {
                    this.request.abort();
                } catch (e) {
                    // For darling IE
                }
            }

            this.request = $.ajax(
                this.endpoint, {
                method: 'POST',
                data: this.getUpdateData(),
                success: function () {
                    self._trigger('success');
                },
                complete: function (request) {
                    self._trigger('complete');

                    if (request.status == 200) {
                        initialise();
                    }
                    self.hideLoader();
                },
                error: function (request) {
                    if (request.status !== 0) {
                        alert(self.options.updateErrorMessage);
                    }
                    self._trigger('error');
                }
            }
            );

            return this;
        }
    });

    function initialise() {
        $(DEFAULT_INIT_SELECTOR).tableUpdater();
    }

    $(function () {
        initialise();
    });
}(jQuery));