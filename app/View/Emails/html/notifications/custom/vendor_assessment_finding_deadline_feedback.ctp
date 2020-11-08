<?= __('Hello!'); ?>
<br>
<br>
<?= __('The finding under the title "%s" will be due in %s days - please ensure its requirements have been met before the due date.', $itemTitle, abs($notificationInstance['reminderDays'])); ?>
<br>
<br>
<?= __('You can provide feedback <a href="%s">here</a>.', $feedbackUrl) ?>
<br>
<br>
<?= __('Regards'); ?>
