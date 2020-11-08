<?php echo __('Hello!'); ?>
<br />
<br />
<?php
if ($itemTitle) {
	echo __('A new comment was added to the item <strong>%s</strong> you are currently following. You can find it <a href="%s">here</a>.', $itemTitle, $url);
}
else {
	echo __('A new comment was added to the item you are currently following. You can find it <a href="%s">here</a>.', $url);
}
?>
<br />
<br />

<em><b><?php echo $additionalData['User']['full_name']; ?></b></em>
<br />
<em><?php echo $additionalData['Comment']['message']; ?></em>

<br />
<br />
<b><?php echo __('Cheers'); ?></b>
<br />
<b><?php echo __('Your friends at Eramba'); ?></b>