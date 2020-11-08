<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
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

		$cssFiles = [
			// GLOBAL
			"LimitlessTheme.icons/icomoon/styles",
			"LimitlessTheme.bootstrap",
			"LimitlessTheme.core",
			"LimitlessTheme.components",
			"LimitlessTheme.colors",
			"eramba.css?app_v=" . Configure::read('Eramba.version')
		];

		$jsFiles = [
			// Core
			"LimitlessTheme.plugins/loaders/pace.min",
			"LimitlessTheme.core/libraries/jquery.min",
			"LimitlessTheme.core/libraries/bootstrap.min",
			"LimitlessTheme.plugins/loaders/blockui.min",
			// Theme
			"LimitlessTheme.plugins/forms/validation/validate.min",
			"LimitlessTheme.plugins/forms/styling/uniform.min",
			"LimitlessTheme.plugins/forms/selects/select2.min",
			"LimitlessTheme.core/app",
			"LimitlessTheme.pages/login_validation",
			"LimitlessTheme.plugins/notifications/pnotify.min",
			"plugins/nprogress/nprogress",
			// Custom
			'eramba'
		];

		echo $this->Html->css($cssFiles);
		echo $this->Html->script($jsFiles);

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>

	<link href="/css/font/Roboto-font/Roboto-Regular.ttf" rel="stylesheet">
</head>
<body class="login-container">

	<?= $this->Ux->renderFlash(); ?>

	<!-- Page container -->
	<div class="page-container page-container-login">

		<?= $this->fetch( 'content' ); ?>

	</div>
	<!-- /page container -->

</body>
</html>
