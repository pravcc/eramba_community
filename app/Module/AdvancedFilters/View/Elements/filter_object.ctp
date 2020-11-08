<?php
$Model = $AdvancedFiltersObject->getModel();

if (!isset($FieldDataCollection)) {
	$FieldDataCollection = $Model->getFieldCollection();
}
?>
<div class="panel panel-flat">
	<?php if ($AdvancedFiltersObject->getName() !== null) : ?>
		<div class="panel-heading">
			<?= $this->AdvancedFilters->renderName($AdvancedFiltersObject); ?>
			<?= $this->AdvancedFilters->renderActions($AdvancedFiltersObject); ?>
		</div>

		<?= $this->AdvancedFilters->renderDescription($AdvancedFiltersObject); ?>
	<?php endif; ?>
	
	<?php
	$activeFilters = $this->ObjectRenderer->render('ObjectRenderer.Base', ['filterObject' => $AdvancedFiltersObject], [
		'AdvancedFilters.ActiveFilters'
	]);

	if (!empty($activeFilters)) {
		echo $this->Html->div('panel-body', $activeFilters);
	}
	?>
	<form data-yjs-form="BulkActionSectionForm" id="BulkActionForm">
		<?=
		$this->element('AdvancedFilters.data_table', [
			'FieldDataCollection' => $FieldDataCollection,
			'AdvancedFiltersObject' => $AdvancedFiltersObject
		]);
		?>
	</form>

	<?php
		/**
		 * Initialize DataTable
		 */
		echo $this->Html->tag('div', "", [
			'id' => 'datatable-init-elem-' . $AdvancedFiltersObject->getId(),
			'style' => 'display: none;',
			'data-yjs-request' => 'index-filters/initDataTable',
			'data-yjs-event-on' => 'init',
			'data-yjs-use-loader' => 'false',
			'data-yjs-app-data-filter-object-id' => $AdvancedFiltersObject->getId()
		]);
	?>
