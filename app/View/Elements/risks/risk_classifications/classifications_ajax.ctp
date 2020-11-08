<?php
App::uses('RiskClassification', 'Model');
App::uses('RiskCalculation', 'Model');


// debug($this->data);
if (isset($this->data[$model][$classModel]) && is_array($this->data[$model][$classModel])) {
	$classificationIds = $this->data[$model][$classModel];
	
}
elseif (isset($this->data[$classModel])) {
	$classificationIds = [];
	foreach ($this->data[$classModel] as $object) {
		$classificationIds[] = $object['id'];
	}
}
// debug($classificationIds);
?>

<script type="text/javascript">
jQuery(function($) {
	var changeEvent = "change.RiskCalculation.<?= '';//$element ?>";
	// $(".risk-classifications-trigger, .risk-classification-select")
	// 	.off(changeEvent)
	// 	.on(changeEvent, function(e) {
	// 		reloadClassifications($("<?= '';//$element ?>"));
	// 	});

	<?php if (isset($justLoaded) && $justLoaded) : ?>
		$(".related-risk-item-input").trigger(changeEvent);
	<?php endif; ?>

	function reloadClassifications(ele) {
		var $ele = ele;
		console.log($ele);
		$.blockUI($(ele));

		var $classificationSelect = $ele.find(".risk-classification-select");

		var relatedItemIds = [];
		$.each($(".related-risk-item-input option:selected"), function(i, e) {
			relatedItemIds.push($(e).val());
		});

		var processIds = [];
		$.each($(".related-process-input option:selected"), function(i, e) {
			processIds.push($(e).val());
		});

		var classificationIds = [];
		$.each($classificationSelect, function(i, e) {
			classificationIds.push($(e).val());
		});

		var buChange = 0;
		if ($(this).is("#risk-bu-id")) {
			buChange = 1;
		}

		var postData = {
			model: "<?= $model ?>",
			type: "<?= $type ?>",
			element: "<?= '';//$element ?>",
			relatedItemIds: JSON.stringify(relatedItemIds),
			processIds: JSON.stringify(processIds),
			classificationIds: JSON.stringify(classificationIds),
			buChange: buChange
		};

		$.ajax({
			type: "GET",
			url: "<?= Router::url(['controller' => controllerFromModel($model), 'action' => 'processClassifications']) ?>",
			data: postData
		})
		.done(function(data){
			$ele.html(data);
			// Plugins.init();
			$.unblockUI($ele);

			var riskScore = $("#risk_score_scope").val();
			var riskScoreMath = $("#risk_score_math_scope").val();

			<?php if ($type == RiskClassification::TYPE_ANALYSIS) : ?>
				updateRiskScoreField(riskScore, riskScoreMath);
			<?php endif; ?>

			<?php if ($model == 'BusinessContinuity') : ?>
				var rpd = $("#rpd_scope").val();
				var mto = $("#mto_scope").val();
				var rto = $("#rto_scope").val();
				var process = $("#process_scope").val();

				$("#rpd-input").val(rpd);
				$("#mto-input").val(mto);
				$("#rto-input").val(rto);

				if (buChange) {
					$("#BusinessContinuityProcess").select2('data', JSON.parse(process));
				}
			<?php endif; ?>

			// App.init();
			// Eramba.Ajax.UI.attachEvents();
			$("#main-content").trigger("Eramba.Risk.processClassifications");
		});

		function updateRiskScoreField(riskScore, math) {
			var riskAppetite = <?= Configure::read('Eramba.Settings.RISK_APPETITE') ?>

			var $riskScoreInput = $("#risk-score-input");
			var $riskScoreMath = $("#risk-score-math");

			$riskScoreInput.val(riskScore);
			var riskMath = '';
			if (math) {
				riskMath = "<br />";
				riskMath += "<div class='alert alert-info'>";
				riskMath += math;
				riskMath += "</div>";
			}
			$riskScoreMath.html(riskMath);

			if (riskScore > riskAppetite) {
				if (!$("#risk-score-group").hasClass("has-risk-error")) {
					$("#risk-score-group").addClass("has-risk-error");
				}
			}
			else {
				$("#risk-score-group").removeClass("has-risk-error");
			}
		}
	}
});
</script>

<?php
// debug($classificationsNotPossible);
// debug($classificationIds);
// debug($this->request->data);
$conds = isset($classificationsNotPossible) && $classificationsNotPossible;
// $conds = $conds || !isset($classificationIds);
// $conds &= empty($this->request->data);
if ($conds) {
	echo $this->Alerts->danger(__('We are not able to classify this risk until you have selected one or more inputs for this risk.'));

	return false;
}

// if magerit is not possible to manage at the moment
$mageritImpossible = !empty($calculationMethod);
$mageritImpossible &= $calculationMethod == RiskCalculation::METHOD_MAGERIT;
$mageritImpossible &= isset($isMageritPossible) && !$isMageritPossible;
if ($mageritImpossible) {
	echo $this->Alerts->danger(__('While using Magerit as a calculation method you need to ensure that:<ul><li>All asset classifications have a value (Asset Management / Settings / Classifications)</li><li>All assets (Asset Management / Asset Identification) must be classified</li></ul><br />This message shows as it seems one or both conditions seem to have issues.'));

	return false;
}

// if (!isset($classificationIds)) {
// 	return false;
// }

if ($this->Form->isFieldError($model . '.' . $classModel)) {
	echo $this->Html->div('validation-error-label', $this->Form->error($model . '.' . $classModel));
}

