<?php
echo $this->Form->create('Attachment', array(
	'url' => array('plugin' => null, 'controller' => 'attachments', 'action' => 'addAjax', $model, $foreign_key),
	'class' => 'dropzone',
	'id' => 'attachments-dropzone'
));

echo $this->Form->end();
?>

<div id="attachments-content-files">
	<?php
	echo $this->element('ajax-ui/attachmentsList', array(
		'attachments' => $attachments
	));
	?>
</div>

<script type="text/javascript">
Eramba.Ajax.UI.removeDropzone();
Eramba.Ajax.UI.dropzoneInit("<?php echo $model; ?>", <?php echo $foreign_key; ?>);
</script>