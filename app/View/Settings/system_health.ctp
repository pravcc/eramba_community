<?php
App::uses('SystemHealthLib', 'Lib');

echo $this->element(CORE_ELEMENT_PATH . 'auto_update_pending');
?>

<?php if (!empty($data)) : ?>
	<?php foreach ($data as $group) : ?>
		<div class="panel panel-white">
			<div class="panel-heading">
				<h4 class="panel-title"><?php echo $group['groupName']; ?></h4>
			</div>
			<div class="table-responsive">
				<table class="table table-hover table-striped table-highlight-head">
					<thead>
						<tr>
							<th>
								<?php echo __('Check'); ?>
							</th>
							<th>
								<?php echo __('Description'); ?>
							</th>
							<th>
								<?php echo __('Current Value'); ?>
							</th>
							<th>
								<?php echo __('Status'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($group['checks'] as $check) : ?>
							<tr>
								<td>
									<?php
									echo $this->SystemHealth->title($check);
									?>
								</td>
								<td><?php echo $check['description']; ?></td>
								<td>
									<?php
									echo $check['value'];
									?>
								</td>
								<td>
									<?php
									echo $this->SystemHealth->status($check['status']);
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	<?php endforeach; ?>
<?php else : ?>
	<?php echo $this->element( 'not_found', array(
		'message' => __( 'No System Health checks found.' )
	) ); ?>
<?php endif; ?>