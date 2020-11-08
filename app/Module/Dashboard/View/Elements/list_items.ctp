<?php
App::uses('DashboardKpi', 'Dashboard.Model');
?>
<table class="table table-dashboard table-dashboard-admin">
	<thead>
		<tr>
			<th>
				<?php echo DashboardKpi::categories($category); ?>
			</th>
			<th>
				<?php echo __('Value'); ?>
			</th>
			<?php //if ($category == DashboardKpi::CATEGORY_OWNER) : ?>
				<th>
					<?php
					echo __('Actions');
					?>
				</th>
			<?php //endif; ?>
		</tr>
	</thead>
	<tbody>
		<!-- blank line for .table-striped to look better -->
		<tr></tr>

		<?php foreach ($items as $item) : ?>
			<?php
			// class style and dataAttrs
			extract($this->DashboardKpi->getThresholdParams($item));
			?>
			<tr class="<?= $class; ?>" style="<?= $style; ?>" <?= $dataAttrs; ?>>
				<td>
					<?php
					echo $item['DashboardKpi']['title'];
					?>
				</td>

				<td>
					<?php
					echo $this->DashboardKpi->getKpiLink($model, $item);
					?>
				</td>
				<td class="text-center">
					<ul class="icons-list">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<i class="icon-menu7"></i>
							</a>

							<ul class="dropdown-menu dropdown-menu-left dropdown-menu-filter-item">
								<?php
								$id = $item['DashboardKpi']['id'];
								?>
								<li>
									<?php
									// edit action for all KPIs
									echo $this->Html->link('<i class="icon-pencil"></i> ' . __('Edit'), '#', [
										'data-yjs-request' => 'crud/showForm',
										'data-yjs-target' => "modal",
									    'data-yjs-datasource-url' => Router::url([
									    	'plugin' => 'dashboard',
									    	'controller' => 'dashboard_kpis',
											'action' => 'edit',
											$id
										]),
									    'data-yjs-event-on' => "click",
										'escape' => false
									]);
									?>
								</li>

								<?php // trash for custom-created KPIs ?>
								<?php if ($category == DashboardKpi::CATEGORY_OWNER) : ?>
									<li>
										<?php
										echo $this->Html->link('<i class="icon-trash"></i> ' . __('Delete'), '#', [
											'data-yjs-request' => 'crud/showForm',
											'data-yjs-target' => "modal",
										    'data-yjs-datasource-url' => Router::url([
												'plugin' => 'dashboard',
									    		'controller' => 'dashboard_kpis',
												'action' => 'delete',
												$id
											]),
										    'data-yjs-event-on' => "click",
											'escape' => false
										]);
										?>
									</li>
								<?php endif; ?>
							</ul>
						</li>
					</ul>
				</td>
			</tr>

		<?php endforeach; ?>

	</tbody>
</table>