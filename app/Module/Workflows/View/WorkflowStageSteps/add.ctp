<?php
App::uses('WorkflowStageStep', 'Workflows.Model');
?>
<div class="row">
	<div class="col-md-7">
		<div class="widget box widget-form wf-next-stage-join-form">
			<div class="widget-header">
				<h4>&nbsp;</h4>
			</div>
			<div class="widget-content">

				<?php
					if (isset($edit)) {
						echo $this->Form->create('WorkflowStageStep', array(
							'url' => array( 'controller' => 'WorkflowStageSteps', 'action' => 'edit' ),
							'class' => 'form-horizontal row-border',
							'novalidate' => true
						));

						echo $this->Form->input( 'id', array( 'type' => 'hidden' ) );
						echo $this->Form->input( 'model', array( 'type' => 'hidden' ) );
						$submit_label = __( 'Edit' );
					}
					else {
						echo $this->Form->create( 'WorkflowStageStep', array(
							'url' => array( 'controller' => 'WorkflowStageSteps', 'action' => 'add', $workflowStageId, $stepType ),
							'class' => 'form-horizontal row-border',
							'novalidate' => true
						) );

						$submit_label = __( 'Add' );
					}
				?>

				<div class="tabbable box-tabs box-tabs-styled">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab_general" data-toggle="tab"><?php echo __('General'); ?></a></li>
						<li><a href="#tab_calls" data-toggle="tab"><?php echo __('Who Calls'); ?></a></li>
						<!-- <li><a href="#tab_triggers" data-toggle="tab"><?php echo __('Who Triggers'); ?></a></li> -->
						<li><a href="#tab_notifications" data-toggle="tab"><?php echo __('Notifications'); ?></a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane fade in active" id="tab_general">
							<?php
							if (isset($stepsValid) && !$stepsValid) {
								echo $this->Ux->getAlert(__('There is already one %s step created for the current workflow stage. It is not available to have more than one at this time.', WorkflowStageStep::stepTypes($stepType)), array(
									'type' => 'danger'
								));
							}

							if ($stepType == WorkflowStageStep::STEP_TYPE_DEFAULT) {
								echo $this->Ux->getAlert(__('Configure a Default step for your current stage here. Remember there can be only one Default step.'), array(
									'type' => 'info'
								));
							}

							if ($stepType == WorkflowStageStep::STEP_TYPE_CONDITIONAL) {
								echo $this->Ux->getAlert(__('Configure a Conditional step for your current stage here. You can have more than one conditional steps configured for your current stage. Each configuration is able to have one or more conditional settings on fields and its values which you can also modify when you edit any item in the current section. These conditions are required to be evaluated positively in order to trigger the step.'), array(
									'type' => 'info'
								));
							}

							if ($stepType == WorkflowStageStep::STEP_TYPE_ROLLBACK) {
								echo $this->Ux->getAlert(__('Configure a Rollback step here. A stage can have only one rollback step which is triggered after the timeout expires.'), array(
									'type' => 'info'
								));
							}

							echo $this->FieldData->inputs([
								// $FieldDataCollection->step_type,
								$FieldDataCollection->wf_next_stage_id
							]);
							?>

							<?php
							if ($stepType == WorkflowStageStep::STEP_TYPE_ROLLBACK) {
								echo $this->FieldData->input($FieldDataCollection->timeout, [
									'min' => 1,
									'max' => 300,
									'default' => 1,
									'style' => 'width:20%;min-width:140px;'
								]);
							}

							if ($stepType == WorkflowStageStep::STEP_TYPE_CONDITIONAL) {
								echo $this->element('Workflows.add_conditions', [
									'workflowStageId' => $workflowStageId
								]);
							}
							?>
						</div>

						<div class="tab-pane fade in" id="tab_calls">
							<?php
							if (WorkflowStageStep::isTypeCallable($stepType)) {
								echo $this->Ux->getAlert(__('Who can call this stage.'), array(
									'type' => 'info'
								));

								echo $this->FieldData->inputs([
									$FieldDataCollection->CallUser,
									$FieldDataCollection->CallGroup
								]);
							}
							elseif (WorkflowStageStep::isTypeConditional($stepType)) {
								echo $this->Ux->getAlert(__('Conditional step triggers a call to next stage automatically based on your defined rules.'), array(
									'type' => 'info'
								));
							}
							else {
								echo $this->Ux->getAlert(__('Rollback step is triggered to the defined stage automatically after timeout expires.'), array(
									'type' => 'info'
								));
							}
							?>
						</div>

						<!-- <div class="tab-pane fade in" id="tab_triggers">
							<?php
							// echo $this->Ux->getAlert(__('Who can trigger this stage.'), array(
							// 	'type' => 'info'
							// ));

							// echo $this->FieldData->inputs([
							// 	$FieldDataCollection->TriggerUser,
							// 	$FieldDataCollection->TriggerGroup
							// ]);
							?>
						</div> -->

						<div class="tab-pane fade in" id="tab_notifications">
							<?php
							echo $this->Ux->getAlert(__('Once this step has been triggered, notification emails configured below are sent.'), array(
								'type' => 'info'
							));

							echo $this->FieldData->inputs([
								$FieldDataCollection->notification_message,
								$FieldDataCollection->NotifyUser,
								$FieldDataCollection->NotifyGroup
							]);
							?>

						</div>

						
					</div>
				</div>

				<div class="form-actions">
					<?php echo $this->Form->submit( $submit_label, array(
						'class' => 'btn btn-primary',
						'div' => false
					) ); ?>
					&nbsp;
					<?php
					echo $this->Ajax->cancelBtn('WorkflowStage');
					?>
				</div>

				<?php echo $this->Form->end(); ?>

			</div>
		</div>
	</div>
	<div class="col-lg-5">
		<?php
		echo $this->element('ajax-ui/sidebarWidget', array(
			'model' => 'WorkflowStage',
			'id' => isset($edit) ? $this->data['WorkflowStage']['id'] : null
		));
		?>
	</div>
</div>
