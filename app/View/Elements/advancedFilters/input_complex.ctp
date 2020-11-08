<?php
App::uses('AdvancedFilter', 'Model');
App::uses('TextQuery', 'Lib/AdvancedFilters/Query');
App::uses('DateQuery', 'Lib/AdvancedFilters/Query');
App::uses('NumberQuery', 'Lib/AdvancedFilters/Query');
App::uses('SelectQuery', 'Lib/AdvancedFilters/Query');
App::uses('MultipleSelectQuery', 'Lib/AdvancedFilters/Query');
App::uses('FilterField', 'AdvancedFilters.Lib');
App::uses('Inflector', 'Utility');

if (!empty($fieldData['hidden'])) {
    return;
}

$FilterField = new FilterField(ClassRegistry::init($filterModel), $field, []);
?>
<?php if (empty($fieldData['filter'])) : ?>
    <div class="form-group">
        <?php
        echo $this->AdvancedFilters->getFieldLabel($FilterField->getLabel());
        echo $this->AdvancedFilters->getFieldShowCheckbox($field);
        ?>
    </div>
<?php elseif ($fieldData['type'] == 'text') : ?>
    <div class="form-group">
        <?php
        echo $this->AdvancedFilters->getFieldLabel($FilterField->getLabel());
        ?>
        <div class="col-md-3">
            <?php echo $this->Form->input($filter['model'] . '.' . $field . '__comp_type', array(
                'type' => 'select',
                'options' => TextQuery::getComparisonTypes(isset($fieldData['filter']['comparisonTypes']) ? $fieldData['filter']['comparisonTypes'] : null),
                'label' => false,
                'div' => false,
                'class' => 'form-control select2 advanced-filter-comp advanced-filter-autoshow',
                'data-target' => '#advanced-filter-' . $field,
            ) ); ?>
        </div>
        <div class="col-md-4">
            <?php echo $this->Form->input($filter['model'] . '.' . $field, array(
                'type' => 'text',
                'label' => false,
                'div' => false,
                'data-form-field' => $field,
                'class' => 'form-control advanced-filter-autoshow',
                'id' => 'advanced-filter-' . $field,
                'maxLength' => 524288 //fix if field inherit DB int column length
            ) ); ?>
        </div>
        <?php
        echo $this->AdvancedFilters->getFieldShowCheckbox($field);
        ?>
    </div>
