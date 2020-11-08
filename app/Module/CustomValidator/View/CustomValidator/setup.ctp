<?php
App::uses('CustomValidatorField', 'CustomValidator.Model');

echo $this->element('section/add', [
	'FieldDataCollection' => $ValidatorCollection
]);
