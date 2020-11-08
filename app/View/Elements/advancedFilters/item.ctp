<?php
$displayValue = $AdvancedFiltersData->getFieldValue($filter, $field, $fieldData, $item);
?>
<td class="<?php echo (!empty($colClass)) ? $colClass : ''; ?>">
    <span class="td-inner">
    	<?php
        $forceNum = (isset($fieldData['type']) && $fieldData['type'] == 'number') ? true : false;
    	echo $this->Eramba->getEmptyValue($displayValue, $forceNum);
    	?>
    </span>
</td>