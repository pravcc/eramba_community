<?php
echo $this->Form->create($model, [
	'data-yjs-form' => $deleteFormName,
	'novalidate' => true
]);
?>
<?=
__(
	'Are you really sure you want to delete selected objects?'
);
?>
<?php echo $this->Form->end(); ?>