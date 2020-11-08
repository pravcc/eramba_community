<?php
App::uses('RiskClassification', 'Model');
App::uses('RiskCalculation', 'Model');

// if magerit is not possible to manage at the moment
if (!empty($calculationMethod) && $calculationMethod == RiskCalculation::METHOD_MAGERIT && !$isMageritPossible) {
	echo $this->Ux->getAlert(__('While using Magerit as a calculation method you need to ensure that:<ul><li>All asset classifications have a value (Asset Management / Settings / Classifications)</li><li>All assets (Asset Management / Asset Identification) must be classified</li></ul><br />This message shows as it seems one or both conditions seem to have issues.'), ['type' => 'danger']);

	return false;
}

// classification model to determine which type of classification we are doing
$classModel = 'RiskClassification';
if ($type == RiskClassification::TYPE_TREATMENT) {
	$classModel = 'RiskClassificationTreatment';
}

if (isset($this->data[$classModel])) {
	$requestData = array();
	foreach ($this->data[$classModel] as $c) {
		$requestData[] = $c['id'];
	}

	$this->request->data[$model][$classModel] = $requestData;
}

if (isset($this->data[$model][$classModel])) {
	$this->request->data['_selected_classification_ids'] = $this->data[$model][$classModel];
}
?>
<?php if (!empty($classifications)) : ?>
	<?php foreach ($classifications as $key => $classification_type) : ?>

		<div class="row form-group">
			<?php if (!empty($calculationMethod) && $calculationMethod == RiskCalculation::METHOD_MAGERIT) : ?>
				<label class="col-md-3 control-label asset-max-val"></label>
				<div class="col-md-5">
					<?php
					echo $this->element('risks/risk_classifications/risk_classification_field_item', array(
						'classification_type' => $classification_type,
						'key' => $key,
						'model' => $model,
						'classModel' => $classModel
					));
					?>
				</div>

				<div class="col-md-3 col-md-offset-1 math-classification math-item">
				</div>
			<?php else : ?>
				<div class="col-md-12">
					<?php
					echo $this->element('risks/risk_classifications/risk_classification_field_item', array(
						'classification_type' => $classification_type,
						'key' => $key,
						'model' => $model,
						'classModel' => $classModel
					));
					?>
				</div>
			<?php endif; ?>
		</div>

	<?php endforeach; ?>
<?php else : ?>
	<?php
	echo $this->Ux->getAlert(__('We haven\'t seen any classification method defined, go to Settings / Classification and define a classification criteria for your risks.'), ['type' => 'info']);
	?>
<?php endif; ?>

<?php if (isset($specialClassificationTypeData)) : ?>
	<div class="row form-group">
		<div class="col-md-5">
			<?php
			echo $this->element('risks/risk_classifications/risk_classification_field_item', array(
				'classification_type' => $specialClassificationTypeData,
				'key' => $key+1,
				'model' => $model,
				'classModel' => $classModel
			));
			?>
		</div>
		<div class="col-md-7">
			<div id="classification-final-math" class="math-item"></div>
		</div>
	</div>
<?php else : ?>
	<div id="classification-final-math" class="math-item"></div>
<?php endif; ?>

<div class="risk-appetite-threshold-note"></div>

<?php
if ($this->Form->isFieldError($model . '.' . $classModel)) {
	echo $this->Form->error($model . '.' . $classModel);
}
?>

