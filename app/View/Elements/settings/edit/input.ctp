<?php
App::uses('Setting', 'Model');
App::uses('Inflector', 'Utility');

// If a custom field element exists, we use it
$fieldElement = 'settings/edit/field/' . $setting['variable'];
if ($this->elementExists($fieldElement)) {

    echo $this->element($fieldElement, [
        'fieldName' => 'Setting.' . $setting['variable'],
        'setting' => $setting
    ]);
}
else { // If we dont have a custom field element, we generate the input according to the settings
    $options = [];

    // custom input function
    $fn = Inflector::variable($setting['variable']);

    // default input function
    if (!method_exists($this->Settings, $fn)) {
        $fn = 'input';
    }

    echo call_user_func_array([$this->Settings, $fn], [$setting, $options]);
}
