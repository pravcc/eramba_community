<?php
if (!empty($settingGroup['info'])) {
    echo $this->Alerts->info($settingGroup['info'], ['type' => 'info']);
}

// additional form elements for specific functionality
$formElement = 'settings/edit/form/' . $settingGroup['slug'];

if ($this->elementExists($formElement)) {
	echo $this->element($formElement);
}
else {
	echo $this->element('settings/edit/form');
}
