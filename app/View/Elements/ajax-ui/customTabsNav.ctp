<?php
if (!isset($activeModule)) {
	$activeModule = 'comments';
}
?>
<?php if (!isset($enableComments) || $enableComments) : ?>
	<li class="<?php if($activeModule == 'comments')echo 'active'; ?> default-tab">
		<a href="#comments" data-toggle="tab" class="<?php if(!empty($commentsCount))echo 'has-items'; ?>">
			<i class="icon-comments"></i> <?php echo __('Comments'); ?>
			<?php /*if (!empty($commentsCount)) : ?>
				<span class="badge"><?php echo $commentsCount; ?></span>
			<?php endif;*/ ?>
		</a>
	</li>
<?php endif; ?>

<?php if (!isset($enableRecords) || $enableRecords) : ?>
	<li class="<?php if($activeModule == 'records')echo 'active'; ?> default-tab pull-right">
		<a href="#records" data-toggle="tab">
			<i class="icon-cog"></i> <?php echo __('Records'); ?>
		</a>
	</li>
<?php endif; ?>

<?php if (!isset($enableAttachments) || $enableAttachments) : ?>
	<li class="<?php if($activeModule == 'attachments')echo 'active'; ?> default-tab">
		<a href="#attachments" data-toggle="tab" class="<?php if(!empty($attachmentsCount))echo 'has-items'; ?>">
			<i class="icon-cloud-upload"></i> <?php echo __('Attachments'); ?>
			<?php /*if (!empty($attachmentsCount)) : ?>
				<span class="badge"><?php echo $attachmentsCount; ?></span>
			<?php endif;*/ ?>
		</a>
	</li>
<?php endif; ?>

<?php if (isset($notificationsModule) && $notificationsModule) : ?>
	<?php if (!isset($enableNotifications) || $enableNotifications) : ?>
		<li class="<?php if($activeModule == 'notifications')echo 'active'; ?> default-tab">
			<a href="#notifications" data-toggle="tab" class="<?php if(!empty($notificationsCount))echo 'has-items'; ?>">
				<i class="icon-info-sign"></i> <?php echo __('Notifications'); ?>
			</a>
		</li>
	<?php endif; ?>
<?php endif; ?>