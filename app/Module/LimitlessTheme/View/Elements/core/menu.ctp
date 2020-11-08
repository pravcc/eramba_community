<ul class="nav navbar-nav navbar-left hidden-xs hidden-sm" id="menu-nav-top">
	<?php foreach ($menuItems as $section) : ?>
		<li class="dropdown">
			<?php
			$sectionName = $section['name'];
			$dataToggle = false;
			if (empty($section['url'])) {
				$section['url'] = '#';
				$sectionName .= ' <i class="icon-caret-down small"></i>';
				$dataToggle = 'dropdown';
			}

			echo $this->Html->link($sectionName, $section['url'], array(
				'class' => 'dropdown-toggle ' . $section['class'],
				'data-toggle' => $dataToggle,
				'escape' => false
			));
			?>

			<?php if (!empty($section['children'])) : ?>
				<ul class="dropdown-menu">
					<?php foreach ($section['children'] as $action) : ?>
					<li>
						<?php 
						echo $this->Html->link( 
							$action['name'], 
							$action['url']
						);
						?>
					</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>