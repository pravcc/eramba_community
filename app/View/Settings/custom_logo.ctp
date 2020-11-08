<div class="pl-10 pr-10">
	<?php
		echo $this->Form->create($clFormName, array(
			'url' => ['controller' => 'settings', 'action' => 'customLogo'],
			'class' => '',
			'type' => 'file',
			'data-yjs-form' => $clFormName
		));
	?>
	
	<?php if (!empty(Configure::read('Eramba.Settings.CUSTOM_LOGO'))): ?>
		<div id="active-logo-form-group" class="form-group" data-yjs-request="eramba/toggleElement/type:hide" data-yjs-use-loader="false">
			<label class="control-label"><?= __('Active Logo'); ?>:</label>
			<div id="active-logo"><?= $this->Eramba->getLogo(); ?></div>
		</div>
	<?php endif; ?>

	<div class="form-group">
		<label class="control-label"><?= __('Logo Upload'); ?>:</label>
		<?php echo $this->Form->input('logo_file', array(
			'type' => 'file',
			'label' => false,
			'div' => false,
			'class' => 'form-control file-styled',
			'data-style' => 'fileinput',
			'required' => false
		) ); ?>
		<span class="help-block"><?= __('Upload your logo here.'); ?></span>
	</div>

	<?= $this->Form->end(); ?>
</div>