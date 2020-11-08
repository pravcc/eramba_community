<?php
App::uses('AdvancedFilter', 'AdvancedFilters.Model');
$maxSelectionSize = AdvancedFilter::MAX_SELECTION_SIZE;
?>
<?php
echo $this->Form->create($filter['model'], array(
	'id' => $formName,
	'class' => 'advanced-filter-form-group advanced-filter-form form-horizontal tab-content row-border',
	'novalidate' => true
));
?>
<div class="tabbable" id="advanced-filter-form-tabs">
	<ul class="nav nav-tabs nav-tabs-top top-divided">
		<?php
		$counter = 0;
		foreach ($filter['fields'] as $key => $fieldSet) {
			if (empty($fieldSet)) {
				continue;
			}
			$class = ($counter == 0 && !isset($successMessage) && !isset($errorMessage)) ? 'active' : '';
			$link = $this->Html->link($key, '#advanced-filter-tab-' . $counter, array(
				'data-toggle' => 'tab',
				'class' => 'content-nav'
			));
			echo $this->Html->tag('li', $link, array('class' => $class));
			$counter++;
		}
		?>
		<li class="pull-right" id="advanced-filter-nav-tab-manage">
			<a href="#advanced-filter-tab-manage" id="advanced-filter-nav-manage" data-toggle="tab"><?php echo __('Manage') ?></a>
		</li>
		<li class="pull-right" id="advanced-filter-nav-tab-options">
			<a href="#advanced-filter-tab-options" id="advanced-filter-nav-options" data-toggle="tab"><?php echo __('Sorting') ?></a>
		</li>
	</ul>
	<div class="tab-content">
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
						'last' => ($fieldData == end($fieldSet)) ? true : false,
						'filter' => $filter,
					));
				}
				?>
			</div>
			<?php $counter++; ?>
		<?php endforeach; ?>

		<div class="tab-pane fade" id="advanced-filter-tab-options">
			<div id="advanced-filter-options-form-wrapper">
				<div class="col-xs-12">
					<?= $this->FieldData->input($AdvancedFilterValueCollection->_limit) ?>
					<?= $this->FieldData->input($AdvancedFilterValueCollection->_order_column) ?>
					<?= $this->FieldData->input($AdvancedFilterValueCollection->_order_direction) ?>
				</div>
			</div>
		</div>

		<div class="tab-pane fade" id="advanced-filter-tab-manage">
			<div id="advanced-filter-manage-wrapper">
				<div class="col-xs-12">
					<?= $this->FieldData->inputs($FieldDataCollection) ?>
					<?= $this->FieldData->input($AdvancedFilterUserSettingCollection->default_index) ?>
				</div>
			</div>
		</div>
	</div>
</div>
<style type="text/css">
	.datepicker-advanced-filters {
		margin-bottom: 5px;
	}
</style>
<?php echo $this->Form->end(); ?>
<script type="text/javascript">
	jQuery(function($) {
		$(".uniform").uniform();
	});
</script>
<script type="text/javascript">
function validateAdvancedFilterSelection() {
	var maxSelectionSize = <?php echo $maxSelectionSize; ?>;

	return $('.advanced-filter-show:checked').length <= maxSelectionSize;
}

$('#advanced-filter-nav-manage').on('click', function() {
	$('#advanced-filter-tabs .tab-pane').removeClass('active');
	$('#advanced-filter-tab-manage').addClass('active');
	$('#advanced-filter-tab-manage').removeClass('hidden');
});
$('#advanced-filter-nav-tabs .content-nav').on('click', function() {
	$('#advanced-filter-tab-manage').addClass('hidden');
	$('#advanced-filter-tab-manage').removeClass('active');
});

$(".advanced-filter-submit").on('click', function(e){
	$('.advanced-filter-form').submit();
	return false;
});

$('.advanced-filter-form').on('submit', function() {
	if (validateAdvancedFilterSelection()) {
		return true;
	}

	$('#max-selection-size-error').removeClass('hidden');
	return false;
});

$("select.select2").select2();
$(".datepicker-advanced-filters").datepicker({
	//defaultDate: +7,
	showOtherMonths:true,
	autoSize: true,
	dateFormat: 'yy-mm-dd'// 'dd-mm-yy'
});

// none functionality
$(".advanced-filter-none").on("change", function(e) {
	var formField = $(this).data("form-field");
	var $noneField = $("#advanced-filter-modal").find(".advanced-filter-none-value[data-form-field=" + formField +"]");
	var $selectField = $("#advanced-filter-modal").find("select.select2[multiple=multiple][data-form-field=" + formField +"]");

	if ($(this).is(":checked")) {
		$noneField.prop("disabled", false);
		$selectField.select2("enable", false);
	}
	else {
		$noneField.prop("disabled", true);
		$selectField.select2("enable", true);
	}
});

$(".advanced-filter-none").each(function(i, e) {
	$(this).trigger("change");
});

function intFilterLimit() {
	$('#advanced-filter-limit-select').val($('#advanced-filter-limit-input').val());
	$('#advanced-filter-limit-select').on('change', function() {
		$('#advanced-filter-limit-input').val($(this).val());
	});
}

function initAutoShow() {

	function autoShow($input) {
		$input.closest('.form-group').find('.advanced-filter-show').prop('checked', true);
		$.uniform.update();
	}

	$('.advanced-filter-autoshow').on('change', function() {
		autoShow($(this));
	});
	$('.advanced-filter-autoshow').on('keyup', function() {
		autoShow($(this));
	});
}

function initDateInputs() {

	function toggleInputs($input) {
		$calendarInput = $input.closest('.advanced-filter-date-inputs').find('.datepicker-advanced-filters');
		$selectInput = $input.closest('.advanced-filter-date-inputs').find('.advanced-filter-date-special-vals');

		if ($input.is(':checked')) {
			$calendarInput.prop('disabled', false);
			$calendarInput.removeClass('hidden');
			$selectInput.prop('disabled', true);
			$selectInput.addClass('hidden');
		}
		else {
			$calendarInput.val('');

			$selectInput.prop('disabled', false);
			$selectInput.removeClass('hidden');
			$calendarInput.prop('disabled', true);
			$calendarInput.addClass('hidden');
		}
	}
	
	$('.advanced-filters-use-calendar').on('change', function() {
		toggleInputs($(this));
	});
	$('.advanced-filters-use-calendar').each(function() {
		toggleInputs($(this));
	});
}

initAutoShow();
intFilterLimit();
initDateInputs();

$(function() {
	intFilterLimit();
});

//empty fields
function disableEmptyField($compInput, init) {
	var targetInput = $compInput.data('target');

	if ($compInput.val() == <?php echo AbstractQuery::COMPARISON_IS_NULL ?> || $compInput.val() == <?php echo AbstractQuery::COMPARISON_IS_NOT_NULL ?>) {
		$(targetInput).prop('disabled', true);
	}
	else {
		if (!init) {
			$(targetInput).prop('disabled', false);
			if ($compInput.data('calendar-target')) {
				$($compInput.data('calendar-target')).trigger('change');
			}
		}
	}
}

$('.advanced-filter-comp').each(function() {
	disableEmptyField($(this), true);
});
$('.advanced-filter-comp').on('change', function() {
	disableEmptyField($(this), false);
});
</script>