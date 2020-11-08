
<!-- Main sidebar -->
<div class="sidebar sidebar-main">
	<div id="sidebar" class="sidebar-content sidebar-fixed" data-yjs-request="eramba/sidebar" data-yjs-event-on="init" data-yjs-use-loader="false">

		<!-- Main navigation -->
		<div class="sidebar-category sidebar-category-visible">
			<div class="category-content no-padding">
				<ul class="navigation navigation-main navigation-accordion">

					<?php foreach ($menuItems as $section) : ?>
						<?php	
						$class = '';		
						if ((!empty($section['url']['controller']) && $section['url']['controller'] == $this->request->params['controller']) ||
							(!empty($section['childSections']) && in_array($this->request->params['controller'], $section['childSections']))) {
							$class = 'active';
						}
						?>
						<li class="<?= $class; ?>">
							<?php
							$sectionIcon = (isset($section['icon']) && $section['icon'] !== '') ? $section['icon'] : 'icon-stack';
							$sectionName = '<i class="' . $sectionIcon . '"></i>' . '<span>' . $section['name'] . '</span>';
							if (empty($section['url'])) {
								$section['url'] = 'javascript:void(0);';
							}

							echo $this->Html->link($sectionName, $section['url'], array(
								'class' => '',
								'escape' => false
							));
							?>

							<?php if (!empty($section['children'])) : ?>
								<ul>
									<?php foreach ($section['children'] as $action) : ?>
										<?php
										$class = '';
										if (isset($action['url']['controller'])) { // this if statement is here only because of 'enterprise' key possibility
											if (($action['url']['controller'] == $this->request->params['controller'] && $action['url']['action'] == $this->request->params['action']) ||
												(!empty($action['childSections']) && in_array($this->request->params['controller'], $action['childSections']))) {
												$class = 'active';
											}
										}

										$title = $action['name'];
										$url = $action['url'];
										$aOptions = [
											'class' => '',
											'escape' => false
										];

										// if MenuComponent evaluated this item as non-accessible and enterprise at the same time
										if (!empty($action['forceEnterprise'])) {
											$title .= ' ' . $this->Labels->danger('enterprise');
											$aOptions['target'] = '_blank';
										}
										?>

										<li class="<?= $class; ?>">
											<?php
											echo $this->Html->link( 
												$title, 
												$url,
												$aOptions
											);
											?>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>

				</ul>
			</div>
		</div>
		<!-- /main navigation -->

	</div>
</div>
<!-- /main sidebar -->