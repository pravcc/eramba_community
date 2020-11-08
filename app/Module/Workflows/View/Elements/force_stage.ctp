<?php				
echo $this->Form->create('WorkflowInstance', array(
	'url' => [
		'plugin' => 'workflows',
		'controller' => 'workflowInstances',
		'action' => 'forceStageForm',
		$model, $foreignKey
	],
	'id' => 'force-stage-form',
	'class' => 'form-horizontal',
	'novalidate' => true
));

if (!$isWorkflowOwner = $Instance->isWorkflowOwner($logged['id'])) {
	echo $this->Ux->getAlert(__('This feature requires Workflow Owner access type.'), [
		'type' => 'warning'
	]);
}

echo $this->FieldData->input($FieldDataCollection->wf_stage_id, [
	'label' => false,
	'between' => '<div class="col-md-12">',
	'disabled' => !$isWorkflowOwner
]);

echo $this->Form->submit(__('Continue'), array(
	'class' => 'btn btn-sm btn-primary',
	'id' => 'force-stage-btn',
	'div' => false,
	'disabled' => !$isWorkflowOwner
));

echo $this->Form->end();


?>
<div class="hidden" style="display:none">
	<?php echo $this->Ajax->flash(); ?>

	<?php if (!empty($ajaxSuccess)) : ?>
		<script type="text/javascript">
			jQuery(function($) {
				var url = "<?php echo Router::url($this->Workflows->getRequestUrl($model, $foreignKey, 'force-stage', $this->request->data['WorkflowInstance']['wf_stage_id'])); ?>";
				
				setTimeout(function() {
					Eramba.Ajax.unblockEle($("#force-stage-wrapper"));
					Eramba.Ajax.UI.requestHandler(url, "edit");
				}, 600);
				
			});
		</script>
	<?php endif; ?>
</div>

<script type="text/javascript">
	jQuery(function($) {
		$("#force-stage-form").on("submit", function(e) {
			e.preventDefault();

			$.ajax({
				type: "POST",
				url: $(this).attr("action"),
				data: $(this).serializeArray(),
				beforeSend: function() {
					Eramba.Ajax.blockEle($("#force-stage-wrapper"));
				}
			}).done(function(data) {
				$("#force-stage-container").html(data);
				FormComponents.init();
				
			});
		});
	});
</script>