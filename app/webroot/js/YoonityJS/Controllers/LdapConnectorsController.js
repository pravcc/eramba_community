"use strict";

YoonityJS.classFile({
	uses: [
		'//Controllers/AppController'
	],
	
	namespace: 'Controllers',
	
	class: {
		LdapConnectorsController: function(scopes)
		{
			// Declare global var for saving reference of future created object
			var _this;
			
			return {
				Extends: scopes.Controllers.AppController,
				
				constructor: function(params)
				{
					// Save current object reference for inner scopes
					_this = this;
					
					// Call parent constructor
					_this._parent.constructor(params);

					var properties = {
						Registry: null
					};

					YoonityJS.Class.InitProperties.call(this, properties, params);
				},

				$ldapConnectorTestRequest: function(params)
				{
					//
					// Load model and set form data
					this.loadModel();
					scopes.addActionScope(function(_this) {
						_this.getModel().set('data[additional][time]', new Date().getTime());
					}, this);
					//

					//
					// Call parent method
					scopes.addActionScope(function(_this) {
						_this._parent.$submitForm({});
					}, this);
					//
				},

				$ldapConnectorType: function(params)
				{
					var $typeField = $(this.Registry.getObject('request').getObject());
					var $authFields = $('#LdapConnectorLdapAuthFilter, #LdapConnectorLdapAuthAttribute, #LdapConnectorLdapNameAttribute, #LdapConnectorLdapEmailAttribute, #LdapConnectorLdapMemberofAttribute');
					var $groupFields = $('#LdapConnectorLdapGrouplistFilter, #LdapConnectorLdapGrouplistName, #LdapConnectorLdapGroupmemberlistFilter, #LdapConnectorLdapGroupAccountAttribute, #LdapConnectorLdapGroupEmailAttribute, #LdapConnectorLdapGroupFetchEmailType, #LdapConnectorLdapGroupMailDomain');
					var $testLdapBtn = $('#test-ldap');
					var $testLdapUserBtn = $('#test-ldap-user');
					var $testLdapGroupBtn = $('#test-ldap-group');

					if ($typeField.val() === 'authenticator') {
						$authFields.closest('.form-group').show();
						$groupFields.closest('.form-group').hide();

						$testLdapBtn.show();
						$testLdapUserBtn.hide();
						$testLdapGroupBtn.hide();

						this.Registry.getObject('modals').toggleTab('authenticator_settings', 'show');
						this.Registry.getObject('modals').toggleTab('group_settings', 'hide');
					} else if ($typeField.val() === 'group') {
						$authFields.closest('.form-group').hide();
						$groupFields.closest('.form-group').show();

						$testLdapBtn.hide();
						$testLdapUserBtn.show();
						$testLdapGroupBtn.show();

						this.Registry.getObject('modals').toggleTab('authenticator_settings', 'hide');
						this.Registry.getObject('modals').toggleTab('group_settings', 'show');
					}

					this.$emailType({});
				},

				$emailType: function(params)
				{
					if ($("#LdapConnectorType").val() !== "group") {
						return true;
					}

					var $attrField = $('#LdapConnectorLdapGroupEmailAttribute');
					var $domainField = $('#LdapConnectorLdapGroupMailDomain');

					var val = $('#LdapConnectorLdapGroupFetchEmailType').val();

					if (!val) {
						$attrField.prop("readonly", true);
						$domainField.prop("readonly", true);

						return true;
					}

					if (val === 'email-attribute') {
						$attrField.prop("readonly", false);
						$domainField.prop("readonly", true);
					}

					if (val == 'account-domain') {
						$attrField.prop("readonly", true);
						$domainField.prop("readonly", false);
					}
				}
			};
		}
	}
});
