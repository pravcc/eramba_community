<!-- Header -->
<header class="header navbar" role="banner">
	<!-- Top Navigation Bar -->
	<div class="container">

		<a id="logo" class="navbar-brand" href="<?php echo Router::url( array('plugin' => null, 'controller' => 'thirdPartyAudits', 'action' => 'index') ); ?>">
			<?php echo $this->Eramba->getLogo(); ?>
		</a>

		<!-- Top Right Menu -->
		<ul class="nav navbar-nav navbar-right">

			<!-- User Login Dropdown -->
			<li class="dropdown user">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">
					<i class="icon-male"></i>
					<span class="username"><?php echo $logged['name'] .' '. $logged['surname']; ?></span>
					<i class="icon-caret-down small"></i>
				</a>
				<ul class="dropdown-menu">
					<li>
						<?php
						echo $this->Ux->logoutBtn([
							'plugin' => true,
							'controller' => 'thirdPartyAudits',
							'action' => 'logout',
							'admin' => false
						]);
						?>
					</li>
				</ul>
			</li>
			<!-- /user login dropdown -->
		</ul>
		<!-- /Top Right Menu -->
	</div>

</header> <!-- /.header -->