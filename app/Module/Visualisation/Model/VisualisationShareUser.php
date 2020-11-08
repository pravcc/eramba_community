<?php
/**
 * @package       Visualisation.Model
 */
App::uses('VisualisationShareBase', 'Visualisation.Model');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');

class VisualisationShareUser extends VisualisationShareBase {
	public $useTable = 'share_users';

	protected $_userFieldsModel = 'UserFields.UserFieldsUser';

	protected $_userFieldsField = 'SharedUser';

	protected $_userFieldsColumn = 'user_id';

	protected $_parentModel = 'Visualisation.VisualisationShare';

	protected $_permissionModel = 'Visualisation.VisualisationUser';

}