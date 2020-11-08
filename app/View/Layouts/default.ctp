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
			"eramba.css?app_v=" . Configure::read('Eramba.version')
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
			"LimitlessTheme.plugins/buttons/spin.min",
			"LimitlessTheme.plugins/buttons/ladda.min",
			"LimitlessTheme.plugins/uploaders/dropzone.min",
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

		echo $this->Html->script("datatables-upgrade/datatables.min.js?v=1.10.13");

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
<?php
echo $this->Html->script("LimitlessTheme.plugins/forms/selects/select2.min");
echo $this->Html->script("LimitlessTheme.plugins/forms/styling/switch.min");
echo $this->Html->script("LimitlessTheme.plugins/forms/styling/switchery.min");
echo $this->Html->script("LimitlessTheme.plugins/notifications/sweet_alert.min");
echo $this->Html->script('LimitlessTheme.core/libraries/jquery_ui/interactions.min');
echo $this->Html->script('LimitlessTheme.core/libraries/jquery_ui/widgets.min');
echo $this->Html->script('LimitlessTheme.core/libraries/jquery_ui/effects.min');
echo $this->Html->script('LimitlessTheme.plugins/extensions/mousewheel.min');
echo $this->Html->script('LimitlessTheme.core/libraries/jquery_ui/globalize/globalize');
echo $this->Html->script("LimitlessTheme.plugins/tables/datatables/extensions/buttons.min");
?>
<?php
echo $this->Html->script("datatables-upgrade/dataTables.colReorder.min.js?v=1.5.1");
echo $this->Html->css("/js/datatables-upgrade/colReorder.dataTables.min.css?v=1.5.1");

echo $this->Html->css('bootstrap-colorpicker');

?>
	<!-- Load YoonityJS Framework -->
	<?= $this->Html->script("YoonityJS/YoonityJS-" . Configure::read('YoonityJS.version') . ".js?app_v=" . Configure::read('Eramba.version')); ?>

	<link href="/css/font/Roboto-font/Roboto-Regular.ttf" rel="stylesheet">

	<style type="text/css">
	/*.menu-organization,
	.menu-assets,
	.menu-controls,
	.menu-risk,
	.menu-compliance,
	.menu-security,
	.menu-system {
		border-bottom-width: 4px;
		border-bottom-style: solid;
		border-color: transparent;
		margin-bottom: -4px;
	}*/
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
	
	<?php
		if ($userJustLogged && !$systemHealthData && isAdmin($logged)) {
			echo $this->element(CORE_ELEMENT_PATH . 'system_health');
		}
		if ($userJustLogged && !empty($autoUpdatePending) && isAdmin($logged)) {
			echo $this->element(CORE_ELEMENT_PATH . 'auto_update_pending');
		}
		if (Configure::read('debug') > 0) {
			echo $this->element(CORE_ELEMENT_PATH . 'debug_enabled');
		}

		// remove CRON cache if rendering comes here
		// if ($this->params['controller'] == 'cron') {
		// 	if ($this->params['action'] == 'daily' || $this->params['action'] == 'yearly') {
		// 		$m = ClassRegistry::init('Cron');
		// 		$m->setCronTaskAsRunning($this->params['action'], false);
		// 	}
		// } 
	?>

	<?= $this->Ux->renderFlash(); ?>

	<?= $this->element($layout_headerPath); ?>

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<?= $this->element($layout_sidebarPath); ?>

			<?php if (!empty($layout_contentPath)) : ?>

				<?= $this->element($layout_contentPath); ?>

			<?php else : ?>

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

						<?= $this->element($layout_pageHeaderPath); ?>

						<!-- Content area -->
						<div class="content" id="main-content"
							data-yjs-request="crud/load"
							data-yjs-target="#main-content"
							data-yjs-datasource-url="<?= Router::url(Router::reverseToArray($this->request)) ?>"
							>

							<?= $this->fetch('content'); ?>

						</div>
						<!-- /content area -->

						<!-- Footer -->
						<div id="page-footer" class="navbar navbar-default <?= $layoutFooter['isEnterprise'] ? 'page-footer-content-bottom' : 'navbar-fixed-bottom' ?>">
							<ul class="nav navbar-nav no-border visible-xs-block">
								<li><a class="text-center collapsed" data-toggle="collapse" data-target="#navbar-second"><i class="icon-circle-up2"></i></a></li>
							</ul>

							<div class="navbar-collapse collapse" id="navbar-second">
								<div class="navbar-text">
									<a href="http://www.eramba.org" target="_blank">eramba Ltd</a> | <?= __('App Version') ?>: <?= $layoutFooter['version'] ?> | <?= __('DB Schema Version') ?>: <?= $layoutFooter['dbVersion'] ?>
								</div>

								<div class="navbar-right">
									<ul class="nav navbar-nav">
										<?php if ($layoutFooter['isEnterprise']): ?>
											<li><a href="mailto:support@eramba.org"><?= __('Support') ?></a></li>
										<?php endif; ?>
										<li><a href="http://www.eramba.org/documentation" target="_blank"><?= __('Documentation') ?></a></li>
										<?php if (!$layoutFooter['isEnterprise']): ?>
											<li><a href="http://www.eramba.org/services" target="_blank"><span class="label label-danger" style="position: relative !important;"><?= __('Upgrade to enterprise version') ?></span></a></li>
										<?php endif; ?>
									</ul>
								</div>
							</div>
						</div>
						<!-- /footer -->

					<?php endif; ?>

				</div>
				<!-- /main content -->

			<?php endif; ?>

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