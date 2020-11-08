<legend class="text-semibold">
	<?= __('Invitation Email Settings') ?>
</legend>

<?= $this->FieldData->input($FieldDataCollection->email_subject) ?>
<?= $this->FieldData->input($FieldDataCollection->email_body) ?>

<legend class="text-semibold">
	<?= __('Reminder Email Settings') ?>
</legend>

<?= $this->FieldData->input($FieldDataCollection->email_reminder_custom) ?>