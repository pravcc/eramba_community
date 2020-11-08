<?php echo __('Hello!'); ?>
<br />
<br />
<?php
if ($itemTitle) {
	echo __('An object reminder you have set has just triggered for item <strong>%s</strong>. Feedback for this item is required. You can find the item <a href="%s">here</a>, and provide feedback <a href="%s">here</a>.', $itemTitle, $url, $feedbackUrl);
}
else {
	echo __('An object reminder you have set has just triggered. Feedback for this item is required. You can find the item <a href="%s">here</a>, and provide feedback <a href="%s">here</a>.', $url, $feedbackUrl);
}
?>
<br />
<br />
<b><?php echo __('Cheers'); ?></b>
<br />
<b><?php echo __('Your friends at Eramba'); ?></b>