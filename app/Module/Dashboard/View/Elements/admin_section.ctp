<div class="panel-heading">
	<h5 class="panel-title">
		<!-- <i class="icon-bar-chart"></i> -->
		<?php echo $this->DashboardKpi->displayModelLabel($model); ?>
	</h5>
</div>

<?php
echo $this->element('Dashboard.category_listing', [
	'categories' => $categories,
	'model' => $model,
	'type' => 'admin'
]);
?>

<?php
// echo $this->element('Dashboard.custom_kpis', [
// 	'model' => $model,
// 	'items' => $items,
// 	'type' => 'admin'
// ]);
?>
