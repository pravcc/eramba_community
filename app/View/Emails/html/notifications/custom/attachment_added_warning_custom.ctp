<?php echo __('Hello!'); ?>
<br />
<br />
<?php
if ($itemTitle) {
	echo __('A new attachment was added to the item <strong>%s</strong> you are currently following.', $itemTitle);
}
else {
	echo __('A new attachment was added to the item you are currently following.');
}
?>
<br />
<br />

<em><b>
	<?php
	printf('%s (%s)',
		basename($additionalData['Attachment']['name']),
		CakeNumber::toReadableSize($additionalData['Attachment']['file_size'])
	);
	?>
</b></em>

<br />
<br />
<b><?php echo __('Cheers'); ?></b>
<br />
<b><?php echo __('Your friends at Eramba'); ?></b>