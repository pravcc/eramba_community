<?php App::uses('VendorAssessment', 'VendorAssessments.Model'); ?>
<?= __('Hello!'); ?>
<br>
<br>
<?= __('The questionnaire under the title "%s" is to be renewed every %s days.', $itemTitle, $itemData['VendorAssessment']['recurrence_period']); ?>
<br>
<br>
<?= __('In %s days the questionnaire will be created and initialized.', abs($notificationInstance['reminderDays'])); ?>
<br>
<br>
<?= __('Regards'); ?>
