<?php echo __('Hello!'); ?>
<br />
<br />
<?php
if ($itemTitle) {
	echo __('A notification <strong>%s</strong> has been triggered by item <strong>%s</strong>. Feedback for this item is required. You can find the item <a href="%s">here</a>, and provide feedback <a href="%s">here</a>.', $notificationTitle, $itemTitle, $url, $feedbackUrl);
}
else {
	echo __('A notification has been triggered: <strong>%s</strong>. Feedback for this item is required. You can find the item <a href="%s">here</a>, and provide feedback <a href="%s">here</a>.', $notificationTitle, $url, $feedbackUrl);
}
?>
<br />
<br />
<b><?php echo __('Cheers'); ?></b>
<br />
<b><?php echo __('Your friends at Eramba'); ?></b>