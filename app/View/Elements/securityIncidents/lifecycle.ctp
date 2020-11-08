<?php if (!empty($item['SecurityIncidentStage'])) : ?>
	<table class="table table-hover table-striped table-bordered table-highlight-head">
		<thead>
			<tr>
				<th>
					<?php echo __('Stage'); ?>
				</th>
				<th>
					<?php echo __('Description'); ?>
				</th>
				<th class="align-center"><?php echo __('Status'); ?></th>
				<th class="align-center" ><?php echo __('Action'); ?>
				<div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'Is important that incidents are systematically analysed and documented. Is expected that each stage has comments, attachments with incidents and tagged as "complete" once the stage requirements are completed.' ); ?>'>
			<?php echo __('Status'); ?>
				<i class="icon-info-sign"></i>
				</div>
				</th>
				<?php /*
				<th class="align-center" ><?php echo __('Workflows'); ?></th>
				*/ ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($item['SecurityIncidentStage'] as $k => $stage) : ?>
			<tr>
				<td><?php echo $stage['name']; ?></td>
				<td><?php echo $stage['description']; ?></td>
				<td class="align-center">
					<?php
						$statuses = getSecurityIncidentStageStatus();
						$labelClass = $stage['SecurityIncidentStagesSecurityIncident']['status']?'label-success':'label-danger';
						$sId = $stage['SecurityIncidentStagesSecurityIncident']['id'];
						$sStatus = $stage['SecurityIncidentStagesSecurityIncident']['status'];
						?>
						<span class="label <?php echo $labelClass ?> stage-<?php echo $sId?>">
							<?php echo $statuses[$sStatus]; ?>
						</span>
				</td>
				<td class="align-center">
					<ul class="table-controls">
						<li class="stage-btn-comp-<?php echo $sId ?> <?php echo $sStatus?'hidden':'' ?>">
							<?php echo $this->Html->link( '<i class="icon-check"></i>', '#', array(
								'class' => 'bs-tooltip',
								'escape' => false,
								'title' => __( 'Complete' ),
								'onclick' => 'processStage(1,'.$stage['SecurityIncidentStagesSecurityIncident']['id'].',this, \''.$statuses[1].'\');return false;'
							) ); ?>
						</li>
						<li class="stage-btn-uncomp-<?php echo $sId ?> <?php echo !$sStatus?'hidden':'' ?>">
							<?php echo $this->Html->link( '<i class="icon-remove"></i>', '#', array(
								'class' => 'bs-tooltip',
								'escape' => false,
								'title' => __( 'Uncomplete' ),
								'onclick' => 'processStage(0,'.$stage['SecurityIncidentStagesSecurityIncident']['id'].',this, \''.$statuses[0].'\');return false;'
							) ); ?>
						</li>

					</ul>
					<?php
					echo $this->Ajax->getActionList($stage['SecurityIncidentStagesSecurityIncident']['id'], array(
						'notifications' => false,
						'edit' => false,
						'trash' => false,

						'style' => 'icons',
						'controller' => 'securityIncidentStagesSecurityIncidents',
						'model' => 'SecurityIncidentStagesSecurityIncident',
						'item' => $item['SecurityIncidentStagesSecurityIncident'][$k]
					));
					?>
				</td>
				<?php /*
				<td class="text-center">
					<?php
					$stageItem = $item['SecurityIncidentStagesSecurityIncident'][$k];
					echo $this->element('workflow/action_buttons_1', array(
						'id' => $stageItem['id'],
						'parentId' => $stage['SecurityIncidentStagesSecurityIncident']['security_incident_id'],
						'item' => $this->Workflow->getActions($stageItem, $stageItem['WorkflowAcknowledgement']),
						'currentModel' => 'SecurityIncidentStagesSecurityIncident'
					));
				?>
			</td>
			*/ ?>

			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php else : ?>
	<?php
	echo $this->element('not_found', array(
		'message' => __('No Stages found.')
	));
	?>
<?php endif; ?>