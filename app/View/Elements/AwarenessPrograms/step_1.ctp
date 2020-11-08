<?= $this->FieldData->input($FieldDataCollection->title) ?>

<?= $this->FieldData->input($FieldDataCollection->description) ?>
<?= $this->FieldData->input($FieldDataCollection->recurrence, [
	'min' => 1,
	'readonly' => !empty($edit) ? true : false
]) ?>
<?= $this->FieldData->input($FieldDataCollection->reminder_amount, [
	'min' => 0,
	'readonly' => !empty($edit) ? true : false
]) ?>
<?= $this->FieldData->input($FieldDataCollection->reminder_apart, [
	'min' => 1,
	'readonly' => !empty($edit) ? true : false
]) ?>
<?= $this->FieldData->input($FieldDataCollection->redirect) ?>