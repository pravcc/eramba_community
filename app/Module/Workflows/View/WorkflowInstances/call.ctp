<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-content">
				<?php
				echo $this->Form->create('WorkflowInstanceRequest', array(
					'url' => $this->Workflows->getCallStageUrl($model, $foreignKey, $stageId),
					'class' => 'form-horizontal row-border',
					'novalidate' => true
				));

				echo $this->Form->input('instanceId', array(
					'type' => 'hidden',
					'value' => $instance['WorkflowInstance']['id']
				));

				echo $this->Form->input('stageId', array(
					'type' => 'hidden',
					'value' => $stageId
				));
				?>

				<div class="alert alert-info">
				TBD TEXT
					<?php
					echo __('Are you sure you want to call next stage "%s"?', 'test');
					?>
				</div>

				<?php
				// if ($this->Form->isFieldError('WorkflowInstanceRequest.user_id')) {
				// 	echo $this->Form->error('WorkflowInstanceRequest.user_id');
				// }
				?>

				<div class="form-actions">
					<?php echo $this->Form->submit(__('Call'), array(
						'class' => 'btn btn-primary',
						'div' => false
					)); ?>
					&nbsp;
					<?php
					echo $this->Ajax->cancelBtn('WorkflowInstanceRequest');
					?>
				</div>

				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>