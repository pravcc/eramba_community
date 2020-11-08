<?php
App::uses('SystemLog', 'SystemLogs.Model');

class LdapSynchronizationSystemLog extends SystemLog
{
	public $relatedModel = 'LdapSync.LdapSynchronization';

	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);

		$this->label = __('Ldap Synchronization Audit Trails');
	}
	
	public function getAdvancedFilterConfig()
	{
		$advancedFilterConfig = $this->_getAdvancedFilterConfig();

		$advancedFilterConfig->multipleSelectField('foreign_key', [ClassRegistry::init('LdapSync.LdapSynchronization'), 'getList'], [
			'label' => __('Ldap Synchronization'),
			'showDefault' => true,
			'insertOptions' => [
				'after' => 'action'
			]
		]);

		$advancedFilterConfig->getConfiguration()->getGroup('general')->removeField('user_id');

		return $advancedFilterConfig->getConfiguration()->toArray();
	}
}
