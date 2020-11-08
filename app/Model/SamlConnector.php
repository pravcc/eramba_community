<?php
App::uses('AppModel', 'Model');

class SamlConnector extends AppModel
{
	public $displayField = 'name';

	const STATUS_DISABLED = 0;
	const STATUS_ACTIVE = 1;

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $mapping = array(
		'titleColumn' => 'name',
		'logRecords' => true,
		'notificationSystem' => array('index'),
		'workflow' => false
	);

	public $actsAs = array(
		'Containable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name'
			)
		),
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'AdvancedFilters.AdvancedFilters'
	);

	public $validate = [
		'name' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Name is required'
		],
		'identity_provider' => [
			'rule' => ['url', true],
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Identity provider have to be a valid URL'
		],
		'idp_certificate' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'IDP x509 Certificate is required'
		],
		'remote_sign_in_url' => [
			'rule' => ['url', true],
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Remote sign in URL have to be a valid URL'
		],
		// 'remote_sign_out_url' => [
		// 	'rule' => 'notBlank',
		// 	'required' => true,
		// 	'allowEmpty' => false,
		// 	'message' => 'Remote sign out URL is required'
		// ],
		'email_field' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Email field is required'
		]
	];

	public $hasMany = array(
	);

	public $hasOne = array(
		'LdapConnectorAuthentication' => array(
			'className' => 'LdapConnectorAuthentication',
			'foreignKey' => 'saml_connector_id'
		)
	);

	public function __construct($id = false, $table = null, $ds = null)
	{
		$this->label = __('SAML Connectors');
		$this->_group = parent::SECTION_GROUP_SYSTEM;

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			]
		];

		$this->fieldData = [
			'name' => [
				'label' => __('Name'),
				'editable' => true,
				'description' => __('Name of this SAML Connector'),
			],
			'identity_provider' => [
				'label' => __('Identity Provider'),
				'editable' => true,
				'description' => __('Identity provider is URL to metadata of you IDP'),
			],
			'idp_certificate' => [
				'label' => __('IDP x509 Certificate'),
				'editable' => true,
				'description' => __('IDP x509 certificate'),
			],
			'remote_sign_in_url' => [
				'label' => __('Remote sign in URL'),
				'editable' => true,
				'description' => __('Remote sign in URL of you IDP'),
			],
			// 'remote_sign_out_url' => [
			// 	'label' => __('Remote sign out URL'),
			// 	'editable' => true,
			// 	'description' => __('Remote sign out URL of you IDP'),
			// ],
			'email_field' => [
				'label' => __('Email field'),
				'editable' => true,
				'description' => __('Name of email attribute in response from IDP'),
			],
			'redirect_urls' => [
				'label' => __('Redirect URLs'),
				'editable' => true,
				'description' => __("These URLs are used for redirecting user back to eramba's portal after he have authenticated with provider. Copy these URLs to your IDP allowed callback URLs field."),
				'renderHelper' => ['SamlConnectors', 'loginRedirectUrlsField']
			],
			'sign_saml_request' => [
				'label' => __('Sign SAML request'),
				'editable' => true,
				'description' => __('Whether or not to sign SAML requests'),
				'type' => 'toggle',
				'renderHelper' => ['SamlConnectors', 'signSamlRequestField']
			],
			'sp_certificate' => [
				'label' => __('SP x509 Certificate'),
				'editable' => true,
				'description' => __('SP x509 certificate'),
				'renderHelper' => ['SamlConnectors', 'spCertificateField']
			],
			'sp_private_key' => [
				'label' => __('SP Private Key'),
				'editable' => true,
				'description' => __('SP Private Key'),
				'renderHelper' => ['SamlConnectors', 'spPrivateKeyField']
			],
			// 'validate_saml_request' => [
			// 	'label' => __('Validate SAML request'),
			// 	'editable' => true,
			// 	'description' => __('Whether or not to validate SAML requests'),
			// 	'type' => 'toggle'
			// ],
			'status' => [
				'label' => __('Status'),
				'editable' => true,
				'description' => __('If the connector is disabled or enabled (is ready to be used across the system)'),
				'options' => [$this, 'getStatuses']
			]
		];

		parent::__construct($id, $table, $ds);
	}

	public function getAdvancedFilterConfig()
	{
		$advancedFilterConfig = $this->createAdvancedFilterConfig()
			->group('general', [
				'name' => __('General')
			])
				->nonFilterableField('id')
				->textField('name', [
					'showDefault' => true
				])
				->selectField('status', [$this, 'statuses'], [
					'showDefault' => true
				]);

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getStatuses() {
		return self::statuses();
	}

	// possible statuses
	public static function statuses($value = null)
	{
		$options = array(
			self::STATUS_DISABLED => __('Disabled'),
			self::STATUS_ACTIVE => __('Active')
		);
		return parent::enum($value, $options);
	}

	public function beforeValidate($options = array())
	{
		// Default validation
		$this->addListValidation('status', array_keys(self::statuses()));

		// Add SP certificate and SP private key validation rules
		if (isset($this->data['SamlConnector']['sign_saml_request']) &&
			$this->data['SamlConnector']['sign_saml_request'] == 1) {
			$this->validator()->add('sp_certificate', array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'SP x509 Certificate is required'
			));
			$this->validator()->add('sp_private_key', array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'SP Private key'
			));
		}

		return true;
	}

	public function getActiveSamlData()
	{
		$data = $this->find('first', [
			'conditions' => [
				'LdapConnectorAuthentication.auth_saml' => 1,
				'SamlConnector.status' => self::STATUS_ACTIVE,
				"`LdapConnectorAuthentication`.`saml_connector_id`=`SamlConnector`.`id`"
			]
		]);

		if (!empty($data)) {
			return $data['SamlConnector'];
		} else {
			return false;
		}
	}

	/**
	 * Restrict deletion if a Connector is still in use.
	 */
	public function beforeDelete($cascade = true)
	{
		return $this->prepareDelete($this->id);
	}

	public function prepareDelete($id)
	{
		$ret = true;

		if ($this->inUse($id)) {
			$ret = false;
			$this->customDeleteMessage = __('SAML Connector cannot be deleted because is in use.');
		}
		
		return $ret;
	}

	/**
	 * Checks if an SAML Connector is used in some part of the system.
	 */
	private function inUse($id)
	{
		$count = 0;
		$count = $this->LdapConnectorAuthentication->find('count', array(
			'conditions' => array(
				'LdapConnectorAuthentication.saml_connetor_id' => $id,
			)
		));
		
		return $count;
	}
}
