<?php echo __( 'Hello!' ); ?>
<br />
<br />
<?php echo __('You should come see a document <b>%s</b>.', $policy['SecurityPolicy']['index']); ?>
<br>
<?php if (!empty($policy['SecurityPolicy']['short_description'])) : ?>
	<?php echo __('Description: %s', $policy['SecurityPolicy']['short_description']); ?>
<?php endif; ?>
<br><br>
<a href="<?php echo $documentUrl; ?>"><?php echo __('Visit Policy Portal'); ?></a>
<br />
<br />
<b><?php echo __('Cheers'); ?></b>
<br />
<b><?php echo __('Your friends at Eramba'); ?></b>