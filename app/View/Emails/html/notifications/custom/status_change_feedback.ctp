<?php $Model = ClassRegistry::init($model); ?>
<?= __('Hello!'); ?>
<br>
<br>
<?= __('The %s "%s" has changed its status from %s to %s.', $Model->label, $itemTitle, $additionalData['oldValue'], $additionalData['newValue']); ?>
<br>
<br>
<?= __('You can provide feedback <a href="%s">here</a>.', $feedbackUrl) ?>
<br>
<br>
<?= __('Regards'); ?>
