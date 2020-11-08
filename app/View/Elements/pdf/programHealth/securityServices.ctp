<?php
$class = 'table table-hover table-striped';
if (isset($pdf) && $pdf) {
	$class = 'table-pdf table-pdf-list';
}
?>
<?php if ($controlsCount) : ?>
	<table class="<?php echo $class; ?>">
		<thead>
			<tr>
				<th><?php echo __('Classification'); ?></th>
				<th><?php echo __('Items'); ?></th>
				<th>
					<?php echo __('Last audit failed'); ?>

					<?php
					echo $this->Html->link('<i class="icon-info-sign"></i>', array(
						'controller' => 'securityServices',
						'action' => 'index',
						'?' => array(
							'audits_last_failed' => true
						)
					), array(
						'class' => 'bs-tooltip',
						'title' => __('Show list'),
						'escape' => false,
						'style' => 'text-decoration:none;'
					));
					?>
				</th>
				<th>
					<?php echo __('Last audit missing'); ?>

					<?php
					echo $this->Html->link('<i class="icon-info-sign"></i>', array(
						'controller' => 'securityServices',
						'action' => 'index',
						'?' => array(
							'audits_last_missing' => true
						)
					), array(
						'class' => 'bs-tooltip',
						'title' => __('Show list'),
						'escape' => false,
						'style' => 'text-decoration:none;'
					));
					?>
				</th>
				<th>
					<?php echo __('Last maintenance missing'); ?>
					
					<?php
					echo $this->Html->link('<i class="icon-info-sign"></i>', array(
						'controller' => 'securityServices',
						'action' => 'index',
						'?' => array(
							'maintenances_last_missing' => true
						)
					), array(
						'class' => 'bs-tooltip',
						'title' => __('Show list'),
						'escape' => false,
						'style' => 'text-decoration:none;'
					));
					?>
				</th>
				<th>
					<?php echo __('Ongoing Corrective Actions'); ?>
					
					<?php
					echo $this->Html->link('<i class="icon-info-sign"></i>', array(
						'controller' => 'securityServices',
						'action' => 'index',
						'?' => array(
							'ongoing_corrective_actions' => true
						)
					), array(
						'class' => 'bs-tooltip',
						'title' => __('Show list'),
						'escape' => false,
						'style' => 'text-decoration:none;'
					));
					?>
				</th>
				<th>
					<?php echo __('Control in Design'); ?>
					
					<?php
					echo $this->Html->link('<i class="icon-info-sign"></i>', array(
						'controller' => 'securityServices',
						'action' => 'index',
						'?' => array(
							'design' => true
						)
					), array(
						'class' => 'bs-tooltip',
						'title' => __('Show list'),
						'escape' => false,
						'style' => 'text-decoration:none;'
					));
					?>
				</th>
				<th>
					<?php echo __('Control with Issues'); ?>
					
					<?php
					echo $this->Html->link('<i class="icon-info-sign"></i>', array(
						'controller' => 'securityServices',
						'action' => 'index',
						'?' => array(
							'control_with_issues' => true
						)
					), array(
						'class' => 'bs-tooltip',
						'title' => __('Show list'),
						'escape' => false,
						'style' => 'text-decoration:none;'
					));
					?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($controlClassifications as $name => $items) : ?>
				<tr>
					<td>
						<?php echo $name; ?>
					</td>
					<td>
						<?php
						echo count($items['SecurityService']);
						?>
					</td>
					<td>
						<?php
						echo $this->ProgramHealth->getSecurityServiceStatusCount($items, array('audits_last_passed'));
						?>
					</td>

					<td>
						<?php
						echo $this->ProgramHealth->getSecurityServiceStatusCount($items, array('audits_last_missing'));
						?>
					</td>
					<td>
						<?php
						echo $this->ProgramHealth->getSecurityServiceStatusCount($items, array('maintenances_last_missing'));
						?>
					</td>
					<td>
						<?php
						echo $this->ProgramHealth->getSecurityServiceStatusCount($items, array('ongoing_corrective_actions'));
						?>
					</td>
					<td>
						<?php
						echo $this->ProgramHealth->getSecurityServiceStatusCount($items, array('security_service_type_id'));
						?>
					</td>
					<td>
						<?php
						echo $this->ProgramHealth->getSecurityServiceStatusCount($items, array('control_with_issues'));
						?>
					</td>
				</tr>
			<?php endforeach; ?>

			<!-- No classification entry -->
			<tr>
				<td>
					<strong><?php echo __('All Others'); ?></strong>
				</td>
				<td>
					<?php
					echo count($noControlClassifications['SecurityService']);
					?>
				</td>
				<td>
					<?php
					echo $this->ProgramHealth->getSecurityServiceStatusCount($noControlClassifications, array('audits_last_passed'));
					?>
				</td>
				<td>
					<?php
					echo $this->ProgramHealth->getSecurityServiceStatusCount($noControlClassifications, array('audits_last_missing'));
					?>
				</td>
				<td>
					<?php
					echo $this->ProgramHealth->getSecurityServiceStatusCount($noControlClassifications, array('maintenances_last_missing'));
					?>
				</td>
				<td>
					<?php
					echo $this->ProgramHealth->getSecurityServiceStatusCount($noControlClassifications, array('ongoing_corrective_actions'));
					?>
				</td>
				<td>
					<?php
					echo $this->ProgramHealth->getSecurityServiceStatusCount($noControlClassifications, array('security_service_type_id'));
					?>
				</td>
				<td>
					<?php
					echo $this->ProgramHealth->getSecurityServiceStatusCount($noControlClassifications, array('control_with_issues'));
					?>
				</td>
			</tr>
		</tbody>
	</table>
<?php else : ?>
	<?php
	echo $this->element('not_found', array(
		'message' => __('No Security Services found.')
	));
	?>
<?php endif; ?>