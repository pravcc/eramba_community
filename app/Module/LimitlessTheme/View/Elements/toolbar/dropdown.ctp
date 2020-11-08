<!-- <li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">
		<i class="icon-gear position-left"></i>
		Settings
		<span class="caret"></span>
	</a>

	<ul class="dropdown-menu dropdown-menu-right">
		<li><a href="#"><i class="icon-user-lock"></i> Account security</a></li>
		<li><a href="#"><i class="icon-statistics"></i> Analytics</a></li>
		<li><a href="#"><i class="icon-accessibility"></i> Accessibility</a></li>
		<li class="divider"></li>
		<li><a href="#"><i class="icon-gear"></i> All settings</a></li>
	</ul>
</li> -->

<?php // temporarily filters dropdown is included here but will be moved to the relevant file when LayoutHelper gives support for dropdowns which it doesnt have now ?>

<?php
App::uses('CustomField', 'CustomFields.Model');
?>

<?php
$customFieldSettingsAllowed = $this->AclCheck->check(Router::url(['plugin' => 'custom_fields', 'controller' => 'customFieldSettings', 'action' => 'edit']));
?>
<?php if (isset($CustomFields) && $customFieldSettingsAllowed): ?>
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
			<?= __('Customization') ?>
			<?php if ($CustomFields->getCount()) : ?>
				<span class="badge badge-counter position-right"><?php echo $CustomFields->getCount(); ?></span>
			<?php endif; ?>
		</a>
		<ul class="dropdown-menu dropdown-menu-right">
			<li>
				<?= $this->Form->create('CustomFieldSetting', array(
					'url' => array('controller' => 'customFieldSettings', 'action' => 'edit', $CustomFields->getModel()->alias),
					'id' => 'custom-fields-settings-form-' . $CustomFields->getModel()->alias,
					'class' => 'checkbox checkbox-switchery switchery-xs checkbox-right',
					'data-yjs-form' => 'custom-fields-settings-form-' . $CustomFields->getModel()->alias,
					'novalidate' => true
				));
				?>
				<label>
					<?php
						$statusInputOptions = [
							'type' => 'checkbox',
							'label' => false,
							'div' => false,
							'class' => 'switchery',
							'checked' => $CustomFields->isEnabled() ? true : false,
							'data-yjs-request' => 'app/submitForm',
							'data-yjs-event-on' => 'change',
							'data-yjs-datasource-url' => Router::url([
								'plugin' => 'custom_fields',
								'controller' => 'customFieldSettings',
								'action' => 'edit',
								$CustomFields->getModel()->alias
							]),
							'data-yjs-forms' => 'custom-fields-settings-form-' . $CustomFields->getModel()->alias,
							'data-yjs-on-success-reload' => '#main-toolbar',
							'data-yjs-on-failure-reload' => '#main-toolbar'
						];
					?>
					<?= $this->Form->input('status', $statusInputOptions);
					?>
					<?= __('Enabled'); ?>
				</label>
				<style>
					.dropdown-menu > li > form.checkbox-switchery > label {
						padding-left: 15px !important;
						display: block;
					}
					.dropdown-menu > li > form.checkbox-switchery > label .switchery {
						left: auto;
						right: 15px;
					}
				</style>
				<?= $this->Form->end(); ?>
			</li>
			<li class="divider"></li>
			<?php $customFormsData = $CustomFields->getCustomFormsData(); ?>
			<?php if (!empty($customFormsData)): ?>
				<?php foreach ($customFormsData as $item) : ?>
				<li class="dropdown-submenu dropdown-submenu-left">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" title="<?= h($item['CustomForm']['name']); ?>">
						<i class="icon-file-empty2 position-left"></i>
						<?= h($item['CustomForm']['name']); ?>
					</a>
					<ul class="dropdown-menu pull-right">
						<?php $customFieldsData = !empty($item['CustomField']) ? $item['CustomField'] : []; ?>
						<?php if (!empty($customFieldsData)): ?>
							<?php foreach ($customFieldsData as $field) : ?>
								<li class="dropdown-submenu dropdown-submenu-left">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" title="<?= h($field['name']) . '&nbsp;(' . CustomField::getCustomFieldTypes($field['type']) . ')'; ?>">
										<i class="icon-enter6 position-left"></i>
										<?= h($field['name']) . '&nbsp;(' . CustomField::getCustomFieldTypes($field['type']) . ')'; ?>
									</a>
									<ul class="dropdown-menu pull-right">
										<li>
											<?=
											$this->Html->link('<i class="icon-pencil position-left"></i> ' . __('Edit Field'), '#', [
												'escape' => false,
												'data-yjs-request' => 'crud/showForm',
												'data-yjs-target' => 'modal',
												'data-yjs-event-on' => 'click',
												'data-yjs-datasource-url' =>  Router::url([
													'plugin' => 'custom_fields',
													'controller' => 'customFields',
													'action' => 'edit',
													$field['id']
												]),
											])
											?>
										</li>
										<li>
											<?=
											$this->Html->link('<i class="icon-trash position-left"></i> ' . __('Delete Field'), '#', [
												'escape' => false,
												'data-yjs-request' => 'crud/showForm',
												'data-yjs-target' => 'modal',
												'data-yjs-event-on' => 'click',
												'data-yjs-datasource-url' =>  Router::url([
													'plugin' => 'custom_fields',
													'controller' => 'customFields',
													'action' => 'delete',
													$field['id']
												]),
											])
											?>
										</li>
									</ul>
								</li>
							<?php endforeach; ?>
							<li class="divider"></li>
						<?php endif; ?>
						<li>
							<?=
							$this->Html->link('<i class="icon-plus2 position-left"></i> ' . __('New Field'), '#', [
								'escape' => false,
								'data-yjs-request' => 'crud/showForm',
								'data-yjs-target' => 'modal',
								'data-yjs-event-on' => 'click',
								'data-yjs-datasource-url' =>  Router::url([
									'plugin' => 'custom_fields',
									'controller' => 'customFields',
									'action' => 'add',
									$item['CustomForm']['id']
								]),
							])
							?>
						</li>
						<li class="divider"></li>
						<li>
							<?=
							$this->Html->link('<i class="icon-pencil position-left"></i> ' . __('Edit Tab'), '#', [
								'escape' => false,
								'data-yjs-request' => 'crud/showForm',
								'data-yjs-target' => 'modal',
								'data-yjs-event-on' => 'click',
								'data-yjs-datasource-url' =>  Router::url([
									'plugin' => 'custom_fields',
									'controller' => 'customForms',
									'action' => 'edit',
									$item['CustomForm']['id']
								]),
							])
							?>
						</li>
						<li>
							<?=
							$this->Html->link('<i class="icon-trash position-left"></i> ' . __('Delete Tab'), '#', [
								'escape' => false,
								'data-yjs-request' => 'crud/showForm',
								'data-yjs-target' => 'modal',
								'data-yjs-event-on' => 'click',
								'data-yjs-datasource-url' =>  Router::url([
									'plugin' => 'custom_fields',
									'controller' => 'customForms',
									'action' => 'delete',
									$item['CustomForm']['id']
								]),
							])
							?>
						</li>
					</ul>
				</li>
				<?php endforeach; ?>
				<li class="divider"></li>
			<?php endif; ?>
			<li>
				<?=
				$this->Html->link('<i class="icon-plus2 position-left"></i> ' . __('New Tab'), '#', [
					'escape' => false,
					'data-yjs-request' => 'crud/showForm',
					'data-yjs-target' => 'modal',
					'data-yjs-event-on' => 'click',
					'data-yjs-datasource-url' =>  Router::url([
						'plugin' => 'custom_fields',
						'controller' => 'customForms',
						'action' => 'add',
						$CustomFields->getModel()->alias
					]),
				])
				?>
			</li>
		</ul>
	</li>
<?php endif; ?>
