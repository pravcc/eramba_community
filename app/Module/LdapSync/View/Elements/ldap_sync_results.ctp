<?php
	App::uses('LdapSyncModule', 'LdapSync.Lib');
?>
<?php if ($syncResult == LdapSyncModule::SYNC_RESULT_SUCCESS): ?>
	<?= $this->Alerts->success($syncResultMsg) ?>
<?php elseif ($syncResult == LdapSyncModule::SYNC_RESULT_FAILURE): ?>
	<?= $this->Alerts->danger($syncResultMsg); ?>
<?php endif; ?>
<?php if (empty($results)): ?>
	<?= $this->Alerts->info(__('No data for synchronization')) ?>
<?php else: ?>
	<div style="margin-top: -20px; margin-left: -20px; margin-right: -20px;">
		<table id="ldap-sync-results-dt" class="table datatable-basic no-footer" style="width: 100%">
			<thead>
				<tr>
					<th><?= __('Login') ?></th>
					<th><?= __('Action') ?></th>
					<th><?= __('Result') ?></th>
					<th><?= __('Errors') ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($results as $result): ?>
				<tr>
					<td><?= $result['login'] ?></td>
					<td>
						<?php
							$actionsFriendly = [
								'add' => [
									'friendly_name' => __('Add'),
									'labelClass' => 'label-success'
								],
								'update' => [
									'friendly_name' => __('Update'),
									'labelClass' => 'label-primary'
								],
								'skip' => [
									'friendly_name' => __('Skip'),
									'labelClass' => 'label-default'
								],
								'disable' => [
									'friendly_name' => __('Disable'),
									'labelClass' => 'label-info'
								],
								'delete' => [
									'friendly_name' => __('Delete'),
									'labelClass' => 'label-danger'
								]
							];
							echo '<span class="label ' . $actionsFriendly[$result['action']]['labelClass'] . '">' . $actionsFriendly[$result['action']]['friendly_name'] . ' (' . $result['actionMsg'] . ')' . '</span>';
						?>
					</td>
					<td>
						<?php if (isset($result['actionResult'])): ?>
							<?php
								$resLabelName = '';
								$resLabelClass = '';
								if ($result['actionResult'] == 1) {
									$resLabelName = __('Success');
									$resLabelClass = 'label-success';
								} else {
									$resLabelName = __('Failure');
									$resLabelClass = 'label-danger';
								}
							?>	
							<span class="label <?= $resLabelClass ?>"><?=$resLabelName ?></span>
						<?php else: ?>
							<?= '-' ?>
						<?php endif; ?>
					</td>
					<td>
						<?php if ($result['action'] == 'add' || $result['action'] == 'update'): ?>
							<?php if (empty($result['validationErrors'])): ?>
								<?= __('No errors') ?>
							<?php else: ?>
								<?php foreach($result['validationErrors'] as $error): ?>
									<?= $error[0] ?><br>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php else: ?>
							<?= '-' ?>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>
<script>
	$("#ldap-sync-results-dt").DataTable({
		'scrollX': true,
		'autoWidth': true,
		'responsive': true
	});
</script>