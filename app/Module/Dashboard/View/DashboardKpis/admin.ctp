<?php
if (!$dashboardReady) {
	echo $this->Alerts->info(__('Your dashboard is getting ready, once its completed, it will display here.'.PHP_EOL.'This process can take up to 2 hours.'), [
		'type' => 'info'
	]);

	return true;
}
?>
<div class="text-right">
	<?php
	$dateFormat = 'jS M g:ma'
	?>
	<?php if ($recalculationEvent !== false) : ?>
		<p><?= __('%s was the last time we calculated KPIs for this page', date($dateFormat, strtotime($recalculationEvent))); ?></p>
	<?php endif; ?>
	<?php if ($storedValuesEvent !== false) : ?>
		<p><?= __('%s was the last time we stored values ready for CSV download', date($dateFormat, strtotime($storedValuesEvent))); ?></p>
	<?php endif; ?>
</div>


<?php
// add toolbar buttons for dashboard pages
$this->Dashboard->addToolbarBtns();
echo $this->Html->css("Dashboard.dashboard.css?11062017");

$C = (isset($data['ComplianceManagement'])) ? $data['ComplianceManagement'] : [];
$A = (isset($data['AwarenessProgram'])) ? $data['AwarenessProgram'] : [];
unset($data['ComplianceManagement']);
unset($data['AwarenessProgram']);
?>

<div class="row">
	<div class="col-md-6">
		<?php $i=0;foreach ($data as $model => $categories) : ?>

				<?php if (ceil((count($data)-2) / 2) == $i) : ?>
					</div>
					<div class="col-md-6">
				<?php endif; ?>

				<?php
				echo $this->DashboardKpi->widget('admin_section', $model, $categories);
				?>

		<?php $i++;endforeach; ?>
	</div>
</div>

<?php
// ddd($C);
echo $this->DashboardKpi->widget('admin_section', 'ComplianceManagement', $C);
echo $this->DashboardKpi->widget('admin_section', 'AwarenessProgram', $A);
?>

<script type="text/javascript">
	/*jQuery(function($) {
		$('.dashboard-sparkline').each(function () {
			var barColor = App.getLayoutColorCode($(this).data('bar-color'));

			// means it has got its relevant hex value in case its a string color name
			if (barColor != '') {
				$(this).data('bar-color', barColor);
			}

			var config = $.extend(true, {}, Plugins.getSparklineStatboxDefaults(), $(this).data());
			$(this).sparkline('html', config);
		});
	});*/
</script>
