<?= __('Hello!'); ?>
<br>
<br>
<?= __('A finding has been created for the account review %s, in particular for the pull id %s triggered on the date %s and the account %s.', $itemData['AccountReviewPull']['AccountReview']['title'], $itemData['AccountReviewPull']['hash'], date('Y-m-d', strtotime($itemData['AccountReviewPull']['created'])), implode(', ', Hash::extract($itemData, 'AccountReviewFeedback.{n}.AccountReviewFeedRow.user'))); ?>
<br>
<br>
<?= __('You can provide feedback <a href="%s">here</a>.', $feedbackUrl) ?>
<br>
<br>
<?= __('Regards'); ?>
