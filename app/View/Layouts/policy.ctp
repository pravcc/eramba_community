<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<title>
		<?php
		echo $title_for_layout .(!empty($title_for_layout) ? ' | ' : ''). (defined('NAME_SERVICE') ? NAME_SERVICE : DEFAULT_NAME);
		?>
	</title>
	<?php
		echo $this->Html->meta(
			'favicon.ico',
			'/favicon.png',
			array('type' => 'icon')
		);

		$cssFiles = array(
			'bootstrap.min',
			'fontawesome/font-awesome.min',
			'policy',
			'policy-document',
			"LimitlessTheme.icons/icomoon/styles",
			"LimitlessTheme.components",
			"LimitlessTheme.colors",
		);

		$jsFiles = array(
			'libs/jquery-1.10.2.min',
			'plugins/jquery-ui/jquery-ui-1.10.2.custom.min',
			'bootstrap.min',
			"LimitlessTheme.plugins/notifications/pnotify.min",
			'plugins/nprogress/nprogress',
			'plugins/bootbox/bootbox.min',
			'plugins/slimscroll/jquery.slimscroll.min',
			'eramba',
			'policy-document'
		);

		echo $this->Html->css( $cssFiles );
		echo $this->Html->script( $jsFiles );

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>

	<link href="/css/font/Montserrat-font/Montserrat-Regular.ttf" rel="stylesheet">
</head>
<body>

	<?php
	echo $this->Ux->renderFlash();
	?>

	<div class="policy-header-wrapper">
		<div class="container">
			<div class="row">
				<div class="col-xs-6">
					<a id="logo" class="navbar-brand" href="<?php echo Router::url( array('controller' => 'policy', 'action' => 'index', 'admin' => false, 'plugin' => null) ); ?>">
						<?php echo $this->Eramba->getLogo(DEFAULT_LOGO_WHITE_URL); ?>
					</a>
				</div>
				<div class="col-xs-6">
					<div class="logout-wrapper">
						<?php
						if ($isGuest === false) {
							echo $this->Html->link(__('Logout'), array(
								'controller' => 'policy',
								'action' => 'logout'
							));
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="search-wrapper">
		<div class="container">
			<?php echo $this->Form->create('SecurityPolicy', array('class' => 'filter-form')); ?>
				<div class="row">
					<div class="col-xs-9">
						<?php echo $this->Form->create('SecurityPolicy', array('class' => 'filter-form')); ?>
						<?php
						echo $this->Form->input('policy_search', array(
							'label' => false,
							'div' => 'search-input-wrapper',
							'placeholder' => __('Search'),
							'class' => 'form-control search-field'
						));
						?>
						<?php echo $this->Form->end(); ?>
					</div>
					<div class="col-xs-3">
						<div class='btn btn-default btn-search reset-btn'>
							<?php echo __('Reset') ?>
						</div>
					</div>
				</div>
		</div>
	</div>
	<script type="text/javascript">
		$( document ).ready(function() {
			$(".reset-btn").on('click', function(){
				$(".search-field").val("");
				$(".filter-form").submit();
			});
		});
	</script>


	<div class="container">
		<div class="content">
			<?php echo $this->fetch( 'content' ); ?>
		</div>

		<div class="policy-footer">
			<?php echo __('This portal is generated using <a href="http://www.eramba.org" target="_blank">www.eramba.org</a>'); ?>
		</div>
	</div>

</body>
</html>
