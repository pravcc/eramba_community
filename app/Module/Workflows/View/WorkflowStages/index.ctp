<?php
$this->Html->addCrumb($modelLabel, '#');
$this->Html->addCrumb(WorkflowsModule::name(), '#');
?>
<?php
App::Uses('WorkflowStageStep', 'Workflows.Model');
// debug($data);
?>

<?php
// lets warn users in case hourly cron is not running properly because workflow timeout feature requires it
if (empty($hourlyCronStatus)) {
	$systemHealth = $this->Html->link(__('System Health'), [
		'plugin' => null,
		'controller' => 'settings',
		'action' => 'systemHealth'
	]);

	echo $this->Ux->getAlert(__('Workflows require the hourly CRON to be configured and run properly. It appears there is something wrong yours, please make sure to resolve this issue and then proceed to configure Workflows.
		More information about status of your CRON is available in %s.', $systemHealth), [
		'type' => 'danger'
	]);
}
?>

<div id="workflow-setting-wrapper">
	<?php
	// echo $this->element('../WorkflowSettings/add', [
	// 	'FieldDataCollection' => $FieldDataSettingsCollection
	// ]);
	?>
</div>

<div class="widget">
	<div class="btn-toolbar pull-right">
		<div class="btn-group">
			<?php
			echo $this->Html->link(__('Back'), array(
				'plugin' => false,
				'controller' => controllerFromModel($model),
				'action' => 'index'
			), array(
				'class' => 'btn btn-info',
				'escape' => false
			));
			?>
		</div>
	</div>

	<div class="btn-toolbar">
		<div class="btn-group">
			<?php
			echo $this->Html->link('<i class="icon-plus-sign"></i>' . __('Add New Stage'),
				array(
					'plugin' => 'workflows',
					'controller' => 'workflowStages',
					'action' => 'add',
					$model
				), array(
				'class' => 'btn',
				'data-ajax-action' => 'add',
				'escape' => false
			));
			 
			echo $this->Html->link('<i class="icon-cog"></i>' . __('General Settings'),
				array(
					'plugin' => 'workflows',
					'controller' => 'workflowSettings',
					'action' => 'edit',
					$setting['WorkflowSetting']['id'], $model
				), array(
				'class' => 'btn',
				'data-ajax-action' => 'edit',
				'escape' => false
			));
			?>
		</div>
	</div>
</div>

