<?php
App::uses('AppModel', 'Model');

class SecurityPolicyDocumentType extends AppModel
{
	const EDITABLE = 1;
	const NOT_EDITABLE = 0;

	const TYPE_PROCEDURE = 1;
	const TYPE_STANDARD = 2;
	const TYPE_POLICY = 3;

	public $displayField = 'name';
	
	public $actsAs = [
		'AuditLog.Auditable',
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'AdvancedFilters.AdvancedFilters'
	];

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

    public $mapping = null;

	public $validate = [
		'name' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'message' => 'This field cannot be left blank'
			],
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'Same type already exists'
			)
		],
	];

	public $hasMany = [
		'SecurityPolicy'
	];

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Security Policy Document Types');

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
		];

		$this->fieldData = [
			'name' => [
				'label' => __('Name'),
				'editable' => false,
				'description' => __('Give a name to this document type')
			],
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
				]);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function isEditable($id) {
		$data = $this->find('count', [
			'conditions' => [
				'SecurityPolicyDocumentType.id' => $id,
				'SecurityPolicyDocumentType.editable' => self::EDITABLE
			]
		]);

		return (boolean) $data;
	}

	public function hasDocumentInstances($id) {
		if ($this->SecurityPolicy->Behaviors->enabled('SoftDelete')) {
			$configSoftDelete = $this->SecurityPolicy->softDelete(null);
			$this->SecurityPolicy->softDelete(false);
		}

		$data = $this->SecurityPolicy->find('count', [
			'conditions' => [
				'SecurityPolicy.security_policy_document_type_id' => $id
			]
		]);

		if ($this->SecurityPolicy->Behaviors->enabled('SoftDelete')) {
			$this->SecurityPolicy->softDelete($configSoftDelete);
		}

		return (boolean) $data;
	}

	public function isDeletable($id = null) {
		if ($id === null) {
			$id = $this->id;
		}

		if (!$this->isEditable($id) || $this->hasDocumentInstances($id)) {
			return false;
		}

		return true;
	}

	public function beforeDelete($cascade = true) {
		$ret = parent::beforeDelete($cascade);

		$ret &= $this->isDeletable();

		return (boolean) $ret;
	}

	public static $staticTypesTransferMap = [
		SECURITY_POLICY_PROCEDURE => self::TYPE_PROCEDURE,
		SECURITY_POLICY_STANDARD => self::TYPE_STANDARD,
		SECURITY_POLICY_POLICY => self::TYPE_POLICY,
	];

	/**
	 * @deprecated This function can corrupt existing data and was supposed to use only once in very old release.
	 */
	public function syncSecurityPolicyDocumentTypes() {
		if ($this->SecurityPolicy->Behaviors->enabled('SoftDelete')) {
			$configSoftDelete = $this->SecurityPolicy->softDelete(null);
			$this->SecurityPolicy->softDelete(false);
		}

		$data = $this->SecurityPolicy->find('all', [
			'fields' => [
				'SecurityPolicy.id',
				'SecurityPolicy.document_type',
			],
			'contain' => []
		]);

		$ret = true;

		foreach ($data as $item) {
			$this->SecurityPolicy->create();
			$this->SecurityPolicy->id = $item['SecurityPolicy']['id'];

			$documentType = static::$staticTypesTransferMap[$item['SecurityPolicy']['document_type']];
			$ret &= $this->SecurityPolicy->save(['security_policy_document_type_id' => $documentType], [
	            'validate' => false,
	            'fieldList' => ['security_policy_document_type_id'],
	            'callbacks' => false
	        ]);
		}

		if ($this->SecurityPolicy->Behaviors->enabled('SoftDelete')) {
			$this->SecurityPolicy->softDelete($configSoftDelete);
		}

		return $ret;
	}
}
