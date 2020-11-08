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

$selected = null;
if (isset($this->data['_selected_classification_ids'])) {
	// debug($calculationMethod);
	// on magerit it depends on the order
	if (!empty($calculationMethod) && $calculationMethod == RiskCalculation::METHOD_MAGERIT) {
		if (!empty($this->data['_selected_classification_ids'][$key])) {
			$val = $this->data['_selected_classification_ids'][$key];
			if (in_array($val, $options_ids)) {
				$selected = $val;
				unset($this->request->data['_selected_classification_ids'][$key]);
			}
		}
	}
	else {
		foreach($this->data['_selected_classification_ids'] as $cIndex => $ac) {
			if (in_array($ac, $options_ids)) {
				$selected = $ac;
				unset($this->request->data['_selected_classification_ids'][$cIndex]);
				
				break;
			}
		}
	}

}

echo $this->Form->input($model . '.' . $classModel . '.', array(
	'options' => $options,
	'label' => false,
	'div' => false,
	'empty' => __( 'Classification' ) . ': ' . $classification_type['RiskClassificationType']['name'],
	'class' => 'form-control risk-classification-select',
	'selected' => $selected,
	'id' => 'risk-classification-select-' . $key,
	'data-index-key' => $key
));
?>

<div class="alert alert-info risk-classification-info-block" id="risk-classification-select-helper-<?php echo $key; ?>"></div>
