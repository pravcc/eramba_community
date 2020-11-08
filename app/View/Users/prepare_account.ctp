<?php echo $this->Form->create( 'User', array(
	'url' => array(
		'controller' => 'users',
		'action' => 'prepareAccount',
		$prepareAccountUserId,
		'?' => [
			'redirect' => $prepareAccountRedirect
		]
	),
	'class' => 'login-form-custom',
	'style' => 'margin-left: -260px'
) ); ?>

<div class="panel panel-body login-form" style="margin-bottom: 0; width: 520px;">
	<div class="text-center mb-20">
		<div class="spinner-wrapper" style="position:absolute;top:-40px;left:50%;width:30px;height:30px;margin-left:-20px;"></div>
		<?php
		$heading = __('We are getting your account ready');
		$subheading = __('Dont worry, this setup process only happens on your first login!');
		?>

		<h3 class="form-title"><?= $heading ?></h3>
		<p><?= $subheading ?></p>

	</div>
</div>

<?php echo $this->Form->end(); ?>
<script type="text/javascript">
	jQuery(function($) {
		$(".login-form-custom").submit();
		$(".spinner-wrapper").block({ 
            message: '<i class="icon-spinner2 spinner"></i>',
            overlayCSS: {
                backgroundColor: 'none',
                opacity: 0.8,
                cursor: 'wait'
            },
            css: {
                border: 0,
                padding: 0,
                backgroundColor: 'none'
            }
        });
	});
</script>