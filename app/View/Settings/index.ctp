<?php 
App::uses('Setting', 'Model');

if (empty($settings)) {
	echo $this->Alerts->info('Settings are not available for you.');
	return;
}

$settingRows = array_chunk($settings, 3, true);
?>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-flat">
			<div class="panel-body">
				
				<?php foreach ($settingRows as $settingRow): ?>

					<div class="row">
						
						<?php foreach ($settingRow as $settingsGroup): ?>

							<div class="col-md-4">
								<div class="content-group">
									<h6 class="text-semibold heading-divided">
										<?= $this->Icons->render('folder6', ['class' => 'icon-folder6 position-left']) ?>
										<?= $settingsGroup['name'] ?>
									</h6>
									<div class="list-group no-border">

										<?php foreach ($settingsGroup['children'] as $settingSubGroupKey => $settingSubGroup): ?>

											<?php
											$icon = $this->Icons->render('file-text2');

											$options = [
												'class' => 'list-group-item',
												'escape' => false
											];

											// url
											if (!empty($settingSubGroup['url'])) {
												$url = Router::url($settingSubGroup['url']);
											}
											else {
												$url = Router::url(['controller' => 'settings', 'action' => 'edit', $settingSubGroupKey]);
											}

											if (!empty($settingSubGroup['modal'])) {
												$options = array_merge([
													'data-yjs-request' => 'crud/showForm',
													'data-yjs-target' => 'modal',
												    'data-yjs-event-on' => "click",
												    'data-yjs-modal-size-width' => ($settingSubGroupKey == 'HEALTH') ? '80' : '60',
												    'data-yjs-datasource-url' => $url
												], $options);
											}

											echo $this->Html->link(
												$icon . ' ' . $settingSubGroup['name'],
												empty($settingSubGroup['modal']) ? $url : '#',
												$options
											);
											?>
											
										<?php endforeach; ?>

									</div>
								</div>
							</div>

						<?php endforeach; ?>

					</div>

				<?php endforeach; ?>

			</div>
		</div>
	</div>
</div>
