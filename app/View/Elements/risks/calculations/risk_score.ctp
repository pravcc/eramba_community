<?php
App::uses('RiskAppetite', 'Model');

$value = $risk[$model]['risk_score'];
$html = $value;
if (!empty($risk[$model]['risk_score_formula'])) {
	$text = $value . ' ' . $this->Icon->icon('info-sign');
	$html = $this->Html->div('bs-popover', $text, [
		'data-trigger' => 'hover',
		'data-placement' => 'top',
		'data-original-title' => __('Risk Score Calculation Formula'),
		'data-content' => $risk[$model]['risk_score_formula'],
		'data-html' => true,
		'data-container' => 'body',
		'escape' => false
	]);
}

if ($appetiteMethod == RiskAppetite::TYPE_THRESHOLD) {
	$html = $this->RiskAppetites->label($risk['risk_appetite_threshold'][RiskClassification::TYPE_ANALYSIS], $html);
}

echo $html;