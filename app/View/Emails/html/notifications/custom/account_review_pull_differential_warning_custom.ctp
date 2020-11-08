<?php 
$AccountReviewPullsHelper = $this->Helpers->load('AccountReviews.AccountReviewPulls');
?>
<?= __('Hello,'); ?>
<br>
<br>
<?= __('A differential account review %s pull has finished and there seems to be items for your review:', $itemData['AccountReview']['title']); ?>
<ul>
	<li>
		<?= __('%s new accounts', $itemData['AccountReviewPull']['count_added']) ?>
	</li>
	<li>
		<?= __('%s removed accounts', $itemData['AccountReviewPull']['count_deleted']) ?>
	</li>
	<li>
		<?= __('%s accounts with change in roles', $itemData['AccountReviewPull']['count_role_change']) ?>
	</li>
</ul>
<br>
<?= __('Regards'); ?>
