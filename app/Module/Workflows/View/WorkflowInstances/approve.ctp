<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-content">
				<?php
				echo $this->Form->create('WorkflowApproval', array(
					'url' => $this->Workflows->getApproveStageUrl($instanceRequestId),
					'class' => 'form-horizontal row-border',
					'novalidate' => true
				));

				echo $this->Form->input('instanceRequestId', array(
					'type' => 'hidden',
					'value' => $instanceRequestId
				));
				?>

				<div class="alert alert-info">
				TBD TEXT
					<?php
					echo __('Are you sure you want to approve the request for next stage "%s"?', 'test');
					?>
				</div>

				<?php
				// if ($this->Form->isFieldError('WorkflowInstanceRequest.user_id')) {
				// 	echo $this->Form->error('WorkflowInstanceRequest.user_id');
				// }
				?>

				<div class="form-actions">
					<?php echo $this->Form->submit(__('Approve'), array(
						'class' => 'btn btn-primary',
						'div' => false
					)); ?>
					&nbsp;
					<?php
					echo $this->Ajax->cancelBtn('WorkflowApproval');
					?>
				</div>

				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>