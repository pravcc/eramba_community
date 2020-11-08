<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-content">
				<?php
				echo $this->Form->create('WorkflowNextStep', array(
					'url' => array('controller' => 'workflows', 'action' => 'acknowledge', $acknowledgementId),
					'class' => 'form-horizontal row-border',
					'novalidate' => true
				));

				echo $this->Form->input('_step', array(
					'type' => 'hidden',
					'value' => WORKFLOW_APPROVED
				));
				?>

				<div class="alert alert-info">
					<?php
					$fullUrl = Router::url($url, true);
					echo __('Modifications in an object currently in "Approved" status are required. Your approval is required before this changes can take place. The object is <a href="%s" target="_blank">located here</a>.', $fullUrl);
					?>
				</div>

				<div class="form-group">
					<label class="col-md-2 control-label"><?php echo __('Comment'); ?>:</label>
					<div class="col-md-10">
						<?php echo $this->Form->input('comment', array(
							'type' => 'textarea',
							'label' => false,
							'div' => false,
							'class' => 'form-control'
						)); ?>
					</div>
				</div>

				<div class="form-actions">
					<?php echo $this->Form->submit(__('Continue'), array(
						'class' => 'btn btn-danger',
						'div' => false
					)); ?>
					&nbsp;
					<?php echo $this->Html->link(__('Cancel'), $url, array(
						'class' => 'btn btn-inverse'
					)); ?>
				</div>

				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>
