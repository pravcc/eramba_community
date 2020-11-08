<div class="panel panel-flat">
	<div class="panel-heading">
	</div>
	<div class="panel-body">
		<?php
		echo $this->Html->link('<i class="icon-info22"></i> ' . __('Synchronize'), [
			'plugin' => 'visualisation',
			'controller' => 'visualisationSettings',
			'action' => 'sync'
		], array(
			'class' => 'btn btn-primary',
			'data-popup' => 'popover',
			'data-trigger' => 'hover',
			'data-original-title' => __('IMPORTANT'),
			'data-content' => __('Unless requested by support is not necessary to sync visualisations and therefore you should not have the need to click here.'),
			'data-placement' => 'right',
			'escape' => false
		));

		?>
		
	</div>
	<div class="table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th>
						<?php echo $this->Paginator->sort('VisualisationSetting.model', __('Section')); ?>
					</th>
					<th>
						<?php
						echo __('Exempted Users');
						?>
					</th>
					<th>
						<?php
						echo __('Status');
						?>
					</th>
					<th>
						<?php
						echo __('Action');
						?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data as $entry) : ?>
					<?php
					$Model = $entry['VisualisationSetting']['model'];
					$mapClass = ClassRegistry::mapModelName($Model);
					list($plugin, $class) = pluginSplit($mapClass);

					$pluginPath = null;
					if ($plugin && !AppModule::loaded($plugin)) {
						continue;
					}

					App::uses($class, $pluginPath . 'Model');
					$Class = ClassRegistry::init($class);
					?>
					<tr>
						<td>
							<?php
							echo $Class->groupLabel();
							?>
						</td>
						<td>
							<?= $this->UserField->showUserFieldRecords($entry['ExemptedUser']); ?>
						</td>
						<td>
							<?php
							echo call_user_func_array("VisualisationSetting::statuses", [$entry['VisualisationSetting']['status']]);
							?>
						</td>
						<td>
							<?php
							echo $this->Html->link('<i class="icon-pencil"></i> ' . __('Edit'), '#', [
								'data-yjs-request' => 'crud/showForm',
								'data-yjs-target' => "modal",
							    'data-yjs-datasource-url' => Router::url([
							    	'plugin' => 'visualisation',
							    	'controller' => 'VisualisationSettings',
									'action' => 'edit',
									$entry['VisualisationSetting']['model']
								]),
							    'data-yjs-event-on' => "click",
								'escape' => false
							]);
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>