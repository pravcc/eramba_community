<div class="form-group">
	<?php
	echo $this->WorkflowConditionFields->input([$FieldDataCondsCollection->field, $index], [
		'class' => ['workflows-field-input'],
		'data-index-id' => $index
	]);
	?>
	<div id="wf-conditions-value-field-<?php echo $index; ?>">
		<?php
		if (!empty($condition['field'])) {
			echo $this->element('Workflows.../WorkflowStageSteps/add_condition_value', [
				'FieldDataCondsCollection' => $FieldDataCondsCollection,
				'index' => $index,
				'FieldDataValueEntry' => ClassRegistry::init($sectionModel)->getFieldDataEntity($condition['field'])
			]);
		}
		?>
	</div>
</div>