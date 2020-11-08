<?php
if (!empty($data['AssetClassification']['criteria'])) {
	$text = $data['AssetClassification']['criteria'];
	echo $this->Alerts->info($text);
}
?>