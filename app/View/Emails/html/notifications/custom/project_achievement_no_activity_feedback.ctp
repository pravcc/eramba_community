<?= __('Hello!'); ?>
<br>
<br>
<?= __('We have noticed the task %s (under the project %s) has not been updated in the last %s days, this means it has not been edited and comments/attachments have not been included.', $itemTitle, $itemData['Project']['title'], abs($notificationInstance['reminderDays'])); ?>
<br>
<br>
<?= __('You can provide feedback <a href="%s">here</a>.', $feedbackUrl) ?>
<br>
<br>
<?= __('Regards'); ?>