<script type="text/javascript">
jQuery(function($) {
	var $wrapper = $("<?php echo $element; ?>");
	$wrapper.add($(".risk-classifications-trigger"))
	// $(".risk-classification-select, #risk-asset-id")
		.off("change.RiskCalculation.Eramba")
		.on("change.RiskCalculation.Eramba", function(e) {
			$wrapper = $("<?php echo $element; ?>");
			var $classificationSelect = $wrapper.find(".risk-classification-select");
			var $assetMaxVal = $wrapper.find(".asset-max-val");
			var $mathClassification = $wrapper.find(".math-classification");
			var $classificationFinalMath = $wrapper.find("#classification-final-math");
			var $riskScoreInput = $("#risk-score-input");
			var $riskScoreMath = $("#risk-score-math");
			var $riskAppetiteNote = $wrapper.find(".risk-appetite-threshold-note");
console.log($riskScoreInput);
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
				var p = $(e).closest(".row");
				p.find(".max-vals").html();
				classificationIds.push($(e).val());
			});

			var buChangeVal = 0;
			if ($(this).is("#risk-bu-id")) {
				buChangeVal = 1;
			}

			var postData = {
				relatedItemIds: JSON.stringify(relatedItemIds),
				processIds: JSON.stringify(processIds),
				classificationIds: JSON.stringify(classificationIds),
				buChange: buChangeVal
			};

			$.ajax({
				url: "<?= Router::url(['controller' => controllerFromModel($model), 'action' => 'calculateRiskScoreAjax']) ?>",
				type: "POST",
				dataType: "JSON",
				data: postData,
				beforeSend: function( xhr ) {
					App.blockUI($(".risk-classification-fields"));
				}
			})
			.done(function(data) {
				$assetMaxVal.empty();
				$mathClassification.empty();
				$classificationFinalMath.empty();

				<?php if ($element == '#risk-classification-analysis') : ?>
					$riskScoreInput.val("");
					$riskScoreInput.val(data.riskScore);
					var math = '';
					if (data.riskCalculationMath && data.riskCalculationMath.length) {
						math = "<br />";
						math += "<div class='alert alert-info'>";
						math += data.riskCalculationMath;
						math += "</div>";
					}
					$riskScoreMath.html(math);
				<?php endif; ?>

				if (data.otherData.assetMaxVal && data.otherData.assetMaxVal.length) {
					var maxVal;
					$assetMaxVal.each(function(i, e) {
						if (typeof data.otherData.assetMaxVal[i] != "undefined") {
							maxVal = data.otherData.assetMaxVal[i].maxValue;
							if (data.otherData.assetMaxVal[i].maxValue == null) {
								maxVal = '-';
							}
							$(e).html(data.otherData.assetMaxVal[i].assetType + ": " + maxVal);
						}
					});
				}

				if (data.otherData.classificationsPartMath && data.otherData.classificationsPartMath.length) {
					var math_part;
					$mathClassification.each(function(i, e) {
						if (typeof data.otherData.classificationsPartMath[i] != "undefined") {
							if (data.otherData.classificationsPartMath[i]) {
								math_part = "<div class='alert alert-info'>";
								math_part += data.otherData.classificationsPartMath[i];
								math_part += "</div>";

								$(e).html(math_part);
							}
						}
					});
				}
				else {
					$(".risk-classification-group .math").empty();
				}

				if (data.otherData.classificationsSecondPartMath) {
					var math = "<div class='alert alert-info'>";
					math += data.otherData.classificationsSecondPartMath;
					math += "</div>";
					$classificationFinalMath.html(math);
				}
				else {
					$classificationFinalMath.empty();
				}
				
				<?php if ($type == RiskClassification::TYPE_ANALYSIS) : ?>
					if (data.riskScore > data.riskAppetite) {
						if (!$("#risk-score-group").hasClass("has-risk-error")) {
							$("#risk-score-group").addClass("has-risk-error");
						}
					}
					else {
						$("#risk-score-group").removeClass("has-risk-error");
					}
				<?php endif; ?>

				if (typeof data.rpd !== 'undefined') {
					$("#rpd-input").val(data.rpd);
				}
				if (typeof data.mto !== 'undefined') {
					$("#mto-input").val(data.mto);
				}
				if (typeof data.rto !== 'undefined') {
					$("#rto-input").val(data.rto);
				}

				if (typeof data.buChange !== 'undefined' && data.buChange) {
					$("#BusinessContinuityProcess").select2('data', data.process);
				}

				$.each($classificationSelect, function(i, e) {
					var key = $(e).data("index-key");
					var $helpBlock = $wrapper.find("#risk-classification-select-helper-" + key);
					var classificationSelected = $(e).val();

					if (!classificationSelected) {
						$helpBlock.text("<?php echo __('No criteria has been specified for this classification'); ?>");
					}
					else {
						$.each(data.classificationCriteria, function(ii, ee) {
							if (ii == classificationSelected) {
								$helpBlock.text(ee);
							}
						});
					}
				});	

				<?php if ($appetiteMethod == RiskAppetite::TYPE_THRESHOLD) : ?>
					var appetiteNote = "<div class='alert threshold-alert label-' style='background-color:" + data.riskAppetiteThreshold.data.color + "'>";
					appetiteNote += "<strong>" + data.riskAppetiteThreshold.data.title + "</strong><br />";
					appetiteNote += data.riskAppetiteThreshold.data.description;
					appetiteNote += "</div>";
					$riskAppetiteNote.html(appetiteNote);
				<?php endif; ?>

				App.unblockUI($(".risk-classification-fields"));
			});
		});

	$(".risk-classifications-trigger").trigger("change.RiskCalculation.Eramba");
});
</script>
