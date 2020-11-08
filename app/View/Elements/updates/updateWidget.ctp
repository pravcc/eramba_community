<?php
App::uses('CakeSession', 'Model/Datasource');
?>

<?php
if (!empty($errorMessage))
{
	echo $this->Html->div('alert alert-danger bg-danger label-custom-alert', nl2br($errorMessage));
}
?>

<?php if (!empty($successMessage)): ?>
	<?php
	echo $this->Html->div('alert alert-success bg-success label-custom-alert', $successMessage);

	echo $this->Html->div('alert alert-info bg-info label-custom-alert', __('You have been logged out of the application. Click <a href="%s" style="text-decoration: underline;">here</a> to login.', Router::url(['plugin' => null, 'controller' => 'users', 'action' => 'login'])));

	echo $this->Html->div('alert alert-warning bg-warning label-custom-alert hidden', __('Cleanup process that clears the cache was not successful, please clear the cache manually by running a terminal command from eramba_v2/app directory: Console/cake update deleteCache'),
		[
			'id' => 'update-cache-error'
		]
	);

	echo $this->Html->div('alert alert-warning bg-warning label-custom-alert hidden', 
		__('ACL synchronization was not successful, please go to Settings / Access List and synchronize ACL manually if the system requires it.'), [
		'id' => 'sync-acl'
	]);
	?>

	<?php
	//remove whole session
	$sessionKeys = array_keys(CakeSession::read());
	foreach ($sessionKeys as $sessionKey) {
		CakeSession::delete($sessionKey);
	}
	?>

	<script type="text/javascript">
		(function()
		{
			function blockUI(type)
			{
				if (type === 'block') {
					$.blockUI({ 
					    message: '<i class="icon-spinner4 spinner"></i>',
					    overlayCSS: {
					        backgroundColor: '#1b2024',
					        opacity: 0.8,
					        cursor: 'wait'
					    },
					    css: {
					        border: 0,
					        color: '#fff',
					        padding: 0,
					        backgroundColor: 'transparent'
					    }
					});
				} else if (type === 'unblock') {
					$.unblockUI();
				}
			}

			
			function showCacheError() {
				$('#update-cache-error').removeClass('hidden');
			}

			function showAclError() {
				$('#sync-acl').removeClass('hidden');
			}

			//
			// Synchronize ACL
			function syncAcl()
			{
				blockUI('block');
				
				$.ajax({
					url: "<?= Router::url(['plugin' => false, 'admin' => false, 'controller' => 'UpdateProcess', 'action' => 'syncAcl']) ?>"
				}).done(function(response) {
					if (!response) {
						showAclError();
					} else {
						var ret = JSON.parse(response);
						if (!ret['synchronized']) {
							showAclError();
						}
					}
				}).fail(function() {
					showAclError();
				}).always(function() {
					blockUI('unblock');
				});
			}
			//

			//
			// Delete cache then call syncAcl function
			function finishUpdate()
			{
				blockUI('block');

				$.ajax({
					url: "<?= Router::url('/delete_cache.php') ?>",
				}).done(function(response) {
					if (!response) {
						showCacheError();
					}
				}).fail(function() {
					showCacheError();
				}).always(function() {
					blockUI('unblock');
					syncAcl();
				});
			}
			//
			
			finishUpdate();
		})();
	</script>
<?php endif; ?>

<div class="well well-sm">
	<strong><?php echo __('System version'); ?>:</strong> <?php echo Configure::read('Eramba.version'); ?>
</div>

<?php if ($update['response']['updates'] && !empty($update['response']['pending'])) : ?>
	<div class="well well-sm mt-10">
		<strong><?php echo __('Latest available version'); ?>:</strong> <?php echo $update['response']['latest_version']; ?>
	</div>
	<div class="panel panel-flat mt-10">
		<div class="panel-heading">
			<h4 class="panel-title"><i class="icon-download"></i> <?php echo __('Available updates'); ?></h4> 
		</div>
		<div class="table-responsive">
			<table class="table table-striped">
				<thead> 
					<tr>
						<th width="100"><?php echo __('Version'); ?></th>
						<th width="140"><?php echo __('Release date'); ?></th>
						<th><?php echo __('Changelog'); ?></th>
						<th width="100"></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($update['response']['pending'] as $key => $item) : ?>
						<?php if ($item == reset($update['response']['pending'])) : ?>
							<tr class="update-loading">
								<td colspan="4" style="padding: 0; border: none;">
									<div class="progress progress-striped active" style="display: none;"><div class="progress-bar progress-bar-success" style="width: 100%"></div></div>
								</td>
							</tr>
							<tr class="hidden">
								<td colspan="4">
								</td>
							</tr>
						<?php endif; ?>
						<tr>
							<td><?php echo $item['version']; ?></td>
							<td><?php echo date('d. m. Y', strtotime($item['date'])); ?></td>
							<td><?php echo nl2br($item['changelog']); ?></td>
							<td class="text-center">
								<?php if ($key == 0 && empty($successMessage)) : // only for default state, not after update process because the user is anyway logged out automatically ?>
									<a href="<?php echo Router::url(array('plugin' => null, 'controller' => 'UpdateProcess', 'action' => 'update', time())); ?>" class="btn-update btn btn-sm btn-success"><?php echo __('Update'); ?></a>
								<?php else : ?>
									<a href="#" disabled="disabled" class="btn btn-sm btn-success"><?php echo __('Update'); ?></a>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div> 
	</div>
<?php elseif (empty($errorMessage)) : ?>
	<div class="mt-10">
		<?= $this->Html->div('alert alert-success bg-success label-custom-alert', __('System is up to date. No updates available.')); ?>
	</div>
<?php endif; ?>