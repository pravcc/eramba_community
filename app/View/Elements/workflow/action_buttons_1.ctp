<ul class="table-controls">
	<?php if ($item['owner']) : ?>

		<?php if ($item['draft']) : ?>
			<li>
				<?php
				echo $this->Html->link('<i class="icon-hand-right"></i>', array(
					'controller' => 'workflows',
					'action' => 'requestValidation',
					$currentModel,
					$id
				), array(
					'class' => 'bs-tooltip',
					'escape' => false,
					'title' => __('Request Validation')
				));
				?>
				<br>
				<span class="label label-default label-margin-top"><?php echo __('Draft'); ?></span>
				<br />
				<span class="label label-success label-margin-top"><?php echo __('Owner'); ?></span>
			</li>
		<?php endif; ?>

		<?php if ($item['getValidation']) : ?>
			<li>
				<span class="label label-primary"><?php echo __('Validation requested'); ?></span>
				<br />
				<span class="label label-success label-margin-top"><?php echo __('Owner'); ?></span>
			</li>
		<?php endif; ?>

		<?php if ($item['validated']) : ?>
			<li>
				<?php
				echo $this->Html->link('<i class="icon-hand-right"></i>', array(
					'controller' => 'workflows',
					'action' => 'requestApproval',
					$currentModel,
					$id
				), array(
					'class' => 'bs-tooltip',
					'escape' => false,
					'title' => __('Request Approval')
				));
				?>
				<br>
				<span class="label label-primary label-margin-top"><?php echo __('Validated'); ?></span>
				<br />
				<span class="label label-success label-margin-top"><?php echo __('Owner'); ?></span>
			</li>
		<?php endif; ?>

		<?php if ($item['getApproval']) : ?>
			<li>
				<span class="label label-primary"><?php echo __('Approval requested'); ?></span>
				<br />
				<span class="label label-success label-margin-top"><?php echo __('Owner'); ?></span>
			</li>
		<?php endif; ?>

		<?php if ($item['approved']) : ?>
			<li>
				<?php if ($item['acknowledgement']) : ?>
					<span class="label label-primary"><?php echo __('Acknowledgement requested'); ?></span>
				<?php else : ?>
					<span class="label label-success"><?php echo __('Approved'); ?></span>
				<?php endif; ?>
				<br />
				<span class="label label-success label-margin-top"><?php echo __('Owner'); ?></span>
			</li>
		<?php endif; ?>

	<?php else : ?>
		
		<?php if ((in_array(null, $workflowData[$currentModel]['validator']) || in_array($id, $workflowData[$currentModel]['validator'])) && $item['getValidation']) : ?>
			<li>
				<?php echo $this->Html->link( '<i class="icon-ok"></i>', array(
					'controller' => 'workflows',
					'action' => 'validateItem',
					$currentModel,
					$id
				), array(
					'class' => 'bs-tooltip',
					'escape' => false,
					'title' => __('Validate')
				) ); ?>
			</li>
		<?php elseif ($item['getValidation']) : ?>
			<li>
				<span class="label label-primary"><?php echo __('Validation requested'); ?></span>
			</li>
		<?php endif; ?>

		<?php if ((in_array(null, $workflowData[$currentModel]['approver']) || in_array($id, $workflowData[$currentModel]['approver'])) && $item['getApproval']) : ?>
			<li>
				<?php echo $this->Html->link( '<i class="icon-ok"></i>', array(
					'controller' => 'workflows',
					'action' => 'approveItem',
					$currentModel,
					$id
				), array(
					'class' => 'bs-tooltip',
					'escape' => false,
					'title' => __('Approve')
				) ); ?>
			</li>
		<?php elseif ($item['getApproval']) : ?>
			<li>
				<span class="label label-primary"><?php echo __('Approval requested'); ?></span>
			</li>
		<?php endif; ?>

		<?php if ($item['validated']) : ?>
			<li>
				<span class="label label-primary"><?php echo __('Validated'); ?></span>
			</li>
		<?php endif; ?>

		<?php if ($item['approved']) : ?>
			<li>
				<?php if ($item['acknowledgement']) : ?>
					<span class="label label-primary"><?php echo __('Acknowledgement requested'); ?></span>
				<?php else : ?>
					<span class="label label-success"><?php echo __('Approved'); ?></span>
				<?php endif; ?>
			</li>
		<?php endif; ?>

		<?php if ($item['draft']) : ?>
			<li>
				<span class="label label-default"><?php echo __('Draft'); ?></span>
			</li>
		<?php endif; ?>

	<?php endif; ?>
	
</ul>