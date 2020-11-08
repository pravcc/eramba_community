<div class="text-center">
	<p>
		<?php echo __('Copyright') ?> 2011-<?= date('Y') ?> <a href="http://www.eramba.org/" target="_blank"><strong>eramba Ltd</strong></a>
	</p>
	<p>
		<strong>
			<?= $this->Html->link(__('User License'), [
				'controller' => 'pages',
				'action' => 'license'
			], [
				'target' => '_blank'
			]) ?>
		</strong>
	</p>
	<p>
		<?= __('App version') ?> <strong><?= Configure::read('Eramba.version') ?></strong>
	</p>
	<p>
		<?= __('Db schema version') ?> <strong><?= DB_SCHEMA_VERSION ?></strong>
	</p>
</div>