<?php
App::uses('ErambaHelper', 'View/Helper');
App::uses('LdapConnector', 'Model');

class LdapConnectorsHelper extends ErambaHelper {
	public $settings = array();
	public $helpers = ['Html', 'Text', 'Form', 'FieldData.FieldData', 'Limitless.Alerts'];
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function getStatuses($item) {
		$statuses = array();

		if ($item['LdapConnector']['status'] == 1) {
			$statuses[] = $this->getLabel(LdapConnector::statuses($item['LdapConnector']['status']), 'success');
			
		}
		elseif ($item['LdapConnector']['status'] == 0) {
			$statuses[] = $this->getLabel(LdapConnector::statuses($item['LdapConnector']['status']), 'warning');
		}

		return $this->processStatuses($statuses);
	}

	public function ldapGroupAccountAttributeField(FieldDataEntity $Field)
	{
		$out = $this->FieldData->input($Field, [
			'after' => $this->Alerts->info(__('If this Group connector is planned to be used in conjunction with already existing Auth connector then this attribute field is mandatory be the same and also retrieve the same values as the field "LDAP Account Attribute" you have configured in Auth connector.'))
		]);

		return $out;
	}

	public function ldapPasswordField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, [
			'type' => 'password'
		]);
	}

	public function ldapAuthFilterField(FieldDataEntity $Field)
	{
		$hiddenInput = $this->Form->input('_ldap_auth_filter_username_value', [
				'type' => 'hidden',
				'id' => 'ldap-auth-filter-username-value'
			]);

		$out = $this->FieldData->input($Field, [
			'beforeAfter' => $hiddenInput
		]);

		return $out;
	}

	public function typeField(FieldDataEntity $Field)
	{
		$formName = $this->_View->viewVars['formName'];
		$out = $this->FieldData->input($Field, [
			'data-yjs-request' => 'ldapConnectors/ldapConnectorType',
			'data-yjs-event-on' => 'init|change',
			'data-yjs-use-loader' => 'false'
		]);

		$out .= $this->Html->tag('div', false, [
			'id' => 'test-ldap-yjs-request',
			'class' => 'hidden',
			'data-yjs-request' => 'crud/submitForm',
			'data-yjs-event-on' => 'click',
			'data-yjs-datasource-url' => 'ldapConnectors/testLdapForm/user',
			'data-yjs-target' => 'modal',
			'data-yjs-on-modal-success' => 'none',
			'data-yjs-forms' => $formName
		]);

		$out .= $this->Html->tag('div', false, [
			'id' => 'test-ldap-user-yjs-request',
			'class' => 'hidden',
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-event-on' => 'click',
			'data-yjs-target' => 'modal',
			'data-yjs-datasource-url' => 'ldapConnectors/testLdapForm/group'
		]);
		$out .= $this->Html->tag('div', false, [
			'id' => 'test-ldap-group-yjs-request',
			'class' => 'hidden',
			'data-yjs-request' => 'ldapConnectors/ldapConnectorTestRequest',
			'data-yjs-event-on' => 'click',
			'data-yjs-target' => 'modal',
			'data-yjs-datasource-url' => '/ldapConnectors/testLdap/listGroups',
			'data-yjs-forms' => $formName,
			'data-yjs-on-modal-success' => 'none'
		]);

		return $out;
	}

	public function ldapGroupFetchEmailTypeField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, [
			'data-yjs-request' => 'ldapConnectors/emailType',
			'data-yjs-event-on' => 'init|change',
			'data-yjs-use-loader' => 'false'
		]);
	}
}
