<?php
App::uses('AppModel', 'Model');

class VisualisationAppModel extends AppModel {
	public $tablePrefix = 'visualisation_';

	// find and extract existing objects
	protected function _findExistingExtracted($model, $foreignKey = null, $UserObject) {
		$exists = $UserObject->findExisting($model, $foreignKey);
		$UserObject->initAcl();
		$columnName = $UserObject->getUserFieldsColumn();

		$existsExtractedUserFields = array_filter(
			Hash::extract($exists, '{n}.' . $UserObject->alias . '.user_fields_' . $columnName)
		);

		return $UserObject->getUserFieldsModel()->find('list', [
			'conditions' => [
				'id' => $existsExtractedUserFields
			],
			'fields' => [
				$columnName
			],
			'recursive' => -1
		]);
	}
}
