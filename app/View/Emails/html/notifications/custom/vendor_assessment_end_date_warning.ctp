<?= __('Hello!'); ?>
<br>
<br>
<?= __('The questionnaire under the title "%s" will automatically stop in %s days. ', $itemTitle, abs($notificationInstance['reminderDays'])); ?>
<br>
<br>
<?= __('Please make sure all answers needed have been provided by following this <a href="%s"><strong>link</strong></a>.', $url); ?>
<br>
<br>
<?= __('Regards'); ?>
