<div class="widget-header">
	<h4>
		<i class="icon-bar-chart"></i>
		<?php echo $this->DashboardKpi->displayModelLabel($model); ?>
	</h4>
</div>

<?php
echo $this->element('Dashboard.category_listing', [
	'categories' => $categories,
	'model' => $model,
	'type' => 'user'
]);
?>