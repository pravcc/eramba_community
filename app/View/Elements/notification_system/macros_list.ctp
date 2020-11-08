<?php if (!empty($macros)) : ?>
	<br /><br />
	<?php echo __('For the email subject and body, you can also use macros listed below:'); ?>
	<br />
	
	<?php
	if (!isset($style)) {
		$style = 'horizontal';
	}
	?>
	<dl class="dl-<?php echo $style; ?> dl-notification-macros">
		<?php foreach ($macros as $macro => $options) : ?>
			<dt>%<?php echo $macro; ?>%</dt>
			<dd><?php echo $options['name']; ?></dd>
		<?php endforeach; ?>
	</dl>
<?php endif; ?>