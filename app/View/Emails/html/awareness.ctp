<?php if (isset($isDemo) && $isDemo) : ?>
	<b style="text-transform:uppercase;"><?php echo __('This is a demo email'); ?></b>
<?php endif; ?>

<br />
<br />
<?php
echo nl2br($body);
?>
<br /><br />
<a href="<?php echo $url; ?>" target="_blank"><?php echo __('Visit Awareness Portal'); ?></a>
