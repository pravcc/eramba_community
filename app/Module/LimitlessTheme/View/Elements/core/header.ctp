<!-- Main navbar -->
<div id="navbar" class="navbar-fixed-wrapper" data-yjs-request="eramba/navbar" data-yjs-event-on="init" data-yjs-use-loader="false">
	<div class="navbar navbar-inverse navbar-fixed">
		<div class="navbar-header">
			<a id="header-logo" class="navbar-brand" 
			data-yjs-request="app/load" 
			data-yjs-datasource-url="/settings/getLogo/1" 
			data-yjs-target="self" 
			data-yjs-use-loader="false" 
			href="<?= Router::url( array('controller' => 'pages', 'action' => 'welcome', 'admin' => false, 'plugin' => null) ); ?>">
				<?= $this->Eramba->getLogo(); ?>
			</a>

			<ul class="nav navbar-nav visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
				<li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
			</ul>
		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">
			<ul class="nav navbar-nav" style="display: none !important;">
				<li><a class="sidebar-control hidden-xs" data-yjs-request="eramba/toggleSidebar" data-yjs-event-on="click" data-yjs-use-loader="false"><i class="icon-paragraph-justify3"></i></a></li>
				<span style="display: none" data-yjs-request="eramba/toggleSidebar/readOnly::true" data-yjs-event-on="init" data-yjs-use-loader="false"></span>
			</ul>

			<?= $this->PageToolbar->render(); ?>

			<div class="navbar-right">
				<ul class="nav navbar-nav">

					<?php //echo $this->element(CORE_ELEMENT_PATH . 'notifications'); ?>
					
					<?= $this->element('AppNotification.header_notifications') ?>

					<li class="dropdown dropdown-user">
						<a class="dropdown-toggle" data-toggle="dropdown">
							<?= $this->Ux->createLetterUserPic($logged['name']); ?>
							<span><?= $logged['name'] .' '. $logged['surname']; ?></span>
							<i class="caret"></i>
						</a>

						<ul class="dropdown-menu dropdown-menu-right">
							<li>
								<?php
								echo $this->Html->link(
									'<i class="icon-user-plus"></i> '. __('My Profile'),
									'#',
									[
										'escape' => false,
										'data-yjs-request' => "crud/showForm",
										'data-yjs-event-on' => 'click',
										'data-yjs-target' => 'modal',
										'data-yjs-datasource-url' => Router::url(['controller' => 'users', 'action' => 'profile', 'admin' => false, 'plugin' => null])
									]
								);
								?>
							</li>
							<li class="divider"></li>
							<li>
								<?= $this->Ux->logoutBtn(); ?>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- /main navbar -->