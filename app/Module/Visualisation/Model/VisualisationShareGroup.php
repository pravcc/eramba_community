<?php
/**
 * @package       Visualisation.Model
 */
App::uses('VisualisationShareBase', 'Visualisation.Model');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');

class VisualisationShareGroup extends VisualisationShareBase {
	public $useTable = 'share_groups';

	protected $_userFieldsModel = 'UserFields.UserFieldsGroup';

	protected $_userFieldsField = 'SharedUserGroup';

	protected $_userFieldsColumn = 'group_id';

	protected $_parentModel = 'Visualisation.VisualisationShare';

	protected $_permissionModel = 'Visualisation.VisualisationGroup';

	protected function _parseUsers($permissionForeignKey) {
		return ClassRegistry::init('UsersGroup')->find('list', [
			'conditions' => [
				'UsersGroup.group_id' => $permissionForeignKey
			],
			'fields' => [
				'UsersGroup.user_id'
			],
			'recursive' => -1
		]);
	}
}