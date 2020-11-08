<?php
App::uses('SystemLog', 'SystemLogs.Model');

class UserSystemLog extends SystemLog
{
	public $relatedModel = 'User';

	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);

		$this->label = __('User Audit Trails');
	}

	public function getAdvancedFilterConfig()
	{
		$advancedFilterConfig = $this->_getAdvancedFilterConfig();

		$advancedFilterConfig
			->group('general')
				->multipleSelectField('sub_foreign_key', [ClassRegistry::init('Portal'), 'portals'], [
					'label' => __('Portal'),
					'showDefault' => true,
					'insertOptions' => [
						'after' => 'action'
					]
				]);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function bindSubjectModel($Model, $foreignKey = 'foreign_key')
	{
		if ($Model->alias == 'User') {
			return;
		}

		parent::bindSubjectModel($Model, $foreignKey);
	}
}