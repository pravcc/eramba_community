<?php
App::uses('ErambaHelper', 'View/Helper');
App::uses('Portal', 'Model');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');

class LdapSynchronizationHelper extends ErambaHelper
{
	public $settings = array();
	public $helpers = ['Ux', 'Html', 'Text', 'FieldData.FieldData', 'FormReload', 'LimitlessTheme.Alerts'];
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

    public function nameField(FieldDataEntity $Field)
    {
        $out = $this->Alerts->info(__('The sync process runs every hour and is capable of sync up to 500 user accounts per run. If your group has more than 500 users it might take more than one hour to fully sync. Note that eramba caches LDAP results for 5 hours, LDAP Directory changes will be unnoticed while the cache is not updated or force cleaned using System / Settings / Clear Cache.'));

        $out .= $this->FieldData->input($Field);

        return $out;
    }

    public function ldapConnectorIdField(FieldDataEntity $Field)
    {
        return $this->FieldData->input($Field, $this->FormReload->triggerOptions([
            'field' => $Field
        ]));
    }
}
