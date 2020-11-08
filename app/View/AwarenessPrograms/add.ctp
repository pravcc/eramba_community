<div
	id="wizarde"
	class="wizarde"
	data-yjs-request="awarenessWizard/init/reset::<?= ($this->request->is('get')) ? 1 : 0 ?>"
	data-yjs-event-on="init"
	data-yjs-use-loader="false"
>
	<?php
	$query = [
		'modalId' => $modal->getModalId(),
		'formName' => $formName,
	];

	if (isset($edit)) {
		echo $this->Form->create('AwarenessProgram', [
			'url' => ['controller' => 'awarenessPrograms', 'action' => 'edit'],
			'type' => 'file',
			'novalidate' => true,
			'id' => 'wizard-form',
			'data-yjs-form' => $formName,
			'data-wizarde-validation-url' => Router::url(['controller' => 'awarenessPrograms', 'action' => 'validateStep', '__STEP__', '?' => $query])
		]);

		echo $this->Form->input('id', ['type' => 'hidden']);
	}
	else {
		echo $this->Form->create('AwarenessProgram', [
			'url' => ['controller' => 'awarenessPrograms', 'action' => 'add'],
			'type' => 'file',
			'novalidate' => true,
			'id' => 'wizard-form',
			'data-yjs-form' => $formName,
			'data-wizarde-validation-url' => Router::url(['controller' => 'awarenessPrograms', 'action' => 'validateStep', '__STEP__', '?' => $query])
		]);
	}
	?>
		<div class="wizarde-header clearfix">
			<ul>
				<li class="wizarde-header-tab current" data-wizarde-index="1">
					<a>
						<span class="number">1</span>
						<?= __('General') ?>
					</a>
				</li>
				<li class="wizarde-header-tab">
					<a>
						<span class="number">2</span>
						<?= __('LDAP') ?>
					</a>
				</li>
				<li class="wizarde-header-tab">
					<a>
						<span class="number">3</span>
						<?= __('Uploads') ?>
					</a>
				</li>
				<li class="wizarde-header-tab">
					<a>
						<span class="number">4</span>
						<?= __('Texts') ?>
					</a>
				</li>
				<li class="wizarde-header-tab">
					<a>
						<span class="number">5</span>
						<?= __('Email') ?>
					</a>
				</li>
			</ul>
		</div>
		<div class="wizarde-content">
			<div class="wizarde-content-tab">
				<?= $this->element('AwarenessPrograms/step_1') ?>
			</div>
			<div class="wizarde-content-tab">
				<?= $this->element('AwarenessPrograms/step_2') ?>
			</div>
			<div class="wizarde-content-tab">
				<?= $this->element('AwarenessPrograms/step_3') ?>
			</div>
			<div class="wizarde-content-tab">
				<?= $this->element('AwarenessPrograms/step_4') ?>
			</div>
			<div class="wizarde-content-tab">
				<?= $this->element('AwarenessPrograms/step_5') ?>
			</div>
		</div>
	<?= $this->Form->end() ?>
</div>
