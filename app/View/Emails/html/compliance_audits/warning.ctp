<?php echo __( 'Hello!' ); ?>
<br />
<br />
<?php if ($model == 'Comment') : ?>
	<?php echo __('New comment has been added for an Audit item "%s" that belongs to Third Party Audit "%s". You can view the comment <a href="%s">here</a>.', $title, $audit, $url); ?>
<?php elseif ($model == 'Attachment') : ?>
	<?php echo __('New attachment has been uploaded for an Audit item "%s" that belongs to Third Party Audit "%s". You can view the attachment <a href="%s">here</a>.', $title, $audit, $url); ?>
<?php else : ?>
	<?php echo __('New feedback has been provided for an Audit item "%s" that belongs to Third Party Audit "%s". You can view the feedback <a href="%s">here</a>.', $title, $audit, $url); ?>
<?php endif; ?>
<br />
<br />
<b><?php echo __('Cheers'); ?></b>
<br />
<b><?php echo __('Your friends at Eramba'); ?></b>