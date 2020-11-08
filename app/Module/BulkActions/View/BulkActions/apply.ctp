<?php if (empty($valid)) : ?>
	<div class="hidden">
		<?php echo $this->Ajax->flash(); ?>
	</div>
	<?php
	if (empty($submitAction)) {
		return true;
	}
	?>
<?php endif; ?>

<div class="bulk-action-content-holder-inner">

	<?php
	App::uses('BulkAction', 'BulkActions.Model');
	if ($type == BulkAction::TYPE_DELETE) {
		echo $this->element('BulkActions.delete');
	}

	if ($type == BulkAction::TYPE_EDIT) {
		echo $this->element('BulkActions.edit');
	}
	?>

	<div>
		<?php
		echo $this->Form->submit(__('Submit'), array(
			'class' => 'btn btn-primary',
			'div' => false,
			'id' => 'bulk-action-submit'
		));
		?>
	</div>

</div>

<script type="text/javascript">
jQuery(function($) {
	var $table = $(".advanced-filter-table");
	var $applyAction = $("#bulk-action-apply");
	var $cancelAction = $("#bulk-action-cancel");

	actionInProgress();
	function actionInProgress() {
		$table.addClass("bulk-action-in-progress");
		$table.removeClass("table-hover");

		$applyAction.addClass("hidden");
		$cancelAction.removeClass("hidden");
	}
});
</script>