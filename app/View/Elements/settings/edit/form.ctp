<?php
echo $this->Form->create($formName, [
	'url' => $formUrl,
	'data-yjs-form' => $formName
]);

foreach ($settingGroup['settings'] as $key => $setting) {
    echo $this->element('settings/edit/input', [
        'setting' => $setting
    ]);
}

// additional group elements for specific functionality
$groupElement = 'settings/edit/group/' . $settingGroup['slug'];
if ($this->elementExists($groupElement)) {
	echo $this->element($groupElement);
}

echo $this->Form->end();