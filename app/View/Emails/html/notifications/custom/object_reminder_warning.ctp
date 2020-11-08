<?php echo __('Hello!'); ?>
<br />
<br />
<?php
if ($itemTitle) {
	echo __('An object reminder you have set has just triggered for item <strong>%s</strong>. You can find it <a href="%s">here</a>', $itemTitle, $url);
}
else {
	echo __('An object reminder you have set has just triggered. You can find it <a href="%s">here</a>', $url);
}
?>
<br />
<br />
<b><?php echo __('Cheers'); ?></b>
<br />
<b><?php echo __('Your friends at Eramba'); ?></b>