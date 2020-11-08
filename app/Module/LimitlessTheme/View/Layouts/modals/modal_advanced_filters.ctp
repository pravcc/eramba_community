<?php
App::uses('AdvancedFilter', 'AdvancedFilters.Model');
$maxSelectionSize = AdvancedFilter::MAX_SELECTION_SIZE;

if (!isset($activeFilterId)) {
	// $activeFilterId = -1;
}
?>
<div class="modal-content modal-content-custom">
	<div class="modal-header">
		<h5 class="modal-title"><?= $title_for_layout ?></h5>
		<button type="button" class="close" 
			data-yjs-request="app/closeModal" 
			data-yjs-modal-id="<?= $modal->getModalId(); ?>" 
			data-yjs-event-on="click">&times;</button>
	</div>
	<div class="modal-body">
		<?= $this->fetch('content'); ?>
	</div>
	<div class="modal-footer">
		<p id="min-selection-size-error" class="hidden pull-left text-danger" style="padding-top: 8px;">
			<?=
			__('Please select some of the fields to be shown');
			?>
		</p>
		<p id="max-selection-size-error" class="hidden pull-left text-danger" style="padding-top: 8px;">
			<?=
			__('We can\'t show more than %d fields - please uncheck fields before you select new ones', $maxSelectionSize);
			?>
		</p>

		<button type="button" class="btn btn-link" 
			data-yjs-request="app/closeModal" 
			data-yjs-modal-id="<?= $modal->getModalId(); ?>" 
			data-yjs-event-on="click"><?= __('Close'); ?></button>

		<?php
		$reload = '#main-content';
		if (isset($activeFilterId)) {
			$reload = '#advanced-filter-object-' . $activeFilterId;
		}
		?>
		<button type="button" class="btn btn-primary" 
			id="filter-save-btn"
			data-yjs-request="crud/submitForm" 
			data-yjs-target="modal" 
			data-yjs-on-modal-success="close" 
			data-yjs-datasource-url="<?= $formUrl; ?>" 
			data-yjs-forms="#<?= $formName; ?>" 
			data-yjs-event-on="click"
			data-yjs-modal-id="<?= $modal->getModalId(); ?>" 
			data-yjs-on-success-reload="<?= $reload; ?>|#main-toolbar" 
			<?php
			/* data-yjs-on-success-reload="#advanced-filter-object-<?= $activeFilterId; ?>|#advanced-filter-object-|#main-content" */
			?>
			><?= __('Save'); ?></button>

		<button type="button" class="btn btn-primary"
			id="filter-submit-btn"
			data-filter-submit-url="<?= Router::url(ClassRegistry::init($filterModel)->getMappedRoute()); ?>"><?= __('Filter'); ?></button>
	</div>
</div>
<script type="text/javascript">
jQuery(function($) {
	var $allTabs = $("#advanced-filter-form-tabs").find('a[data-toggle="tab"]');
	var $filterTabs = $allTabs.filter(":not(#advanced-filter-nav-manage)");
	var $submitTabs = $("#advanced-filter-nav-manage");

	// $filterTabs.on('show.bs.tab', function (e) {
	// 	$("#filter-save-btn").hide();
	// 	$("#filter-submit-btn").show();
	// });

	$filterTabs.eq(0).trigger("show.bs.tab");

	// $submitTabs.on('show.bs.tab', function (e) {
	// 	$("#filter-save-btn").show();
	// 	$("#filter-submit-btn").hide();
	// });

	$("#filter-submit-btn").on("click", function(e) {
		e.preventDefault();

		$("#min-selection-size-error, #max-selection-size-error").addClass("hidden");
		if ($('.advanced-filter-show:checked').length == 0) {
			$("#min-selection-size-error").removeClass("hidden");
			return false;
		}

		if ($('.advanced-filter-show:checked').length >= <?= ($maxSelectionSize - 1); ?>) {
			$("#max-selection-size-error").removeClass("hidden");
			return false;
		}

		var $form = $("#<?= $formName; ?>");
		var submitUrl = $(this).data('filter-submit-url');

		$form.attr("action", submitUrl);
		$form.trigger("submit");
	});
});
</script>
