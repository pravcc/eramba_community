<?php
App::uses('DashboardKpi', 'Dashboard.Model');

// add toolbar buttons for dashboard pages
$this->Dashboard->addToolbarBtns();
echo $this->Html->css("Dashboard.dashboard.css?11062017");

$order = DashboardKpi::listModelsForType(DashboardKpi::TYPE_USER);

if (!$dashboardReady) {
	echo $this->Alerts->info(__('Your dashboard is getting ready, once its completed, it will display here.'. PHP_EOL .'This process can take up to 2 hours.'), [
		'type' => 'info'
	]);

	return true;
}
?>
<div class="row">
	<div class="col-md-12">
		<?php $i=0;foreach ($order as $model) : ?>
			<?php
			if (!isset($data[$model])) {
				trigger_error(sprintf('Your data is missing synchronization for Dashboard functionality for "%s"', $model));
				continue;
			}

			$categories = $data[$model];
			echo $this->DashboardKpi->widget('user_section', $model, $categories);
			?>
		
		<?php $i++;endforeach; ?>
	</div>
</div>

<script type="text/javascript">
	// jQuery(function($) {
	// 	$('.dashboard-sparkline').each(function () {
	// 		var config = $.extend(true, {}, Plugins.getSparklineStatboxDefaults(), $(this).data());
	// 		$(this).sparkline('html', config);
	// 	});
	// });
</script>