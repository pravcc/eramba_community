"use strict";

YoonityJS.classFile({
	uses: [
		'//Controllers/AppController'
	],
	
	namespace: 'Controllers',
	
	class: {
		IndexFiltersController: function(scopes)
		{
			// Declare global var for saving reference of future created object
			var _this;
			
			return {
				Extends: scopes.Controllers.AppController,
				
				filterObjectId: '',
				$filterObject: null,
				dataTableConfig: null,
				$dataTable: null,
				table: null,
				bulkActionCheckboxesSelector: ".bulk-action-checkbox:not(.bulk-action-check-all-checkbox):not(:disabled)",

				constructor: function(params)
				{
					// Save current object reference for inner scopes
					_this = this;
					
					// Call parent constructor
					_this._parent.constructor(params);

					var properties = {
						Registry: null,
						colsParams: {
							'resize': [],
							'order': [],
							'sort': [],
							'wrap': []
						}
					};

					YoonityJS.Class.InitProperties.call(this, properties, params);

					_this.filterObjectId = $(_this.Registry.getObject('request').getObject()).attr('data-yjs-app-data-filter-object-id');
					_this.$filterObject = $("#advanced-filter-object-" + _this.filterObjectId);
					_this.dataTableConfig = YoonityJS.Globals.vars.get('eramba.filters.datatables.datatableConfig-' + _this.filterObjectId, null);
					_this.$dataTable = YoonityJS.Globals.vars.get('eramba.filters.datatables.datatable-' + _this.filterObjectId, null);
					_this.table = YoonityJS.Globals.vars.get('eramba.filters.datatables.table-' + _this.filterObjectId, null);
					
					var colsParams = YoonityJS.Globals.vars.get('eramba.filters.datatables.filter-' + _this.filterObjectId + '.columns-params', false);
					if (colsParams == false) {
						YoonityJS.Globals.vars.set('eramba.filters.datatables.filter-' + _this.filterObjectId + '.columns-params', _this.colsParams);
					} else {
						_this.colsParams = colsParams;
					}
				},

				$initDataTable: function(params)
				{
					$(".datatable-scrollable").on('show.bs.dropdown', function(e) {
						var eleHeight = $(e.target).outerHeight();
						var dropdown = $(e.target).children('.dropdown-menu');

						var left = $(e.target).offset().left - $(dropdown).width() + $(e.target).width();
						if (left < 100) {
							left = 100;
						}

						dropdown.css({
							position: 'absolute',
							left: left,
							top: $(e.target).offset().top + eleHeight,
							display: "block"
						}).data('prevObject', $(dropdown.prevObject)).appendTo('body');

						$(this).on('hidden.bs.dropdown', function () {
							dropdown.css({display: "none"}).appendTo(e.target);
						});
					});

					_this.initDataTableCustomPlugins(true);

					//
					// Reinitialize custom plugins and save columns order after columns were reordered
					_this.table.on('column-reorder', function(event, settings, details) {
						_this.initDataTableCustomPlugins();

						_this.saveColumnsOrderToDb();
					});
					//
					
					//
					// Reinitialize custom plugins after rows were sorted
					_this.table.on('order.dt', function(event, settings) {
						_this.initDataTableCustomPlugins();
					});
					//
					
					// _this.table.on('draw.dt', function(event) {
					// 	_this.initDataTableCustomPlugins();
					// });

					//
					// Initialize YoonityJS template
					_this.table.rows().nodes().to$().each(function(index, node) {
						new YoonityJS.InitTemplate({
							template: node
						});
						
					});
					//
					
					_this.$initGeneral();
					_this.$inlineEvents();

					_this.$initBulkActionEvents();
					_this.$toggleFilterBtns();
				},

				initDataTableCustomPlugins: function(firstInit)
				{
					var initFunc = function() {
						//
						// Initially set columns width of all resizable columns
						var columnsWidth = [];
						_this.table.columns().header().to$().each(function(index, node)
						{
							(function()
							{
								var
									columnSlug = $(node).attr('data-e-column-slug'),
									columnWidth = $(node).width(),
									columnResizable = $(node).attr('data-e-column-resizable');

								//
								// If column does not have its slug set or resizable is disabled by data-e-column-resizable attribute, stop this function
								if (columnSlug === undefined || columnResizable === 'false') {
									return;
								}
								//
								
								columnsWidth.push({
									'slug': columnSlug,
									'width': columnWidth
								});
							})();
						});
						_this.setColumnsWidth(columnsWidth);
						//
						
						// Set columns order
						_this.setColumnsOrder();

						// Initialize orderable UI
						_this.initSortable();

						// Set column sort
						_this.setColumnsSort(firstInit);

						// Destroy previously initialized resizable UI if exists
						_this.destroyResizable();

						// Initialize resizable UI
						_this.initResizable();

						// Set column width
						_this.setColumnsWidth();

						// Set column text wrap
						_this.setColumnsTextWrap(firstInit);
					};

					if (firstInit) {
						initFunc();
					} else {
						setTimeout(initFunc, 100);
					}
				},

				/**
				 * Set order for each column
				 */
				setColumnsOrder()
				{
					var
						bodyTable = $(_this.$dataTable.selector),
						headTableWrapper = bodyTable.closest('.dataTables_scroll').find('.dataTables_scrollHeadInner').first(),
						headTable = headTableWrapper.find('.datatable-scrollable').first(),
						orderColsParams = _this.colsParams['order'][0];
					
					if (!orderColsParams) {
						return;
					}

					var
						ordered = [],
						colsOrder = orderColsParams['order'].split('|');
					for (var i in colsOrder) {
						var
							columnSlug = colsOrder[i],
							column = headTable.find('th[data-e-column-slug="' + columnSlug + '"]').first().attr('data-column-index');
						if (column !== undefined) {
							ordered.push(parseInt(column));
						}
					}
					var
						fixed = [],
						others = [];
					_this.table.columns().header().to$().each(function(index, node) {
						var
							columnSlug = $(node).attr('data-e-column-slug'),
							column = parseInt($(node).attr('data-column-index'));
						if (!columnSlug) {
							fixed.push(column);
						} else if (!YoonityJS.Globals.inArray(columnSlug, colsOrder)) {
							others.push(column);
						}

					});

					var completeOrder = fixed.concat(ordered, others);

					// Set order
					_this.table.colReorder.order(completeOrder);
				},

				/**
				 * Save order of columns to DB
				 */
				saveColumnsOrderToDb: function()
				{
					var
						colsOrder = [],
						param = 'columns-order';
					_this.table.columns().header().to$().each(function(index, node) {
						var columnSlug = $(node).attr('data-e-column-slug');
						if (!columnSlug) {
							columnSlug = '-';
						}
						colsOrder.push(columnSlug);
					});

					var orderStr = colsOrder.join('|');

					// Remove old value
					_this.colsParams['order'] = [];

					// Set new value
					_this.colsParams['order'].push({
						slug: param,
						order: orderStr
					});

					//
					// Save order to DB
					_this.createRequestForColumnParamSave('order', param, orderStr);
					//
				},

				/**
				 * Initialize sortable UI for DataTable
				 */
				initSortable: function()
				{
					_this.table.columns().header().to$().each(function(index, node) {
						(function(i, n) {
							var column = parseInt($(n).attr('data-column-index'));

							// Skip non-orderable columns
							var isColumnOrderable = $(_this.table.column(column).header()).attr('data-orderable');
							if (isColumnOrderable === 'false') {
								return;
							}

							// Remove native datatables sortable click event
							$(n).off('click.DT');

							var $sortBtn = $(n).find('.sorting_btn').first();

							$sortBtn.off('click');
							$sortBtn.on('click', function(event) {
								event.preventDefault();
								event.stopPropagation();

								var sortType = 'asc';
								if($(_this.table.column(column).header()).hasClass('sorting_asc')) {
									sortType = 'desc';
								}
								_this.table.column(column).order(sortType).draw();

								_this.saveColumnSortToDb(column, sortType);
							});
						})(index, node);
					});
				},

				/**
				 * Set sorting setting for each column
				 */
				setColumnsSort: function(firstInit)
				{
					var
						bodyTable = $(_this.$dataTable.selector),
						headTableWrapper = bodyTable.closest('.dataTables_scroll').find('.dataTables_scrollHeadInner').first(),
						headTable = headTableWrapper.find('.datatable-scrollable').first(),
						sortColsParams = _this.colsParams['sort'];
					for (var cp in sortColsParams) {
						var
							columnSlug = sortColsParams[cp]['slug'],
							column = parseInt(headTable.find('th[data-e-column-slug="' + columnSlug + '"]').first().attr('data-column-index')),
							sort = sortColsParams[cp]['sort'];

						_this.table.column(column).order(sort);
					}

					if (firstInit) {
						_this.table.draw();
					}
				},

				/**
				 * Stores columns sort setting locally and also save it to DB
				 * @param  {int} column Index of column of which width will be saved in Db
				 * @param  {int} type   Type of sort (asc|desc) of column
				 */
				saveColumnSortToDb: function(column, type)
				{
					_this.storeColumnsSortSetting();

					//
					// Save column param to DB
					var columnSlug = $(_this.table.column(column).header()).attr('data-e-column-slug');
					_this.createRequestForColumnParamSave('sort', columnSlug, type);
					//
				},

				/**
				 * Store sort setting of each column
				 */
				storeColumnsSortSetting: function()
				{
					// Remove old values
					_this.colsParams['sort'] = [];

					// Set new values
					_this.table.columns().header().to$().each(function(index, node) {
						var
							column = parseInt($(node).attr('data-column-index')),
							columnSlug = $(node).attr('data-e-column-slug'),
							sortType = false;

						if ($(node).hasClass('sorting_asc')) {
							sortType = 'asc';
						} else if ($(node).hasClass('sorting_desc')) {
							sortType = 'desc';
						}

						if (sortType != false) {
							_this.colsParams['sort'].push({
								slug: columnSlug,
								sort: sortType
							});
						}
					});
				},

				/**
				 * Initialize resizable functionality
				 */
				initResizable: function()
				{
					var rsBodyTable = $(_this.$dataTable.selector);
					
					_this.table.columns().header().to$().each(function(index, node) {
						(function(i, n) {
							var column = parseInt($(n).attr('data-column-index'));

							// Skip non-resizable columns
							var isColumnResizable = $(_this.table.column(column).header()).attr('data-e-column-resizable');
							if (isColumnResizable === 'false') {
								return;
							}

							var
								nthChildIndex = i + 1,
								rsBodyTableHeader = rsBodyTable.find('thead > tr > th:nth-child(' + nthChildIndex + ')');

							// Set the same width to both table headers (visible and hidden)
							rsBodyTableHeader.each(function() {
								$(this).width($(n).width());
							});

							var
								rsHeadTable = $(n).closest('.datatable-scrollable'),
								rsScrollHeadTable = $(n).closest('.dataTables_scrollHeadInner'),
								thWidth = $(_this.table.column(column).header()).width(),
								tdWidth = _this.table.column(column).nodes().to$().first().width(),
								initialDiff = 0,
								initialThDiff = 0;

							$(n).resizable({
								alsoResize: [rsHeadTable, rsScrollHeadTable, rsBodyTable, rsBodyTableHeader],
								handles: "e",
								create: function(event, ui) {
									//
									// Disable colReorder and ordering when using resizable event
									$(n).children('.ui-resizable-handle').hover(
									function()
									{
										_this.table.colReorder.disable();
									},
									function()
									{
										_this.table.colReorder.enable();
									});
									//
								},
								resize: function(event, ui) {
									var
										tempThWidth = $(_this.table.column(column).header()).width(),
										tempTdWidth = _this.table.column(column).nodes().to$().first().width(),
										tempDiff = Math.max(tempThWidth, tempTdWidth) - Math.min(tempThWidth, tempTdWidth),
										actualThDiff = Math.max(tempThWidth, ui.size.width) - Math.min(tempThWidth, ui.size.width);

									if (ui.size.width < ui.originalSize.width) {
										if (actualThDiff >= initialThDiff + 5) {
											$(n).resizable("option", "minWidth", tempThWidth);
										} else if (tempDiff >= initialDiff + 5) {
											$(n).resizable("option", "minWidth", Math.max(tempThWidth, tempTdWidth));
										}
									}

									//
									// Set height back to auto after resize so table won't exceed its height by resize plugin
									rsHeadTable.css('height', 'auto');
									rsScrollHeadTable.css('height', 'auto');
									rsBodyTable.css('height', 'auto');
									rsBodyTableHeader.css('height', 'auto');
									//
								},
								start: function(event, ui) {
									$(n).resizable("option", "minWidth", 50);

									var
										tempThWidth = $(_this.table.column(column).header()).width(),
										tempTdWidth = _this.table.column(column).nodes().to$().first().width();

									initialThDiff = Math.max(tempThWidth, ui.size.width) - Math.min(tempThWidth, ui.size.width);
									initialDiff = Math.max(tempThWidth, tempTdWidth) - Math.min(tempThWidth, tempTdWidth);

									_this.table.column(column).nodes().to$().css('white-space', 'initial');
								},
								stop: function(event, ui) {
									var
										tempThWidth = $(_this.table.column(column).header()).width(),
										tempTdWidth = _this.table.column(column).nodes().to$().first().width(),
										newWidth = Math.max(tempThWidth, tempTdWidth);

									_this.table.column(column).nodes().to$().height(1);
									$(_this.table.column(column).header()).height(1);
									
									//
									// Set height back to auto after resize so table won't exceed its height by resize plugin
									rsHeadTable.css('height', 'auto');
									rsScrollHeadTable.css('height', 'auto');
									rsBodyTable.css('height', 'auto');
									rsBodyTableHeader.css('height', 'auto');
									//
									
									_this.saveColumnWidthToDb(column, ui.size.width);
								}
							});
						})(index, node);
					});
				},

				/**
				 * Destroy initialization of resizable functionality
				 */
				destroyResizable: function()
				{
					_this.table.columns().header().to$().each(function(index, node) {
						(function(n) {
							if ($(n).resizable("instance")) {
								$(n).resizable("destroy");
							}
						})(node);
					});
				},

				/**
				 * Set width of each column
				 */
				setColumnsWidth: function(columnsWidth)
				{
					var
						bodyTable = $(_this.$dataTable.selector),
						headTableWrapper = bodyTable.closest('.dataTables_scroll').find('.dataTables_scrollHeadInner').first(),
						headTable = headTableWrapper.find('.datatable-scrollable').first(),
						resizeColsParams = columnsWidth || _this.colsParams['resize'];
					
					for (var cp in resizeColsParams) {
						var
							columnSlug = resizeColsParams[cp]['slug'],
							column = parseInt(headTable.find('th[data-e-column-slug="' + columnSlug + '"]').first().attr('data-column-index')),
							width = resizeColsParams[cp]['width'];

						if (!column) {
							continue;
						}

						var
							headTableHeaderCol = _this.table.column(column).header(),
							bodyTableHeaderCol = bodyTable.find('thead > tr > th:nth-child(' + (column + 1) + ')');

						// Disable white-space nowrap setting
						_this.table.column(column).nodes().to$().css('white-space', 'initial');

						//
						// Save actual width of wrappers
						var
							bodyTableWidth = bodyTable.width(),
							headTableWrapperWidth = headTableWrapper.width(),
							headTableWidth = headTable.width();
						//
						
						//
						// Save actual width of column
						var headTableHeaderColWidth = $(headTableHeaderCol).width();
						//
						
						//
						// Set new width of wrappers
						var
							ntwTemp = Math.max(headTableHeaderColWidth, width) - Math.min(headTableHeaderColWidth, width),
							ntw = 0;
						if (headTableHeaderColWidth > width) {
							ntw = headTableWidth - ntwTemp;
							
						} else if (headTableHeaderColWidth < width) {
							ntw = headTableWidth + ntwTemp;
						}

						if (ntw > 0) {
							bodyTable.width(ntw);
							headTableWrapper.width(ntw);
							headTable.width(ntw);
						}
						//
						
						//
						// Set new width of column
						$(headTableHeaderCol).width(width);
						bodyTableHeaderCol.width(width);
						//
					}

				},

				/**
				 * Stores columns width locally and also save it to DB
				 * @param  {int} column Index of column of which width will be saved in Db
				 * @param  {int} width  Width of column
				 */
				saveColumnWidthToDb: function(column, width)
				{
					_this.storeColumnsWidth();

					//
					// Save column param to DB
					var columnSlug = $(_this.table.column(column).header()).attr('data-e-column-slug');
					_this.createRequestForColumnParamSave('resize', columnSlug, width);
					//
				},

				/**
				 * Store width of each column
				 */
				storeColumnsWidth: function()
				{
					// Remove old values
					_this.colsParams['resize'] = [];

					// Set new values
					_this.table.columns().header().to$().each(function(index, node) {
						var
							columnIndex = parseInt($(node).attr('data-column-index')),
							columnSlug = $(node).attr('data-e-column-slug'),
							resizable = $(node).attr('data-e-column-resizable');

						//
						// If column does not have its slug set or resizable is disabled by data-e-column-resizable attribute, stop this function
						if (columnSlug !== undefined && resizable !== 'false') {
							_this.colsParams['resize'].push({
								slug: columnSlug,
								width: $(_this.table.column(columnIndex).header()).width()
							});
						}
						//
					});
				},

				/**
				 * Set text wrapping setting for each column
				 */
				setColumnsTextWrap: function(firstInit)
				{
					//
					// Disable popover as default
					if (firstInit) {
						_this.table.column(column).nodes().to$().each(function(index, node) {
							var
								$contentWrapper = $(node).find('.datatable-cell-content-wrapper'),
								$textContentWrapper = $contentWrapper.find('.text-content-wrapper');

							$textContentWrapper.each(function()
							{
								$(this).closest('span[data-popup="popover"]').popover('disable');
							});
						});
					}
					//

					var
						bodyTable = $(_this.$dataTable.selector),
						headTableWrapper = bodyTable.closest('.dataTables_scroll').find('.dataTables_scrollHeadInner').first(),
						headTable = headTableWrapper.find('.datatable-scrollable').first(),
						wrapColsParams = _this.colsParams['wrap'];
					for (var cp in wrapColsParams) {
						var
							columnSlug = wrapColsParams[cp]['slug'],
							column = parseInt(headTable.find('th[data-e-column-slug="' + columnSlug + '"]').first().attr('data-column-index')),
							wrap = wrapColsParams[cp]['wrap'];

						this.setColumnTextWrap(column, wrap === 'on' ? 'enable' : 'disable');

						$(_this.table.column(column).header()).attr('data-text-wrapped', wrap === 'on' ? 'true' : 'false');
					}
				},

				setColumnTextWrap: function(column, type)
				{
					_this.table.column(column).nodes().to$().each(function(index, node) {
						var
							$contentWrapper = $(node).find('.datatable-cell-content-wrapper'),
							$textContentWrapper = $contentWrapper.find('.text-content-wrapper'),
							cellLengthLimit = 150,
							textLengthLimit = cellLengthLimit,
							cellTextLength = 0;

						if ($textContentWrapper.length > 1) {
							textLengthLimit = cellLengthLimit / $textContentWrapper.length;
						}

						$textContentWrapper.each(function()
						{
							cellTextLength += $(this).text().length;
						});

						if (cellTextLength > cellLengthLimit) {
							$textContentWrapper.each(function()
							{
								var $popoverElem = $(this).closest('span[data-popup="popover"]');

								if (type === 'disable') { // Unwrap text
										var $origin = $(this).parent().children('.text-wrap-origin');
										if ($origin.length) {
											$(this).html($origin.text());
											$origin.remove();
										}

										$popoverElem.popover('disable');
								} else if (type === 'enable') { // Wrap text
									if ($(this).text().length > textLengthLimit) {
										var
											$origin = $(document.createElement('div')),
											$originOld = $(this).parent().children('.text-wrap-origin');

										if (!$originOld.length) {
											$origin.addClass('text-wrap-origin');
											$origin.html($(this).text());

											$(this).html($(this).text().substring(0, textLengthLimit) + '...');

											$(this).parent().append($origin);

											$popoverElem.popover('enable');
										}
									}
								}
							});
						}
					});
				},

				/**
				 * Toggle column wrapping
				 * @param {object} params columnSlug (Name of column)
				 */
				$toggleColumnWrapping: function(params)
				{
					var
						columnSlug = params.columnSlug || false,
						bodyTable = $(_this.$dataTable.selector),
						headTableWrapper = bodyTable.closest('.dataTables_scroll').find('.dataTables_scrollHeadInner').first(),
						headTable = headTableWrapper.find('.datatable-scrollable').first(),
						column = parseInt(headTable.find('th[data-e-column-slug="' + columnSlug + '"]').first().attr('data-column-index')),
						isTextWrapped = $(_this.table.column(column).header()).attr('data-text-wrapped');

					this.setColumnTextWrap(column, isTextWrapped === 'true' ? 'disable' : 'enable');

					var isTextWrapped = isTextWrapped === 'true' ? 'false' : 'true';
					$(_this.table.column(column).header()).attr('data-text-wrapped', isTextWrapped);

					_this.saveColumnWrappingToDb(column, isTextWrapped === 'true' ? 'on' : 'off');
				},

				/**
				 * Stores columns wraping setting locally and also save it to DB
				 * @param  {int} column Index of column of which width will be saved in Db
				 * @param  {int} type  Type of wrapping setting (on|off) of column
				 */
				saveColumnWrappingToDb: function(column, type)
				{
					_this.storeColumnsWrapping();

					//
					// Save column param to DB
					var columnSlug = $(_this.table.column(column).header()).attr('data-e-column-slug');
					_this.createRequestForColumnParamSave('wrap', columnSlug, type);
					//
				},

				/**
				 * Store wrapping setting of each column
				 */
				storeColumnsWrapping: function()
				{
					// Remove old values
					_this.colsParams['wrap'] = [];

					// Set new values
					_this.table.columns().header().to$().each(function(index, node) {
						var
							column = parseInt($(node).attr('data-column-index')),
							columnSlug = $(node).attr('data-e-column-slug'),
							textWrapped = $(node).attr('data-text-wrapped'),
							nodeWrapSetting = textWrapped === 'true' ? 'on' : 'off';

						if (columnSlug !== undefined && textWrapped !== undefined) {
							_this.colsParams['wrap'].push({
								slug: columnSlug,
								wrap: nodeWrapSetting
							});
						}
					});
				},

				/**
				 * Creates YJS request for saving column param to DB
				 * @param  {string} type  Type of column param
				 * @param  {string} slug  Column slug
				 * @param  {string} value Value of column param
				 * @return {void}
				 */
				createRequestForColumnParamSave: function(type, slug, value)
				{
					var $elem = $(document.createElement('div'));
					$elem.attr('data-yjs-request', 'index-filters/saveColumnParamToDb/filterObjectId::' + _this.filterObjectId + '/type::' + type + '/slug::' + slug + '/value::' + value);
					$elem.attr('data-yjs-server-url', 'post::/advanced_filters/advancedFilterUserParams/save');

					new YoonityJS.Init({object: $elem[0]});
				},

				/**
				 * Send column params to backend via AJAX
				 * @param  {object} params filterObjectId (ID of filter), type (general|order|sort|resize|wrap), slug (Name of column), value (Value of param)
				 * @return {void}
				 */
				$saveColumnParamToDb: function(params)
				{
					var
						filterObjectId = params.filterObjectId || false,
						type = params.type || false,
						slug = params.slug || false,
						value = params.value || false,
						types = {
							'general': 0,
							'order': 1,
							'sort': 2,
							'resize': 3,
							'wrap': 4
						};

					if (filterObjectId === false || type === false || slug === false || value === false || !types[type]) {
						return;
					}

					//
					// Send ajax request
					this.loadModel();
					scopes.addActionScope(function(_this)
					{
						_this.getModel().process({
							data: {
								'advanced_filter_id': filterObjectId,
								'type': types[type],
								'param': slug,
								'value': value
							}
						});
					}, this);
					//
				},

				$initGeneral: function(params)
				{
					// switchery toggles (simple checkboxes)
					var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery'));
					elems.forEach(function(html) {
						var switchery = new Switchery(html);
					});

					// datepicker
					$(".datepicker").each(function(i, e) {
						$(e).datepicker($(e).data());
					});

					$("input.select2, select.select2").each(function(i, e) {
						$(e).select2($(e).data());
					});

					$(".tabbable > .nav > li:first > a").trigger("click");
				},

				$inlineEvents: function(params)
				{
					$(".trigger-inline-edit").off('click.eramba').on('click.eramba', function(e) {
						e.preventDefault();
						var $td = $(this).closest(".dropdown-menu").data("prevObject").closest("td");

						$($td).find(".cell-editable").removeClass("hidden");
						$($td).find(".cell-displayed").addClass("hidden");
					});

					$(".cell-editable form").off("submit.eramba").on("submit.eramba", function(e) {
						var $td = $(this).closest("td");

						$.ajax({
							"method": "POST",
							"url": $(this).attr("action"),
							"data": $(this).serializeArray(),
							"beforeSend": function() {
								$td.block({ 
									message: '<i class="icon-spinner4 spinner"></i>',
									//timeout: 2000, //unblock after 2 seconds
									overlayCSS: {
										backgroundColor: '#000',
										opacity: 0.3,
										cursor: 'wait'
									},
									css: {
										border: 0,
										padding: 0,
										backgroundColor: 'transparent'
									}
								});	
							}
						}).done(function(data) {
							$td.html(data);
							_this.$inlineEvents();
							_this.$initGeneral();
							_this.$initBulkActionEvents();
							$(window).trigger("resize");
							_this.table.columns.adjust().draw();

							$td.unblock();
						});

						e.preventDefault();
					});

					$(".cell-close-btn").off('click.eramba').on('click.eramba', function(e) {
						var $td = $(this).closest("td");

						$td.find(".cell-displayed").removeClass("hidden");
						$td.find(".cell-editable").addClass("hidden");
					});
				},

				$initBulkActionEvents: function(params)
				{
					//
					// Initialize bulk edit checkboxes
					_this.table.rows().nodes().to$().find(".uniform").uniform();
					//

					var $bulkCheckAll = _this.$filterObject.find(".bulk-action-check-all-checkbox");

					//
					// Uncheck all checkboxes first
					$bulkCheckAll.prop('checked', false);
					$.uniform.update($bulkCheckAll);
					_this.table.$(_this.bulkActionCheckboxesSelector).removeAttr('checked');
					_this.$toggleFilterBtns();
					//

					var $bulkCheckbox = _this.table.rows().nodes().to$().find(_this.bulkActionCheckboxesSelector);

					$bulkCheckAll.off("change").on("change", function(e) {
						if ($(this).is(":checked")) {
							$bulkCheckbox.prop("checked", true);
						}
						else {
							$bulkCheckbox.prop("checked", false);
						}

						$.uniform.update($bulkCheckbox);

						_this.$toggleFilterBtns();
					});

					$bulkCheckbox.off("change").on("change", function(e) {
						if ($bulkCheckbox.filter(":checked").length == $bulkCheckbox.length) {
							$bulkCheckAll.prop("checked", true);
						}
						else {
							$bulkCheckAll.prop("checked", false);
						}

						$.uniform.update($bulkCheckAll);

						_this.$toggleFilterBtns();
					});
				},

				$toggleFilterBtns: function(params)
				{
					var
						$filterDefaultBtns = _this.$filterObject.find('.datatable-default-button'),
						$filterBulkBtns = _this.$filterObject.find('.bulk-action-button'),
						$bulkCheckbox = _this.table.rows().nodes().to$().find(_this.bulkActionCheckboxesSelector),
						count = $bulkCheckbox.filter(":checked").length;

					if (count) {
						$filterDefaultBtns.addClass('hidden');
						$filterBulkBtns.removeClass('hidden');
					} else {
						$filterDefaultBtns.removeClass('hidden');
						$filterBulkBtns.addClass('hidden');
					}
				},

				$initRow: function(params)
				{
					var $row = $(_this.Registry.getObject('request').getObject());

					$row.css({opacity: '0.5'});
					$row.find('.cell-action-dropdown').remove();

					_this._parent.$load({});
					_this.addCallback('shutdown', function(content) {
						_this.reorganizeColumnsAfterInlineEdit(_this.Registry.getObject('request').getObject());
						_this.reinitializeDatatable();

						$row.css({opacity: '1'});
					});
				},

				reorganizeColumnsAfterInlineEdit: function(rowNode)
				{
					_this.table.columns().header().to$().each(function(index, node) {
						var
							$actualCellNode = $(rowNode).find('td').eq(index),
							$previousCellNode = $(rowNode).find('td').eq(index - 1),
							columnSlug = $(node).attr('data-e-column-slug');

						var $cellNode = $(rowNode).find('td[data-e-column-slug="' + columnSlug + '"]');
						if (columnSlug !== $actualCellNode.attr('data-e-column-slug')) {
							if ($cellNode.length) {
								if (index == 0) {
									$rowNode.prepend($cellNode);
								} else {
									$cellNode.insertAfter($previousCellNode);
								}
							}
						}
					});
				},

				/**
				 * Reinitialize datatables
				 */
				reinitializeDatatable: function()
				{
					this.table.off('column-reorder');
					this.table.off('order.dt');

					$(this.$dataTable.selector).DataTable().destroy();
					this.table = $(this.$dataTable.selector).DataTable(this.dataTableConfig);

					YoonityJS.Globals.vars.set('eramba.filters.datatables.table-' + this.filterObjectId, this.table);

					// Call initialization YJS request for datatable
					new YoonityJS.Init({object: $('#datatable-init-elem-' + _this.filterObjectId)[0]});
				},

				setQueryParameter: function (queryParam, defaultValue, params)
				{
					var
						advFilterId = params.id,
						newPage = params[queryParam],
						oldPage = defaultValue,
						queryParam = '_' + queryParam,
						// queryParam = '_page',
						filterSelector = "#advanced-filter-object-" + advFilterId,
						$filterObject = $(filterSelector),
						advFilterUrl = $filterObject.data('yjs-datasource-url'),
						urlParams = YoonityJS.Globals.parseURLParams(advFilterUrl, 'get'),
						newAdvFilterUrl = "",
						newPageParam = queryParam + '=' + newPage;

					// Get old page
					if (urlParams &&
						urlParams[queryParam] !== undefined &&
						urlParams[queryParam].length) {
						oldPage = urlParams[queryParam][0];
					}

					// Check if old page equals to new page and if yes, terminate request
					if (oldPage === newPage) {
						return;
					}

					//
					// Create new url for filter object (with new page)
					if (oldPage) {
						newAdvFilterUrl = advFilterUrl.replace(queryParam + '=' + oldPage, newPageParam);
					} else {
						newAdvFilterUrl = advFilterUrl;
						if (!urlParams) {
							newAdvFilterUrl += '?';
						} else {
							newAdvFilterUrl += '&';
						}

						newAdvFilterUrl += newPageParam;
					}
					//
					
					// Set new URL to filter object
					$filterObject.attr("data-yjs-datasource-url", newAdvFilterUrl);
					$filterObject.data("yjs-datasource-url", newAdvFilterUrl);
				},

				$setLimit: function(params)
				{
					var
						advFilterId = params.id,
						filterSelector = "#advanced-filter-object-" + advFilterId,
						$ele = $(this.Registry.getObject('request').getObject());

					params.pageLimit = $ele.val();
					this.setQueryParameter("pageLimit", 0, params);

					// force first page when limit is changed
					params.page = 1;
					this.setQueryParameter("page", 0, params);

					// Reload filter object
					_this.$triggerRequest({
						0: filterSelector
					});
				},

				$setPage: function(params)
				{
					var
						advFilterId = params.id,
						filterSelector = "#advanced-filter-object-" + advFilterId;

					this.setQueryParameter("page", 0, params);

					// Reload filter object
					_this.$triggerRequest({
						0: filterSelector
					});
				}
			};
		}
	}
});