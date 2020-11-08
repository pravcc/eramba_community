<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-content">

				<?php
					if (isset($edit)) {
						echo $this->Form->create('Workflow', array(
							'url' => array('controller' => 'workflows', 'action' => 'edit'),
							'class' => 'form-horizontal row-border'
						));

						echo $this->Form->input('id', array('type' => 'hidden'));
						$submit_label = __('Edit');
					}
					else {
						echo $this->Form->create('Workflow', array(
							'url' => array('controller' => 'workflows', 'action' => 'add'),
							'class' => 'form-horizontal row-border'
						));
						
						$submit_label = __('Add');
					}
				?>

				<div class="form-group form-group-first">
					<label class="col-md-2 control-label"><?php echo __('Name'); ?>:</label>
					<div class="col-md-10">
						<?php echo $this->Form->input('name', array(
							'label' => false,
							'div' => false,
							'class' => 'form-control',
							'readonly' => true
						)); ?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-2 control-label"><?php echo __('Who Validates?'); ?>:</label>
					<div class="col-md-10">
						<?php
						$selected = array();
						if (isset($this->request->data['ValidatorUser'])) {
							foreach ($this->request->data['ValidatorUser'] as $entry) {
								$selected[] = $entry['id'];
							}
						}

						if (isset($this->request->data['Workflow']['validator_user_id']) && is_array($this->request->data['Workflow']['validator_user_id'])) {
							foreach ($this->request->data['Workflow']['validator_user_id'] as $entry) {
								$selected[] = $entry;
							}
						}

						echo $this->Form->input('validator_user_id', array(
							'options' => $users,
							'label' => false,
							'div' => false,
							'class' => 'select2 col-md-12 full-width-fix select2-offscreen',
							'multiple' => true,
							'selected' => $selected,
							'disabled' => $workflowInherited
						));
						?>
						<span class="help-block"><?php echo __('Select one or more users that will act as validators. You can optionally not select anyone and the workflows will automatically skip to the "approval" phase.'); ?></span>

						<?php
						$selected = array();
						if (isset($this->request->data['ValidatorScope'])) {
							foreach ($this->request->data['ValidatorScope'] as $entry) {
								$selected[] = $entry['WorkflowsValidatorScope']['custom_identifier'];
							}
						}

						if (isset($this->request->data['Workflow']['validator_scope_id']) && is_array($this->request->data['Workflow']['validator_scope_id'])) {
							foreach ($this->request->data['Workflow']['validator_scope_id'] as $entry) {
								$selected[] = $entry;
							}
						}

						echo $this->Form->input('validator_scope_id', array(
							'options' => $scopes,
							'label' => false,
							'div' => false,
							'class' => 'select2 col-md-12 full-width-fix select2-offscreen',
							'multiple' => true,
							'selected' => $selected,
							'disabled' => $workflowInherited
						));
						?>
						<span class="help-block"><?php echo __('Select one or more roles (not users) that will act as validators. You can optionally not select anyone and the workflows will automatically skip to the "approval" phase. If roles are not diplayed this is because they have not been configured properly.'); ?></span>

						<?php
						if (!empty($customFields)) :
							$selected = array();
							if (isset($this->request->data['ValidatorCustom'])) {
								foreach ($this->request->data['ValidatorCustom'] as $entry) {
									$selected[] = $entry['custom_identifier'];
								}
							}

							if (isset($this->request->data['Workflow']['custom_validators']) && is_array($this->request->data['Workflow']['custom_validators'])) {
								foreach ($this->request->data['Workflow']['custom_validators'] as $entry) {
									$selected[] = $entry;
								}
							}

							echo $this->Form->input('custom_validators', array(
								'options' => $customFields,
								'label' => false,
								'div' => false,
								'class' => 'select2 col-md-12 full-width-fix select2-offscreen',
								'multiple' => true,
								'selected' => $selected,
								'disabled' => $workflowInherited
							));
							?>
							<span class="help-block"><?php echo __('Select one or more custom roles that will act as validators. + None'); ?></span>
						<?php endif; ?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-2 control-label"><?php echo __('Who Approves?'); ?>:</label>
					<div class="col-md-10">
						<?php
						$selected = array();
						if (isset($this->request->data['ApproverUser'])) {
							foreach ($this->request->data['ApproverUser'] as $entry) {
								$selected[] = $entry['id'];
							}
						}

						if (isset($this->request->data['Workflow']['approver_user_id']) && is_array($this->request->data['Workflow']['approver_user_id'])) {
							foreach ($this->request->data['Workflow']['approver_user_id'] as $entry) {
								$selected[] = $entry;
							}
						}

						echo $this->Form->input('approver_user_id', array(
							'options' => $users,
							'label' => false,
							'div' => false,
							'class' => 'select2 col-md-12 full-width-fix select2-offscreen',
							'multiple' => true,
							'selected' => $selected,
							'disabled' => $workflowInherited
						));
						?>
						<span class="help-block"><?php echo __('Select one or more users that will act as approvers. You can optionally not select anyone and the "approval" phase and the workflows will be skipped and the item will be approved automatically once the validator (if defined) has validated.'); ?></span>

						<?php
						$selected = array();
						if (isset($this->request->data['ApproverScope'])) {
							foreach ($this->request->data['ApproverScope'] as $entry) {
								$selected[] = $entry['WorkflowsApproverScope']['custom_identifier'];
							}
						}

						if (isset($this->request->data['Workflow']['approver_scope_id']) && is_array($this->request->data['Workflow']['approver_scope_id'])) {
							foreach ($this->request->data['Workflow']['approver_scope_id'] as $entry) {
								$selected[] = $entry;
							}
						}

						echo $this->Form->input('approver_scope_id', array(
							'options' => $scopes,
							'label' => false,
							'div' => false,
							'class' => 'select2 col-md-12 full-width-fix select2-offscreen',
							'multiple' => true,
							'selected' => $selected,
							'disabled' => $workflowInherited
						));
						?>
						<span class="help-block"><?php echo __('Select one or more roles (not users) that will act as approvers. You can optionally not select anyone for the "approval" phase and the workflows will be skipped and the item will be approved automatically once the validator (if defined) has validated. If roles are not diplayed this is because they have not been configured properly.'); ?></span>

						<?php
						if (!empty($customFields)) :
							$selected = array();
							if (isset($this->request->data['ApproverCustom'])) {
								foreach ($this->request->data['ApproverCustom'] as $entry) {
									$selected[] = $entry['custom_identifier'];
								}
							}

							if (isset($this->request->data['Workflow']['custom_approvers']) && is_array($this->request->data['Workflow']['custom_approvers'])) {
								foreach ($this->request->data['Workflow']['custom_approvers'] as $entry) {
									$selected[] = $entry;
								}
							}

							echo $this->Form->input('custom_approvers', array(
								'options' => $customFields,
								'label' => false,
								'div' => false,
								'class' => 'select2 col-md-12 full-width-fix select2-offscreen',
								'multiple' => true,
								'selected' => $selected,
								'disabled' => $workflowInherited
							));
							?>
							<span class="help-block"><?php echo __('Select one or more custom roles that will act as approvers. + None'); ?></span>
						<?php endif; ?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-2 control-label"><?php echo __('Email notifications'); ?>:</label>
					<div class="col-md-10">
						<label class="checkbox">
							<?php echo $this->Form->input('notifications', array(
								'type' => 'checkbox',
								'label' => false,
								'div' => false,
								'class' => 'uniform'
							)); ?>
							<?php echo __('Enable'); ?>
						</label>
						<span class="help-block"><?php echo __('Check if you want validators and approvers to receive email notifications. If unchecked users will only get notifications once they log into the system'); ?></span>
					</div>
				</div>


				<div class="form-actions">
					<?php echo $this->Form->submit($submit_label, array(
						'class' => 'btn btn-primary',
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
