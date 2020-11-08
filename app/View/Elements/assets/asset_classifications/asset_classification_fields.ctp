<?php
if (isset($this->data['AssetClassification']) && !empty($edit)) {
	$requestData = array();
	foreach ($this->data['AssetClassification'] as $c) {
		$requestData[] = $c['id'];
	}

	$this->request->data['Asset']['asset_classification_id'] = $requestData;
}

if (isset($this->data['Asset']['asset_classification_id'])) {
	$this->request->data['Asset']['_selected_classification_ids'] = $this->data['Asset']['asset_classification_id'];
}
?>
<?php foreach ($classifications as $key => $classification_type) : ?>

	<?php
	echo $this->element('assets/asset_classifications/asset_classification_field_item', array(
		'classification_type' => $classification_type
	));
	?>

<?php endforeach; ?>