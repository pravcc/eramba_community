<?php
App::uses('BulkAction', 'BulkActions.Model');

echo $this->Form->create($filter['model'], array(
	'url' => array('action' => 'bulkAction'),
	'novalidate' => true,
	'id' => 'advanced-filter-form'
));

echo $this->Form->input('BulkAction.model', array(
	'type' => 'hidden',
	'value' => $filter['model']
));

$bulkActions = !empty($filter['settings']['bulk_actions']);
?>


<?php if (empty($bulkActions)) : ?>
	<script type="text/javascript">
		jQuery(function($) {
			$(".checkbox-column").remove();
		});
	</script>
	<?php
	return true;
	?>
<?php endif; ?>

<div class="advanced-filter-table-header">
	<div class="row">
		<div class="col-md-6">
			<div class="bulk-actions">
				<?php
				$actionTypes = BulkAction::actionTypes();

				// in case only specified bulk actions should be available
				if (isset($filter['settings']['bulk_actions']) && is_array($filter['settings']['bulk_actions'])) {
					$actions = array();
					foreach ($filter['settings']['bulk_actions'] as $action) {
						$actions[$action] = BulkAction::actionTypes($action);
					}

					$actionTypes = $actions;
				}

				echo $this->Form->input('BulkAction.type', array(
					'options' => array('' => '') + $actionTypes,
					'label' => __('Apply action') . ': ',
					'data-minimum-results-for-search' => '-1',
					'data-placeholder' => __('Select action...'),
					'class' => 'select2',
					'div' => false,
					'id' => 'bulk-action-select-field'
				));

				echo $this->Form->submit(__('Apply'), array(
					'class' => 'btn btn-default',
					'div' => false,
					'id' => 'bulk-action-apply',
				));

				echo $this->Form->button(__('Cancel'), array(
					'type' => 'button',
					'class' => 'btn btn-danger hidden',
					'div' => false,
					'id' => 'bulk-action-cancel'
				));
				?>
			</div>
		</div>
		<div class="col-md-6">
			<?php echo $this->element(CORE_ELEMENT_PATH . 'pagination_numbers' ); ?>
		</div>
	</div>
</div>

<div id="bulk-action-content-holder">

</div>

<script type="text/javascript">
	var afterApplyAction = null;
	jQuery(function($) {
		var $bulkActionContent = $("#bulk-action-content-holder");

		var $tableContainer = $(".advanced-filter-table-widget");
		var $table = $(".advanced-filter-table");
		var $form = $("#advanced-filter-form");
		var $applyAction = $("#bulk-action-apply");
		var $cancelAction = $("#bulk-action-cancel");
		var $data = $();

		applyAction();
		function applyAction() {
			$applyAction.on("click", function(e) {
				e.preventDefault();

				var data = getFormData();

				beforeAnyAction();
				$.ajax({
					type: "POST",
					url: "/bulkActions/apply",
					data: data
				}).done(function(data) {
					$data.remove();

					$data = $(data);
					// afterApplyAction($data);

					$bulkActionContent.html($data);

					// var $tbody = $table.find("tbody");
					// $tbody.prepend($data);

					afterAnyAction($data);
				});
			});
		}

		function afterApplyAction(html) {
			$applyAction.addClass("hidden");
			$cancelAction.removeClass("hidden");
		}

		$cancelAction.on("click", function(e) {
			cancelActionInProgress();
		});

		function cancelActionInProgress() {
			$table.removeClass("bulk-action-in-progress");
			$table.addClass("table-hover");

			$applyAction.removeClass("hidden");
			$cancelAction.addClass("hidden");

			$bulkActionContent.empty();

			var $checkAll = $("#check-all-checkbox");
			$checkAll.prop("checked", false).trigger("change");
			$.uniform.update($checkAll);
		}

		function beforeAnyAction() {
			App.blockUI($tableContainer);
		}

		function afterAnyAction(html) {
			FormComponents.init();

			$bulkActionContent.find(".datepicker").each(function(i, e) {
				if (typeof $(this).data('datepicker') == "undefined") {
					$(this).datepicker({
						showOtherMonths:true,
						autoSize: true,
						dateFormat: 'yy-mm-dd'
					});
				}
			});

			$(html).find(".leave-unchanged-checkbox").on("change", function(e) {
				var $fieldCell = $(this).closest(".bulk-action-field-cell");

				if ($(this).is(":checked")) {
					$fieldCell.addClass("being-changed");
				}
				else {
					$fieldCell.removeClass("being-changed");
				}
			}).trigger("change");

			attachSubmitAction($data);

			App.unblockUI($tableContainer);
		}

		function attachSubmitAction(html) {
			var $submit = $(html).find("#bulk-action-submit");
			$submit.on("click", function(e) {
				e.preventDefault();

				var data = getFormData();

				beforeAnyAction();
				$.ajax({
					type: "POST",
					url: "/bulkActions/submit",
					data: data
				}).done(function(data) {
					$data.remove();

					$data = $(data);
					afterSubmitAction($data);

					// var $tbody = $table.find("tbody");
					// $tbody.prepend($data);

					$bulkActionContent.html($data);

					afterAnyAction($data);
				});
			});
		}

		function afterSubmitAction(html) {

		}

		function getFormData() {
			return $form.serializeArray();
		}
	});
</script>