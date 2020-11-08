<?php 
App::uses('AccountReviewFeedback', 'AccountReviews.Model');
App::uses('AccountReviewStats', 'AccountReviews.Lib');
?>
<?= __('Hello,'); ?>
<br>
<br>
<?= __('Feedback for the account review %s has been submitted by %s with the following results:', $itemData['AccountReview']['title'], $additionalData['user']); ?>
<ul>
	<li>
		<?php
		$count = AccountReviewStats::pullFeedbackAnswersCount($itemData, AccountReviewFeedback::ANSWER_OK, false);
		$label = AccountReviewFeedback::answers()[AccountReviewFeedback::ANSWER_OK];
		echo __('%s "%s" accounts', $count, $label);
		?>
	</li>
	<li>
		<?php
		$count = AccountReviewStats::pullFeedbackAnswersCount($itemData, AccountReviewFeedback::ANSWER_NOT_OK, false);
		$label = AccountReviewFeedback::answers()[AccountReviewFeedback::ANSWER_NOT_OK];
		echo __('%s "%s" accounts', $count, $label);
		?>
	</li>
	<li>
		<?php
		$count = AccountReviewStats::pullFeedbackAnswersCount($itemData, AccountReviewFeedback::ANSWER_NOT_SURE, false);
		$label = AccountReviewFeedback::answers()[AccountReviewFeedback::ANSWER_NOT_SURE];
		echo __('%s "%s" accounts', $count, $label);
		?>
	</li>
</ul>
<br>
<?= __('The pull id associated with this feedback is: %s', $itemData['AccountReviewPull']['hash']); ?>
<br>
<br>
<?= __('Regards'); ?>
