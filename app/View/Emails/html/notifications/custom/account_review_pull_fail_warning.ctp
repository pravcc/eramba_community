<?php 
$SystemLogsHelper = $this->Helpers->load('SystemLogs.SystemLogs');
?>
<?= __('Hello,'); ?>
<br>
<br>
<?= __('The pull for the account review %s did not go well, errros are listed in system logs of pull.', $itemData['AccountReview']['title']) ?>
<br>
<br>
<?= __('Please use this <strong>%s</strong> to access logs.', $SystemLogsHelper->getLink(__('link'), 'AccountReviews.AccountReviewPull', $itemData['AccountReviewPull']['id'])) ?>
<br>
<br>
<?= __('The pull id is: %s', $itemData['AccountReviewPull']['hash']); ?>
<br>
<br>
<?= __('Regards'); ?>
