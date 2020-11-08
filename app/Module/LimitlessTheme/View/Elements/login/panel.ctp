<?= $this->Form->create(isset($model) ? $model : 'User', [
	'class' => 'login-form-custom'
]) ?>
	<div class="panel panel-body login-form">
		<?php
		// header
		if (!isset($loginHeaderPath)) {
			$loginHeaderPath = 'LimitlessTheme.login/header';
		}
		echo $this->element($loginHeaderPath);

		// form
		if (!isset($loginFormPath)) {
			$loginFormPath = 'LimitlessTheme.login/form';
		}
		echo $this->element($loginFormPath);

		// footer
		if (!isset($loginFooterPath)) {
			$loginFooterPath = 'LimitlessTheme.login/footer';
		}
		echo $this->element($loginFooterPath);
		?>
	</div>
<?= $this->Form->end() ?>