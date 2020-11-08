<?php
App::uses('AdvancedFilterCron', 'Model');
$prefix = (empty($advancedFilterEdit)) ? 'Create' : 'Edit';
$action = (empty($advancedFilterEdit)) ? 'saveAdvancedFilter' : 'saveAdvancedFilter/' . $activeFilter['AdvancedFilter']['id'];
$formId = 'advanced-filter-manage-form-' . $prefix;
$updateElem = (empty($advancedFilterEdit)) ? '#advanced-filters-collapse-create .panel-body' : '#advanced-filters-collapse-edit .panel-body';
// dd($action);
if (!empty($advancedFilterEdit) && empty($this->request->data['Edit'])) {
    $this->request->data['Edit'] = $activeFilter;
}
?>

<?php if (!empty($errorMessage)) : ?>
    <div class="alert alert-danger fade in">
        <i class="icon-exclamation-sign"></i>
        <strong><?php echo __('Error'); ?>:</strong> <?php echo nl2br($errorMessage); ?>
    </div>
<?php endif; ?>

<?php if (!empty($successMessage)) : ?>
    <?php
    // $filterRedirect = '';
    // if (empty($filter['settings']['url'])) {
    //     $filterRedirect = Router::url(array('controller' => $this->request->params['controller'], 'action' => 'index', '?' => $this->AdvancedFilters->getFilterQuery($activeFilter)));
    // }
    // else {
    //     $url = $filter['settings']['url'];
    //     $url['?'] = $this->AdvancedFilters->getFilterQuery($activeFilter);
    //     $filterRedirect = Router::url($url);
    // }
    $filterRedirect = $this->AdvancedFilters->getFilterRedirectUrl($activeFilter['AdvancedFilter']['id']);
    ?>
    <div class="alert alert-success fade in">
        <i class="icon-exclamation-sign"></i>
        <strong><?php echo __('Info'); ?>:</strong> <?php echo $successMessage; ?>
    </div>
    <script type="text/javascript">
        var advancedFilterSaveSuccess = true;
        var advancedFilterSaveRedirect = "<?php echo $filterRedirect; ?>";
    </script>
<?php endif; ?>

<?php echo $this->Form->create('AdvancedFilter', array(
    'url' => array(
        'plugin' => 'advanced_filters',
        'controller' => 'advancedFilters',
        'action' => $action,
        $activeFilter['AdvancedFilter']['id']
    ),
    'class' => 'advanced-filter-form-group form-horizontal',
    'id' => $formId
)); ?>

<?php echo $this->Form->input($prefix . '.AdvancedFilter.model', array(
    'type' => 'hidden',
    'value' => $filter['model']
)); ?>

<?php echo $this->Form->input($prefix . '.AdvancedFilter._json_filter_fields', array(
    'type' => 'hidden',
    'id' => 'json-filter-fields-' . $formId 
)); ?>

<div class="form-group">
    <?php
    echo $this->AdvancedFilters->getFieldLabel(__('Filter name'));
    ?>
    <div class="col-md-10">
        <?php echo $this->Form->input($prefix . '.AdvancedFilter.name', array(
            'type' => 'text',
            'label' => false,
            'div' => false,
            'class' => 'form-control',
            'error' => __('This field is rquired.')
        )); ?>
    </div>
</div>
<div class="form-group">
    <?php
    echo $this->AdvancedFilters->getFieldLabel(__('Description'));
    ?>
    <div class="col-md-10">
        <?php echo $this->Form->input($prefix . '.AdvancedFilter.description', array(
            // 'type' => 'text',
            'label' => false,
            'div' => false,
            'class' => 'form-control',
            // 'error' => __('This field is rquired.')
        )); ?>
        <?php
        $disabled = false;
        $label = __('Make filter private ?');
        if (!empty($advancedFilterEdit) && $activeFilter['AdvancedFilter']['user_id'] != $logged['id']) {
            $disabled = true;
            $label = $this->Html->tag('span', __('Make filter private ?') . ' <i class="icon-info-sign"></i>', array(
                'class' => 'bs-popover',
                'data-trigger' => 'hover',
                'data-placement' => 'right',
                'data-content' => __('Only user who created this filter can modify this setting'),
            ));
        }
        $checkbox = $this->Form->input($prefix . '.AdvancedFilter.private', array(
            'type' => 'checkbox',
            'label' => false,
            'class' => 'uniform advanced-filter-show',
            'div' => false,
            'disabled' => $disabled,
        )) . $label;
        echo $this->Html->tag('label', $checkbox, array(
            'class' => 'checkbox',
            'escape' => false
        ));

        $checkbox = $this->Form->input($prefix . '.AdvancedFilterUserSetting.default_index', array(
            'type' => 'checkbox',
            'label' => false,
            'class' => 'uniform advanced-filter-show',
            'div' => false,
        )) . __('Show as default index page ?');
        echo $this->Html->tag('label', $checkbox, array(
            'class' => 'checkbox',
            'escape' => false
        ));
        ?>
        <br>
        <?php
        $checkbox = $this->Form->input($prefix . '.AdvancedFilter.log_result_count', array(
            'type' => 'checkbox',
            'label' => false,
            'class' => 'uniform advanced-filter-show',
            'div' => false,
        )) . __('Store the number of results in a daily log ?');
        echo $this->Html->tag('label', $checkbox, array(
            'class' => 'checkbox',
            'escape' => false
        ));

        $checkbox = $this->Form->input($prefix . '.AdvancedFilter.log_result_data', array(
            'type' => 'checkbox',
            'label' => false,
            'class' => 'uniform advanced-filter-show',
            'div' => false,
        )) . __('Store full filter results in a daily log ?');
        echo $this->Html->tag('label', $checkbox, array(
            'class' => 'checkbox',
            'escape' => false
        ));

        echo $this->Html->tag('span', __('We\'ll store up to %s records.', AdvancedFilterCron::EXPORT_ROWS_LIMIT), array(
            'class' => 'help-block'
        ));
        ?>
    </div>
</div>

<div class="form-group">
    <div class="col-md-12 clearfix">
        
    </div>
</div>

<?php echo $this->Form->end(); ?>

<script type="text/javascript">
// $(".advanced-filter-form").
$('#<?php echo $formId ?>').on('submit', function() {
    // update filter fields form data as serialized value for a single field in create form
    $("#json-filter-fields-<?= $formId; ?>").val(JSON.stringify($(".advanced-filter-form").serialize()));

    App.blockUI($('#advanced-filter-modal-content'));
    $.ajax({
        url: "<?php echo Router::url(array('plugin' => 'advanced_filters', 'controller' => 'advancedFilters', 'action' => $action));//$this->request->params['controller'] ?>",
        type: 'POST',
        data: $('.advanced-filter-form-group').serialize()
    }).done(function(response) {
        $('<?php echo $updateElem; ?>').html(response);
        FormComponents.init();

        if (typeof advancedFilterSaveSuccess == "boolean" && advancedFilterSaveSuccess) {
            window.location.href = advancedFilterSaveRedirect;
        }

        App.unblockUI($('#advanced-filter-modal-content'));
    });

    return false;
});

</script>