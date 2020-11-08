<?php
$this->Html->css('ImportTool.styles');

if ($ImportToolData->hasNonUtf8Character()) {
	echo $this->Alerts->warning(__('The file you are trying to import is not UTF-8 compatible, please export the CSV file again this time using UTF-8 compatibility and try the upload again.'));
}

//issue warning
if (!$ImportToolData->isImportable()) {
	echo $this->Alerts->warning(__('We found some problems on the CSV file you just uploaded.'));
}

$model = $ImportToolData->getModel()->alias;

echo $this->Form->create('ImportTool', [
	'data-yjs-form' => $formName
]);

$allCheckbox = $this->Form->input('ImportTool.checkAll', [
	'type' => 'checkbox',
	'label' => false,
	'div' => false,
	'class' => 'uniform',
	'checked' => true,
	'id' => 'check-all-checkbox'
]);

$table = $this->Tables->table(null, ['class' => 'table table-hover table-striped import-tool-preview-table']);

//TABLE HEADER
$tableHeaderRow = $table->header()->row();

//checkbox select all
$tableHeaderRow->column($allCheckbox);

//set import fields headers
foreach ($ImportToolData->getArguments() as $arg) {
	$label = $arg->getLabel();

	$tooltip = $arg->getHeaderTooltip();
	if (!empty($tooltip)) {
		$label = $this->Popovers->auto($label, $tooltip, $label);
	}

	$tableHeaderRow->column($label);
}

//status column
$tableHeaderRow->column(['content' => __('Status'), 'class' => 'text-center']);

//TABLE BODY
foreach ($ImportToolData->getData() as $row => $ImportToolRow) {
	$tableRow = $table->body()->row();

	$rowStructureErrors = $ImportToolRow->getStructureErrors();
	$missingStructureCells = $ImportToolRow->getMissingStructureCells();

	$checkboxClass = ['uniform', 'import-row-checkbox'];
	if (!$ImportToolRow->isImportable()) {
		$checkboxClass[] = 'import-row-invalid';
	}

	//checkbox
	$checkbox = $this->Form->input('ImportTool.checked][', [
		'type' => 'checkbox',
		'label' => false,
		'div' => false,
		'class' => $checkboxClass,
		'hiddenField' => false,
		'value' => $row,
		'disabled' => (!$ImportToolRow->isImportable())
	]);
	$tableRow->column($checkbox);

	//import values
	foreach ($ImportToolData->getArguments() as $index => $ImportToolArgument) {
		$previewValue = $ImportToolRow->getPreviewData($ImportToolArgument);
		$multipleValue = is_array($previewValue);
		$validationErrors = $ImportToolRow->getValidationErrors($ImportToolArgument);
		$showValidationErrors = $validationErrors && !$rowStructureErrors;
		$missingCell = in_array($index, $missingStructureCells);

		$class = ($showValidationErrors) ? 'danger' : '';
		$class .= ($missingCell) ? ' warning' : '';

		$error = null;
		$errorTitle = null;
		$truncatePopover = true;

		if ($showValidationErrors) {
			$error = implode('<br>', $validationErrors);
			$errorTitle = __('Validation Error');
			$truncatePopover = false;
		}
		elseif ($missingCell) {
			$error = __('This field is missing in your uploaded file');
			$errorTitle = __('Structural Error');
			$truncatePopover = false;
		}

		$previewValue = (!is_array($previewValue)) ? [$previewValue] : $previewValue;
		$content = '';
		$objectInCell = false;

		foreach ($previewValue as $value) {
			$contentItem = '';

			if ($value instanceof ImportToolObject) {
				$objectInCell = true;

				$contentItem = $this->ImportTool->previewObjectValue($value);
			}
			else {
				$contentItem = $this->Content->truncate($this->Content->text($value), 30, ['popover' => $truncatePopover]);

				if ($multipleValue) {
					$contentItem = $this->Html->tag('span', $contentItem, ['class' => 'import-object', 'escape' => false]);
				}
			}

			$content .= $contentItem;
		}

		if ($error !== null && !$objectInCell) {
			$value = $this->Popovers->auto($value, $error, $errorTitle, ['element' => 'div', 'icon' => true]);
		}

		$tableRow->column([
			'content' => $content,
			'class' => $class
		]);
	}

	//import row status
	$labels = [];

	if ($ImportToolRow->isImportable()) {
		$labels[] = $this->Label->success(__('Ok'));
	}
	else {
		$rowValidationErrors = $ImportToolRow->getValidationErrors();

		if (!empty($rowValidationErrors)) {
			$label = $this->Label->danger(__('Row appears to be invalid'));

			$validationTooltip = $this->ImportTool->getValidationErrorsContent($rowValidationErrors, $model);

			if (!empty($validationTooltip)) {
				$label = $this->Popovers->auto($label, $validationTooltip, __('Validation Errors Help'));
			}

			$labels[] = $label;
		}

		if (!empty($rowStructureErrors)) {
			foreach ($rowStructureErrors as $error) {
				$labels[] = $this->Label->warning($error);
			}
		}
	}

	$tableRow->column([
		'content' => implode('<br>', $labels),
		'class' => 'text-center import-tool-status-cell'
	]);
}

echo $this->Html->div('table-responsive', $table->render());

echo $this->Form->end();

$ImportToolData = null;
?>

<script type="text/javascript">
function getImportableCheckboxes() {
	return $(".import-row-checkbox:not(.import-row-invalid)");
}

function getCheckedCheckboxes() {
	return getImportableCheckboxes().filter(":checked");
}

$("#check-all-checkbox").on("import:change", function(e) {
	$.uniform.update();
	$("#import-submit-btn").prop("disabled", !getCheckedCheckboxes().length);
});

$("#check-all-checkbox").on("change", function(e) {
	if ($(this).is(":checked")) {
		getImportableCheckboxes().prop("checked", true);
	}
	else {
		getImportableCheckboxes().prop("checked", false);
	}

	$(this).trigger("import:change");
}).trigger("change");

getImportableCheckboxes().on("change", function(e) {
	var checked = getCheckedCheckboxes().length;
	if (checked < getImportableCheckboxes().length) {
		$("#check-all-checkbox").prop("checked", false);

		$.uniform.update('#check-all-checkbox');
	}
	else if(checked == getImportableCheckboxes().length) {
		$("#check-all-checkbox").prop("checked", true);

		$.uniform.update('#check-all-checkbox');
	}

	$("#check-all-checkbox").trigger("import:change");
});
</script>