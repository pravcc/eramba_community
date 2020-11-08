<?php foreach ($data as $AdvancedFiltersObject) : ?>
	<?php
	$FilterModel = $AdvancedFiltersObject->getModel();
	// $filterController = $FilterModel->getMappedController();
	$action = $this->request->params['action'];
	// $params = implode('/', $this->request->params['pass']);

	$routeParams = [
		'action' => $action
	];
	$routeParams = array_merge($routeParams, $this->request->params['pass']);

	$routeParams['?'] = array_merge($this->request->query, [
		'advanced_filter' => 1,
		'reload_advanced_filter_only' => 1
	]);
	if ($AdvancedFiltersObject->isDbFilter) {
		$routeParams['?']['advanced_filter_id'] = $AdvancedFiltersObject->getId();
	} else {
		$routeParams['?']['__custom_id'] = $AdvancedFiltersObject->getId();
	}
	
	$datasourceUrl = Router::url($FilterModel->getMappedRoute($routeParams), [
		'full' => false,
		'escape' => true
	]);
	?>

	<div id="advanced-filter-object-<?= $AdvancedFiltersObject->getId(); ?>"
		class="advanced-filter-object"
		data-yjs-request="crud/load"
		data-yjs-target="self"
		data-yjs-datasource-url="<?= $datasourceUrl; ?>"
		>
		<?php if ($AdvancedFiltersObject->isExecuted()): ?>
			<?=
			$this->element('AdvancedFilters.filter_object', [
				'FieldDataCollection' => $FieldDataCollection,
				'AdvancedFiltersObject' => $AdvancedFiltersObject
			]);
			?>
		<?php else: ?>
			<div class="panel panel-flat">
				<div class="panel-heading">
					<h5 class="panel-title">
						<?= $AdvancedFiltersObject->getName(); ?>
					</h5>
				</div>
				<div class="panel-body">
					<div style="text-align: center; padding: 0;">
						<button class="btn btn-default" style="width: 100%; border: 1px dashed #c3c3c3" data-yjs-request="app/triggerRequest/#advanced-filter-object-<?= $AdvancedFiltersObject->getId(); ?>"
								data-yjs-event-on="click"
								data-yjs-use-loader="false"><?= $this->Icons->render('filter4'); ?>&nbsp;<?= __("Load %s filter", $AdvancedFiltersObject->getName()); ?></button>
					</div>
				</div>
			</div>
			<script>
				YoonityJS.ready(function()
				{
					var
						filterObjectId = '<?= $AdvancedFiltersObject->getId(); ?>',
						$advFilterObj = $("#advanced-filter-object-" + filterObjectId),
						advFilterClientRect = $advFilterObj[0].getBoundingClientRect(),
						initializedDatatable = false;
					$(window).on('scroll', function()
					{
						var
							winScrollPos = $(this).scrollTop(),
							winHeight = $(window).height(),
							offset = 40,
							advFilterPos = advFilterClientRect.top;

						if ((winScrollPos + winHeight - offset) >= advFilterPos && !initializedDatatable) {
							new YoonityJS.Init({
								object: $advFilterObj
							});
							initializedDatatable = true;
						}
					}).trigger('scroll');
				});
			</script>
		<?php endif; ?>
	</div>
<?php endforeach; ?>

<?php
	/**
	 * Initialize initial tooltip if this feature is active for actual logged user
	 */
	if (isset($Tooltips) && $Tooltips->active()) {
		echo $this->TooltipsCrud->setupTooltip('initial');
	}
?>
