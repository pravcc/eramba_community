<?php
App::uses('AwarenessProgram', 'Model');
?>
<legend class="text-semibold">
	<?= __('Configure Associations') ?>
</legend>

<?= $this->FieldData->input($FieldDataCollection->SecurityPolicy) ?>

<legend class="text-semibold">
	<?= __('Configure Program Steps') ?>
</legend>

<?= $this->Form->input('uploads_sort_json', [
	'type' => 'hidden',
	'id' => 'uploads-sort-json',
	'default' => json_encode(AwarenessProgram::processUploadSorting([]))
]) ?>
<?= $this->FieldData->input($FieldDataCollection->text_file) ?>
<?= $this->FieldData->input($FieldDataCollection->text_file_frame_size) ?>
<?= $this->FieldData->input($FieldDataCollection->video) ?>
<?= $this->FieldData->input($FieldDataCollection->questionnaire) ?>