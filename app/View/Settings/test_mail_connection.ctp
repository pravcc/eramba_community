<div class="pl-10 pr-10">
	<?php
		echo $this->Form->create($testMailFormName, [
			'data-yjs-form' => $testMailFormName
		]);
	?>

	<div class="form-group">
	    <?php
	        $options = [
	            'label' => [
	                'class' => 'control-label',
	                'text' => __('Enter email')
	            ],
	            'div' => false,
	            'class' => 'form-control',
	            'type' => 'email',
	            'default' => isset($logged['email']) ? $logged['email'] : ''
	        ];

	        echo $this->Form->input('email', $options);
	    ?>
	    <span class="help-block"><?= __('Please fill in your email address to be able to test mail connection.'); ?></span>
	</div>

	<?php echo $this->Form->end(); ?>
</div>