</div>
<script>
	YoonityJS.ready(function() {
		var
			filterObjectId = '<?= $AdvancedFiltersObject->getId(); ?>',
			$dataTable = $("#datatable-" + filterObjectId),
			dataTableConfig = {
				paging: false,
				info: false,
				initComplete: function() {
					var $datatableWrapper = $(this).closest('.dataTables_wrapper');

					// datatable custom HTML
					var $customHtml = $(this).closest(".dataTables_wrapper").next(".datatable-custom-elems");

					if ($customHtml.length) {
						// move footer
						$customHtml.children(".datatable-custom-footer").clone().appendTo($datatableWrapper.find(".datatable-footer"));

						// move length dropdown
						$customHtml.children(".dataTables_length").clone().appendTo($datatableWrapper.find(".datatable-header"));

						//
						// Initialize YJS on new appended elements
						var wrappers = [$datatableWrapper.find(".datatable-footer"), $datatableWrapper.find(".datatable-header")];
						for (var w in wrappers) {
							var
								$wrapper = wrappers[w],
								$initializedYjsEvents = $wrapper.find('[data-yjs-initialized-events]');
							if ($initializedYjsEvents.length) {
								$initializedYjsEvents.removeAttr('data-yjs-initialized-events');

								new YoonityJS.InitTemplate({
									template: $wrapper
								});
							}
						}
						//
					}

					$(this).closest('.dataTables_wrapper').find('.dataTables_length > label > select').select2({
						width: '50%',
						minimumResultsForSearch: Infinity
					});
				},
				autoWidth: false,
				ordering: true,
				order: [],
				// responsive: true,
				columnDefs: [{ 
		            orderable: false,
		            width: '100px',
		            targets: [ 0 ]
		        }],
				"dom": '<"datatable-header"fBl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
				colReorder: {
					fixedColumnsLeft: $('#advanced-filter-object-' + filterObjectId + ' .data-table-fixed').length,
					realtime: false
				},
				"stateSave": false,
		        scrollX: true,
		        "lengthMenu": [[10, 20, 25, 50, 100], [10, 20, 25, 50, 100]],
				buttons: [
					<?php if (empty($portalAccess)) : ?>
						{
		                    extend: 'collection',
		                    text: '<?= __('CSV'); ?> <span class="caret"></span>',
		                    className: 'btn btn-default datatable-default-button btn-icon btn-csv-dropdown',
		                    buttons: [
		                        {
		                            extend: 'csvHtml5',
		                            text: '<?= __('Export Current Page'); ?>',
		                            fieldSeparator: '<?= Configure::read('Eramba.Settings.CSV_DELIMITER'); ?>',
		                            exportOptions: {
		                                columns: '.exportable-cell',
		                                format: {
		                                    body: function ( data, row, column, node ) {
		                                        return $(node).data("search");
		                                    }
		                                }
		                            }
		                        },
		                        {
		                            text: '<?= __('Export All Pages'); ?>',
		                            action: function(e, dt, node, config) {
		                            	<?php if ($AdvancedFiltersObject->isDbFilter) : ?>
			                                window.location = "<?php echo Router::url([
			                                    'plugin' => 'advanced_filters',
			                                    'controller' => 'advancedFilters',
			                                    'action' => 'exportCsvAll',
			                                    $AdvancedFiltersObject->getId()
			                                ]); ?>"
		                                <?php else : ?>
		                                	window.location = "<?php echo Router::url([
			                                    'plugin' => 'advanced_filters',
			                                    'controller' => 'advancedFilters',
			                                    'action' => 'exportCsvAllQuery',
			                                    $Model->alias,
			                                    '?' => array_merge($this->request->query, [
													'advanced_filter' => 1
												])
			                                ]); ?>"
		                                <?php endif; ?>
		                            }
		                        }
		                    ]
		                },
						<?php if ($AdvancedFiltersObject->csvCountExport) : ?>
						{
							className: 'btn btn-default datatable-default-button',
							text: 'CSV Daily Count',
							action: function(e, dt, node, config) {
								window.location = "<?php echo Router::url([
									'plugin' => 'advanced_filters',
									'controller' => 'advancedFilters',
									'action' => 'exportDailyCountResults',
									$AdvancedFiltersObject->getId()
								]); ?>"
							}
						},
						<?php endif; ?>
						<?php if ($AdvancedFiltersObject->csvDataExport) : ?>
						{
							className: 'btn btn-default datatable-default-button datatable-button-first',
							text: 'CSV Daily Data',
							action: function(e, dt, node, config) {
								window.location = "<?php echo Router::url([
									'plugin' => 'advanced_filters',
									'controller' => 'advancedFilters',
									'action' => 'exportDailyDataResults',
									$AdvancedFiltersObject->getId()
								]); ?>"
							}
						},
						<?php endif; ?>
						<?php
						// manage button only for real filter object instances
						// $conds = !$Trash->isTrash();
						// $conds = $AdvancedFiltersObject->manageFilter() !== false;
						?>
						<?php if ($AdvancedFiltersObject->manageFilter()) : ?>
						{
							className: 'btn btn-default datatable-default-button datatable-button-last',
							text: '<?= __('Manage Filter'); ?>',
							action: function(e, dt, node, config) {
								var url = $(node).closest("[data-edit-url]").data('edit-url');
								// return window.location = url;
							
								$(node).data('yjs-request', 'crud/showForm');
								$(node).data('yjs-target', 'modal');
								$(node).data('yjs-event-on', 'click');
								$(node).data('yjs-datasource-url', url);
								var YoonityJSObject = new YoonityJS.Init({
									object: node
								});
							}
						},
						<?php endif; ?>

						<?php if (isset($BulkActions) && $BulkActions->enabled()) : ?>
							<?php if ($Model->alias != 'Queue' && $Model->alias != 'User' && !$Model instanceof MappingRelation) : ?>
								{
									className: 'btn btn-primary bulk-action-button datatable-button-first hidden',
									text: 'Bulk Edit',
									action: function(e, dt, node, config) {
										var $filterObject = $(node).closest(".advanced-filter-object");
										var filterId = $filterObject.attr("id");

										$(node).data('yjs-request', 'crud/bulkAction/id::' + filterId);
										$(node).data('yjs-target', 'modal');
										$(node).data('yjs-event-on', 'click');
										$(node).data('yjs-datasource-url', "<?= Router::url($Model->getMappedRoute([
											'action' => 'edit',
											'?' => [
												'BulkActions' => true
											]
										])); ?>");

										var YoonityJSObject = new YoonityJS.Init({
											object: node
										});
									}
								},
							<?php endif; ?>	
							<?php if ($Model->alias != 'VendorAssessmentFeedback') : ?>				
								{
									className: 'btn btn-danger bulk-action-button datatable-button-last hidden',
									text: 'Bulk Delete',
									action: function(e, dt, node, config) {
										var $filterObject = $(node).closest(".advanced-filter-object");
										var filterId = $filterObject.attr("id");

										$(node).data('yjs-request', 'crud/bulkAction/id::' + filterId);
										$(node).data('yjs-target', 'modal');
										$(node).data('yjs-event-on', 'click');
										$(node).data('yjs-datasource-url', "<?= Router::url($Model->getMappedRoute([
											'action' => 'delete',
											'?' => [
												'BulkActions' => true
											]
										])); ?>");

										var YoonityJSObject = new YoonityJS.Init({
											object: node
										});
									}
								}
							<?php endif; ?>	
						<?php endif; ?>
					<?php endif; ?>
				]
			};

		var table = $dataTable.DataTable(dataTableConfig);

		YoonityJS.Globals.vars.set('eramba.filters.datatables.datatableConfig-' + filterObjectId, dataTableConfig);
		YoonityJS.Globals.vars.set('eramba.filters.datatables.datatable-' + filterObjectId, $dataTable);
		YoonityJS.Globals.vars.set('eramba.filters.datatables.table-' + filterObjectId, table);

		<?php
		//
		// Prepare columns params saved in DB
		$FilterParamSet = $AdvancedFiltersObject->FilterParamSet();
		$columnResizeSettings = [];
		$columnOrderSettings = [];
		$columnSortSettings = [];
		$columnTextWrapSettings = [];
		$columnSettingsJson = "";
		if (!empty($FilterParamSet)) {
			foreach ($AdvancedFiltersObject->FilterParamSet()->getGroup(AdvancedFilterUserParam::TYPE_COLUMN_RESIZE) as $slug => $width) {
				$columnResizeSettings[] = [
					'slug' => $slug,
					'width' => $width
				];
			}
			
			foreach ($AdvancedFiltersObject->FilterParamSet()->getGroup(AdvancedFilterUserParam::TYPE_COLUMN_ORDER) as $slug => $order) {
				$columnOrderSettings[] = [
					'slug' => $slug,
					'order' => $order
				];
			}
			
			foreach ($AdvancedFiltersObject->FilterParamSet()->getGroup(AdvancedFilterUserParam::TYPE_COLUMN_SORT) as $slug => $sort) {
				$columnSortSettings[] = [
					'slug' => $slug,
					'sort' => $sort
				];
			}
			
			foreach ($AdvancedFiltersObject->FilterParamSet()->getGroup(AdvancedFilterUserParam::TYPE_COLUMN_TEXT_WRAP) as $slug => $wrap) {
				$columnTextWrapSettings[] = [
					'slug' => $slug,
					'wrap' => $wrap
				];
			}
		}
		$columnSettingsJson = json_encode([
			'resize' => $columnResizeSettings,
			'order' => $columnOrderSettings,
			'sort' => $columnSortSettings,
			'wrap' => $columnTextWrapSettings
		]);
		//
		?>
		var columnsParams = YoonityJS.Globals.vars.get('eramba.filters.datatables.filter-' + filterObjectId + '.columns-params', false);
		if (columnsParams === false) {
			YoonityJS.Globals.vars.set('eramba.filters.datatables.filter-' + filterObjectId + '.columns-params', JSON.parse('<?= $columnSettingsJson ?>'));
		}
	});
</script>
