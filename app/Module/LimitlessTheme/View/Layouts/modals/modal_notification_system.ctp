<?php
App::uses('NotificationSystem', 'NotificationSystem.Model');
?>
<div class="modal-content modal-content-custom">
	<div class="modal-header">
		<h5 class="modal-title"><?= $title_for_layout ?></h5>
		<button type="button" class="close" 
			data-yjs-request="app/closeModal" 
			data-yjs-modal-id="<?= $modal->getModalId(); ?>" 
			data-yjs-event-on="click">&times;</button>
	</div>
	<div class="modal-body">
		<?= $this->fetch('content'); ?>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-link" 
			id="notification-system-close-btn" 
			data-yjs-request="app/closeModal" 
			data-yjs-modal-id="<?= $modal->getModalId(); ?>" 
			data-yjs-event-on="click"><?= __('Close'); ?></button>
		<button type="button" class="btn btn-primary pull-right" 
			id="filter-save-btn"
			data-yjs-request="crud/submitForm" 
			data-yjs-target="modal" 
			data-yjs-on-modal-success="close" 
			data-yjs-datasource-url="<?= $formUrl; ?>" 
			data-yjs-forms="<?= $formName; ?>" 
			data-yjs-modal-id="<?= $modal->getModalId(); ?>" 
			data-yjs-event-on="click"
			data-yjs-on-success-reload="#main-toolbar" 
		"><?= __('Save'); ?></button>

		<?php if (isset($edit)) : ?>
			<?=
			$this->Html->link(__('List already sent emails'), $this->AdvancedFilters->filterUrl('queue', [
					'model' => 'NotificationSystem.NotificationSystem',
					'foreign_key' => $this->data['NotificationSystem']['id']
				]), [
				'class' => 'btn btn-default',
				'target' => '_blank'
			]);
			?>
			<?php if ($notificationType != NOTIFICATION_TYPE_REPORT) : ?>
				<?=
				$this->Html->link(__('Advanced Settings'), '#', [
					'class' => 'btn btn-default',
					'data-yjs-request' => 'crud/showForm',
					'data-yjs-datasource-url' => Router::url([
						'controller' => 'notificationObjects',
						'action' => 'index',
						$this->data['NotificationSystem']['id']
					]),
					'data-yjs-target' => 'modal',
					'data-yjs-parent-model' => 'NotificationSystem.NotificationSystem',
					'data-yjs-event-on' => 'click',
					// 'disabled' => true
				]);
				?>
			<?php endif; ?>

			<?php
			echo $this->Html->link(__('Delete'), '#', [
				'class' => 'btn btn-danger pull-left',
				'data-yjs-request' => 'crud/showForm',
				'data-yjs-target' => "modal",
			    'data-yjs-datasource-url' => Router::url([
					'action' => 'delete',
					$this->data['NotificationSystem']['id']
				]),
			    'data-yjs-event-on' => "click",
				'escape' => false
			]);
			?>
		<?php endif; ?>
	</div>
</div>