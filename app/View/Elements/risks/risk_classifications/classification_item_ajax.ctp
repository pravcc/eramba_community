<?php
App::uses('RiskCalculation', 'Model');

$options = array();
$options_ids = array();
if ( empty( $classification_type['RiskClassification'] ) ) {
	return true;
}

foreach ( $classification_type['RiskClassification'] as $risk_classification ) {
	$name = $risk_classification['name'];
	if (!empty($risk_classification['value'])) {
		$name .= ' (' . $risk_classification['value'] . ')';
	}

	$options[ $risk_classification['id'] ] = array(
		'name' => $name,
		'value' => $risk_classification['id'],
		'data-risk-value' => $risk_classification['value']
	);
	$options_ids[] = $risk_classification['id'];
}

$fieldOptions = array(
	'options' => $options,
	'label' => false,
	'div' => false,
	'empty' => __( 'Classification' ) . ': ' . $classification_type['RiskClassificationType']['name'],
	'class' => 'form-control risk-classification-select',
	'selected' => $selected,
	'id' => 'risk-classification-select-' . $key,
	'data-index-key' => $key,
	'selected' => $selected
);
$fieldOptions = array_merge($fieldOptions, [
	'data-yjs-request' => 'app/triggerRequest/.risk-classification-reload',
	'data-yjs-event-on' => 'change',
	'data-yjs-use-loader' => 'false'
]);

echo $this->Form->input($model . '.' . $classModel . '.', $fieldOptions);
?>
<br />

<?php
if ($selected && isset($classificationCriteria[$selected])) {
	$infoText = $classificationCriteria[$selected];

	// info box with a description for currently selected classification item
	echo $this->Alerts->info($infoText, [
		'class' => ['risk-classification-info-block'],
		'id' => 'risk-classification-select-helper-' . $key
	]);
}
else {
	// $infoText = __('No criteria has been specified for this classification');
}
?>
