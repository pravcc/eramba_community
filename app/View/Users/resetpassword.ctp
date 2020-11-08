<?php echo $this->Form->create( 'User', array(
	'class' => 'login-form-custom'
) ); ?>

<div class="panel panel-body login-form">
	<div class="text-center mb-20">

		<h3 class="form-title"><?php echo __( 'Did you forgot your password?' ); ?></h3>
		<p><?php echo __('Enter your email. We will send you email containing a link with simple tutorial how to change your password.'); ?></p>
		<br />

		<!-- Input Fields -->
		<div class="form-group has-feedback has-feedback-left">
			<?php echo $this->Form->input( 'email', array(
				'label' => false, 
				'div' => false,
				'placeholder' => __( 'Enter email address' ),
				'class' => 'form-control',
				'required' => true,
				'data-rule-required' => 'true',
				'data-rule-email' => 'true',
				'data-msg-required' => __( 'Please enter your email.' )
			) ); ?>
			<div class="form-control-feedback">
				<i class="icon-envelope text-muted"></i>
			</div>
		</div>

		<div class="form-actions">
			<?php 
			$backUrl = Router::url(['plugin' => false, 'controller' => 'users', 'action' => 'login'], true);
			if (!empty($this->request->query['redirect'])) {
				$backUrl = $this->request->query['redirect'];
			}

			echo $this->Html->link( __( 'Back' ),
				$backUrl,
				array( 'class' => 'btn btn-default pull-left' )
			); ?>

			<?php echo $this->Form->submit( __( 'Reset Your Password' ),array(
				'class' => 'submit btn btn-primary pull-right',
				'div' => false
			) ); ?>
		</div>
	</div>
</div>

<?php echo $this->Form->end(); ?>