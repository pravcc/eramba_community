<?= $this->element('LimitlessTheme.login/form') ?>

<?php if ($hasPublicDocs) : ?>
	<hr>
	<div class="form-group">
		<?= $this->Html->link(__('Login as Guest'),
			['controller' => 'policy', 'action' => 'guestLogin'],
			['class' => 'btn bg-blue btn-block']
		) ?>
	</div>
<?php endif; ?>