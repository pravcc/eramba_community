<?= __('Hello!'); ?>
<br>
<br>
<?= __('This is a reminder for the deadline of a finding called %s asociated with the account review %s.', $itemTitle, $itemData['AccountReviewPull']['AccountReview']['title']); ?>
<?php if ($notificationInstance['reminderDays'] < 0) : ?>
	<?= __('The deadline for this finding is %s.', $itemData['AccountReviewFinding']['deadline']); ?>
<?php else : ?>
	<?= __('The deadline for this finding expired on %s.', $itemData['AccountReviewFinding']['deadline']); ?>
<?php endif; ?>

<br>
<br>
<?= __('Regards'); ?>
