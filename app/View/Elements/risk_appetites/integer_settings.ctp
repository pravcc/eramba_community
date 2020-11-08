<?php
echo $this->FieldData->input($FieldDataCollection->risk_appetite, [
	'label' => false,
	'value' => RISK_APPETITE,
	'type' => 'number'
]);
?>