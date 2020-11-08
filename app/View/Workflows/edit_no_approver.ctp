<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-content">
				<?php
				echo $this->Form->create('WorkflowNextStep', array(
					'url' => array('controller' => 'workflows', 'action' => 'editNoApprover', $model, $id),
					'class' => 'form-horizontal row-border',
					'novalidate' => true
				));

				echo $this->Form->input('_editWarning', array(
					'type' => 'hidden',
					'value' => 1
				));
				?>

				<div class="alert alert-danger">
					<?php
					echo $warning;
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
						'class' => 'btn btn-inverse',
						'data-dismiss' => 'modal'
					)); ?>
				</div>

				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>