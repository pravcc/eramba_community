<?php
App::uses('RiskAppetite', 'Model');

$value = $risk[$model]['residual_risk'];
$html = $value;
$formula = getResidualRiskFormula($risk[$model]['residual_score'], $risk[$model]['risk_score']);

if ($appetiteMethod == RiskAppetite::TYPE_THRESHOLD) {
	$formula = $risk[$model]['residual_risk_formula'];
}

if (!empty($formula)) {
	$text = $value . ' ' . $this->Icon->icon('info-sign');
	$html = $this->Html->div('bs-popover', $text, [
		'data-trigger' => 'hover',
		'data-placement' => 'top',
		'data-original-title' => __('Residual Score Calculation Formula'),
		'data-content' => $formula,
		'data-html' => true,
		'data-container' => 'body',
		'escape' => false
	]);
}

if ($appetiteMethod == RiskAppetite::TYPE_THRESHOLD) {
	$html = $this->RiskAppetites->label($risk['risk_appetite_threshold'][RiskClassification::TYPE_TREATMENT], $html);
}

echo $html;