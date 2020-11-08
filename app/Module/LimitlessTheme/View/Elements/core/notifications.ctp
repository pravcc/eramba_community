<?php
if (!isset($newNotifications)) {
	return false;
}

$count = count($newNotifications);
?>
<li id="notification-dropdown" class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">
		<i class="icon-bubble-notification"></i>
		<span class="visible-xs-inline-block position-right">Users</span>
		<?php if (!empty($newNotifications)) : ?>
			<span class="badge bg-warning-400"><?= $count; ?></span>
		<?php endif; ?>
	</a>
	
	<div class="dropdown-menu dropdown-content width-350">
		<div class="dropdown-content-heading">
			<?= __('Notifications'); ?>
			<ul class="icons-list">
				<li><a href="#"><i class="icon-gear"></i></a></li>
			</ul>
		</div>

		<ul class="media-list dropdown-content-body">
			<?php if (empty($newNotifications)) : ?>
				<li class="media">
					<div class="media-body">
						<span class="text-muted">
							<?= __('You have no new notifications'); ?>
						</span>
					</div>
				</li>
			<?php else : ?>
				<li class="media">
					<div class="media-body">
						<span class="text-muted">
							<?= sprintf(__n('You have %d new notification', 'You have %d new notifications', $count), $count); ?>
						</span>
					</div>
				</li>
				<?php foreach ($newNotifications as $notification) : ?>
					<li class="media">
						<div class="media-body">
							<a href="<?= $notification['Notification']['url']; ?>" class="media-heading">
								<span class="text-muted"><?= $notification['Notification']['title']; ?></span>
								<span class="media-annotation pull-right">
									<?php
									echo CakeTime::timeAgoInWords($notification['Notification']['created'], array(
										'accuracy' => array('day' => 'day', 'hour' => 'hour')
									));
									?>
								</span>
							</a>
						</div>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>
		</ul>

		<div class="dropdown-content-footer">
			<?php
			echo $this->Html->link('<i class="icon-menu display-block"></i>', [
				'controller' => 'pages',
				'action' => 'dashboard'
			],
			[
				'escape' => false,
				'data-popup' => 'tooltip',
				'title' => __('View all notifications')
			])
			?>
		</div>
	</div>
</li>