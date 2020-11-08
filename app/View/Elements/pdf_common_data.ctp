<?php
echo $this->element('CustomFields.' . CUSTOM_FIELDS_DISPLAY_ELEMENT_PATH . 'accordion', array(
	'item' => $item, // single database item in a variable
	'layout' => 'pdf'
));
?>

<?php if (!(empty($item['Attachment']) && empty($item['Comment']) && empty($item['SystemRecord']))) : ?>
	<pagebreak></pagebreak>
<?php endif; ?>

<?php if (!empty($item['Attachment'])): ?>

	<div class="row">
		<div class="col-xs-12">

			<div class="header">
				<div class="subtitle">
					<h2>
						<?php echo __('Attachments'); ?>
					</h2>
				</div>
			</div>

		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">

			<div class="body">
				<?php foreach ( $item['Attachment'] as $attachment ) : ?>

					<div class="item">
						<table class="double-column">
							<tr>
								<th>
									<?php echo __('Filename'); ?>
								</th>
								<th>
									<?php echo __('Created'); ?>
								</th>
							</tr>
							<tr>
								<td>
									<?php echo $attachment['name']; ?>
								</td>
								<td>
									<?php echo $attachment['created']; ?>
								</td>
							</tr>
						</table>
					</div>
			
				<?php endforeach; ?>
			</div>

		</div>
	</div>

	<div class="separator"></div>
<?php endif; ?>


<?php if (!empty($item['Comment'])): ?>
	
	<div class="row">
		<div class="col-xs-12">

			<div class="header">
				<div class="subtitle">
					<h2>
						<?php echo __('Comments'); ?>
					</h2>
				</div>
			</div>

		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="body">

				<?php $count = 0; ?>
				<?php foreach ( $item['Comment'] as $comment ) : ?>

					<?php $count++; ?>
					<div class="item">
						<table class="triple-column">
							<tr>
								<th>
									<?php echo __('Who'); ?>
								</th>
								<th>
									<?php echo __('Created'); ?>
								</th>
								<th>
									<?php echo __('Modified'); ?>
								</th>
							</tr>
							
							<tr>
								<td>
									<?php echo $comment['User']['name'] . ' ' . $comment['User']['surname']; ?>
								</td>
								<td>
									<?php echo $comment['created']; ?>
								</td>
								<td>
									<?php echo $comment['modified']; ?>
								</td>
							</tr>
						</table>
					</div>

					<div class="item">
						<table>
							<tr>
								<th>
									<?php echo __('Message'); ?>
								</th>
							</tr>
							
							<tr>
								<td>
									<?php echo $comment['message']; ?>
								</td>
							</tr>
						</table>
					</div>

					<?php if ( $count < count($item['Comment'])): ?>
						<div class="separator"></div>
					<?php endif; ?>

				<?php endforeach; ?>

			</div>
		</div>
	</div>

	<div class="separator"></div>
<?php endif; ?>


<?php if (!empty($item['SystemRecord'])): ?>
	<div class="row">
		<div class="col-xs-12">

			<div class="header">
				<div class="subtitle">
					<h2>
						<?php echo __('System records'); ?>
					</h2>
				</div>
			</div>

		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="body">

				<?php if (!empty($item['SystemRecord'])) : ?>
					<?php
					$workflowStatuses = getWorkflowStatuses();
					?>
					<div class="item">
						<table class="table-pdf table-pdf-system-records" style="">
							<thead>
								<tr>
									<th><?php echo __('Date'); ?></th>
									<th><?php echo __('Type'); ?></th>
									<!-- <th><?php echo __('Item'); ?></th> -->
									<th><?php echo __('Notes'); ?></th>
									<?php /*<th><?php echo __('Workflow Status'); ?></th>*/ ?>
									<?php /*<th><?php echo __('Workflow Comment'); ?></th>*/ ?>
									<th><?php echo __('IP'); ?></th>
									<th><?php echo __('User'); ?></th>
								</tr>
							</thead>
							
							<tbody>
								<?php foreach ($item['SystemRecord'] as $record) : ?>
									<tr>
										<td><?php echo $record['created']; ?></td>
										<td><?php echo getSystemRecordTypes($record['type']); ?></td>
										<!-- <td><?php echo $record['item']; ?></td> -->
										<td><?php echo $record['notes']; ?></td>
										<?php /*
										<td>
											<?php
											if ($record['workflow_status'] !== null) {
												echo $workflowStatuses[$record['workflow_status']];
											}
											?>
										</td>
										*/ ?>
										<?php /*<td><?php echo $record['workflow_comment'] ? $record['workflow_comment'] : '-'; ?></td>*/ ?>
										<td><?php echo $record['ip']; ?></td>
										<td><?php echo $record['User']['name'] . ' ' . $record['User']['surname']; ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>

					<?php /*if ( $count < count($item['SystemRecord'])): ?>
						<div class="separator"></div>
					<?php endif;*/ ?>

				<?php endif; ?>

			</div>
		</div>
	</div>
<?php endif; ?>