<?php if (!empty($data)) : ?>
	<?php foreach ($data as $item) : ?>
		<div class="widget box">
			<div class="widget-header">
				<h4>
					<?php echo __('Workflow Stage: %s', $item['WorkflowStage']['name']); ?>
				</h4>
				<div class="toolbar no-padding">
					<div class="btn-group">
						<span class="btn btn-xs widget-collapse hidden"><i class="icon-angle-down"></i></span>
						<span class="btn btn-xs dropdown-toggle" data-toggle="dropdown">
							<?php echo __( 'Manage' ); ?> <i class="icon-angle-down"></i>
						</span>
						<?php
						$addStep = array(
							'controller' => 'WorkflowStageSteps',
							'action' => 'add',
							$item['WorkflowStage']['id']
						);
						$defaultUrl = $addStep + [1 => WorkflowStageStep::STEP_TYPE_DEFAULT];
						$conditionalUrl = $addStep + [1 => WorkflowStageStep::STEP_TYPE_CONDITIONAL];
						$rollbackUrl = $addStep + [1 => WorkflowStageStep::STEP_TYPE_ROLLBACK];
						
						// no next steps can be configured if this is a last stage
						if ($item['WorkflowStage']['stage_type'] != WorkflowStage::STAGE_LAST) {
							$this->Ajax->addToActionList(__('Add Default Step'), $defaultUrl, 'plus-sign', 'add');
							$this->Ajax->addToActionList(__('Add Conditional Step'), $conditionalUrl, 'plus-sign', 'add');

							if ($item['WorkflowStage']['stage_type'] != WorkflowStage::STAGE_INITIAL) {
								$this->Ajax->addToActionList(__('Add Rollback'), $rollbackUrl, 'plus-sign', 'add');
							}
						}

						echo $this->Ajax->getActionList($item['WorkflowStage']['id'], array(
							'notifications' => false,
							'attachments' => false,
							'records' => false,
							'comments' => false,
							'model' => 'WorklowStage',
							'item' => $item
						));
						?>
					</div>
				</div>
			</div>
			<div class="widget-subheader">
				<table class="table table-hover table-striped table-bordered table-highlight-head">
					<thead>
						<tr>
							<th>
								<?php
								echo __('Stage Type');
								?>
							</th>
							<th>
								<?php
								echo __('Description');
								?>
							</th>
							<th>
								<?php
								echo __('Approval Method');
								?>
							</th>
							<th>
								<?php
								echo __('Approval Users');
								?>
							</th>
							<th>
								<?php
								echo __('Approval Groups');
								?>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<?php
								echo WorkflowStage::stageTypes($item['WorkflowStage']['stage_type'])
								?>
							</td>
							<td>
								<?php
								echo $item['WorkflowStage']['description'];
								?>
							</td>
							<td>
								<?php
								echo WorkflowStage::approvalMethods($item['WorkflowStage']['approval_method'])
								?>
							</td>
							<td>
								<?php
								echo $this->Ux->text($this->Users->listNames($item, 'ApprovalUser'));
								?>
							</td>
							<td>
								<?php
								echo $this->Ux->text($this->Groups->listNames($item, 'ApprovalGroup'));
								?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="widget-content" style="">
				<?php if (!empty($item['WorkflowStageStep'])) : ?>
					<table class="table table-hover table-striped">
						<thead>
							<tr>
								<th><?php echo __('Step To Stage'); ?></th>
								<th><?php echo __('Step Type'); ?></th>
								<th class="align-center"><?php echo __('Action'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($item['WorkflowStageStep'] as $nextStageJoin) : ?>
								<?php
								$nextStage = $allStages[$nextStageJoin['wf_next_stage_id']];
								?>
								<tr>
									<td>
										<?php
										echo $nextStage['WorkflowStage']['name'];
										?>
									</td>
									<td>
										<?php
										echo $this->Workflows->stepTypeLabel($nextStageJoin['step_type']);
										?>
									</td>
									<td class="align-center">
										<?php
										echo $this->Ajax->getActionList($nextStageJoin['id'], array(
											'attachments' => false,
											'records' => false,
											'comments' => false,
											'style' => 'icons',
											'controller' => 'WorkflowStageSteps',
											'model' => 'WorkflowStageStep',
											'item' => $nextStage
										));
										?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else : ?>
					<?php
					if ($item['WorkflowStage']['stage_type'] != WorkflowStage::STAGE_LAST) {
						echo $this->Ux->getAlert(__('There aren\'t any next possible stages defined for this stage.'), [
							'type' => 'info'
						]);
					}
					else {
						echo $this->Ux->getAlert(__('This is a last stage for this workflow. No additional steps can be configured on this stage.'), [
							'type' => 'info'
						]);
					}
					?>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach ; ?>

	<?php echo $this->element( CORE_ELEMENT_PATH . 'pagination' ); ?>
<?php else : ?>
	<?php echo $this->element( 'not_found', array(
		'message' => __( 'No Workflow Stages found.' )
	) ); ?>
<?php endif; ?>

<script type="text/javascript">
	jQuery(function($) {
		var toggleState = getToggleState();

		var $settingsForm = "#workflows-settings-form";
		var $settingsWrapper = $("#workflow-setting-wrapper");

		function getToggleState() {
			return $("#workflows-toggle").is(":checked");
		}

		$settingsWrapper.on("submit", $settingsForm, function(e) {
			e.preventDefault();

			// lets not run ajax saving if toggle has not been changed for this case
			// if (getToggleState() == toggleState) {
			// 	return true;
			// }

			var formData = $(this).serializeArray();

			$.ajax({
				type: "POST",
				url: $(this).prop("action"),
				data: formData
			}).done(function(data) {
				$settingsWrapper.html(data);

				FormComponents.init();
				toggleState = getToggleState();
			});
		});

	});
</script>
