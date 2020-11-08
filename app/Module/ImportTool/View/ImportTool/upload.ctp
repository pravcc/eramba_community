<div class="mb-20">
	<?= $this->Buttons->primary(__('Download Template'), [
		'href' => $this->ImportTool->getDownloadUrl($model),
	]) ?>
</div>

<?= $this->FieldDataCollection->form($FieldDataCollection, [
	'type' => 'file',
	'raw' => true,
	'tabs' => false,
	'form_name' => $formName
]); ?>
