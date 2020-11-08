<?php
App::uses('ConcurrentEditAppModel', 'ConcurrentEdit.Model');

class ConcurrentEdit extends ConcurrentEditAppModel
{
	public $actsAs = array(
		'Containable'
	);

	public $belongsTo = [
		'User'
	];

	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);
	}

	/**
	 * Set new or update existing CE record in database
	 * @param  string $model      Model
	 * @param  int    $foreignKey Foreign key
	 * @param  int    $userId     User ID
	 * @return boolean            Returns true or false (true if CE record exists otherwise false)
	 */
	public function setRecord($model, $foreignKey, $userId)
	{
		$record = $this->getCurrentLockedRecord($model, $foreignKey, $userId);
		if (!empty($record)) {
			$data = [
				'id' => $record['ConcurrentEdit']['id']
			];
		} else {
			$data = [
				'model' => $model,
				'foreign_key' => $foreignKey,
				'user_id' => $userId
			];

			$this->removeRecords($model, $foreignKey);
		}

		$data['last_update'] = date('Y-m-d H:i:s', time());

		$this->save($data, false);
	}

	public function removeRecords($model, $foreignKey)
	{
		$this->deleteAll([
			'model' => $model,
			'foreign_key' => $foreignKey
		]);
	}

	public function getCurrentLockedRecord($model, $foreignKey, $userId = null, $time = null)
	{
		$conditions = [
			'ConcurrentEdit.model' => $model,
			'ConcurrentEdit.foreign_key' => $foreignKey
		];

		if (!empty($userId)) {
			$conditions['ConcurrentEdit.user_id'] = $userId;
		}

		if (!empty($time)) {
			$conditions['ConcurrentEdit.last_update >'] = date('Y-m-d H:i:s', time() - $time);
		}

		$record = $this->find('first', [
			'fields' => [
				'id', 
				'last_update', 
				'user_id', 
				'User.name', 
				'User.surname'
			],
			'conditions' => $conditions,
			'contain' => [
				'User'
			]
		]);

		if (!empty($record)) {
			return $record;
		} else {
			return false;
		}
	}
}