<?php
if ($classificationTypeCount >= 2) {
	echo $this->FieldData->input($FieldDataCollection->RiskClassificationType, [
		'label' => false,
		'id' => 'threshold-types'
	]);

	echo $this->Html->link(__('Add Threshold'), array(
		'plugin' => false,
		'controller' => 'riskAppetites',
		'action' => 'thresholdItem'
	), array(
		'class' => 'btn btn-default',
		'id' => 'add-threshold-btn'
	));

	echo '<br /><br />';
	
	$thresholdWrapper = $this->element('../RiskAppetites/threshold_item', [
		'RiskAppetiteThresholdCollection' => $FieldDataThresholdCollection,
		'isDefault' => true,
		'threshold' => $this->request->data['RiskAppetiteThresholdDefault']
	]);

	if (!empty($this->request->data['RiskAppetiteThreshold'])) {
		// $index = 0;
		foreach ($this->request->data['RiskAppetiteThreshold'] as $thresholdKey => $threshold) {
			$thresholdWrapper .= $this->element('../RiskAppetites/threshold_item', [
				'RiskAppetiteThresholdCollection' => $FieldDataThresholdCollection,
				'index' => $thresholdKey,
				'isDefault' => false,
				'threshold' => $threshold
			]);

			$index = $thresholdKey;

			// $index++;
		}
	}

	echo $this->Html->div('threshold-wrapper', $thresholdWrapper);
}
else {
	echo __('This setting can not be used until you define more than one Risk Classification Type.');
}
?>
<script type="text/javascript">
	jQuery(function($) {
		var $addThresholdBtn = $("#add-threshold-btn");
		var requiredTypes = <?php echo RiskAppetite::REQUIRED_COUNT; ?>;

		$("#threshold-types").on("change", function(e) {
			var value = $(this).val() || [];
			console.log(value);
			if (value.length == requiredTypes) {
				$addThresholdBtn.removeClass("disabled");
			}
			else {
				$addThresholdBtn.addClass("disabled");
			}
		}).trigger("change");

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