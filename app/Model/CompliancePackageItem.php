<?php
App::uses('AppModel', 'Model');
App::uses('Hash', 'Utility');
App::uses('InheritanceInterface', 'Model/Interface');

class CompliancePackageItem extends AppModel implements InheritanceInterface
{
	public $displayField = 'name';

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];
	
	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'AuditLog.Auditable',
		'Utils.SoftDelete',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'item_id', 'name', 'description', 'audit_questionaire'
			)
		),
		'ModuleDispatcher' => [
			'behaviors' => [
				'Reports.Report',
			]
		],
		'Visualisation.Visualisation',
		'AssociativeDelete.AssociativeDelete' => [
			'associations' => ['ComplianceManagement']
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
		'AdvancedFilters.AdvancedFilters'
	);

	public $mapping = array(
		// 'indexController' => 'compliancePackages',
		'indexController' => array(
			'basic' => 'compliancePackages',
			'advanced' => 'compliancePackageItems',
			'params' => array('compliance_package_id')
		),
		'titleColumn' => 'name',
		'logRecords' => true,
		'notificationSystem' => true,
		'workflow' => false
	);

	public $validate = array(
		'compliance_package_id' => array(
			// 'rule' => 'notBlank',
			// 'required' => true,
			// 'allowEmpty' => false,
			// 'on' => 'create'
			// ,
			'validatePackage' => [
				'rule' => 'validatePackage',
				'message' => 'Select Compliance Package or set New Compliance Package'
			]
		),
		'name' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'item_id' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		)
	);

	public $belongsTo = array(
		'CompliancePackage'
	);

	public $hasOne = array(
		'ComplianceManagement'
	);

	public $hasMany = array(
		'ComplianceFinding',
		'ComplianceAuditSetting',
	);

	public function validatePackage($check) {
		return (!empty($this->data['CompliancePackageItem']['compliance_package_id']) || (!empty($this->data['CompliancePackage']['package_id']) && !empty($this->data['CompliancePackage']['name'])));
	}

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Compliance Package Items');
		$this->_group = parent::SECTION_GROUP_COMPLIANCE_MGT;

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
			'compliance-package-item' => [
				'label' => __('Compliance Package Item')
			]
		];

		$this->fieldData = [
			'compliance_package_id' => [
				'label' => __('Chapter Name'),
				'editable' => false,
				'inlineEdit' => false,
				'macro' => [
					'name' => 'compliance_package'
				],
				'empty' => __('Choose one or leave empty to create a new one using fields below'),
				'options' => [
					'passParams' => true,
					'callable' => [$this, 'getCompliancePackages'],
				],
				'renderHelper' => ['CompliancePackageItems', 'compliancePackageField'],
			],
			'name' => [
				'label' => __('Item Name'),
				'group' => 'compliance-package-item',
				'inlineEdit' => true,
				'editable' => true,
			],
			'item_id' => [
				'label' => __('Item ID'),
				'group' => 'compliance-package-item',
				'editable' => true,
				'inlineEdit' => true,
			],
			'description' => [
				'label' => __('Item Description'),
				'group' => 'compliance-package-item',
				'editable' => true,
				'inlineEdit' => true,
			],
			'audit_questionaire' => [
				'label' => __('Item Additional Information'),
				'group' => 'compliance-package-item',
				'editable' => true,
				'inlineEdit' => true,
			]
		];

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Compliance Package Item'),
			'pdf_file_name' => __('compliance_package_items'),
			'csv_file_name' => __('compliance_package_items'),
			'actions' => false,
			'reset' => array(
				'controller' => 'complianceAudits',
				'action' => 'index'
			)
		);

		parent::__construct($id, $table, $ds);
	}

	public function getAdvancedFilterConfig()
	{
		$advancedFilterConfig = $this->createAdvancedFilterConfig()
			->group('general', [
				'name' => __('General')
			])
				->nonFilterableField('id')
				->multipleSelectField('CompliancePackage-compliance_package_regulator_id', [ClassRegistry::init('CompliancePackageRegulator'), 'getList'], [
					'label' => __('Compliance Package Name'),
				])
				->textField('CompliancePackage-package_id', [
					'showDefault' => true,
				])
				->textField('CompliancePackage-name', [
					'showDefault' => true,
				])
				->textField('CompliancePackage-description', [
					'showDefault' => true,
				])
				->textField('item_id', [
					'showDefault' => true,
				])
				->textField('name', [
					'showDefault' => true,
				])
				->textField('description', [
					'showDefault' => true,
				]);

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function parentModel()
	{
		return 'CompliancePackageRegulator';
	}

	public function parentNode($type) {
		$data = $this->find('first', [
			'conditions' => [
				'CompliancePackageItem.id' => $this->id
			],
			'fields' => [
				'CompliancePackage.compliance_package_regulator_id'
			],
			'recursive' => 0
		]);

		if (isset($data['CompliancePackage']['compliance_package_regulator_id'])) {
			$parentId = $data['CompliancePackage']['compliance_package_regulator_id'];
		} else {
			return null;
		}

		return [
			$this->parentModel() => [
				'id' => $parentId
			]
		];
    }

	public function getReportsConfig()
	{
		return [
			'finder' => [
                'options' => [
                    'contain' => [
                        'CompliancePackage' => [
							'CompliancePackageRegulator',
						],
						'ComplianceManagement',
						'ComplianceFinding',
						'ComplianceAuditSetting'
                    ]
                ]
            ],
			'table' => [
				'model' => [
					'CompliancePackage',
				]
			],
		];
	}

	public function getMacrosConfig()
	{
		return [
			'assoc' => [
				'CompliancePackage'
			],
		];
	}

	public function getNotificationSystemConfig()
	{
		return parent::getNotificationSystemConfig();
	}

	public function getThirdParties() {
		return $this->CompliancePackage->getThirdParties();
	}

	public function findByComplianceAudit($data = array(), $filter) {
		$this->ComplianceAuditSetting->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceAuditSetting->Behaviors->attach('Search.Searchable');

		$query = $this->ComplianceAuditSetting->getQuery('all', array(
			'conditions' => array(
				'ComplianceAuditSetting.compliance_audit_id' => $data[$filter['name']]
			),
			'fields' => array(
				'ComplianceAuditSetting.compliance_package_item_id'
			),
			'contain' => array()
		));

		return $query;
	}

	public function getComplianceAudits() {
		$data = $this->CompliancePackage->ThirdParty->ComplianceAudit->find('list', array(
			'fields' => array('ComplianceAudit.id', 'ComplianceAudit.name'),
			'joins' => array(
				array(
					'table' => 'third_parties',
					'alias' => 'ThirdParty',
					'type' => 'INNER',
					'conditions' => array(
						'ThirdParty.id = ComplianceAudit.third_party_id'
					),
				),
				array(
					'table' => 'compliance_packages',
					'alias' => 'CompliancePackage',
					'type' => 'INNER',
					'conditions' => array(
						'ThirdParty.id = CompliancePackage.third_party_id'
					)
				),
				array(
					'table' => 'compliance_package_items',
					'alias' => 'CompliancePackageItem',
					'type' => 'INNER',
					'conditions' => array(
						'CompliancePackage.id = CompliancePackageItem.compliance_package_id'
					)
				),
				array(
					'table' => 'compliance_audit_settings',
					'alias' => 'ComplianceAuditSetting',
					'type' => 'INNER',
					'conditions' => array(
						'CompliancePackageItem.id = ComplianceAuditSetting.compliance_package_item_id'
					)
				),
			),
			'group' => array('ComplianceAudit.id'),
			'contain' => array()
		));

		return $data;
	}

	public function getItem($id) {
		$data = $this->find('first', array(
			'conditions' => array(
				'id' => $id
			),
			'recursive' => -1
		));

		return $data;
	}

	/**
	 * Binds an audit setting based on Audit ID.
	 */
	public function bindSingleComplianceAuditSetting($auditId) {
		$this->bindModel(array(
			'hasOne' => array(
				'ComplianceAuditSettingSingle' => array(
					'className' => 'ComplianceAuditSetting',
					'conditions' => array(
						'ComplianceAuditSettingSingle.compliance_audit_id' => $auditId
					)
				)
			)
		));
	}

	public function getCompliancePackages(FieldDataEntity $Field, $compliancePackageRegulatorId = null)
	{
		$conds = [];
		if ($compliancePackageRegulatorId !== null) {
			$conds = [
				'CompliancePackage.compliance_package_regulator_id' => $compliancePackageRegulatorId
			];
		}

		$data = $this->CompliancePackage->find('list', [
			'conditions' => $conds,
			'recursive' => -1
		]);

		return $data;
	}
}
