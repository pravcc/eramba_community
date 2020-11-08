<?php
$AdvancedFiltersObject = $data[0];
$Item = $AdvancedFiltersObject->getData()[0];

echo $this->element('AdvancedFilters.data_table_row', [
	'Item' => $Item,
	'AdvancedFiltersObject' => $AdvancedFiltersObject
])
?>