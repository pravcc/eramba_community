<?php
App::uses('ErambaHelper', 'View/Helper');
App::uses('Portal', 'Model');

class UsersHelper extends ErambaHelper {
	public $settings = array();
	public $helpers = ['Ux', 'Html', 'Text', 'AdvancedFilters', 'FieldData.FieldData', 'LimitlessTheme.Alerts', 'FormReload'];
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function getStatuses($user) {
		$statuses = array();

		if ($user['User']['blocked']) {
			$statuses[] = $this->getLabel(__('Brute Force Blocked'), 'danger');
		}

		if ($user['User']['status'] == USER_NOTACTIVE) {
			$statuses[] = $this->getLabel(__('Disabled'), 'danger');
		}

		return $this->processStatuses($statuses);
	}

    public function processPortals($user)
    {
        $portals = Portal::portals();
        $userPortals = $user['Portal'];
        $list = "";
        foreach ($userPortals as $up) {
            if (isset($portals[$up['id']])) {
                $list .= $portals[$up['id']] . '<br>';
            }
        }

        if (strlen($list) > 0) {
            $list = substr($list, 0, -4);
        }

        return $list;
    }

	/**
     * Get list of full_names from data array.
     * 
     * @return array List of names.
     */
    public function getNameList($data, $modelAlias = 'User') {
        $list = array();
        foreach ($data[$modelAlias] as $item) {
            $list[] = $item['full_name'];
        }

        return $list;
    }

    public function listNames($data, $modelAlias = 'User') {
    	$list = $this->getNameList($data, $modelAlias);

    	return $this->Ux->commonListOutput($list);
    }

    public function listWithFilterLinks($users) {
        $list = [];
        foreach ($users as $item) {
            $list[] = $this->AdvancedFilters->getItemFilteredLink($item['full_name'], 'User', $item['id']);
        }
        return implode(', ', $list);
    }

    public function ldapUserField(FieldDataEntity $Field)
    {
        $out = '';
        $ldapUser = isset($this->request->data['User']['ldap_user']) ? $this->request->data['User']['ldap_user'] : false;
        if (!$this->_isEditAction() && !empty($this->_getLdapAuthConnectorData('auth_users'))) {
            $out = $this->FieldData->input($Field, array_merge([
                'id' => 'ldap-user-field',
                'class' => ['select2-manual-init'],
                'type' => 'select',
                'options' => $ldapUser === false ? [__('Choose an LDAP user') => ''] : [$ldapUser => $ldapUser],
            ], $this->FormReload->triggerOptions(['field' => $Field])));

            $out .= $this->Html->scriptBlock("
                jQuery(function($) {
                    $(\"#ldap-user-field\").select2({
                        minimumInputLength: 1,
                        ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                            url: \"/users/searchLdapUsers\",
                            dataType: 'json',
                            quietMillis: 550,
                            data: function (params) {
                                return {
                                    q: params.term, // search term
                                };
                            },
                            processResults: function (data) { // parse the results into the format expected by Select2.
                                // since we are using custom formatting functions we do not need to alter the remote JSON data
                                if (typeof data.success != \"undefined\" && !data.success) {
                                    new PNotify({
                                        title: 'Error occurred',
                                        addclass: 'bg-danger',
                                        text: data.message,
                                        timeout: 6000
                                    });

                                    return {
                                        results: []
                                    };
                                }

                                return {
                                    results: data
                                };
                            },
                            cache: true
                        },
                        initSelection: function(element, callback) {
                            // the input tag has a value attribute preloaded that points to a preselected repository's id
                            // this function resolves that id attribute to an object that select2 can render
                            // using its formatResult renderer - that way the repository name is shown preselected
                            var id = $(element).val();
                            if (id !== \"\") {
                                callback({
                                    id: id,
                                    text: id
                                });
                            }
                        },
                        escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
                    });
                });
            ");
            
            //
            // Add alert with time when ldap cache was updated
            if (($cacheTime = Cache::read('last_cache_update', 'ldap')) !== false) {
                $out .= $this->Alerts->info(__('Last cache update: ' . date('Y-m-d H:i:s', $cacheTime['time'])));
            }
            //
        }

