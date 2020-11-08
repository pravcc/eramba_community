<?php
App::uses('ObjectStatusAppModel', 'ObjectStatus.Model');

class Status extends ObjectStatusAppModel {

	public $actsAs = array(
		'Containable',
	);

	public $hasMany = [
		'ObjectStatus' => [
			'className' => 'ObjectStatus.ObjectStatus',
		]
	];

	public $attribute = 'name';

	public function listAttributes(Model $Model) {
		return $Model->Behaviors->ObjectStatus->getObjectStatusFields($Model);
	}

	public function joinAttributes(Model $Model) {
		return [
			[
				'table' => 'object_status_object_statuses',
				'alias' => 'ObjectStatus',
				'type' => 'INNER',
				'conditions' => [
					'ObjectStatus.model' =>  $Model->alias,
					'ObjectStatus.foreign_key = ' . $Model->escapeField($Model->primaryKey),
					'ObjectStatus.status' => '1'
				]	
			]
		];
	}

	public function applyAttributes(Model $Model, $attribute) {
		return [
			'ObjectStatus.name' => $attribute
		];
	}


/**
 * Return status id.
 * 
 * @param  string $model Model name.
 * @param  string $name Status name.
 * @return mixed Status id.
 */
	public function getStatusId($model, $name) {
		$data = $this->find('first', [
			'conditions' => [
				'Status.model' => $model,
				'Status.name' => $name,
			],
			'fields' => [
				'Status.id'
			]
		]);

		return (!empty($data['Status']['id'])) ? $data['Status']['id'] : null;
	}

}
