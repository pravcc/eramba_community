<?php 
if (!empty($fieldData['hidden'])) {
    return;
}
?>
<?php if (empty($fieldData['filter'])) : ?>
	<div class="form-group">
		<?php
		echo $this->AdvancedFilters->getFieldLabel($fieldData['name']);
		echo $this->AdvancedFilters->getFieldShowCheckbox($field);
		?>
	</div>
<?php elseif ($fieldData['type'] == 'text') : ?>
	<div class="form-group">
		<?php
		echo $this->AdvancedFilters->getFieldLabel($fieldData['name']);
		?>
		<div class="col-md-7">
			<?php echo $this->Form->input($field, array(
				'type' => 'text',
				'label' => false,
				'div' => false,
				'class' => 'form-control advanced-filter-autoshow',
				'id' => 'advanced-filter-' . $field
			) ); ?>
		</div>
		<?php
		echo $this->AdvancedFilters->getFieldShowCheckbox($field);
		?>
	</div>
<?php elseif ($fieldData['type'] == 'select') : ?>
	<div class="form-group">
		<?php
		echo $this->AdvancedFilters->getFieldLabel($fieldData['name']);
		?>
		<div class="col-md-7">
			<?php $optionsVar = $field . '_data'; ?>
			<?php echo $this->Form->input($field, array(
				'type' => 'select',
				'empty' => (isset($fieldData['data']['empty'])) ? $fieldData['data']['empty'] : __('[ not selected ]'),
				'options' => $$optionsVar,
				'label' => false,
				'div' => false,
				'class' => 'form-control advanced-filter-autoshow',
				'id' => 'advanced-filter-' . $field
			) ); ?>
		</div>
		<?php
		echo $this->AdvancedFilters->getFieldShowCheckbox($field);
		?>
	</div>
<?php elseif ($fieldData['type'] == 'multiple_select') : ?>
	<?php
	$isNoneField = in_array($field, $filterNoneFields);
	?>
	<div class="form-group">
		<?php
		echo $this->AdvancedFilters->getFieldLabel($fieldData['name']);
		?>
		<div class="<?php echo ($isNoneField) ? 'col-md-5' : 'col-md-7'; ?>">
			<?php $optionsVar = $field . '_data'; ?>
			<?php echo $this->Form->input($field, array(
				// 'type' => 'select',
				'multiple' => true,
				'options' => $$optionsVar,
				'label' => false,
				'div' => false,
				'class' => 'select2 col-md-12 full-width-fix advanced-filter-autoshow',
				'data-form-field' => $field,
				'id' => 'advanced-filter-' . $field
			) ); ?>
		</div>
		<?php
		if ($isNoneField) {
			echo $this->AdvancedFilters->getMultiselectNoneCheckbox($field);
		}
		echo $this->AdvancedFilters->getFieldShowCheckbox($field);
		?>
	</div>
<?php elseif ($fieldData['type'] == 'date') : ?>
	<div class="form-group">
		<?php
		echo $this->AdvancedFilters->getFieldLabel($fieldData['name']);
		?>
		<div class="col-md-3">
			<?php echo $this->Form->input($field . '__comp_type', array(
				'type' => 'select',
				'options' => getComparisonTypes(true, true),
				'label' => false,
				'div' => false,
				'class' => 'form-control'
			) ); ?>
		</div>
		<div class="col-md-4">
			<?php echo $this->Form->input($field, array(
				// for possible conflict with the same input id while adding/editing and buggy datepicker because of this, we set some custom id
				'id' => 'advanced-filter-' . $field,
				'type' => 'text',
				'label' => false,
				'div' => false,
				'class' => 'form-control datepicker-advanced-filters advanced-filter-autoshow'
			) ); ?>
		</div>
		<?php
		echo $this->AdvancedFilters->getFieldShowCheckbox($field);
		?>
	</div>
<?php elseif ($fieldData['type'] == 'number') : ?>
	<div class="form-group">
		<?php
		echo $this->AdvancedFilters->getFieldLabel($fieldData['name']);
		?>
		<div class="col-md-3">
			<?php echo $this->Form->input($field . '__comp_type', array(
				'type' => 'select',
				'options' => getComparisonTypes(true),
				'label' => false,
				'div' => false,
				'class' => 'form-control',
			) ); ?>
		</div>
		<div class="col-md-4">
			<?php echo $this->Form->input($field, array(
				'type' => 'number',
				'label' => false,
				'div' => false,
				'class' => 'form-control advanced-filter-autoshow',
				'id' => 'advanced-filter-' . $field
			) ); ?>
		</div>
		<?php
		echo $this->AdvancedFilters->getFieldShowCheckbox($field);
		?>
	</div>
<?php endif; ?>

<?php //if (!$last) echo '<hr/>'; ?>