<?php elseif ($fieldData['type'] == 'select' || $fieldData['type'] == 'object_status') : ?>
    <div class="form-group">
        <?php
        echo $this->AdvancedFilters->getFieldLabel($FilterField->getLabel());
        ?>
        <div class="col-md-3">
            <?php echo $this->Form->input($filter['model'] . '.' . $field . '__comp_type', array(
                'type' => 'select',
                'options' => SelectQuery::getComparisonTypes(isset($fieldData['filter']['comparisonTypes']) ? $fieldData['filter']['comparisonTypes'] : null),
                'label' => false,
                'div' => false,
                'class' => 'form-control select2 advanced-filter-comp advanced-filter-autoshow',
                'data-target' => '#advanced-filter-' . $field,
            ) ); ?>
        </div>
        <div class="col-md-4">
            <?php $optionsVar = Inflector::slug($field) . '_data'; ?>
            <?php echo $this->Form->input($filter['model'] . '.' . $field, array(
                'type' => 'select',
                'empty' => (isset($fieldData['data']['empty'])) ? $fieldData['data']['empty'] : __('[ not selected ]'),
                'options' => $$optionsVar,
                'label' => false,
                'div' => false,
                'data-form-field' => $field,
                'class' => 'form-control select2 advanced-filter-autoshow',
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
        echo $this->AdvancedFilters->getFieldLabel($FilterField->getLabel());
        ?>
        <div class="col-md-3">
            <?php echo $this->Form->input($filter['model'] . '.' . $field . '__comp_type', array(
                'type' => 'select',
                'options' => MultipleSelectQuery::getComparisonTypes(isset($fieldData['filter']['comparisonTypes']) ? $fieldData['filter']['comparisonTypes'] : null),
                'label' => false,
                'div' => false,
                'class' => 'form-control select2 advanced-filter-comp advanced-filter-autoshow',
                'data-target' => '#advanced-filter-' . $field,
            ) ); ?>
        </div>
        <div class="<?php echo ($isNoneField) ? 'col-md-4' : 'col-md-4'; ?>">
            <?php $optionsVar = Inflector::slug($field) . '_data'; ?>
            <?php echo $this->Form->input($filter['model'] . '.' . $field, array(
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
        // if ($isNoneField) {
        //  echo $this->AdvancedFilters->getMultiselectNoneCheckbox($field);
        // }

        echo $this->AdvancedFilters->getFieldShowCheckbox($field);
        ?>
    </div>
<?php elseif ($fieldData['type'] == 'date') : ?>
    <div class="form-group">
        <?php
        echo $this->AdvancedFilters->getFieldLabel($FilterField->getLabel());
        ?>
        <div class="col-md-3">
            <?php echo $this->Form->input($filter['model'] . '.' . $field . '__comp_type', array(
                'type' => 'select',
                'options' => DateQuery::getComparisonTypes(isset($fieldData['filter']['comparisonTypes']) ? $fieldData['filter']['comparisonTypes'] : null),
                'label' => false,
                'div' => false,
                'class' => 'form-control select2 advanced-filter-comp advanced-filter-autoshow',
                'data-target' => '#advanced-filter-' . $field . ', #advanced-filter-special-' . $field . ', #advanced-filter-use-calendar-' . $field,
                'data-calendar-target' => '#advanced-filter-use-calendar-' . $field,
            ) ); ?>
        </div>
        <div class="col-md-4 advanced-filter-date-inputs">
            <?php
            // debug($filter[]);
            echo $this->Form->input($filter['model'] . '.' . $field, array(
                // for possible conflict with the same input id while adding/editing and buggy datepicker because of this, we set some custom id
                'id' => 'advanced-filter-' . $field,
                'type' => 'text',
                'label' => false,
                'div' => false,
                'data-form-field' => $field,
                'class' => 'form-control datepicker-advanced-filters advanced-filter-autoshow',
            ));

            echo $this->Form->input($filter['model'] . '.' . $field, array(
                'id' => 'advanced-filter-special-' . $field,
                'type' => 'select',
                'options' => DateQuery::getSpecialValues(),
                'empty' => __('[ not selected ]'),
                'label' => false,
                'div' => false,
                'data-form-field' => $field,
                'class' => 'form-control advanced-filter-date-special-vals advanced-filter-autoshow'
            )); 
            
            echo $this->Form->input($filter['model'] . '.' . $field . '__use_calendar', array(
                'id' => 'advanced-filter-use-calendar-' . $field,
                'type' => 'checkbox',
                'label' => __('Use Calendar'),
                'class' => 'uniform advanced-filters-use-calendar',
                'default' => true
            )); ?>
        </div>
        <?php
        echo $this->AdvancedFilters->getFieldShowCheckbox($field);
        ?>
    </div>
<?php elseif ($fieldData['type'] == 'number') : ?>
    <div class="form-group">
        <?php
        echo $this->AdvancedFilters->getFieldLabel($FilterField->getLabel());
        ?>
        <div class="col-md-3">
            <?php echo $this->Form->input($filter['model'] . '.' . $field . '__comp_type', array(
                'type' => 'select',
                'options' => NumberQuery::getComparisonTypes(isset($fieldData['filter']['comparisonTypes']) ? $fieldData['filter']['comparisonTypes'] : null),
                'label' => false,
                'div' => false,
                'class' => 'form-control advanced-filter-comp advanced-filter-autoshow select2',
                'data-target' => '#advanced-filter-' . $field,
            ) ); ?>
        </div>
        <div class="col-md-4">
            <?php echo $this->Form->input($filter['model'] . '.' . $field, array(
                'type' => 'number',
                'label' => false,
                'div' => false,
                'class' => 'form-control advanced-filter-autoshow',
                'data-form-field' => $field,
                'id' => 'advanced-filter-' . $field
            ) ); ?>
        </div>
        <?php
        echo $this->AdvancedFilters->getFieldShowCheckbox($field);
        ?>
    </div>
<?php endif; ?>

<?php //if (!$last) echo '<hr/>'; ?>