        return $out;
    }

    public function nameField(FieldDataEntity $Field)
    {
        return $this->FieldData->input($Field, [
            'readonly' => (!empty($this->_View->get('profile'))) ? true : false,
        ]);
    }

    public function surnameField(FieldDataEntity $Field)
    {
        return $this->FieldData->input($Field, [
            'readonly' => (!empty($this->_View->get('profile'))) ? true : false,
        ]);
    }

    public function emailField(FieldDataEntity $Field)
    {
        return $this->FieldData->input($Field, [
            'readonly' => (!empty($this->_View->get('profile'))) ? true : false,
        ]);
    }

    public function loginField(FieldDataEntity $Field)
    {
        return $this->FieldData->input($Field, [
            'readonly' => $this->_isUserAdminById() ? true : false
        ]);
    }

    public function localAccountField(FieldDataEntity $Field)
    {
        $out = '';
        if (!empty($this->_getLdapAuthConnectorData('auth_users')) ||
            !empty($this->_getLdapAuthConnectorData('oauth_google')) ||
            !empty($this->_getLdapAuthConnectorData('auth_saml'))) {
            $options = [
                'disabled' => !$this->_isAddActionOrAdmin() || $this->_isUserAdminById(),
                'id' => 'local-account',
                'data-custom-id' => 'local-account'
            ];

            if (!$this->_isEditAction() && $this->request->is('get')) {
                $options['checked'] = false;
            }

            $out = $this->FieldData->input($Field, $options);

            if (!$this->_isUserAdminById()) {
                $statusField = ($this->_isUserAdminById() || !empty($this->_getLdapAuthConnectorData('oauth_google')) || !empty($this->_getLdapAuthConnectorData('auth_saml'))) ? '' : ', #user-status';
                $out .= $this->Html->scriptBlock("
                    jQuery(function($) {
                        $('[data-custom-id=\"local-account\"]').on(\"change\", function(e) {
                            if ($(this).is(\":checked\")) {
                                $(\"#user-password, #user-verify-password" . $statusField . "\").removeAttr(\"disabled\");
                            }
                            else {
                                $(\"#user-password, #user-verify-password" . $statusField . "\").attr(\"disabled\", \"disabled\");
                            }
                        }).trigger(\"change\");
                    });
                ");
            }
        }

        return $out;
    }

    public function oldPassField(FieldDataEntity $Field)
    {
        return $this->FieldData->input($Field, [
            'type' => 'password'
        ]);
    }

    public function passwordField(FieldDataEntity $Field)
    {
        $FieldDataCollection = ClassRegistry::init('User')->getFieldCollection();

        $disabled = !$this->_isProfileAction() && !empty($this->_getLdapAuthConnectorData('auth_users')) && !$this->_isUserAdminById() ? true : false;

        $out = $this->FieldData->input($Field, [
            'type' => 'password',
            'disabled' => $disabled,
            'id' => 'user-password'
        ]);

        $out .= $this->passwordPolicyAlert();

        $out .= $this->FieldData->input($FieldDataCollection->pass2, [
            'type' => 'password',
            'disabled' => $disabled,
            'id' => 'user-verify-password'
        ]);

        return $out;
    }

    /**
     * Reusable alert box that explains the requirements for the succesful password validation.
     * 
     * @return string Alert box.
     */
    public function passwordPolicyAlert() {
        return $this->Alerts->info(__('The password must be 8-30 long, it must consist of alphanumeric characters with at least one number and optionally, include the following: “!@#$%^&()][” characters'));
    }

    public function portalField(FieldDataEntity $Field)
    {
        $out = '';
        if ($this->_isAddAction() || $this->request->data['User']['id'] != ADMIN_ID) {
            $out = $this->FieldData->input($Field, [
            ]);
        }

        return $out;
    }

    public function groupField(FieldDataEntity $Field)
    {
        $out = $this->FieldData->input($Field, [
        ]);

        $out .= "<div id=\"group-conflicts\"></div>";

        $out .= $this->Html->scriptBlock("
            jQuery(function($) {
                $(\"#UserGroup\").on(\"change\", function(e) {
                    var groups = [];
                    $.each($(\"#UserGroup option:selected\"), function(i, e) {
                        groups.push($(e).val());
                    });

                    $.ajax({
                        type: \"GET\",
                        url: \"" . Router::url(['controller' => 'users', 'action' => 'checkConflicts']) . "\",
                        data: {
                            groups: groups
                        }
                    })
                    .done(function(data) {
                        $(\"#group-conflicts\").html(data);
                    })
                });
            });
        ");

        return $out;
    }

    public function statusField(FieldDataEntity $Field)
    {
        $out = $this->FieldData->input($Field, [
            'disabled' => $this->_isUserAdminById(),
            'id' => 'user-status'
        ]);

        return $out;
    }

    //
    // Replacement for old readonly variable
    protected function _isNoAdminEdit()
    {
        return $this->_isEditAction() && !$this->_isLoggedUserAdmin() ? true : false;
    }

    //
    // Replacement for old cond variable
    protected function _isAddActionOrAdmin()
    {
        return $this->_isAddAction() || $this->_isLoggedUserAdmin() ? true : false;
    }

    /**
     * Whether or not is add action
     */
    protected function _isAddAction()
    {
        return $this->request->action === 'add' ? true : false;
    }

    /**
     * Whether or not is edit action
     */
    protected function _isEditAction()
    {
        return $this->request->action === 'edit' ? true : false;
    }

    /**
     * Whether or not is profile action
     */
    protected function _isProfileAction()
    {
        return $this->request->action === 'profile' ? true : false;
    }

    /**
     * Whether or not is user admin - is admin with id 1 or belongs to group with id 10
     */
    protected function _isLoggedUserAdmin()
    {
        return isset($this->_View->viewVars['isUserAdmin']) && $this->_View->viewVars['isUserAdmin'] == true ? true : false;
    }

    /**
     * Whether or not is user admin with id 1 and it's edit action
     */
    protected function _isUserAdminById()
    {
        return $this->_isEditAction() && isset($this->data['User']['id']) && $this->data['User']['id'] == ADMIN_ID ? true : false;
    }

    protected function _getLdapAuthConnectorData($key)
    {
        $ldapAuth = $this->_View->get('ldapAuth');
        $keyTemp = '';
        if (in_array($key, ['auth_users', 'oauth_google', 'auth_saml'], true)) {
            $keyTemp = $key;
        }
        
        return !empty($ldapAuth['LdapConnectorAuthentication'][$keyTemp]) ? $ldapAuth['LdapConnectorAuthentication'][$keyTemp] : null;
    }
}