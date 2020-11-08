<?php
/**
 * @package       Visualisation.Model
 */
App::uses('VisualisationShareBase', 'Visualisation.Model');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');

class VisualisationSettingsUser extends VisualisationShareBase {
	public $useTable = 'settings_users';

	protected $_userFieldsModel = 'UserFields.UserFieldsUser';

	protected $_userFieldsField = 'ExemptedUser';

	protected $_userFieldsColumn = 'user_id';

	protected $_parentModel = 'Visualisation.VisualisationSetting';

	protected $_permissionModel = 'Visualisation.VisualisationUser';

}