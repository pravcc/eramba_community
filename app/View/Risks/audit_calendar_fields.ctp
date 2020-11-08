<?php
$dateFieldId = $fieldsClass . '-' . $fieldsCount;
?>
<div class="row <?= $fieldsClass; ?>" id="<?= $dateFieldId; ?>">
	<div class="col-xs-3">
		<?=
		$this->FieldData->input([$AuditCalendarCollection->day, $fieldsCount], [
			'options' => $this->get('days'),
			'label' => false
		]);
		?>
	</div>
	<div class="col-xs-3">
		<?=
		$this->FieldData->input([$AuditCalendarCollection->month, $fieldsCount], [
			'options' => $this->get('months'),
			'label' => false
		]);
		?>
	</div>
	<div class="col-xs-1">
		<a href="#" title="<?= __('Remove date'); ?>" data-yjs-request="crud/removeInputField/fieldId::<?= $dateFieldId; ?>" data-yjs-event-on="click" data-yjs-use-loader="false">
			<?= $this->Icons->render('close2', [
				'style' => 'position: relative; height: 35px; line-height: 35px; margin: auto auto auto auto; font-size: 16px; color: #ab0101;'
			]); ?>
		</a>
	</div>
	<div class="col-md-12" style="margin-top: -15px; margin-bottom: 10px">
		<?php
		$inputField = $fieldName . '_' . $fieldsCount;
		if ($this->Form->isFieldError($inputField)) {
			echo $this->Form->error($inputField);
		}
		?>
	</div>
</div>
