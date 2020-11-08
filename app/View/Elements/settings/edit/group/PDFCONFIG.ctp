<?php
$url = Router::url([
	'plugin' => null,
	'controller' => 'settings',
	'action' => 'downloadTestPdf',
	'?' => [
		'path' => $this->request->data['Setting']['PDF_PATH_TO_BIN']
	]
], true);
?>
<input name="test_download" id="pdf-test-download-btn" class="btn btn-primary" type="submit" value="<?= __('Test PDF') ?>"
	data-yjs-request="crud/submitForm"
	data-yjs-target="modal"
	data-yjs-modal-id="<?= $modal->getModalId() ?>"
	data-yjs-datasource-url="/settings/edit/PDFCONFIG"
	data-yjs-event-on="click"
	data-yjs-on-success="#setting-pdf-download"
	data-yjs-forms="<?= $formName ?>"
>
<div id="setting-pdf-download"
	data-yjs-request="eramba/redirectLocation"
	data-yjs-event-on="none"
	data-yjs-data-url="<?= $url ?>"
	data-yjs-use-loader="false"
></div>
<br>
<p>
	<?= __('Your testing PDF should look like %s. Press "Test PDF" to check if generated PDF looks like provided example.', $this->Html->link(__('this example'), Router::url('/test_pdf.pdf', true), ['target' => '_blank'])) ?>
</p>
