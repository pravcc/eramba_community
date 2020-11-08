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
			Router::url('/favicon.png', true),
			array('type' => 'icon', 'fullBase' => true)
		);

		$cssFiles = array(
			'bootstrap.min',
			'main',
			'plugins',
			// 'responsive',
			// 'icons',
			'fontawesome/font-awesome.min',
			// 'eramba',
			//'policy',
			//'policy-document',
			'eramba',
			'pdf-general.css?03052016',
			'pdf'
		);

		$jsFiles = array(
			'libs/jquery-1.10.2.min',
			'plugins/jquery-ui/jquery-ui-1.10.2.custom.min',
			'bootstrap.min',
			'plugins/flot/jquery.flot.min',
			'plugins/flot/jquery.flot.tooltip.min',
			'plugins/flot/jquery.flot.resize.min',
			'plugins/flot/jquery.flot.time.min',
			'plugins/flot/jquery.flot.orderBars.min',
			'plugins/flot/jquery.flot.pie.min',
			'plugins/flot/jquery.flot.growraf.min',
			'app',
			'plugins',
			'eramba'
		);

		echo $this->Html->css( $cssFiles, array('fullBase' => true) );
		echo $this->Html->script( $jsFiles, array('fullBase' => true) );

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>

	<link href="/css/font/Montserrat-font/Montserrat-Regular.ttf" rel="stylesheet"> 
</head>
<body>

	<?php
	echo $this->Ux->renderFlash();

	//PDF debug mode info
	if (Configure::read('debug')) {
		echo $this->Eramba->getNotificationBox(__('<b>NOTE</b>: You are in DEBUG MODE. PDF documents are rendered into view. If you want to get documents in PDF format please disable DEBUG MODE.'), ['class' => 'alert-danger text-center']);
	}
	?>

	<div class="pdf-header-wrapper" style="padding-top:14px;">
		<div class="container">
			<div class="row">
				<div class="col-xs-6">
					<a class="navbar-brand" target="_blank" href="<?php echo Router::url('http://www.eramba.org', true ); ?>" style="padding-top:0;">
						<?php echo $this->Eramba->getLogo(DEFAULT_LOGO_WHITE_URL, true); ?>
					</a>
				</div>
				<div class="col-xs-6">
					&nbsp;
				</div>
			</div>
		</div>
	</div>

	<br />
	<div class="container" style="">
		<div class="pdf-content">
			<?php echo $this->fetch( 'content' ); ?>
		</div>

		<div class="pdf-footer">
			<?php
			$link = $this->Html->link('www.eramba.org', 'http://www.eramba.org', array(
				'target' => '_blank'
			));
			$link = $this->Html->tag('strong', $link);

			$date = date('Y-m-d H:i:s', strtotime("now"));
			$date = $this->Html->tag('strong', $date);

			$user = $logged['login'];
			if (!empty($logged['full_name'])) {
				$user = $logged['full_name'];
			}
			$user = $this->Html->tag('strong', $user);

			echo __('This portal was generated on %s by %s using %s', $date, $user, $link);
			?>
		</div>
	</div>

</body>
</html>
