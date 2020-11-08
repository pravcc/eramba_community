<?php 
echo $this->Form->input('advanced_filter', array(
	'type' => 'hidden',
	'value' => true
));

if (!empty($activeFilterId)) {
	echo $this->Form->input('advanced_filter_id', array(
		'type' => 'hidden',
		'value' => $activeFilterId
	));
}

if (!empty($filter['settings']['active_filter'])) {
	echo $this->Form->input(ADVANCED_FILTER_PARAM, array(
		'type' => 'hidden',
		'value' => $filter['settings']['active_filter']
	));
}
?>
<?php $counter = 0; ?>
<?php foreach ($filter['fields'] as $key => $fieldSet) : ?>
	<?php if (empty($fieldSet)) continue; ?>
	<div class="tab-pane fade in <?php echo ($counter == 0 && !isset($successMessage) && !isset($errorMessage)) ? 'active' : ''; ?>" id="advanced-filter-tab-<?php echo $counter ?>">
		<?php
		foreach ($fieldSet as $field => $fieldData) {
			$elem = (isset($fieldData['filter']['method']) && $fieldData['filter']['method'] == 'findComplexType') ? 'input_complex' : 'input';
			echo $this->element(ADVANCED_FILTERS_ELEMENT_PATH . $elem, array(
				'field' => $field,
				'fieldData' => $fieldData,
				'last' => ($fieldData == end($fieldSet)) ? true : false
			));
		}
		?>
	</div>
	<?php $counter++; ?>
<?php endforeach; ?>

<script type="text/javascript">
	jQuery(function($) {
		$('select.select2').select2();
		$( ".datepicker-advanced-filters" ).datepicker({
			//defaultDate: +7,
			showOtherMonths:true,
			autoSize: true,
			dateFormat: 'yy-mm-dd'// 'dd-mm-yy'
		});
	});
</script>