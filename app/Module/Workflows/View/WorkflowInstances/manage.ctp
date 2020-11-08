<?php
$this->Html->addCrumb($modelLabel, '#');
$this->Html->addCrumb($objectTitle, '#');
$this->Html->addCrumb(WorkflowsModule::name(), '#');

echo $this->Html->css("Workflows.workflows.css");

if ($workflowsEnabled !== true) {
	echo $this->Ux->getAlert(__('Workflows are not enabled on this section. Please enable them and then manage workflow on objects here.'), [
		'type' => 'danger'
	]);

	return true;
}
?>
<div class="row row-bg">
	<?php if ($Instance->hasRollback()) : ?>
		<div class="col-sm-6 col-md-4">
			<div class="statbox widget box box-shadow">
				<div class="widget-content">
					<div class="visual red">
						<i class="icon-chevron-left"></i>
					</div>
					<div class="title"><?php echo __('Possible Rollback'); ?></div>
					<div class="value">
						<?php
						echo $Instance->getRollbackStep()->WorkflowNextStage->name;
						?>
					</div>
					<?php
					echo $this->Workflows->manageActionBtn(false);
					?>
				</div>
			</div>
		</div>
	<?php else : ?>
		<div class="col-sm-6 col-md-4">
			<div class="statbox widget box" style="pointer-events: none;">
				<div class="widget-content">
					<div class="visual red" style="background-color: #d2d2d2;">
						<i class="icon-chevron-left"></i>
					</div>
					<div class="title">
						<?php
						echo __('Rollback Not Available');
						?>
					</div>
					<div class="value">&nbsp;</div>
					<?php
					echo $this->Workflows->manageActionBtn(false);
					?>
				</div>
			</div>
		</div>
	<?php endif; ?>
	
	<div class="col-sm-6 col-md-4" style="">
		<div class="statbox widget box box-shadow workflow-current-stage"">
			<div class="widget-content">
				<div class="visual cyan">
					<i class="icon-ok"></i>
				</div>
				<div class="title">
					<?php
					echo __('Current Stage');
					?>
				</div>
				<div class="value">
					<?php
					echo $Instance->getStage()->name;
					?>
				</div>
				<?php
				echo $this->Workflows->manageActionBtn(false);
				?>
			</div>
		</div>
	</div>
<?php
// debug($Instance->isStatusPending());
?>
	<?php if (!$Instance->isLastStage()) : ?>
		<?php if ($Instance->isStatusPending()) : ?>
			<div class="col-sm-6 col-md-4">
				<div class="statbox widget box box-shado">
					<div class="widget-content">
						<div class="visual green">
							<i class="icon-chevron-right"></i>
						</div>
						<div class="title">
							<?php echo __('Pending Approval'); ?>
						</div>
						<div class="value">
							<?php
							echo $Instance->getPendingStage()->name;
							?>
						</div>
						<?php
						$url = $this->Workflows->getRequestUrl(
							$model,
							$foreignKey,
							'approve-stage',
							$Instance->getPendingStage()->id
						);

						$canApproveStage = $Instance->canApproveStage($logged['id']);
						?>
						<?php if ($canApproveStage) : ?>
							<?php if ($Instance->currentUserApproved()) : ?>
								<a class="more disabled readonly" href="javascript:void(0);" style="pointer-events: none;">
									<?php echo __('Approved'); ?> 
									<i class="icon-ok"></i>
								</a>
							<?php else : ?>
								<?php
								echo $this->Workflows->manageActionBtn(__('Approve Stage'), $url);
								?>
							<?php endif; ?>
						<?php else : ?>
							<?php
							echo $this->Workflows->manageActionBtn(false);
							?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php else : ?>
			<div class="col-sm-6 col-md-4">
				<div class="statbox widget box box-shado">
					<div class="widget-content">
						<div class="visual green">
							<i class="icon-chevron-right"></i>
						</div>
						<div class="title">
							<?php echo __('Next Default Stage'); ?>
						</div>
						<div class="value">
							<?php
							echo $Instance->getDefaultStep()->WorkflowNextStage->name;
							?>
						</div>
						<?php
						$url = $this->Workflows->getRequestUrl(
							$model,
							$foreignKey,
							'call-stage',
							$Instance->getDefaultStep()->WorkflowNextStage->id
						);

						$canCallStage = $Instance->canCallStage($logged['id']);
						?>
						<?php if ($canCallStage) : ?>
							<?php
							echo $this->Workflows->manageActionBtn(__('Call Stage'), $url);
							?>
						<?php else : ?>
							<?php
							echo $this->Workflows->manageActionBtn(false);
							?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	<?php else : ?>
		<div class="col-sm-6 col-md-4">
			<div class="statbox widget box" style="pointer-events: none;">
				<div class="widget-content">
					<div class="visual green" style="background-color: #d2d2d2;">
						<i class="icon-chevron-right"></i>
					</div>
					<div class="title">
						<?php echo __('Next Stage Not Available'); ?>
					</div>
					<div class="value">
						&nbsp;
					</div>
					<?php
					echo $this->Workflows->manageActionBtn(false);
					?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>

