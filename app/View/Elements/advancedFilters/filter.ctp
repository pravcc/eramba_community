<?php
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');
//debug($filter);
?>


<div class="row">
	<div class="col-md-12">
		<div class="widget box widget-form">
			<div class="widget-header">
				<h5><?= (!empty($filter['settings']['scrollable_tabs'])) ? '<br><br><br>' : '&nbsp;' ?></h5>
			</div>
			<div class="widget-content">
				<div class="tabbable box-tabs box-tabs-styled <?= (!empty($filter['settings']['scrollable_tabs'])) ? 'box-tabs-2-rows' : '' ?>">
					<ul class="nav nav-tabs" id="advanced-filter-nav-tabs">
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
						<li class="pull-right <?php echo (isset($successMessage) || isset($errorMessage))  ? 'active' : ''; ?>" id="advanced-filter-nav-tab-manage">
							<a href="#advanced-filter-tab-manage" id="advanced-filter-nav-manage" data-toggle="tab"><i class="icon-cog"></i> <?php echo __('Manage') ?></a>
						</li>
					</ul>
					<div id="advanced-filter-tabs">
						<?php
						echo $this->element(ADVANCED_FILTERS_ELEMENT_PATH . 'filterFieldsForm');
						?>
						<div class="tab-pane <?php echo (isset($successMessage) || isset($errorMessage)) ? 'active' : 'hidden'; ?>" id="advanced-filter-tab-manage">
							<div id="advanced-filter-manage-form-wrapper">
								<?php echo $this->element(ADVANCED_FILTERS_ELEMENT_PATH . 'filterList'); ?>
								<?php echo $this->element(ADVANCED_FILTERS_ELEMENT_PATH . 'manageForm'); ?>
							</div>
						</div>
					</div>
				</div>

				<div id="max-selection-size-error" class="alert alert-danger fade in hidden">
					<i class="icon-exclamation-sign"></i>
					<strong><?php echo __('Error'); ?>:</strong> <?php echo __('We cant show more than %s fields - please uncheck fields before you select new ones.', $filter['settings']['max_selection_size']); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
function validateAdvancedFilterSelection() {
	var maxSelectionSize = <?php echo $filter['settings']['max_selection_size']; ?>;

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

$('select.select2').select2();
$( ".datepicker-advanced-filters" ).datepicker({
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