<?php
$modelTitle = (isset($modelTitle)) ? $modelTitle : ClassRegistry::init($model)->label(['singular' => true]);

echo $this->Form->create($model, [
	'data-yjs-form' => $deleteFormName,
	'novalidate' => true
]);

echo __('You are about to delete this <strong>%s</strong> - <strong>%s?</strong>. All it\'s items and all its Compliance Analysis relationships will also be PERMANENTLY deleted.', $modelTitle, $recordTitle);

echo $this->Form->end();
?>