if (!empty($classifications)) : ?>
	<?php foreach ($classifications as $key => $classification_type) : ?>
		<?php
		$selected = null;
		if (isset($classificationIds) && isset($classificationIds[$key])) {
			$selected = $classificationIds[$key];
		}

		$fieldOptions = [
			'classification_type' => $classification_type,
			'key' => $key,
			'model' => $model,
			'classModel' => $classModel,
			'selected' => $selected,
			'classificationCriteria' => $classificationCriteria
		];
		?>

		<div class="row form-group">
			<?php if (!empty($calculationMethod) && $calculationMethod == RiskCalculation::METHOD_MAGERIT) : ?>
				<div class="col-md-12">
					<div>
						<?php
						echo $this->element('risks/risk_classifications/classification_item_ajax', $fieldOptions);
						?>
					</div>
					<div class="asset-max-val">
						<?php
						// ddd($otherData);
						if (isset($otherData['assetMaxVal'])) {
							$val = null;
							if (!empty($otherData['assetMaxVal'][$key]['maxValue'])) {
								$val = $otherData['assetMaxVal'][$key]['maxValue'];
							}

							$val = $this->Ux->text($val);
						
							$maxValText = __('From the assets in the scope of this risk, the highest value for %s is %s (%s)');
						
							echo $this->Alerts->info(sprintf(
								$maxValText,
								$otherData['assetMaxVal'][$key]['assetType'],
								$val,
								$otherData['assetMaxVal'][$key]['name']
							));
						}
						?>
					</div>
					<div class="math-classification math-item">
						<?php
						if (!empty($otherData['classificationsPartMath'][$key])) {
							$text = $otherData['classificationsPartMath'][$key];
							
							echo $this->Alerts->info($this->Ux->text($text));
						}
						?>
					</div>
				</div>
			<?php else : ?>
				<div class="col-md-12">
					<?php
					echo $this->element('risks/risk_classifications/classification_item_ajax', $fieldOptions);
					?>
				</div>
			<?php endif; ?>
		</div>

	<?php endforeach; ?>
<?php else : ?>
	<?php
	echo $this->Alerts->info(__('We haven\'t seen any classification method defined, go to Settings / Classification and define a classification criteria for your risks.'));
	?>
<?php endif; ?>

<?php
// final math
$finalMath = null;

// debug($otherData);
if (!empty($otherData['classificationsSecondPartMath'])) {
	$finalMath = $this->Alerts->info($this->Ux->text($otherData['classificationsSecondPartMath']), [
	]);

	$finalMath = $this->Html->div('math-item', $finalMath, [
		'id' => 'classification-final-math',
		'escape' => false
	]);
}
?>

<?php if (isset($specialClassificationTypeData)) : ?>
	<div class="row form-group">
		<div class="col-md-12">
			<?php
			if (isset($classificationIds) && isset($classificationIds[$key+1])) {
				$selected = $classificationIds[$key+1];
			}

			echo $this->element('risks/risk_classifications/classification_item_ajax', array(
				'classification_type' => $specialClassificationTypeData,
				'key' => $key+1,
				'model' => $model,
				'classModel' => $classModel,
				'selected' => $selected,
				'classificationCriteria' => $classificationCriteria
			));
			?>
		</div>
		<div class="col-md-7">
			<?= $finalMath; ?>
		</div>
	</div>
<?php else : ?>
	<?= $finalMath; ?>
<?php endif; ?>

<?php //echo $riskCalculationMath; ?>

<?php if ($appetiteMethod == RiskAppetite::TYPE_THRESHOLD) : ?>
<div class="risk-appetite-threshold-note">
	<div class='alert threshold-alert label-' style='background-color:<?= $riskAppetiteThreshold['data']['color']; ?>'>
		<strong><?= $riskAppetiteThreshold['data']['title'] ?></strong><br />
		<?= $riskAppetiteThreshold['data']['description'] ?>
	</div>
</div>
<?php endif; ?>

<?php
// fields that should update outside of the ajax scope

echo $this->Form->input('risk_score_scope', [
	'type' => 'hidden',
	'value' => $riskScore
]);

echo $this->Form->input('risk_score_math_scope', [
	'type' => 'hidden',
	'value' => $riskCalculationMath
]);

if ($model == 'BusinessContinuity' && $classModel == 'RiskClassification') {
	echo $this->Form->input('rpd_scope', [
		'id' => 'rpd_scope',
		'type' => 'hidden',
		'value' => $rpd
	]);

	echo $this->Form->input('mto_scope', [
		'id' => 'mto_scope',
		'type' => 'hidden',
		'value' => $mto
	]);

	echo $this->Form->input('rto_scope', [
		'id' => 'rto_scope',
		'type' => 'hidden',
		'value' => $rto
	]);

	echo $this->Form->input('process_scope', [
		'id' => 'process_scope',
		'type' => 'hidden',
		'value' => json_encode($process)
	]);
}
?>
<?php if ($model == 'BusinessContinuity' && $classModel == 'RiskClassification') : ?>
<script>
	var rpd = $("#rpd_scope").val();
	var mto = $("#mto_scope").val();
	var rto = $("#rto_scope").val();
	var process = $("#process_scope").val();

	$("#rpd-input").val(rpd);
	$("#mto-input").val(mto);
	$("#rto-input").val(rto);

	// if (buChange) {
	// 	$("#BusinessContinuityProcess").select2('data', JSON.parse(process));
	// }
</script>
<?php endif; ?>
