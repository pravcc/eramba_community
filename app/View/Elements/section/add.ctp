<?php
$formOptions = [
	'raw' => true,
	'tabs' => true,
	'form_name' => $formName,
];

if (!empty($modal) && !empty($modal->getModalId())) {
	$formOptions['modal_id'] = $modal->getModalId();
}

echo $this->FieldDataCollection->form($FieldDataCollection, $formOptions);
?>

<script>
	(function()
	{
		var
			modalId = <?= !empty($modal) && !empty($modal->getModalId()) ? $modal->getModalId() : 'false'; ?>,
			intervalName = 'ceInterval',
			modals = YoonityJS.Globals.vars.get('modals');
		
		if (modalId && !modals[modalId].intervals['intervalName']) {
			var
				$elem = $(document.createElement('div')),
				model = <?= isset($ceModel) ? "'" . $ceModel . "'" : 'null' ?>,
				foreignKey = <?= isset($ceForeignKey) ? "'" . $ceForeignKey . "'" : 'null' ?>;

			if (model != null) {
				$elem.data('yjs-request', 'app/load');
				$elem.data('yjs-datasource-url', 'concurrent-edit/echo/' + model + '/' + foreignKey);
				$elem.data('yjs-use-loader', 'false');
				var interval = setInterval(function()
				{
					new YoonityJS.Init({
						object: $elem
					});
				}, 7000);

				//
				// Add interval ID to currently opened modal
				modals[modalId].intervals.intervalName = interval;
				//
			}
		}
	})();
</script>