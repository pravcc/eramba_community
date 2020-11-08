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
			"eramba"
		];

		$jsFiles = [
			// Core
			"LimitlessTheme.plugins/loaders/pace.min",
			"LimitlessTheme.core/libraries/jquery.min",
			"LimitlessTheme.core/libraries/bootstrap.min",
			"LimitlessTheme.plugins/loaders/blockui.min",
			// Theme
			//"LimitlessTheme.plugins/tables/datatables/datatables.min",
			"LimitlessTheme.plugins/forms/selects/select2.min",
			"LimitlessTheme.plugins/forms/styling/uniform.min",
			"LimitlessTheme.core/app",
			"LimitlessTheme.pages/datatables_basic",
			"LimitlessTheme.plugins/notifications/pnotify.min",

			// Temp velocity
			"LimitlessTheme.plugins/velocity/velocity.min",
			"LimitlessTheme.plugins/velocity/velocity.ui.min",
			"LimitlessTheme.pages/components_popups"

			// "LimitlessTheme.core/app",
			// "LimitlessTheme.pages/animations_velocity_ui"
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

	<script type="text/javascript">
	//<![CDATA[
		// jQuery(function($) {
		// 	Eramba.debug = <?php echo Configure::read('debug'); ?>;
		// 	Eramba.Ajax.currentIndex = "<?php echo str_replace('&amp;', '&', addslashes(Purifier::clean(Router::reverse($this->params), 'Strict'))); ?>";

		// 	<?php if (isset($pushState)) : ?>
		// 		Eramba.Ajax.UI.initPushState = <?php echo json_encode($pushState); ?>;
		// 	<?php endif; ?>

		// 	Eramba.locale = {
		// 		errorTitle: "<?php echo __('Something went wrong'); ?>",
		// 		code: "<?php echo __('Code'); ?>",
		// 		message: "<?php echo __('Message'); ?>",
		// 		requestUrl: "<?php echo __('Request URL'); ?>",
		// 		error403: "<?php echo __('Your session probably expired and you have been logged out of the application.'); ?>",
		// 		errorHuman: "<?php echo __('Error occured and the request failed.<br />Enable debug mode if you want more information.<br />If this problem persist, contact the support.'); ?>",
		// 		tryAgain: "<?php echo __('Please try again or <span>reload the page</span>.'); ?>"
		// 	};

		// 	Eramba.init();
		// });
	//]]>
	</script>


</head>
<body>
	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<div class="content-wrapper">
				<div class="content">
					<div class="modal modal-custom-fixed in" style="display: block">
						<div class="modal-dialog modal-lg" style="top: 5%; width: 90%; height: 90%">
							<div class="modal-content modal-content-custom">
								<div class="modal-header <?= $modal->getHeader('class'); ?>">
									<?php if ($modal->getHeader('buttons.close') == true): ?>
										<button type="button" class="close" 
												data-yjs-request="app/closeModal" 
												data-yjs-modal-id="<?= $modal->getModalId(); ?>" 
												data-yjs-event-on="click|keyup-27-detach"
												data-yjs-use-loader="false"><i class="icon-x"></i></button>
									<?php endif; ?>
									<h5 class="modal-title"><?= $modal->getHeader('heading'); ?></h5>
								</div>
								<div class="modal-body">
									<?php
										if ($modal->getBody() !== '') {
											echo $modal->getBody();
										} else {
											echo $this->fetch('content');
										}
									?>
								</div>
								<div class="modal-footer">
									<?php
										foreach ($modal->getFooter('buttons', []) as $btn) {
											echo $this->Html->tag($btn['tag'], $btn['text'], $btn['options']);
										}
									?>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-backdrop in"></div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>