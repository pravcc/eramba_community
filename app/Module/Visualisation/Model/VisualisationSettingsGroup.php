<?php
/**
 * @package       Visualisation.Model
 */
App::uses('VisualisationShareBase', 'Visualisation.Model');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');

class VisualisationSettingsGroup extends VisualisationShareBase {
	public $useTable = 'settings_groups';

	protected $_userFieldsModel = 'UserFields.UserFieldsGroup';

	protected $_userFieldsField = 'ExemptedUserGroup';

	protected $_userFieldsColumn = 'group_id';

	protected $_parentModel = 'Visualisation.VisualisationSetting';

	protected $_permissionModel = 'Visualisation.VisualisationGroup';

	protected function _parseUsers($groupId) {
		$data = ClassRegistry::init('UsersGroup')->find('list', [
			'conditions' => [
				'UsersGroup.group_id' => $groupId
			],
			'fields' => [
				'UsersGroup.user_id'
			],
			'recursive' => -1
		]);

		return $data;
	}
}