<?php
App::uses('AppModel', 'Model');
App::uses('BulkAction', 'BulkActions.Model');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlockChartSetting', 'Reports.Model');
App::uses('Hash', 'Utility');
App::uses('InheritanceInterface', 'Model/Interface');
App::uses('UserFields', 'UserFields.Lib');

class ComplianceManagement extends AppModel implements InheritanceInterface
{
	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];
	
	public $mapping = array(
		'indexController' => 'complianceManagements',
		'titleColumn' => false,
		'logRecords' => true,
		'workflow' => false,
	);

	public $config = array(
		'actionList' => array(
			'trash' => false,
			'notifications' => false
		)
	);

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'AuditLog.Auditable' => array(
			'ignore' => array(
				'created',
				'modified',
			)
		),
		'Utils.SoftDelete',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'description', 'compliance_treatment_strategy_id', 'efficacy', 'legal_id', 'compliance_exception_id', 'compliance_package_item_id'
			)
		),
		'Visualisation.Visualisation',
		'ModuleDispatcher' => [
			'behaviors' => [
				'CustomFields.CustomFields',
				'Reports.Report',
			]
		],
		'ObjectStatus.ObjectStatus',
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'AdvancedQuery.AdvancedFinder',
		'Macros.Macro',
		'AdvancedFilters.AdvancedFilters',
		'UserFields.UserFields' => [
			'fields' => [
				'Owner'
			]
		],
		'CustomLabels.CustomLabels'
	);

	public $validate = array(
		'efficacy' => array(
			'rule' => 'notBlank',
			'required' => true
		)
	);

	public $hasAndBelongsToMany = array(
		'SecurityService',
		'SecurityPolicy',
		'Risk',
		'ThirdPartyRisk',
		'BusinessContinuity',
		'Project' => array(
			'with' => 'ComplianceManagementsProject'
		),
		'ComplianceAnalysisFinding',
		'ComplianceException',
		'Asset'
	);

	public $belongsTo = array(
		'ComplianceTreatmentStrategy',
		// 'ComplianceException',
		'CompliancePackageItem',
		'Legal'
	);

	public $hasMany = array(
	);

	public $packageContain = array(
		'CompliancePackageItem' => array(
			'ComplianceManagement' => array(
				'SecurityService' => array(
					/*'conditions' => array(
						'OR' => array(
							'audits_all_done' => 0,
							'audits_last_missing' => 1,
							'audits_last_passed' => 0,
							'maintenances_all_done' => 0,
							'maintenances_last_missing' => 1,
							'maintenances_last_passed' => 0
						)
					),*/
					'SecurityServiceType'
				),
				'SecurityPolicy',
				'ComplianceException' => array(
					// 'conditions' => array(
					// 	'expired' => 1
					// )
				),
				'Project'
			)
		)
	);

	public $thirdPartyJoins = array(
		array(
			'table' => 'compliance_packages',
			'alias' => 'CompliancePackage',
			'type' => 'INNER',
			'conditions' => array(
				'CompliancePackageItem.compliance_package_id = CompliancePackage.id'
			)
		),
		array(
			'table' => 'compliance_package_regulators',
			'alias' => 'CompliancePackageRegulator',
			'type' => 'INNER',
			'conditions' => array(
				'CompliancePackageRegulator.id = CompliancePackage.compliance_package_regulator_id'
			)
		),
	);

	public function __construct($id = false, $table = null, $ds = null)
	{
		//
		// Init helper Lib for UserFields Module
		$UserFields = new UserFields();
		//
		
		$this->label = __('Compliance Analysis Item');
		$this->_group = parent::SECTION_GROUP_COMPLIANCE_MGT;

		$this->fieldGroupData = array(
			'item-details' => array(
				'label' => __('Compliance Item Details')
			),
			'default' => array(
				'label' => __('General')
			),
			'mitigation-options' => array(
				'label' => __('Mitigation Options')
			),
			'findings' => array(
				'label' => __('Findings')
			),
			'compliance-drivers' => array(
				'label' => __('Compliance Drivers')
			),
			'asset' => array(
				'label' => __('Asset')
			),
		);

		$this->fieldData = array(
			'details' => array(
				'label' => __('Details'),
				'group' => 'item-details',
				'editable' => true,
				'renderHelper' => ['ComplianceManagements', 'detailsField']
			),
			'compliance_package_item_id' => array(
				'label' => __('Compliance Package Item'),
				'editable' => false,
				'hidden' => true,
			),
			'efficacy' => array(
				'label' => __('Compliance Efficacy'),
				'options' => 'getPercentageOptions',
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('OPTIONAL: Describe in terms of percentage how well your mitigation addresses the requirements for this compliance item')
			),
			'Owner' => $UserFields->getFieldDataEntityData($this, 'Owner', [
				'label' => __('Owner'), 
				'description' => __('Select one or more user accounts or groups that are most related to this particular requirement. If in doubt, simply select "Admin".'),
				'dependency' => true,
				'inlineEdit' => true,
				'quickAdd' => true
			]),
			'compliance_treatment_strategy_id' => array(
				'label' => __('Current Compliance Status'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Select your desired compliance goal:<br>Compliant: you wish to be compliant.<br>Not Applicable: this item is not applicable to the scope of this program.<br>Not Compliant: your organisation has no interest in being compliant with this requirement.'),
				'macro' => [
					'name' => 'compliant_status'
				]
			),
			'description' => array(
				'label' => __('Description'),
				'editable' => true,
				'inlineEdit' => true,
			),
			'Project' => array(
				'label' => __('Mitigation Projects'),
				'editable' => true,
				'inlineEdit' => true,
				'options' => array($this, 'getProjectsNotCompleted'),
				'description' => __('OPTIONAL: If you havent got controls and policies that meet this requirement, you can select a project that addresses this issue (Projects are defined in Security Operations / Project Management).')
			),
			'SecurityService' => array(
				'label' => __('Mitigation Internal Controls'),
				'group' => 'mitigation-options',
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Select one or more controls (from Control Catalogue / Security Services) used to mitigate this compliance requirement.')
			),
			'SecurityPolicy' => array(
				'label' => __('Mitigating Security Policies'),
				'group' => 'mitigation-options',
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Select one or more policies (from Control Catalogue / Security Policies) that mitigate this compliance requirement (they can replace security controls when none is applicable).')
			),
			'ComplianceException' => array(
				'label' => __('Compliance Exception'),
				'group' => 'mitigation-options',
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('If the compliance status (from the first tab) is "Not Aplicable" or "Not Compliant" you might want to set a Compliance Exception to state that in a formal record. This is an optional record.')
			),
			'ComplianceAnalysisFinding' => array(
				'label' => __('Compliance Findings'),
				'group' => 'findings',
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('OPTIONAL: Select or create one or more Compliance Findings (from Compliance Management / Compliance Finginds) for this compliance requirements. This is typically used when your auditors have identified that your mitigation for this control is innefective and you want to keep track of such incompliance until remediation.')
			),
			'Risk' => array(
				'label' => __('Asset Risks'),
				'group' => 'compliance-drivers',
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('OPTIONAL: Certain standards (such as ISO 27001) require you to describe the drivers for meeting their controls. You can use Risks (from Risk Management / Asset Risk Management) as drivers.'),
				'advancedFilter' => 'risk_id'
			),
			'ThirdPartyRisk' => array(
				'label' => __('Third Party Risks'),
				'group' => 'compliance-drivers',
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('OPTIONAL: Certain standards (such as ISO 27001) require you to describe the drivers for meeting their controls. You can use Risks (from Risk Management / Third Party Risk Management) as drivers.')
			),
			'BusinessContinuity' => array(
				'label' => __('Business Continuities'),
				'group' => 'compliance-drivers',
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('OPTIONAL: Certain standards (such as ISO 27001) require you to describe the drivers for meeting their controls. You can use Risks (from Risk Management / Business Impact Analysis) as drivers.')
			),
			'legal_id' => array(
				'label' => __('Liabilities'),
				'group' => 'compliance-drivers',
				'editable' => true,
				'inlineEdit' => true,
				'empty' => __('Choose one ...'),
				'description' => __('OPTIONAL: If there are liabilities (from Organisation / Legal Constrains) that require you to meet this particular requirement select them here.'),
				'macro' => [
					'name' => 'liability'
				]
			),
			'Asset' => array(
				'label' => __('Assets'),
				'group' => 'asset',
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('OPTIONAL: Select one or more assets (from Asset Managemnet / Asset Identification) that are related to this compliance requirement.'),
				'advancedFilter' => 'asset_id'
			),
			'item_name' => [
				'label' => __('Item Name'),
				'editable' => false,
			],
			'item_id' => [
				'label' => __('Item ID'),
				'inlineEdit' => false,
			],
			'item_description' => [
				'label' => __('Item Description'),
				'inlineEdit' => false,
			],
			'package_name' => [
				'label' => __('Chapter Name'),
				'editable' => false,
			],
			'package_id' => [
				'label' => __('Chapter ID'),
				'inlineEdit' => false,
			],
			'package_description' => [
				'label' => __('Chapter Description'),
				'inlineEdit' => false,
			],
			'compliance_package_regulator_name' => [
				'label' => __('Compliance Package'),
				'inlineEdit' => false,
				'macro' => [
					'name' => 'compliance_package'
				]
			],
			'mappings' => [
				'label' => __('Mappings'),
				'inlineEdit' => false,
				'hidden' => true
			]
		);

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Compliance Analysis'),
			'pdf_file_name' => __('compliance_analysis'),
			'csv_file_name' => __('compliance_analysis'),
			'view_item' => array(
				'ajax_action' => array(
					'controller' => 'complianceManagements',
					'action' => 'analyze'
				)
			),
			'history' => true,
			'bulk_actions' => array(
				BulkAction::TYPE_EDIT
			),
			'use_new_filters' => true
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
					'label' => __('Package Name'),
					'findField' => 'CompliancePackageItem.CompliancePackage.compliance_package_regulator_id',
					'returnField' => 'ComplianceManagement.compliance_package_item_id',
					'fieldData' => 'CompliancePackageItem.CompliancePackage.compliance_package_regulator_id'
				])
				->textField('CompliancePackage-package_id', [
					'findField' => 'CompliancePackageItem.CompliancePackage.package_id',
					'returnField' => 'ComplianceManagement.compliance_package_item_id',
					'fieldData' => 'CompliancePackageItem.CompliancePackage.package_id'
				])
				->textField('CompliancePackage-name', [
					'findField' => 'CompliancePackageItem.CompliancePackage.name',
					'returnField' => 'ComplianceManagement.compliance_package_item_id',
					'fieldData' => 'CompliancePackageItem.CompliancePackage.name'
				])
				->textField('CompliancePackage-description', [
					'findField' => 'CompliancePackageItem.CompliancePackage.description',
					'returnField' => 'ComplianceManagement.compliance_package_item_id',
					'fieldData' => 'CompliancePackageItem.CompliancePackage.description'
				])
				->textField('CompliancePackageItem-item_id', [
					'showDefault' => true,
				])
				->textField('CompliancePackageItem-name', [
					'showDefault' => true,
				])
				->textField('CompliancePackageItem-description', [
					'showDefault' => true,
				])
				->textField('CompliancePackageItem-audit_questionaire')
				->numberField('efficacy')
				->userField('Owner', 'Owner', [
					'showDefault' => true
				])
				->multipleSelectField('compliance_treatment_strategy_id', [ClassRegistry::init('ComplianceTreatmentStrategy'), 'getList'], [
					'label' => __('Compliance Strategy'),
					'showDefault' => true,
				])
				->textField('description');

		if (AppModule::loaded('Mapping')) {
			$advancedFilterConfig->group('general')->nonFilterableField('mappings', [
				'label' => __('Mappings')
			]);
		}

		$this->Project->relatedFilters($advancedFilterConfig);

		$this->SecurityService->relatedFilters($advancedFilterConfig)
            ->objectStatusField('ObjectStatus_security_service_audits_last_not_passed', 'security_service_audits_last_not_passed')
            ->objectStatusField('ObjectStatus_security_service_audits_last_missing', 'security_service_audits_last_missing')
            ->objectStatusField('ObjectStatus_security_service_control_with_issues', 'security_service_control_with_issues')
            ->objectStatusField('ObjectStatus_security_service_maintenances_last_missing', 'security_service_maintenances_last_missing');

        $this->SecurityPolicy->relatedFilters($advancedFilterConfig)
            ->objectStatusField('ObjectStatus_security_policy_expired_reviews', 'security_policy_expired_reviews');

        $advancedFilterConfig
            ->group('ComplianceException', [
                'name' => __('Compliance Exception')
            ])
            	->multipleSelectField('ComplianceException', [ClassRegistry::init('ComplianceException'), 'getList'], [
                    'label' => __('Compliance Exception')
                ])
                ->textField('ComplianceException-description', [
                    'label' => __('Compliance Exception Description')
                ])
                ->selectField('ComplianceException-status', [ClassRegistry::init('ComplianceException'), 'statuses'], [
                    'label' => __('Compliance Exception Status')
                ])
                ->multipleSelectField('ComplianceException-status', [ClassRegistry::init('ComplianceException'), 'statuses'], [
                    'label' => __('Compliance Exception Status')
                ])
                ->objectStatusField('ObjectStatus_compliance_exception_expired', 'compliance_exception_expired');

        $this->Risk->relatedFilters($advancedFilterConfig);
		$this->ThirdPartyRisk->relatedFilters($advancedFilterConfig);
		$this->BusinessContinuity->relatedFilters($advancedFilterConfig);

		$advancedFilterConfig
			->group('Legal', [
				'name' => __('Liability')
			])
				->multipleSelectField('legal_id', [ClassRegistry::init('Legal'), 'getList']);

		$this->Asset->relatedFilters($advancedFilterConfig);

		$advancedFilterConfig
			->group('ComplianceAnalysisFinding', [
				'name' => __('Compliance Analysis Finding')
			])
				->multipleSelectField('ComplianceAnalysisFinding', [ClassRegistry::init('ComplianceAnalysisFinding'), 'getList'], [
					'label' => __('Compliance Analysis Finding')
				])
				->dateField('ComplianceAnalysisFinding-due_date', [
					'label' => __('Compliance Analysis Finding Due Date')
				])
				->selectField('ComplianceAnalysisFinding-status', [ClassRegistry::init('ComplianceAnalysisFinding'), 'statuses'], [
					'label' => __('Compliance Analysis Finding Status')
				])
				->selectField('ComplianceAnalysisFinding-expired', [ClassRegistry::init('ComplianceAnalysisFinding'), 'getStatusFilterOption'], [
					'label' => __('Compliance Analysis Finding Expired')
				]);

		if (AppModule::loaded('CustomFields')) {
			$this->customFieldsFilters($advancedFilterConfig);
		}

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function relatedFilters($advancedFilterConfig)
	{
		$advancedFilterConfig
			->group('ComplianceManagement', [
				'name' => __('Compliance Analysis')
			])
				->multipleSelectField('CompliancePackage-compliance_package_regulator_id', [ClassRegistry::init('CompliancePackageRegulator'), 'getList'], [
					'label' => __('Compliance Package'),
					'findField' => 'ComplianceManagement.CompliancePackageItem.CompliancePackage.compliance_package_regulator_id',
					'fieldData' => 'ComplianceManagement.CompliancePackageItem.CompliancePackage.compliance_package_regulator_id',
				])
				->textField('CompliancePackage-package_id', [
					'label' => __('Requirement Chapter Number'),
					'findField' => 'ComplianceManagement.CompliancePackageItem.CompliancePackage.package_id',
					'fieldData' => 'ComplianceManagement.CompliancePackageItem.CompliancePackage.package_id',
				])
				->textField('CompliancePackage-name', [
					'label' => __('Requirement Chapter Title'),
					'findField' => 'ComplianceManagement.CompliancePackageItem.CompliancePackage.name',
					'fieldData' => 'ComplianceManagement.CompliancePackageItem.CompliancePackage.name',
				])
				->textField('CompliancePackageItem-item_id', [
					'label' => __('Requirement Item Number'),
					'findField' => 'ComplianceManagement.CompliancePackageItem.item_id',
					'fieldData' => 'ComplianceManagement.CompliancePackageItem.item_id',
				])
				->textField('CompliancePackageItem-name', [
					'label' => __('Requirement Item Title'),
					'findField' => 'ComplianceManagement.CompliancePackageItem.name',
					'fieldData' => 'ComplianceManagement.CompliancePackageItem.name',
				]);

		return $advancedFilterConfig;
	}

	public function parentModel()
	{
		return 'CompliancePackageRegulator';
	}

	public function parentNode($type) {
		if (!$this->id) {
			return null;
		}

		$data = $this->find('first', [
			'conditions' => [
				'ComplianceManagement.id' => $this->id
			],
			'fields' => [
				'ComplianceManagement.compliance_package_item_id'
			],
			'recursive' => -1
		]);

		$cpiId = $data['ComplianceManagement']['compliance_package_item_id'];

		$data = $this->CompliancePackageItem->find('first', [
			'conditions' => [
				'CompliancePackageItem.id' => $cpiId
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

	public function getDisplayFilterFields()
	{
		return ['CompliancePackage-compliance_package_regulator_id', 'CompliancePackageItem-name'];
	}

	public function getObjectStatusConfig() {
        return [
        	'compliance_analysis_finding_expired' => [//
            	'title' => __('Finding Expired'),
                'inherited' => [
                	'ComplianceAnalysisFinding' => 'expired'
            	],
            	'storageSelf' => false,
            	'regularTrigger' => true,
            ],
            'security_policy_expired_reviews' => [//
            	'title' => __('Policy Review Expired'),
                'inherited' => [
                	'SecurityPolicy' => 'expired_reviews'
            	],
            	'storageSelf' => false,
            	'regularTrigger' => true,
            ],
            'compliance_exception_expired' => [//
            	'title' => __('Exception Expired'),
            	'type' => 'danger',
                'inherited' => [
                	'ComplianceException' => 'expired'
            	],
            	'storageSelf' => false,
            	'regularTrigger' => true,
            ],
            'security_service_control_in_design' => [//
                'title' => __('Control in Design'),
                'inherited' => [
                	'SecurityService' => 'control_in_design'
            	],
            	'storageSelf' => false
            ],
            'security_service_audits_last_not_passed' => [//
            	'title' => __('Control Audit Failed'),
                'inherited' => [
                	'SecurityService' => 'audits_last_not_passed'
            	],
            	'type' => 'danger',
            	'storageSelf' => false,
            	'regularTrigger' => true,
            ],
            'security_service_audits_last_missing' => [//
            	'title' => __('Control Audit Expired'),
                'inherited' => [
                	'SecurityService' => 'audits_last_missing'
            	],
            	'storageSelf' => false,
            	'regularTrigger' => true,
            ],
            'security_service_maintenances_last_missing' => [//
            	'title' => __('Control Maintenance Expired'),
                'inherited' => [
                	'SecurityService' => 'maintenances_last_missing'
            	],
            	'storageSelf' => false,
            	'regularTrigger' => true,
            ],
            'security_service_maintenances_last_not_passed' => [
            	'title' => __('Control Maintenance Failed'),
            	'type' => 'danger',
                'inherited' => [
                	'SecurityService' => 'maintenances_last_not_passed'
            	],
            	'storageSelf' => false,
            	'regularTrigger' => true,
            ],
            'security_service_control_with_issues' => [//
            	'title' => __('Control Issues'),
                'inherited' => [
                	'SecurityService' => 'control_with_issues'
            	],
            	'type' => 'danger',
            	'storageSelf' => false
            ],
            'risk_expired_reviews' => [//
            	'title' => __('Risk Review Expired'),
                'inherited' => [
                	'Risk' => 'expired_reviews'
            	],
            	'storageSelf' => false,
            	'regularTrigger' => true,
            ],
            'risk_risk_above_appetite' => [
            	'title' => __('Risk Above Appetite'),
                'inherited' => [
                	'Risk' => 'risk_above_appetite'
            	],
            	'type' => 'danger',
            	'storageSelf' => false,
            	'hidden' => true
            ],
            'third_party_risk_expired_reviews' => [//
            	'title' => __('Third Party Risk Review Expired'),
                'inherited' => [
                	'ThirdPartyRisk' => 'expired_reviews'
            	],
            	'storageSelf' => false,
            	'regularTrigger' => true,
            ],
            'third_party_risk_risk_above_appetite' => [
            	'title' => __('Third Party Risk Above Appetite'),
                'inherited' => [
                	'ThirdPartyRisk' => 'risk_above_appetite'
            	],
            	'type' => 'danger',
            	'storageSelf' => false,
            	'hidden' => true
            ],
            'business_continuity_expired_reviews' => [//
            	'title' => __('Business Continuity Review Expired'),
                'inherited' => [
                	'BusinessContinuity' => 'expired_reviews'
            	],
            	'storageSelf' => false,
            	'regularTrigger' => true,
            ],
            'business_continuity_risk_above_appetite' => [
            	'title' => __('Business Continuity Above Appetite'),
                'inherited' => [
                	'BusinessContinuity' => 'risk_above_appetite'
            	],
            	'storageSelf' => false,
            	'type' => 'danger',
            	'hidden' => true
            ],
            'project_expired' => [
                'title' => __('Project Expired'),
                'inherited' => [
                    'Project' => 'expired'
                ],
                'storageSelf' => false,
                'regularTrigger' => true,
            ],
            'project_expired_tasks' => [
                'title' => __('Project Task Expired'),
                'inherited' => [
                    'Project' => 'expired_tasks'
                ],
                'storageSelf' => false,
                'regularTrigger' => true,
            ],
            'project_ongoing' => [
            	'title' => __('Project Ongoing'),
                'inherited' => [
                	'Project' => 'ongoing'
            	],
            	'type' => 'success',
                'storageSelf' => false
            ],
            'project_planned' => [
                'title' => __('Project Planned'),
                'inherited' => [
                    'Project' => 'planned'
                ],
                'type' => 'success',
                'storageSelf' => false
            ],
            'project_closed' => [
                'title' => __('Project Closed'),
                'inherited' => [
                    'Project' => 'closed'
                ],
                'type' => 'success',
                'storageSelf' => false
            ],
            'project_no_updates' => [
                'title' => __('Project Missing Updates'),
                'inherited' => [
                    'Project' => 'no_updates'
                ],
                'storageSelf' => false,
                'regularTrigger' => true,
            ],
        ];
    }

    public function getReportsConfig()
    {
    	$compliancePackage = [
			'title' => __('Compliance Package by Treatment Strategy'),
			'description' => __('This chart shows for every chapter in a given compliance package two stacked bars, one shows the treatment strategy (Compliant, NA and Not Applicable) and the second one the status (Ok, Missing Policies, Missing Audits, Projects Expired, Risk Expired and No Controls / Policies).'),
			'type' => ReportBlockChartSetting::TYPE_BAR,
			'dataFn' => 'packageByTreatmentStrategy'
		];

		return [
			'finder' => [
				'options' => [
					'contain' => Hash::merge($this->containList(), [
						'CompliancePackageItem' => [
							'CompliancePackage' => [
								'CompliancePackageRegulator'
							]
						]
					])
				]
			],
			'table' => [
				'model' => [
					'CompliancePackageItem', 'ComplianceAnalysisFinding', 'ComplianceException',
				]
			],
			'chart' => [
				1 => array_merge($compliancePackage, [
					'templateType' => ReportTemplate::TYPE_ITEM
				]),
				2 => array_merge($compliancePackage, [
					'templateType' => ReportTemplate::TYPE_SECTION
				]),
			]
		];
	}

	public function getSectionInfoConfig()
	{
		return [
			'map' => [
				'Asset' => [
					'BusinessUnit',
				],
				'ComplianceAnalysisFinding',
				'SecurityService' => [
					'SecurityServiceAudit',
					'SecurityServiceIssue',
					'SecurityServiceMaintenance',
				],
				'Project' => [
					'ProjectAchievement',
				],
				'SecurityPolicy',
				'ComplianceException',
				'Risk',
				'ThirdPartyRisk',
				'BusinessContinuity',
				'Legal'
			]
		];
	}

	public function afterFind($results, $primary = false) {
		if ($primary) {
			foreach ($results as $key => &$item) {
				if (isset($item['ComplianceTreatmentStrategy']['name']) && $item['ComplianceTreatmentStrategy']['name'] === null) {
					$item['ComplianceTreatmentStrategy']['name'] = __('Undefined');
				}
			}
		}

		return $results;
	}

	/**
	 * Create a record having default basic values possible.
	 */
	public function addItem($compliancePackageItemId) {
		if (is_array($compliancePackageItemId)) {
			$ret = true;
			foreach ($compliancePackageItemId as $id) {
				$ret &= $this->addItem($id);
			}

			return $ret;
		}

		$owners = $this->_findItemInheritedOwners($compliancePackageItemId);
		$data = [
			'ComplianceManagement' => [
				'compliance_package_item_id' => $compliancePackageItemId,
				'compliance_treatment_strategy_id' => null,
				'efficacy' => '0',
				'Owner' => $owners
			]
		];

		$this->create();
		$this->set($data);
		
		$ret = $this->save();
		
		return (bool) $ret;
	}

	protected function _findItemInheritedOwners($itemId)
	{
		$data = $this->CompliancePackageItem->find('first', [
			'conditions' => [
				'CompliancePackageItem.id' => $itemId
			],
			'contain' => [
				'CompliancePackage' => [
					'CompliancePackageRegulator' => [
						'Owner',
						'OwnerGroup'
					]
				]
			]
		]);

		$regulator = $data['CompliancePackage']['CompliancePackageRegulator'];

		$ownersUsers = Hash::extract($regulator, 'Owner.{n}.id');
        $ownersGroups = Hash::extract($regulator, 'OwnerGroup.{n}.id');
        $owners = [];
        foreach ($ownersUsers as $ownerUser) {
            $owners[] = 'User-' . $ownerUser;
        }
         foreach ($ownersGroups as $ownerGroup) {
            $owners[] = 'Group-' . $ownerGroup;
        }

        // fallback for possible empty owners
        // which shouldn't happen as its mandatory way back in CompliancePackageRegulator model
        if (empty($owners)) {
            $owners = ['User-' . ADMIN_ID];
        }

        return $owners;
	}

	/**
	 * @deprecated in favor of AppModel::findByHabtm()
	 */
	public function findByProjects($data = array()) {
		$this->ComplianceManagementsProject->Behaviors->attach('Containable', array(
				'autoFields' => false
			)
		);
		$this->ComplianceManagementsProject->Behaviors->attach('Search.Searchable');

		$query = $this->ComplianceManagementsProject->getQuery('all', array(
			'conditions' => array(
				'ComplianceManagementsProject.project_id' => $data['project_id']
			),
			'fields' => array(
				'ComplianceManagementsProject.compliance_management_id'
			)
		));

		return $query;
	}

	public function getThirdParties() {
		$data = $this->CompliancePackageItem->CompliancePackage->ThirdParty->find('all', array(
			'conditions' => array(
			),
			'fields' => array(
				'ThirdParty.id',
				'ThirdParty.name',
				'ThirdParty.description'
			),
			'contain' => array(
				'CompliancePackage' => array(
					'CompliancePackageItem'
				)
			),
			'order' => array( 'ThirdParty.id' => 'ASC' ),

		));
		$data = $this->filterComplianceData($data);

		$list = array();
		foreach ($data as $item) {
			$list[$item['ThirdParty']['id']] = $item['ThirdParty']['name'];
		}

		return $list;
	}

	public function getOwners() {
		$this->Owner->virtualFields['full_name'] = 'CONCAT(Owner.name, " ", Owner.surname)';
		$owners = $this->Owner->find('list', array(
			'conditions' => array(),
			'fields' => array('Owner.id', 'Owner.full_name'),
		));

		return $owners;
	}

	public function getStrategies() {
		$strategies = $this->ComplianceTreatmentStrategy->find('list', array(
			'fields' => array('ComplianceTreatmentStrategy.id', 'ComplianceTreatmentStrategy.name'),
			'order' => array('ComplianceTreatmentStrategy.name' => 'ASC'),
			'recursive' => -1
		));
		
		return $strategies;
	}

	public function getExceptions() {
		$exceptions = $this->ComplianceException->find('list', array(
			'fields' => array('ComplianceException.id', 'ComplianceException.title'),
			'order' => array('ComplianceException.title' => 'ASC'),
			'recursive' => -1
		));

		return $exceptions;
	}

	public function getLegals() {
		$legals = $this->Legal->find('list', array(
			'fields' => array('Legal.id', 'Legal.name'),
			'order' => array('Legal.name' => 'ASC'),
			'recursive' => -1
		));

		return $legals;
	}

	public function getControlFilterStatusOptions() {
		$arr = array(
			'audits_last_missing' => 'Audits Last missing text',
			'maintenances_last_missing' => 'maintenance missing text from model'
		);

		return $arr;
	}

	public function getServices() {
		$services = $this->SecurityService->find('list', array(
			'fields' => array('SecurityService.id', 'SecurityService.name')
		));

		return $services;
	}

	public function getPolicies() {
		return $this->SecurityPolicy->getListWithType();
	}

	public function getRisks() {
		$risks = $this->Risk->find('list', array(
			'fields' => array('Risk.id', 'Risk.title'),
			'order' => array('Risk.title' => 'ASC'),
			'recursive' => -1
		));

		return $risks;
	}

	public function getProjects()
    {
    	return $this->Project->getList(false);
    }

	public function getProjectsNotCompleted() {
		return $this->Project->getList();
	}

	public function getThirdPartyRisks() {
		$risks = $this->ThirdPartyRisk->find('list', array(
			'fields' => array('ThirdPartyRisk.id', 'ThirdPartyRisk.title'),
			'order' => array('ThirdPartyRisk.title' => 'ASC'),
			'recursive' => -1
		));

		return $risks;
	}

	public function getBusinessContinuities() {
		$risks = $this->BusinessContinuity->find('list', array(
			'fields' => array('BusinessContinuity.id', 'BusinessContinuity.title'),
			'order' => array('BusinessContinuity.title' => 'ASC'),
			'recursive' => -1
		));

		return $risks;
	}

	public function getAssets() {
		return $this->Asset->getList();
	}

	public function getAnalysisFindings() {
		return $this->ComplianceAnalysisFinding->getList();
	}

	public function filterComplianceData($data) {
		return filterComplianceData($data);
	}

	/**
	 * Get commonly needed Compliance data through to Third Party name.
	 * 
	 * @param  array  $ids ComplianceManagement IDs.
	 */
	public function getCommonComplianceData($ids = array()) {
		$data = $this->find('all', array(
			'conditions' => array(
				'ComplianceManagement.id' => $ids
			),
			'fields' => array(
				'ComplianceManagement.id',
				'ComplianceManagement.compliance_package_item_id',
				'CompliancePackageItem.*',
				'CompliancePackage.name',
				'CompliancePackage.compliance_package_regulator_id',
				'CompliancePackageRegulator.name'
			),
			'joins' => $this->thirdPartyJoins,
			'recursive' => 0
		));

		// fill array keys with the item's ID
		$data = array_combine(Hash::extract($data, '{n}.ComplianceManagement.id'), array_values($data));
		
		return $data;
	}

	// sync missing compliance management rows in the table
	public function syncObjects() {
		$data = $this->CompliancePackageItem->find('list', [
			'fields' => ['id'],
			'recursive' => -1
		]);

		return $this->ComplianceAnalysisFinding->complianceIntegrityCheck($data);
	}

	public function hasSectionIndex()
	{
		return true;
	}
}
