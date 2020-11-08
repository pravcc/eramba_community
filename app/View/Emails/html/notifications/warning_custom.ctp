<?php echo __('Hello!'); ?>
<br />
<br />
<?php
if ($itemTitle) {
	echo __('Item <strong>%s</strong> has triggered this notification: <strong>%s</strong>.', $itemTitle, $notificationTitle);
}
else {
	echo __('A notification has been triggered: <strong>%s</strong>.', $notificationTitle);
}
?>
<br />
<br />
<b><?php echo __('Cheers'); ?></b>
<br />
<b><?php echo __('Your friends at Eramba'); ?></b>