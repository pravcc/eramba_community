<?php
$modelTitle = (isset($modelTitle)) ? $modelTitle : ClassRegistry::init($model)->label(['singular' => true]);

echo $this->Form->create($model, [
	'data-yjs-form' => $deleteFormName,
	'novalidate' => true
]);

echo __('Are you really sure you want to delete <strong>%s</strong> - <strong>%s?</strong>', $modelTitle, $recordTitle);

echo $this->Form->end();
?>