<div class="widget box">
	<div class="widget-header">
		<h4>
			<?php echo __('Next Possible Stages'); ?>
		</h4>
	</div>
	<div class="widget-content" style="">
		<?php if (!$Instance->WorkflowStage->NextStage->isEmpty()) : ?>
			<table class="table table-hover table-striped">
				<thead>
					<tr>
						<th><?php echo __('Stage Name'); ?></th>
						<th><?php echo __('Step Type'); ?></th>
						<th><?php echo __('Description'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($Instance->WorkflowStage->NextStage as $NextStage) : ?>
						<?php
						// debug($NextStage);
						?>
						<tr>
							<td>
								<?php
								echo $NextStage->name;
								?>
							</td>
							<td>
								<?php
								echo $this->Workflows->stepTypeLabel($NextStage->WorkflowStageStep->step_type);
								?>
							</td>
							<td>
								<?php
								echo $NextStage->description;
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php else : ?>
			<?php
			echo $this->Ux->getAlert(__('There are no next possible stages available at the moment.'), [
				'type' => 'info'
			]);
			?>
		<?php endif; ?>
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<div class="widget box">
			<div class="widget-header">
				<h4>
					<?php echo __('Force Stage'); ?>
				</h4>
			</div>
			<div class="widget-content" id="force-stage-wrapper">
				<div id="force-stage-container">
					<?php
					echo $this->element('Workflows.force_stage');
					?>
				</div>

				
			
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="widget box">
			<div class="widget-header">
				<h4>
					<?php echo __('Timeout'); ?>
				</h4>
			</div>
			<div class="widget-content" id="timeout-stage-wrapper">
				<div id="timout-stage-container">
					<?php
					echo $this->element('Workflows.timeout');
					?>
				</div>

				<script type="text/javascript">
					jQuery(function($) {
						// $("#timout-stage-container").on("submit", "#timout-stage-form", function(e) {
						// 	e.preventDefault();

						// 	$.ajax({
						// 		type: "POST",
						// 		url: $(this).attr("action"),
						// 		data: $(this).serializeArray()
						// 	}).done(function(data) {
						// 		$("#timout-stage-container").html(data);
						// 	});
						// });
					});
				</script>
			
			</div>
		</div>
	</div>
</div>

<div class="widget box">
	<div class="widget-header">
		<h4>
			<?php echo __('History'); ?>
		</h4>
	</div>
	<div class="widget-content" style="">
		<?php if (!empty($log)) : ?>
			<table class="table table-hover table-striped">
				<thead>
					<tr>
						<th><?php echo __('Type'); ?></th>
						<th><?php echo __('Message'); ?></th>
						<th><?php echo __('Date'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($log as $item) : ?>
						<tr>
							<td>
								<?php
								echo WorkflowInstanceLog::types($item['WorkflowInstanceLog']['type']);
								?>
							</td>
							<td>
								<?php
								echo $item['WorkflowInstanceLog']['message'];
								?>
							</td>
							<td>
								<?php
								echo $this->Ux->datetime($item['WorkflowInstanceLog']['created']);
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php else : ?>
			<?php
			echo $this->Ux->getAlert(__('No history for this workflow instance recorded yet.'), [
				'type' => 'info'
			]);
			?>
		<?php endif; ?>
	</div>
</div>