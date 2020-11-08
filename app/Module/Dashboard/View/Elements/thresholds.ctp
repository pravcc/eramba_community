<?php
echo $this->Html->link(__('Add Threshold'), array(
	'plugin' => 'dashboard',
	'controller' => 'dashboardKpis',
	'action' => 'thresholdItem'
), array(
	'class' => 'btn btn-default',
	'id' => 'add-threshold-btn'
));

echo '<br /><br />';

$thresholdWrapper = '';
if (!empty($this->request->data['DashboardKpiThreshold'])) {
	foreach ($this->request->data['DashboardKpiThreshold'] as $thresholdKey => $threshold) {
		$thresholdWrapper .= $this->element('Dashboard.threshold_item', [
			'DashboardKpiThresholdCollection' => $DashboardKpiThresholdCollection,
			'index' => $thresholdKey,
			'threshold' => $threshold
		]);

		$index = $thresholdKey;
	}
}

echo $this->Html->div('threshold-wrapper', $thresholdWrapper);
?>
<script type="text/javascript">
	jQuery(function($) {
		var $addThresholdBtn = $("#add-threshold-btn");

		var index = "<?php echo isset($index) ? ++$index : 0; ?>";
		$("#add-threshold-btn").on("click", function(e) {
			e.preventDefault();

			var types = $("#threshold-types").val();

			var url = $(this).attr("href");
			$.ajax({
				type: "GET",
				url: url + "/" + index,
				data: {
					types: types
				},
				beforeSend: function() {
					index++;
				}
			}).done(function(data) {
				$(".threshold-wrapper").append(data);
			});
		});
	});
</script>