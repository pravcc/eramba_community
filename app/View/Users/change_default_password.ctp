<?php echo $this->Form->create( 'User', array(
	'url' => array(
		'controller' => 'users',
		'action' => 'changeDefaultPassword',
		$changePassUserId,
		'?' => [
			'redirect' => $changePassRedirect
		]
	),
	'class' => 'login-form-custom',
	'inputDefaults' => [
		'error' => [
			'attributes' => [
				'class' => 'text-left validation-error-label'
			]
		]
	],
	'novalidate' => true
) ); ?>

<div class="panel panel-body login-form">
	<div class="text-center mb-20">

		<?php
			$heading = "";
			$subheading = "";
			if ($changePassUserId == ADMIN_ID) {
				$heading = __('Change your credentials');
				$subheading = __('Please enter your new email and password below');
			} else {
				$heading = __('Change your password');
				$subheading = __('Please enter your new password below');
			}
		?>

		<h3 class="form-title"><?= $heading ?></h3>
		<p><?= $subheading ?></p>
		<br />

		<?php
			if ($changePassUserId == ADMIN_ID) {
				echo $this->Form->input('email', [
					'type' => 'text',
					'label' => false,
					'div' => [
						'class' => 'form-group has-feedback has-feedback-left',
						'errorClass' => 'has-error'
					],
					'after' => $this->Html->tag('div', '<i class="icon-user text-muted"></i>', [
						'class' => 'form-control-feedback'
					]),
					'placeholder' => __('E-mail'),
					'class' => 'form-control',
					'autofocus' => 'autofocus',
					'data-rule-required' => 'true',
					'data-msg-required' => __('Please enter your e-mail.')
				]);
			}
		?>

		<!-- Input Fields -->
		<?= $this->Form->input( 'old_pass', [
			'type' => 'password',
			'label' => false, 
			'div' => [
				'class' => 'form-group has-feedback has-feedback-left',
				'errorClass' => 'has-error'
			],
			'after' => $this->Html->tag('div', '<i class="icon-lock text-muted"></i>', [
				'class' => 'form-control-feedback'
			]),
			'placeholder' => __( 'Current password' ),
			'class' => 'form-control',
			'autofocus' => 'autofocus',
			'data-rule-required' => 'true',
			'data-msg-required' => __( 'Please enter your current password.' )
		]); ?>

		<?= $this->Form->input( 'pass', [
			'type' => 'password',
			'label' => false, 
			'div' => [
				'class' => 'form-group has-feedback has-feedback-left',
				'errorClass' => 'has-error'
			],
			'after' => $this->Html->tag('div', '<i class="icon-lock text-muted"></i>', [
				'class' => 'form-control-feedback'
			]),
			'placeholder' => __('New password'),
			'class' => 'form-control',
			'autofocus' => 'autofocus',
			'data-rule-required' => 'true',
			'data-msg-required' => __('Please enter your new password.')
		]); ?>

		<?= $this->Form->input( 'pass2', [
			'type' => 'password',
			'label' => false, 
			'div' => [
				'class' => 'form-group has-feedback has-feedback-left',
				'errorClass' => 'has-error'
			],
			'after' => $this->Html->tag('div', '<i class="icon-lock text-muted"></i>', [
				'class' => 'form-control-feedback'
			]),
			'placeholder' => __('Verify password'),
			'class' => 'form-control',
			'data-rule-required' => 'true',
			'data-msg-required' => __('Please enter your new password again.')
		]); ?>

		<div class="text-left">
			<?= $this->Users->passwordPolicyAlert(); ?>
		</div>
		<!-- /Input Fields -->

		<!-- Form Actions -->
		<div class="form-actions">
			<?php echo $this->Form->submit( __('Change password'), array(
				'class' => 'submit btn btn-primary pull-right',
				'div' => false
			) ); ?>
		</div>

	</div>
</div>

<?php echo $this->Form->end(); ?>