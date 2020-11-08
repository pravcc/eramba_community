<?php if (!empty($records)) : ?>
	<div class="pull-right">
		<div class="btn-toolbar">
			<div class="btn-group">
				<?php
				echo $this->Html->link(__('View All'), array(
					'controller' => 'systemRecords',
					'action' => 'index',
					$model,
					$foreign_key
				), array(
					'class' => 'btn btn-default',
					'escape' => false
				));
				?>
				<?php
				echo $this->Html->link(__('Export CSV'), array(
					'controller' => 'systemRecords',
					'action' => 'export',
					$model,
					$foreign_key
				), array(
					'class' => 'btn btn-default',
					'escape' => false
				));
				?>
			</div>
		</div>
	</div>
	<br /><br />
	<div class="table-responsive table-responsive-wide">
		<table class="table table-hover table-striped table-condensed">
			<thead>
				<tr>
					<th><?php echo __('Date'); ?></th>
					<th><?php echo __('Type'); ?></th>
					<th><?php echo __('Notes'); ?></th>
					<th><?php echo __('User'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($records as $record) : ?>
					<tr>
						<td>
							<?php
							echo date('Y-m-d', strtotime($record['SystemRecord']['created']));
							?>
						</td>
						<td><?php echo $recordTypes[$record['SystemRecord']['type']]; ?></td>
						<td><?php echo $record['SystemRecord']['notes']; ?></td>
						<td><?php echo $record['User']['name'] . ' ' . $record['User']['surname']; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php else : ?>
	<div class="alert alert-info"><?php echo __('No records found.'); ?></div>
<?php endif; ?>