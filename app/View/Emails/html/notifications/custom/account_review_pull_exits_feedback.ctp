<?php 
$AccountReviewPullsHelper = $this->Helpers->load('AccountReviews.AccountReviewPulls');
?>
<?= __('Hello,'); ?>
<br>
<br>
<?= __('An exit account review %s pull has finished and there seems to be items for your review:', $itemData['AccountReview']['title']); ?>
<ul>
	<li>
		<?= __('%s creeping accounts', ($itemData['AccountReviewPull']['count_current_check'] + $itemData['AccountReviewPull']['count_former_check'])) ?>
	</li>
</ul>
<br>
<?= __('Use the <strong>%s</strong> to access the review and provide feedback.', $AccountReviewPullsHelper->portalLink($itemData['AccountReviewPull']['hash'], __('portal link'))); ?>
<br>
<br>
<?= __('You can provide feedback <a href="%s">here</a>.', $feedbackUrl) ?>
<br>
<br>
<?= __('Regards'); ?>
