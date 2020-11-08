<?php if (!empty($notificationsItemList)) : ?>
	<?php
	echo $this->Form->create('NotificationObject', array(
		'url' => array('controller' => 'notificationSystem', 'action' => 'associateAjax', $model, $foreign_key),
		'class' => 'form-vertical sidebar-widget-form',
		'id' => 'notifications-widget-form',
		'novalidate' => true
	));
	?>

	<div>
		<div class="form-group">
			<label class="control-label"><?php echo __('Associate a Notification'); ?>:</label>
			<?php
			echo $this->Form->input('notification_system_item_id', array(
				'options' => $notificationsItemList,
				'label' => false,
				'div' => false,
				'class' => 'form-control',
				'empty' => __('Choose a Notification')
			));
			?>
			<span class="help-block"><?php echo __('Choose a Notification to associate with this item.'); ?></span>
		</div>

		<div class="form-group">
			<?php
			echo $this->Form->submit(__('Add'), array(
				'class' => 'btn btn-primary',
				'div' => false
			));
			?>
		</div>
	</div>

	<?php echo $this->Form->end(); ?>
<?php endif; ?>

<?php if (!empty($notifications)) : ?>
	<div class="pull-right">
		<div class="btn-toolbar">
			<div class="btn-group">
				<?php
				/*echo $this->Html->link(__('View All'), array(
					'controller' => 'systemRecords',
					'action' => 'index',
					$model,
					$foreign_key
				), array(
					'class' => 'btn btn-default',
					'escape' => false
				));
				?>
				<?php
				echo $this->Html->link(__('Export'), array(
					'controller' => 'systemRecords',
					'action' => 'export',
					$model,
					$foreign_key
				), array(
					'class' => 'btn btn-default',
					'escape' => false
				));*/
				?>
			</div>
		</div>
	</div>

	<div class="table-responsive table-responsive-wide">
		<table class="table table-hover table-striped table-condensed">
			<thead>
				<tr>
					<th><?php echo __('Name'); ?></th>
					<th><?php echo __('Type'); ?></th>
					<th><?php echo __('Feedback'); ?></th>
					<th><?php echo __('Associated'); ?></th>
					<th><?php echo __('Status'); ?></th>
					<th class="align-center"><?php echo __('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($notifications as $item) : ?>
					<tr>
						<td><?php echo $item['NotificationSystem']['name']; ?></td>
						<td>
							<?php
							echo getNotificationTypes($item['NotificationSystem']['type']);
							?>
						</td>
						<td>
							<?php
							echo $item['NotificationSystem']['feedback'] ? __('Required') : '-';
							?>
						</td>
						<td><?php echo $item['NotificationObject']['created']; ?></td>
						<td>
							<?php
							echo $this->NotificationObjects->getStatuses($item);
							?>
						</td>
						<td class="align-center">
							<?php
							$obj = $item['NotificationObject'];

							$removeUrl = array('controller' => 'notificationSystem', 'action' => 'remove', $obj['notification_system_item_id'], $obj['model'], $obj['foreign_key']);
							$disableForObject = array('controller' => 'notificationSystem', 'action' => 'disableForObject', $obj['notification_system_item_id'], $obj['model'], $obj['foreign_key']);
							$enableForObject = array('controller' => 'notificationSystem', 'action' => 'enableForObject', $obj['notification_system_item_id'], $obj['model'], $obj['foreign_key']);
							
							if ($item['NotificationObject']['status'] == NOTIFICATION_OBJECT_ENABLED) {
								$this->Ajax->addToActionList(__('Disable'), $disableForObject, 'remove', 'notification-action');
							}
							else {
								$this->Ajax->addToActionList(__('Enable'), $enableForObject, 'ok', 'notification-action');
							}

							$this->Ajax->addToActionList(__('Remove'), $removeUrl, 'trash', 'notification-action');

							echo $this->Ajax->getUserDefinedActionList(array(
								'listClass' => 'table-controls nested-actions'
							));
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php else : ?>
	<div class="alert alert-info"><?php echo __('No notifications associated.'); ?></div>
<?php endif; ?>