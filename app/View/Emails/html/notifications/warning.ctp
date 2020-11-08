<?php echo __('Hello!'); ?>
<br />
<br />
<?php
if ($itemTitle) {
	echo __('A notification <strong>%s</strong> has been triggered by item: <strong>%s</strong>. You can find it <a href="%s">here</a>', $notificationTitle, $itemTitle, $url);
}
else {
	echo __('A notification <strong>%s</strong> has been triggered. You can find it <a href="%s">here</a>', $notificationTitle, $url);
}
?>
<br />
<br />
<b><?php echo __('Cheers'); ?></b>
<br />
<b><?php echo __('Your friends at Eramba'); ?></b>