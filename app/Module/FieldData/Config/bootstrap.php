<?php
App::uses('FieldDataModule', 'FieldData.Lib');

App::build([
	'Model/FieldData' => [CakePlugin::path('FieldData') . 'Model' . DS . 'FieldData' . DS],
	'Model/FieldData/Extensions' => [CakePlugin::path('FieldData') . 'Model' . DS . 'FieldData' . DS . 'Extensions' . DS]
], App::REGISTER);