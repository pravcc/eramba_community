<?= __('Hello!'); ?>
<br>
<br>
<?= __('A finding has been created for the account review %s, in particular for the pull id %s triggered on the date %s and the account %s', $itemData['AccountReviewPull']['AccountReview']['title'], $itemData['AccountReviewPull']['hash'], date('Y-m-d', strtotime($itemData['AccountReviewPull']['created'])), implode(', ', Hash::extract($itemData, 'AccountReviewFeedback.{n}.AccountReviewFeedRow.name'))); ?>
<br>
<br>
<?= __('Regards'); ?>
