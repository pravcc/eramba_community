<?php $Model = ClassRegistry::init($model); ?>
<?= __('Hello!'); ?>
<br>
<br>
<?= __('The %s "%s" has changed its status from %s to %s.', $Model->label, $itemTitle, $additionalData['oldValue'], $additionalData['newValue']); ?>
<br>
<br>
<?= __('Regards'); ?>
