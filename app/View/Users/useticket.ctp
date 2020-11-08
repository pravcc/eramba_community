<?php echo $this->Form->create( 'User', array(
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

		<h3 class="form-title"><?php echo __( 'Change your password' ); ?></h3>
		<p><?php echo __('Please enter your new password below'); ?></p>
		<br />

		<!-- Input Fields -->
		<?= $this->Form->input( 'pass', array(
			'type' => 'password',
			'label' => false, 
			'div' => [
				'class' => 'form-group has-feedback has-feedback-left',
				'errorClass' => 'has-error'
			],
			'after' => $this->Html->tag('div', '<i class="icon-lock text-muted"></i>', [
				'class' => 'form-control-feedback'
			]),
			'placeholder' => __( 'New password' ),
			'class' => 'form-control',
			'autofocus' => 'autofocus',
			'data-rule-required' => 'true',
			'data-msg-required' => __( 'Please enter your new password.' )
		) ); ?>

		<?= $this->Form->input( 'pass2', array(
			'type' => 'password',
			'label' => false, 
			'div' => [
				'class' => 'form-group has-feedback has-feedback-left',
				'errorClass' => 'has-error'
			],
			'after' => $this->Html->tag('div', '<i class="icon-lock text-muted"></i>', [
				'class' => 'form-control-feedback'
			]),
			'placeholder' => __( 'Verify password' ),
			'class' => 'form-control',
			'data-rule-required' => 'true',
			'data-msg-required' => __( 'Please enter your new password again.' )
		) ); ?>

		<div class="text-left">
			<?= $this->Users->passwordPolicyAlert(); ?>
		</div>
		<!-- /Input Fields -->

		<?php echo $this->Form->input('hash', array('type' => 'hidden')); ?>

		<!-- Form Actions -->
		<div class="form-actions">
			<?php echo $this->Form->submit( __( 'Change password' ), array(
				'class' => 'submit btn btn-primary pull-right',
				'div' => false
			) ); ?>
		</div>

	</div>
</div>

<?php echo $this->Form->end(); ?>