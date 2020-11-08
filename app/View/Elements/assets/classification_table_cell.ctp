<?php
$classifications = array();
if (!empty($assetClassificationData['joinAssets'][$assetId])) {
	foreach ($assetClassificationData['joinAssets'][$assetId] as $classificationId) {
		$c = $assetClassificationData['formattedData'][$classificationId];

		$val = $c['AssetClassificationType']['name'] . ' - ' . $c['AssetClassification']['name'];
		if (!empty($c['AssetClassification']['value'])) {
			$val .= ' (' . $c['AssetClassification']['value'] . ')';
		}

		$classifications[] = $val;
	}
}

echo implode('<br />', $classifications);
?>