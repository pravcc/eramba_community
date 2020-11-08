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
<?= __('Regards'); ?>
