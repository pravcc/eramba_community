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
			// 'bootstrap.min',
			"LimitlessTheme.icons/icomoon/styles",
			"LimitlessTheme.bootstrap",
			"LimitlessTheme.core",
			"LimitlessTheme.components",
			"LimitlessTheme.colors",
			// 'fontawesome/font-awesome.min',
			'policy-login.css?app_v=' . Configure::read('Eramba.version')
		);

		$jsFiles = array(
			'libs/jquery-1.10.2.min',
			'plugins/jquery-ui/jquery-ui-1.10.2.custom.min',
			'bootstrap.min',
			'libs/lodash.compat.min',
			'plugins/uniform/jquery.uniform.min',
			'plugins/nprogress/nprogress',
			"LimitlessTheme.plugins/notifications/pnotify.min",
			"LimitlessTheme.plugins/forms/selects/select2.min",
		);

		echo $this->Html->css($cssFiles);
		echo $this->Html->script($jsFiles);

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>

	<?php
	echo $this->Ux->renderFlash();
	?>

	<div id="login" class="policy-login">

		<div class="container">
			<div class="row">
				<div class="col-md-8 col-md-offset-2 col-sm-12">
					<div class="header clearfix">
						<div class="logo-wrapper">
							<?php echo $this->Eramba->getLogo('/img/logo-portal.png'); ?>
						</div>
						<div class="text-wrapper pull-left">
							<h1><?php echo __('Document Library Portal'); ?></h1>
							<div class="desc">
								<?php echo __('Login to this portal to access the full library of documents'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="main-content">
			<div class="container">

				<?php echo $this->fetch('content'); ?>

				<div class="copyright-text">
					<?php echo $this->element(CORE_ELEMENT_PATH . 'loginFooter'); ?>
				</div>

			</div>
		</div>

	</div>

</body>
</html>
