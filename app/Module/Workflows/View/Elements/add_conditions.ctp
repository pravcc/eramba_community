<?php
// debug($this->request->data);
?>
<div id="wf-conditions-container">
	<div class="text-right">
		<?php
		echo $this->Html->link(__('Add Condition'), [
			'action' => 'addCondition'
		], [
			'class' => 'btn btn-sm btn-primary',
			'id' => 'wf-add-condition'
		]);
		?>
	</div>

	<div id="wf-conditions-wrapper">
		<?php
		// debug($this->request->data);
		if (!empty($this->request->data['WorkflowStageStepCondition'])) {
			foreach ($this->request->data['WorkflowStageStepCondition'] as $index => $condition) {
				echo $this->element('Workflows.../WorkflowStageSteps/add_condition', [
					'FieldDataCondsCollection' => $FieldDataCondsCollection,
					'index' => $index,
					'condition' => $condition
				]);
			}
		}
		?>
	</div>

	<?php
	if ($this->Form->isFieldError('step_conditions')) {
		echo $this->Form->error('step_conditions');
	}
	?>
</div>

<script type="text/javascript">
	jQuery(function($) {
		var $wrapper = $("#wf-conditions-wrapper");
		var $addConditionBtn = $("#wf-add-condition");
		var index = "<?php echo isset($index) ? ++$index : 0; ?>";
		var workflowStageId = "<?php echo $workflowStageId; ?>";

		$addConditionBtn.on("click", function(e) {
			var url = $(this).attr("href");
			$.ajax({
				url: url + "/" + workflowStageId + "/" + index,
				beforeSend: function() {
					index++;
					$wrapper.trigger("wf-conditions-ajax-before");
				}
			}).done(function(data) {
				$wrapper.append(data);
				attachEvents();
				$wrapper.trigger("wf-conditions-ajax-after");

			});

			e.preventDefault();
		});

		if (index == 0) {
			$addConditionBtn.trigger("click");
		}

		function attachEvents() {
			var $fieldInputs = $(".workflows-field-input");
			$fieldInputs.off("change.Workflows").on("change.Workflows", function(e) {
				var fieldName = $(this).val();
				var fieldIndex = $(this).data("index-id");
				console.log(fieldIndex);

				if (fieldName) {
					loadFieldValue(fieldName, fieldIndex);
				}
			});
		}
		attachEvents();

		function loadFieldValue(fieldName, fieldIndex) {
			$.ajax({
				url: "<?php echo Router::url(['plugin' => 'workflows', 'controller' => 'workflowStageSteps', 'action' => 'addConditionValue', $sectionModel]); ?>/" + fieldName + "/" + fieldIndex,
				beforeSend: function() {
					$wrapper.trigger("wf-conditions-ajax-before");
					// Eramba.Ajax.blockEle($(".wf-next-stage-join-form"));
				}
			}).done(function(data) {
				$("#wf-conditions-value-field-" + fieldIndex).html(data);

				$wrapper.trigger("wf-conditions-ajax-after");
				// FormComponents.init();
				// Eramba.Ajax.unblockEle($(".wf-next-stage-join-form"));

			});
		}

		$wrapper.on("wf-conditions-ajax-before", function(e) {
			Eramba.Ajax.blockEle($(".wf-next-stage-join-form"));
		});

		$wrapper.on("wf-conditions-ajax-after", function(e) {
			Eramba.Ajax.UI.modalShownHandler();
			Eramba.Ajax.unblockEle($(".wf-next-stage-join-form"));
		});
	});
</script>