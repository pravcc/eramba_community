<?php

echo $this->WorkflowConditionFields->comparisonInput([$FieldDataCondsCollection->comparison_type, $index], $FieldDataValueEntry);

$FieldDataValueEntry->actsAs($FieldDataCondsCollection->value);
echo $this->WorkflowConditionFields->input([$FieldDataValueEntry, $index], [
	'label' => ['text'=>$FieldDataValueEntry->getLabel()]
]);
// echo $this->WorkflowConditionFields->input([$FieldDataCondsCollection->value, $index]);
?>