<div class="pl-10 pr-10">
	<?php
		echo $this->Form->create($rdFormName, [
			'url' => ['controller' => 'settings', 'action' => 'resetDatabase'],
			'class' => '',
			'id' => 'reset-database-form',
			'data-yjs-form' => $rdFormName
		]);
	?>

	<div class="form-group">
		<label class="control-label"><?= __('Reset database'); ?>:
			<?php echo $this->Form->input('reset_db', array(
				'type' => 'checkbox',
				'label' => false,
				'div' => false,
				'class' => 'uniform',
			) ); ?>
			<span class="help-block"><?= __('Check if you are really sure to reset database.'); ?></span>
		</label>
	</div>

	<?php echo $this->Form->end(); ?>
</div>