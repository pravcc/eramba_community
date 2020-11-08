<?php
App::uses('AccountReviewFeedback', 'AccountReviews.Model');
$AccountReviewPullsHelper = $this->Helpers->load('AccountReviews.AccountReviewPulls');
?>
<?= __('Hello,'); ?>
<br>
<br>
<?= __('There is a pending account review feedback waiting for you since $date.', $itemData['AccountReviewPull']['created']); ?>
<br>
<br>
<?= __('Your feedback is required, you’ll need to validate system accounts by tagging them as "%s", "%s" or "%s"', AccountReviewFeedback::answers()[AccountReviewFeedback::ANSWER_OK], AccountReviewFeedback::answers()[AccountReviewFeedback::ANSWER_NOT_OK], AccountReviewFeedback::answers()[AccountReviewFeedback::ANSWER_NOT_SURE]) ?>
<br>
<br>
<?= __('The name for this account review is %s and the link to access is <strong>%s</strong>.', $itemData['AccountReview']['title'], $AccountReviewPullsHelper->portalLink($itemData['AccountReviewPull']['hash'], __('portal link'))); ?>
<br>
<br>
<?= __('You can provide feedback <a href="%s">here</a>.', $feedbackUrl) ?>
<br>
<br>
<?= __('Regards'); ?>
