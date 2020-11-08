<?php
// debug($items['default']);
?>

<div class="divider"></div>


<div class="widget-content widget-deeper">
	<?php if (!empty($items['default'])) : ?>
		
		<table class="table table-striped table-hover table-dashboard table-dashboard-admin">
			<thead>
				<tr>
					<th>
						<?php echo __('Title'); ?>
					</th>
					<th>
						<?php echo __('Value'); ?>
					</th>
					<th>
						<?php echo __('Weekly'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<!-- blank line for .table-striped to look better -->
				<tr></tr>

				<?php foreach ($items['default'] as $item) : ?>

					<tr>
						<td>
							<?php
							echo $item['DashboardKpi']['title'];
							?>
						</td>

						<td>
							<?php
							echo $this->DashboardKpi->getKpiValue($item);
							?>
						</td>
						<td>
							<?php
							echo $this->Dashboard->getKpiSparkline($item['weekly_per_day']);
							?>
						</td>
					</tr>

				<?php endforeach; ?>

			</tbody>
		</table>

	<?php else : ?>
		<?php
		echo $this->Html->div('no-customizations', __('Customizations are not available here.'));
		?>
	<?php endif; ?>

</div>