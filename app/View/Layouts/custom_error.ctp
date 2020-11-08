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
			"report-blocks-grid",
			"eramba.css?20180512"
		];

		$jsFiles = [
			// Core
			"LimitlessTheme.plugins/loaders/pace.min",
			"LimitlessTheme.core/libraries/jquery.min",
			"LimitlessTheme.core/libraries/bootstrap.min",
			"LimitlessTheme.plugins/loaders/blockui.min",
			// Theme
			"LimitlessTheme.plugins/forms/selects/select2.min",
			"LimitlessTheme.plugins/forms/styling/uniform.min",
			"LimitlessTheme.core/app",
			"LimitlessTheme.pages/datatables_basic",
			"LimitlessTheme.plugins/notifications/pnotify.min",
			"LimitlessTheme.plugins/forms/styling/switch.min",
			"LimitlessTheme.plugins/forms/styling/switchery.min",
			"LimitlessTheme.core/libraries/jquery_ui/interactions.min",
			"LimitlessTheme.core/libraries/jquery_ui/widgets.min",
			"LimitlessTheme.core/libraries/jquery_ui/effects.min",
			"LimitlessTheme.plugins/extensions/mousewheel.min",
			"LimitlessTheme.core/libraries/jquery_ui/globalize/globalize",
			"LimitlessTheme.plugins/editors/summernote/summernote.min",
			"LimitlessTheme.plugins/forms/wizards/steps.min",
			//
			// Temp velocity
			"LimitlessTheme.plugins/velocity/velocity.min",
			"LimitlessTheme.plugins/velocity/velocity.ui.min",
			"LimitlessTheme.pages/components_popups",

			"AutoComplete.auto-complete-new",
			"AutoComplete.auto-complete-associated",
			"plugins/bootstrap-colorpicker/bootstrap-colorpicker",
			// End temp velocity
		];

		echo $this->Html->css($cssFiles);
		echo $this->Html->script($jsFiles);

		echo $this->Html->script("datatables-upgrade/datatables.min");

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>

	<!-- Load YoonityJS Framework -->
	<?= $this->Html->script("YoonityJS/YoonityJS-" . Configure::read('YoonityJS.version') . ".js?app_v=" . Configure::read('Eramba.version')); ?>

	<link href="/css/font/Roboto-font/Roboto-Regular.ttf" rel="stylesheet">

	<style type="text/css">
	.menu-controls:after {
		background-color: <?php echo COLOR_CONTROLS; ?>;
	}
	.menu-risk:after {
		background-color: <?php echo COLOR_RISK; ?>;
	}
	.menu-compliance:after {
		background-color: <?php echo COLOR_COMPLIANCE; ?>;
	}
	.menu-security:after {
		background-color: <?php echo COLOR_SECURITY; ?>;
	}
	<?php if (!empty(Configure::read('Eramba.Settings.CUSTOM_LOGO'))): ?>
		@media only screen and (max-width: 1400px) {
			#logo img {
				content: url(<?php echo $this->Eramba->getCustomLogoUrl(true); ?> );
			}
		}
	<?php endif; ?>
	</style>

	<?php if (isset($showId) && !empty($showId)) : ?>
		<script type="text/javascript">
		//<![CDATA[
		jQuery(function($) {
			var showId = <?php echo (int) $showId; ?>;
			$(".ajax-show[data-itemid=" + showId + "]").trigger("click");
		});
		//]]>
		</script>
	<?php endif; ?>


</head>
<body class="sidebar-xs"
	data-yjs-request="crud/initExternals/elem::body"
	data-yjs-event-on="init"
	data-yjs-use-loader="false"
>

	<?= $this->element($layout_headerPath); ?>

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<?= $this->element($layout_sidebarPath); ?>

			<!-- Main content -->
			<div class="content-wrapper">

				<?php if (isset($AclNotAllowed) && $AclNotAllowed): ?>

					<?php
					echo $this->Html->div(
						'alert alert-danger fade in',
						'<i class="icon-exclamation-sign"></i> ' . __('You don\'t have a permission to view this page.'), array(
							'style' => 'margin-top: 20px;'
						)
					);
					?>

				<?php else: ?>

					<!-- Content area -->
					<div class="content" id="main-content">

						<div class="panel panel-flat">
							<div class="panel-body">
								<?= $this->fetch('content'); ?>
							</div>
						</div>

					</div>
					<!-- /content area -->

				<?php endif; ?>

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

	</div>
	<!-- /page container -->

	<?php
	if (Configure::read('debug')) {
		echo $this->element('LimitlessTheme.toolbar/debug');
	}
	?>
</body>
</html>