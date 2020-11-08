<div class="form-group has-feedback has-feedback-left">
	<?= $this->Form->input('login', [
		'label' => false, 
		'div' => false,
		'placeholder' => __('Username'),
		'class' => 'form-control',
		'autofocus' => 'autofocus',
		'data-rule-required' => 'true',
		'data-msg-required' => __('Please enter your username.')
	]) ?>
	<div class="form-control-feedback">
		<i class="icon-user text-muted"></i>
	</div>
</div>

<div class="form-group has-feedback has-feedback-left">
	<?= $this->Form->input('password', [
		'label' => false, 
		'div' => false,
		'placeholder' => __('Password'),
		'class' => 'form-control',
		'data-rule-required' => 'true',
		'data-msg-required' => __('Please enter your password.')
	]) ?>
	<div class="form-control-feedback">
		<i class="icon-lock2 text-muted"></i>
	</div>
</div>

<?= $this->Translations->loginLanguageSelect() ?>

<div class="form-group">
	<?= $this->Form->input(__( 'Login' ) . $this->Icons->render('arrow-right14', ['class' => 'position-right']), [
		'type' => 'button',
		'class'=> 'btn bg-blue btn-block',
		'div' => false,
		'label' => false
	]) ?>
</div>

<?php if (!empty($showForgotPasswordBtn)) : ?>
	<div class="form-group login-options">
		<div class="row">
			<div class="col-sm-12 text-center">
				<?php
					$resetUrl = [
						'plugin' => false,
						'controller' => 'users',
						'action' => 'resetpassword',
					];

					if ($this->request->params['controller'] != 'users') {
						$resetUrl['?'] = [
							'redirect' => Router::url(null, true)
						];
					}

					echo $this->Html->link(__('Forgot your Password?'), $resetUrl, [
						'class' => 'forgot-password-link'
					]);
				?>
			</div>
		</div>
	</div>
<?php endif; ?>

<?php if (!empty($oauthGoogleAllowed) || !empty($samlAuthAllowed)): ?>
	<div class="content-divider text-muted form-group"><span><?= __('or sign in with') ?></span></div>
	<?php if (!empty($oauthGoogleAllowed)): ?>
		<div class="form-group">
			<a href="<?= $oauthGoogleAuthUrl ?>" class="btn border-danger text-danger btn-flat btn-block"><i class="icon-google"></i>OAuth Google</a>
		</div>
	<?php elseif (!empty($samlAuthAllowed)): ?>
		<div class="form-group">
			<a href="<?= $samlAuthUrl ?>" class="btn border-primary text-primary btn-flat btn-block"><i class=" icon-user-lock"></i>&nbsp;SAML authentication</a>
		</div>
	<?php endif; ?>
<?php endif; ?>
