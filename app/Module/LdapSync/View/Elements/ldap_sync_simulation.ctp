<?php
App::uses('User', 'Model');
App::uses('LdapSyncModule', 'LdapSync.Lib');

foreach ($syncs as $sync) {
	if ($sync['syncResult'] == LdapSyncModule::SYNC_RESULT_FAILURE) {
		echo $this->Alerts->danger($sync['syncResultMsg']);
		break;
	}
}
?>
<?php if (empty($results)): ?>
	<?= $this->Alerts->info(__('No data for synchronization')) ?>
<?php else: ?>
	<div style="margin-top: -20px; margin-left: -20px; margin-right: -20px;">
		<table id="ldap-sync-simulation-dt" class="table datatable-basic no-footer" style="width: 100%">
			<thead>
				<tr>
					<th><?= __('Login') ?></th>
					<th><?= __('Action') ?></th>
					<th><?= __('Name') ?></th>
					<th><?= __('Surname') ?></th>
					<th><?= __('E-mail') ?></th>
					<th><?= __('Status') ?></th>
					<th><?= __('Portal') ?></th>
					<th><?= __('Group') ?></th>
					<th><?= __('Ldap Synchronizations') ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($results as $result): ?>
				<tr>
					<td><?= $result['login'] ?></td>
					<td>
						<?php
							// $actionsFriendly = [
							// 	'add' => [
							// 		'friendly_name' => __('Add'),
							// 		'labelClass' => 'label-success'
							// 	],
							// 	'update' => [
							// 		'friendly_name' => __('Update'),
							// 		'labelClass' => 'label-primary'
							// 	],
							// 	'skip' => [
							// 		'friendly_name' => __('Skip'),
							// 		'labelClass' => 'label-default'
							// 	],
							// 	'disable' => [
							// 		'friendly_name' => __('Disable'),
							// 		'labelClass' => 'label-info'
							// 	],
							// 	'delete' => [
							// 		'friendly_name' => __('Delete'),
							// 		'labelClass' => 'label-danger'
							// 	]
							// ];
							
							$actionsFriendly = [
								'local' => [
									'labelClass' => 'label-default'
								],
								'add-validation-error' => [
									'labelClass' => 'label-warning'
								],
								'add' => [
									'labelClass' => 'label-success'
								],
								'update' => [
									'labelClass' => 'label-info'
								],
								'skip-no-change' => [
									'labelClass' => 'label-default'
								],
								'remove-ignore' => [
									'labelClass' => 'label-default'
								],
								'remove-already-disabled' => [
									'labelClass' => 'label-default'
								],
								'remove-disable' => [
									'labelClass' => 'label-warning'
								],
								'remove-delete' => [
									'labelClass' => 'label-danger'
								],
							];
							echo '<span class="label ' . $actionsFriendly[$result['status']]['labelClass'] . '">' . $result['message'] . '</span>';
						?>
					</td>
					<?php
						$usrData = ($result['status'] == 'add' || $result['status'] == 'update') && isset($result['data']) ? $result['data'] : [];
						$usrAdtData = ($result['status'] == 'add' || $result['status'] == 'update') && isset($result['additional_data']) ? $result['additional_data'] : [];
					?>
					<td><?= isset($usrData['name']) ? $usrData['name'] : '-' ?></td>
					<td><?= isset($usrData['surname']) ? $usrData['surname'] : '-' ?></td>
					<td><?= isset($usrData['email']) ? $usrData['email'] : '-' ?></td>
					<td><?= isset($usrData['status']) ? User::statuses($usrData['status']) : '-' ?></td>
					<td><?= isset($usrAdtData['portals']) ? implode(", ", $usrAdtData['portals']) : '-' ?></td>
					<td><?= isset($usrAdtData['groups']) ? implode(", ", $usrAdtData['groups']) : '-' ?></td>
					<td><?= isset($usrAdtData['ldap_synchronizations']) ? implode(", ", $usrAdtData['ldap_synchronizations']) : '-' ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>
<script>
	$("#ldap-sync-simulation-dt").DataTable({
		'scrollX': true,
		'autoWidth': true,
		'responsive': true
	});
</script>