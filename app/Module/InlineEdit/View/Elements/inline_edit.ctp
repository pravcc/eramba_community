<?php
$Item = $InlineEdit->getItem();
$Field = $InlineEdit->getField();

//disable quick add
$Field->config('quickAdd', false);

$uuid = $InlineEdit->getUuid();

$formName = 'inline-edit-' . $uuid;

$fieldClass = 'item-row-' . $Field->getModelName() . '-' . $Item->getPrimary();

echo $this->FieldDataCollection->form($InlineEdit->getCollection(), [
	'raw' => true,
	'tabs' => false,
	'form_name' => $formName,
]);
?>

<div class="text-right">
	<?= $this->Buttons->link(__('Cancel'), [
		'id' => "popover-cancel-{$uuid}",
		'class' => ['popover-cancel'],
		'data' => [
			'yjs-request' => "app/triggerRequest/.{$fieldClass}",
			'yjs-use-loader' => 'false',
			'yjs-event-on' => 'click',
		]
	]) ?>
	<?= $this->Buttons->primary(__('Save'), [
		'class' => ['popover-submit'],
		'data' => [
			'yjs-request' => 'crud/submitForm',
			'yjs-forms' => $formName,
			'yjs-target' => "#{$uuid}",
			'yjs-datasource-url' => Router::url(Router::reverseToArray($this->request)),
			'yjs-event-on' => "click",
			'yjs-on-success-reload' => ".{$fieldClass}",
		]
	])
	?>
</div>

<?php if ($InlineEdit->getSuccess()) : ?>
	<div 
		data-yjs-request="inlineEdit/closeActivePopover"
		data-yjs-use-loader="false"
		data-yjs-event-on="init"
	></div>
<?php endif; ?>
