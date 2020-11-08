<?php
App::uses('ObjectStatusAppModel', 'ObjectStatus.Model');

class ObjectStatusStatus extends ObjectStatusAppModel {

	//table prefix defined in ObjectStatusAppModel
	public $useTable = 'statuses';

	public $actsAs = array(
		'Containable',
	);


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
				'ObjectStatusStatus.model' => $model,
				'ObjectStatusStatus.name' => $name,
			],
			'fields' => [
				'ObjectStatusStatus.id'
			]
		]);

		return (!empty($data['ObjectStatusStatus']['id'])) ? $data['ObjectStatusStatus']['id'] : null;
	}

}
