<?php
App::uses('ErambaHelper', 'View/Helper');
App::uses('LdapConnectorAuthentication', 'Model');

class LdapConnectorAuthenticationsHelper extends ErambaHelper {
	public $settings = array();
	public $helpers = ['Html', 'Text', 'Form', 'FieldData.FieldData', 'Limitless.Alerts'];
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function generalAuthField(FieldDataEntity $Field)
	{
		extract($this->_View->viewVars);

		$out = $this->FieldData->input($Field, [
			'type' => 'radio',
			'div' => [
				'class' => 'radio'
			],
			'class' => [
				'styled'
			],
			'data-e-custom-id' => 'auth-users-default-radio',
			'options' => array($general_auth_default => __('Use Default authentication'))
		]);

		$out .= '<hr>';

		$out .= $this->Form->input('general_auth', array(
			'type' => 'radio',
			'div' => [
				'class' => 'radio'
			],
			'class' => 'styled',
			'data-e-custom-id' => 'auth-users-ldap-radio',
			'data-select-elem' => 'auth-users-ldap-select',
			'options' => array($general_auth_ldap => __('Use LDAP to authenticate users')),
			'after' => $this->Form->input('auth_users_id', array(
				'options' => $authenticators,
				'label' => false,
				'div' => false,
				'class' => 'form-control mt-10',
				'data-e-custom-id' => 'auth-users-ldap-select',
				'empty' => __('Choose an LDAP Connector'),
				'after' => $this->Html->tag('span', __('OPTIONAL: If you click on the radiobox and select a functional LDAP connector (System / Settings / LDAP Connectors) your system will start authenticating with LDAP instead of the local database. You also need to create user accounts on the system (System / Settings / User Management) so the AD login and the local account login matches (they are the same).'), ['class' => 'help-block'])
			))
		));

		$out .= '<hr>';

		$out .= $this->Form->input('general_auth', array(
			'type' => 'radio',
			'div' => [
				'class' => 'radio mb-20'
			],
			'class' => 'styled',
			'data-e-custom-id' => 'auth-users-oauth-google-radio',
			'data-select-elem' => 'auth-users-oauth-google-select',
			'options' => array($general_auth_oauth_google => __('Use OAuth Google to authenticate users')),
			'after' => $this->Form->input('oauth_google_id', array(
				'options' => $oauthGoogleConnectors,
				'label' => false,
				'div' => false,
				'class' => 'form-control mt-10',
				'data-e-custom-id' => 'auth-users-oauth-google-select',
				'empty' => __('Choose an OAuth Connector'),
				'after' => $this->Html->tag('span', __('OPTIONAL: If you click on the radiobox and select a functional OAuth Connector (System / Settings / OAuth Connectors) users will be able to use Google SignIn button for login. You also need to create user accounts on the system (System / Settings / User Management) so the OAuth login (email) and the local account login (email) matches (they are the same).'), ['class' => 'help-block'])
			))
		));

		$out .= $this->Form->input('general_auth', array(
			'type' => 'radio',
			'div' => [
				'class' => 'radio mb-20'
			],
			'class' => 'styled',
			'data-e-custom-id' => 'auth-users-saml-radio',
			'data-select-elem' => 'auth-users-saml-select',
			'options' => array($general_auth_saml => __('Use SAML to authenticate users')),
			'after' => $this->Form->input('saml_connector_id', array(
				'options' => $samlConnectors,
				'label' => false,
				'div' => false,
				'class' => 'form-control mt-10',
				'data-e-custom-id' => 'auth-users-saml-select',
				'empty' => __('Choose an SAML Connector'),
				'after' => $this->Html->tag('span', __('OPTIONAL: If you click on the radiobox and select a functional SAML Connector (System / Settings / SAML Connectors) your system will start authentication with SAML instead of the local database. You also need to create user accounts on the system (System / Settings / User Management) so the SAML login and the local account login matches (they are the same).'), ['class' => 'help-block'])
			))
		));

		$out .= $this->Html->scriptBlock('
			jQuery(function($) {
				var authObj = new Object();

				authObj.getElemByCustomId = function(customId)
				{
					return $(\'[data-e-custom-id="\' + customId + \'"]\');
				};

				authObj.setReadonlySelectBox = function(triggeredElem, selectElem)
				{
					if (selectElem) {
						if (triggeredElem.is(":checked")) {
							$(selectElem).prop("disabled", false);
						} else {
							$(selectElem).prop("disabled", true);
						}
					}
				};

				authObj.bindAuthEvents = function(checkboxElem, selectElem)
				{
					$checkboxElem = authObj.getElemByCustomId(checkboxElem);

					$checkboxElem.on("change", function(e) {
						authObj.setReadonlySelectBox($(this), authObj.getElemByCustomId(selectElem));
					}).trigger("change");
				};

				authObj.bindAuthRadioEvents = function(radioEleName) {
					$radioElems = $("[name=\'" + radioEleName + "\']");
					$("[name=\'" + radioEleName + "\']").on("change", function(e) {
						$radioElems.each(function() {
							var selectElem = authObj.getElemByCustomId($(this).attr(\'data-select-elem\'));
							authObj.setReadonlySelectBox($(this), selectElem);
						});
					}).trigger("change");
				};

				authObj.bindAuthRadioEvents("data[LdapConnectorAuthentication][general_auth]");
				authObj.bindAuthEvents("auth-awareness-checkbox", "auth-awareness-select");
				authObj.bindAuthEvents("auth-policies-checkbox", "auth-policies-select");
			});
		');

		return $out;
	}

	public function authAwarenessField(FieldDataEntity $Field)
	{
		$version = Configure::read('Eramba.version');
		if ($version[0] == 'c') {
			$Field->toggleEditable(false);
		}
		
		extract($this->_View->viewVars);

		return $this->FieldData->input($Field, [
			'data-e-custom-id' => 'auth-awareness-checkbox',
			'beforeAfter' => $this->Form->input('auth_awareness_id', array(
				'options' => $authenticators,
				'label' => false,
				'div' => false,
				'class' => 'form-control mt-10',
				'data-e-custom-id' => 'auth-awareness-select',
				'empty' => __('Choose an LDAP Connector')
			))
		]);
	}

	public function authPoliciesField(FieldDataEntity $Field)
	{
		extract($this->_View->viewVars);
		
		return $this->FieldData->input($Field, [
			'data-e-custom-id' => 'auth-policies-checkbox',
			'beforeAfter' => $this->Form->input('auth_policies_id', array(
				'options' => $authenticators,
				'label' => false,
				'div' => false,
				'class' => 'form-control mt-10',
				'data-e-custom-id' => 'auth-policies-select',
				'empty' => __('Activate the portal without LDAP')
			))
		]);
	}

	public function authVendorAssessmentField(FieldDataEntity $Field)
	{
		if (!AppModule::loaded('VendorAssessments')) {
			$Field->toggleEditable(false);
		}

		$out = $this->FieldData->input($Field, [
			'data-e-custom-id' => 'auth-vendor-assessmemnt'
		]);

		return $out;
	}

	public function authComplianceAuditField(FieldDataEntity $Field)
	{
		$out = $this->FieldData->input($Field, [
			'data-e-custom-id' => 'auth-compliance-checkbox'
		]);

		return $out;
	}

	public function authAccountReviewField(FieldDataEntity $Field)
	{
		if (!AppModule::loaded('AccountReviews')) {
			$Field->toggleEditable(false);
		}

		$out = $this->FieldData->input($Field, [
			'data-e-custom-id' => 'auth-account-review'
		]);

		return $out;
	}
}