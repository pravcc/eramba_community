<?php
$options = array();
$options_ids = array();

if ( empty( $classification_type['AssetClassification'] ) ) {
	// continue;
	return true;
}

foreach ( $classification_type['AssetClassification'] as $asset_classification ) {
	$name = $asset_classification['name'];
	if (!empty($asset_classification['value'])) {
		$name = sprintf('%s: %s (%s)', $classification_type['AssetClassificationType']['name'], $asset_classification['name'], $asset_classification['value']);
	}

	$options[ $asset_classification['id'] ] = $name;
	$options_ids[] = $asset_classification['id'];
}

$selected = null;
if (isset($this->data['Asset']['_selected_classification_ids'])) {
	foreach($this->data['Asset']['_selected_classification_ids'] as $cIndex => $ac) {
		if (in_array($ac, $options_ids)) {
			$selected = $ac;
			unset($this->request->data['Asset']['_selected_classification_ids'][$cIndex]);
			
			break;
		}
	}
}

$classificationId = $classification_type['AssetClassificationType']['id'];

echo $this->Form->input('Asset.AssetClassification.' . $classificationId, array(//asset_classification_id][
	'options' => $options,
	'label' => false,
	'div' => false,
	'empty' => __('Classification') . ': ' . $classification_type['AssetClassificationType']['name'],
	'class' => 'select2',
	'selected' => $selected,
	'data-yjs-request' => 'app/submitForm',
	'data-yjs-event-on' => 'change|init',
	'data-yjs-target' => '#asset-classification-type-' . $classificationId,
	'data-yjs-datasource-url' => Router::url([
		'controller' => 'assetClassifications',
		'action' => 'getCriteria'
	]),
	'data-yjs-forms' => $formName,
	'data-yjs-form-fields' => 'data[Asset][AssetClassification][' . $classificationId . ']'
));
echo '<br><br>';
echo $this->Html->div('', '', [
	'id' => 'asset-classification-type-' . $classification_type['AssetClassificationType']['id']
]);