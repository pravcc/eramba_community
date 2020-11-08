<?php
$class = 'table table-hover table-striped';
if (isset($pdf) && $pdf) {
	$class = 'table-pdf table-pdf-list';
}
?>
<?php if ($projectsCount) : ?>
	<table class="<?php echo $class; ?>">
		<thead>
			<tr>
				<th><?php echo __('Classification'); ?></th>
				<th><?php echo __('Items'); ?></th>
				<th>
					<?php echo __('Improvement Project Expired'); ?>

					<?php
					echo $this->Html->link('<i class="icon-info-sign"></i>', array(
						'controller' => 'projects',
						'action' => 'index',
						'?' => array(
							'expired' => true
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
					<?php echo __('Improvement Project over Budget'); ?>

					<?php
					echo $this->Html->link('<i class="icon-info-sign"></i>', array(
						'controller' => 'projects',
						'action' => 'index',
						'?' => array(
							'over_budget' => true
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
					<?php echo __('Improvement Project with Expired Tasks'); ?>

					<?php
					echo $this->Html->link('<i class="icon-info-sign"></i>', array(
						'controller' => 'projects',
						'action' => 'index',
						'?' => array(
							'expired_tasks' => true
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
			<?php foreach ($projectClassifications as $name => $items) : ?>
				<tr>
					<td>
						<?php echo $name; ?>
					</td>
					<td>
						<?php
						echo count($items['Project']);
						?>
					</td>
					<td>
						<?php
						echo $this->ProgramHealth->getProjectStatusCount($items, array('expired'));
						?>
					</td>

					<td>
						<?php
						echo $this->ProgramHealth->getProjectStatusCount($items, array('over_budget'));
						?>
					</td>
					<td>
						<?php
						echo $this->ProgramHealth->getProjectStatusCount($items, array('expired_tasks'));
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
					echo count($noProjectClassifications['Project']);
					?>
				</td>
				<td>
					<?php
					echo $this->ProgramHealth->getProjectStatusCount($noProjectClassifications, array('expired'));
					?>
				</td>
				<td>
					<?php
					echo $this->ProgramHealth->getProjectStatusCount($noProjectClassifications, array('over_budget'));
					?>
				</td>
				<td>
					<?php
					echo $this->ProgramHealth->getProjectStatusCount($noProjectClassifications, array('expired_tasks'));
					?>
				</td>
			</tr>
		</tbody>
	</table>
<?php else : ?>
	<?php
	echo $this->element('not_found', array(
		'message' => __('No Projects found.')
	));
	?>
<?php endif; ?>