<div class="awareness-header">
	<div class="container">
		<div class="logo">
			<?php $awarenessUrl = Router::url(array('controller' => 'awareness', 'action' => 'index', 'admin' => false, 'plugin' => null)); ?>
			<a href="<?php echo $awarenessUrl; ?>">
				<?php echo $this->Eramba->getLogo(DEFAULT_LOGO_WHITE_URL); ?>
			</a>
		</div>
	</div>

	<?php if (!empty($logged['login'])) : ?>
		<ul class="nav navbar-nav navbar-right">
			<li class="dropdown user">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">
					<i class="icon-male"></i>
					<span class="username"><?= $logged['login'] ?></span>
					<i class="icon-caret-down small"></i>
				</a>
				<ul class="dropdown-menu">
					<li>
						<?= $this->Ux->logoutBtn([
							'plugin' => null,
							'controller' => 'awareness',
							'action' => 'logout',
							'admin' => false
						]);
						?>
					</li>
				</ul>
			</li>
		</ul>
	<?php endif; ?>
</div>