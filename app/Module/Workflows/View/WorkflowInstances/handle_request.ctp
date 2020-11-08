<div class="widget box">
	<div class="widget-content">
		<?php if (empty($hasAccess)) : ?>
			<div class="alert alert-danger">
				<?php
				echo __('You do not have an access to requested action.');
				?>
			</div>

			<?php
			echo $this->Ajax->cancelBtn('WorkflowHandleRequest');
			?>
		<?php else : ?>
			<?php
			echo $this->Form->create('WorkflowHandleRequest', array(
				'url' => array(
					'plugin' => 'workflows',
					'controller' => 'workflowInstances',
					'action' => 'handleRequest',
					$model, $foreignKey, $requestType, $stageId
				),
				'class' => 'form-horizontal row-border',
				'novalidate' => true
			));
			?>

			<div class="alert alert-info">
				<?php
				echo $message;
				?>
			</div>

			<div class="form-actions">
				<?php
				echo $this->Form->submit(__('Continue'), array(
					'class' => 'btn btn-primary',
					'div' => false
				));
				?>
				&nbsp;
				<?php
				echo $this->Ajax->cancelBtn('WorkflowHandleRequest');
				?>
			</div>

			<?php echo $this->Form->end(); ?>
		<?php endif; ?>
	</div>
</div>