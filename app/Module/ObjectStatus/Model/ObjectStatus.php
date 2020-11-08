<?php
App::uses('ObjectStatusAppModel', 'ObjectStatus.Model');

class ObjectStatus extends ObjectStatusAppModel {

	public $actsAs = array(
		'Containable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'status'
			)
		)
	);

	public $belongsTo = [
		'ObjectStatusStatus' => [
			'className' => 'ObjectStatus.ObjectStatusStatus',
			'foreignKey' => 'status_id'
		],
	];

/**
 * Save object status.
 * 
 * @param  string $name status name
 * @param  mixed $status status value
 * @param  string $model model of associated item
 * @param  int|string $foreignKey id of associated item
 * @return boolean success
 */
	public function saveStatus($name, $status, $model, $foreignKey) {
		$existingData = $this->getStatus($name, $model, $foreignKey);

		$data = [
            'name' => $name,
            'status' => $status,
            'model' => $model,
            'foreign_key' => $foreignKey,
            'ObjectStatusStatus' => [
            	'id' => $this->ObjectStatusStatus->getStatusId($model, $name),
            	'model' => $model,
            	'name' => $name
            ]
		];

		if (!empty($existingData)) {
			$data['id'] = $existingData['ObjectStatus']['id'];
		}

		$this->create();

		return $this->saveAssociated($data);
	}

/**
 * Get object status item.
 * 
 * @param  string $name status name
 * @param  string $model model of associated item
 * @param  int|string $foreignKey id of associated item
 * @return mixed object status data
 */
	public function getStatus($name, $model, $foreignKey) {
		$data = $this->find('first', [
			'conditions' => [
				'ObjectStatus.name' => $name,
				'ObjectStatus.model' => $model,
				'ObjectStatus.foreign_key' => $foreignKey,
			]
		]);

		return $data;
	}

/**
 * Get object status value.
 * 
 * @param  string $name status name
 * @param  string $model model of associated item
 * @param  int|string $foreignKey id of associated item
 * @return boolean
 */
	public function getStatusValue($name, $model, $foreignKey) {
		$data = $this->getStatus($name, $model, $foreignKey);

		if (empty($data)) {
			return false;
		}

		return (boolean) $data['ObjectStatus']['status'];
	}

/**
 * Delete object status.
 * 
 * @param  string $name status name
 * @param  mixed $status status value
 * @param  string $model model of associated item
 * @param  int|string $foreignKey id of associated item
 * @return boolean success
 */
	public function deleteStatus($name, $model, $foreignKey) {
		$status = $this->getStatus($name, $model, $foreignKey);

		if (!empty($status)) {
			return $this->delete($status['ObjectStatus']['id']);
		}

		return true;
	}

}
