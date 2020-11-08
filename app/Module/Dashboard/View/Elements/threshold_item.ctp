<?php
App::uses('DashboardKpiThreshold', 'Dashboard.Model');
?>
<div class="panel panel-flat threshold-item" id="threshold-item-<?= $index; ?>">
	<div class="panel-heading">
		<h4 class="panel-title"><?= __('Threshold'); ?></h4>

		<div class="toolbar no-padding pull-right">
			<div class="btn-group">
				<span class="btn btn-xs circular-charts-reload threshold-remove-item">
					<i class="icon-x"></i> 
					<?php echo __('Remove'); ?>
				</span>
			</div>
		</div>
	</div>
	<div class="panel-body">
		<div class="form-horizontal">
			<?php
			$colorpickerOptions = [
				'data-field-type' => 'color',
				'between' => '<div class="col-md-10"><div class="input-group">',
				'after' => '<span class="input-group-addon input-colorpicker"><i class="icon-cog"></i></span></div>' . $this->FieldData->help($DashboardKpiThresholdCollection->color) . $this->FieldData->description($DashboardKpiThresholdCollection->color) . '</div>',
				'type' => 'text'
			];

			if (isset($index) && $this->Form->isFieldError('threshold_' . $index)) {
				echo $this->Form->error('threshold_' . $index);
			}

			// $i=0;foreach ($classificationOptions as $index2 => $classificationOption) {
			// 	$value = null;
			// 	if (!empty($threshold['RiskAppetiteThresholdClassification'][$i]['risk_classification_id'])) {
			// 		$value = $threshold['RiskAppetiteThresholdClassification'][$i]['risk_classification_id'];
			// 	}

			// 	echo $this->FieldData->input([$RiskAppetiteThresholdClassificationCollection->risk_classification_id, $index], [
			// 		'options' => $classificationOption,
			// 		'inputName' => 'RiskAppetiteThreshold.' . $index . '.RiskAppetiteThresholdClassification.' . $i . '.risk_classification_id',
			// 		'value' => $value,
			// 		'data-field-type' => 'risk-classification'
			// 	]);

			// 	$i++;
			// }

			echo $this->FieldData->input([$DashboardKpiThresholdCollection->type, $index], [
				'class' => ['dashboard-kpi-threshold-type']
			]);
			echo $this->FieldData->input([$DashboardKpiThresholdCollection->title, $index]);
			echo $this->FieldData->input([$DashboardKpiThresholdCollection->description, $index]);
			echo $this->FieldData->input([$DashboardKpiThresholdCollection->color, $index], $colorpickerOptions);

			?>
			<div class="range-type-wrapper">
				<?php
				echo $this->FieldData->input([$DashboardKpiThresholdCollection->min, $index]);
				echo $this->FieldData->input([$DashboardKpiThresholdCollection->max, $index]);
				?>
			</div>
			<div class="percentage-type-wrapper">
				<?php
				echo $this->FieldData->input([$DashboardKpiThresholdCollection->percentage, $index], [
					'min' => 0,
					'max' => 100
				]);
				?>
			</div>
			<?php
			// echo $this->Form->input('RiskAppetiteThreshold.' . $index . '.type', [
			// 	'type' => 'hidden',
			// 	'value' => RiskAppetiteThreshold::TYPE_GENERAL
			// ]);
			?>
		</div>
	</div>
</div>
<script type="text/javascript">
	jQuery(function($) {
		$(".threshold-item .dashboard-kpi-threshold-type").on("change", function(e) {
			var $thresholdItem = $(this).closest(".threshold-item");
			var $rangeWrapper = $thresholdItem.find(".range-type-wrapper");
			var $percentageWrapper = $thresholdItem.find(".percentage-type-wrapper");

			var value = $(this).val();

			if (value == <?= DashboardKpiThreshold::TYPE_RANGE; ?>) {
				$rangeWrapper.show();
				$percentageWrapper.hide();
			}

			if (value == <?= DashboardKpiThreshold::TYPE_CHANGE; ?>) {
				$rangeWrapper.hide();
				$percentageWrapper.show();	
			}
		}).trigger("change");

		$("#threshold-item-<?= $index; ?> .select2").each(function(i, e) {
			if ($(this).data('select2') == undefined) {
				$(e).select2();
			}
		});

		$("[data-field-type=color]")
			.colorpicker({
				useAlpha: false,
				format: "hex"
			})
			.on("colorpickerChange", function(e) {
				changeColor($(this), e.color.toString());
		});

		$("[data-field-type=color]").each(function(i, e) {
			changeColor($(e), $(e).val());
		});

		function changeColor(ele, color) {
			if (color.length) {
				ele.parent().find(".input-group-addon.input-colorpicker").css('background-color', color);
			}
		}

		$(".threshold-remove-item").on("click", function(e) {
			$(this).closest(".threshold-item").fadeOut(500, function(e) {
				$(this).remove();
			});
		});
	});
</script>