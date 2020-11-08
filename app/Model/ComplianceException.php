<?php
App::uses('AppModel', 'Model');
App::uses('UserFields', 'UserFields.Lib');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');
App::uses('ImportToolModule', 'ImportTool.Lib');
App::uses('Tag', 'Model');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlockChartSetting', 'Reports.Model');

class ComplianceException extends AppModel
{
	public $displayField = 'title';

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $mapping = array(
		'titleColumn' => 'title',
		'logRecords' => true,
		'notificationSystem' => array('index'),
		'workflow' => false
	);

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'AuditLog.Auditable',
		'Utils.SoftDelete',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'title', 'description', 'expiration', 'closure_date', 'status'
			)
		),
		'ModuleDispatcher' => [
			'behaviors' => [
				'CustomFields.CustomFields',
				'Reports.Report',
			]
		],
		'Visualisation.Visualisation',
		'ObjectStatus.ObjectStatus',
		'UserFields.UserFields' => [
			'fields' => ['Requestor']
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'AdvancedQuery.AdvancedFinder',
		'Macros.Macro',
		'ImportTool.ImportTool',
		'AdvancedFilters.AdvancedFilters',
		'CustomLabels.CustomLabels'
	);

	public $validate = array(
		'title' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'expiration' => array(
			'rule' => 'date'
		),
		'closure_date' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'This field cannot be left blank'
			),
			'date' => array(
				'rule' => 'date',
				'message' => 'Please enter a valid date'
			)
		),
		'Requestor' => array(
			'rule' => array('multiple', array('min' => 1))
		),
		'status' => [
			'rule' => ['inList', [self::STATUS_CLOSED, self::STATUS_OPEN]],
			'required' => true,
			'message' => 'Not supported value'
		],
		'closure_date_toggle' => array(
			'rule' => ['inList', [self::CLOSURE_DATE_TOGGLE_ON, self::CLOSURE_DATE_TOGGLE_OFF]],
			'required' => true,
			'message' => 'Not supported value'
		),
	);

	public $hasMany = array(
		'Tag' => array(
			'className' => 'Tag',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'Tag.model' => 'ComplianceException'
			)
		)
	);

	public $hasAndBelongsToMany = array(
		'ComplianceManagement'
	);

	public function __construct($id = false, $table = null, $ds = null)
	{
		//
		// Init helper Lib for UserFields Module
		$UserFields = new UserFields();
		//
		
		$this->label = __('Compliance Exception');
        $this->_group = parent::SECTION_GROUP_COMPLIANCE_MGT;

		$this->notificationSystem = array(
			'macros' => array(
				'EXCEPTION_ID' => array(
					'field' => 'ComplianceException.id',
					'name' => __('Compliance Exception ID')
				),
				'EXCEPTION_TITLE' => array(
					'field' => 'ComplianceException.title',
					'name' => __('Compliance Exception Title')
				),
				'EXCEPTION_REQUESTER' => $UserFields->getNotificationSystemData('Requestor', [
					'name' => __('Compliance Exception Requester')
				]),
				'EXCEPTION_EXPIRATION' => array(
					'field' => 'ComplianceException.expiration',
					'name' => __('Compliance Exception Expiration')
				),
				'EXCEPTION_CLOSURE_DATE' => array(
					'field' => 'ComplianceException.closure_date',
					'name' => __('Compliance Exception Closure Date')
				),
				'EXCEPTION_STATUS' => array(
					'type' => 'status',
					'name' => __('Compliance Exception Status'),
					'status' => array(
						'model' => 'ComplianceException'
					)
				),
			),
			'customEmail' =>  true
		);

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			),
		);

		$this->fieldData = [
			'title' => array(
				'label' => __('Title'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Provide a descriptive title. Example: Lack of budgets for compliance with 2.3 of PCI-DSS'),
			),
			'description' => array(
				'label' => __('Description'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('OPTIONAL: A good description should include what the compliance is (threat, vulnerabilities, impact, etc.), the options which were considered and discarded, etc.'),
			),
			'expiration' => array(
				'label' => __('Expiration'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Set the date at which this exception will be reconsidered. You can setup notifications to alarm the requester or anyone else before the date is due.'),
			),
			'expired' => array(
				'label' => __('Expired'),
				'editable' => false,
				'hidden' => true
			),
			'closure_date_toggle' => array(
				'label' => __('Closure date auto'),
				'type' => 'toggle',
				'editable' => false,
				'description' => __('Uncheck this option if you want to manually set a closure date, otherwise the date will be set when the status of the exception is changed from “open” to “close”.'),
				'hidden' => true
			),
			'closure_date' => [
				'label' => __('Closure Date'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Set the date when this exception was closed.'),
				'renderHelper' => ['ComplianceExceptions', 'closureDateField']
			],
			'status' => array(
				'label' => __('Status'),
				'editable' => true,
				'inlineEdit' => true,
				'options' => array($this, 'statuses'),
				'description' => __('Register if this exception is closed or open (valid).')
			),
			'Requestor' => $UserFields->getFieldDataEntityData($this, 'Requestor', [
				'label' => __('Requester'),
				'description' => __('This is usually the individual who approved the compliance exception, this is typically someone with sufficient authority to make such decisions.'),
				'quickAdd' => true,
				'inlineEdit' => true,
			]),
			'Tag' => array(
                'label' => __('Tags'),
				'editable' => true,
				'type' => 'tags',
				'description' => __('OPTIONAL: Use tags to profile your compliance exceptions, examples are "PCI-DSS", "Budget Issues", Etc.'),
				'empty' => __('Add a tag')
            ),
            'ComplianceManagement' => array(
				'label' => __('Compliance Management'),
				'editable' => false,
				'hidden' => true
			),
		];

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Compliance Exceptions'),
			'pdf_file_name' => __('compliance_exceptions'),
			'csv_file_name' => __('compliance_exceptions'),
			'bulk_actions' => true,
			'trash' => true,
			'history' => true,
			'use_new_filters' => true,
			'add' => true,
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
				->textField('title', [
					'showDefault' => true
				])
				->userField('Requestor', 'Requestor', [
					'showDefault' => true
				])
				->dateField('expiration', [
					'showDefault' => true
				])
				->multipleSelectField('Tag-title', [$this, 'getTags'], [
					'label' => __('Tags'),
					'showDefault' => true
				])
				->dateField('closure_date', [
					'showDefault' => true
				])
				->selectField('status', [$this, 'statuses'], [
					'showDefault' => true
				])
				->objectStatusField('ObjectStatus_expired', 'expired');

		$this->ComplianceManagement->relatedFilters($advancedFilterConfig);

		if (AppModule::loaded('CustomFields')) {
			$this->customFieldsFilters($advancedFilterConfig);
		}

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getImportToolConfig()
	{
		return [
			'ComplianceException.title' => [
				'name' => __('Title'),
				'headerTooltip' => __('This field is mandatory, provide a descriptive title.')
			],
			'ComplianceException.description' => [
				'name' => __('Description'),
				'headerTooltip' => __('Optional, good description should include what the compliance is (threat, vulnerabilities, impact, etc.), the options which were considered and discarded, etc.')
			],
			'ComplianceException.Requestor' => UserFields::getImportArgsFieldData('Requestor', [
				'name' => __('Requester'),
			], false),
			'ComplianceException.expiration' => [
				'name' => __('Expiration'),
				'headerTooltip' => __('This field is mandatory, set the date at which this exception will be reconsidered. You can setup notifications to alarm the requester or anyone else before the date is due. Set the date with the format YYYY-MM-DD.')
			],
			'ComplianceException.closure_date_toggle' => [
				'name' => __('Closure Date Auto'),
				'headerTooltip' => __('Optional, set "%s" if you want to manually set a closure date, otherwise set "%s" or left this field blank and the date will be set when the status of the exception is changed from “open” to “close”.', self::CLOSURE_DATE_TOGGLE_OFF, self::CLOSURE_DATE_TOGGLE_ON)
			],
			'ComplianceException.closure_date' => [
				'name' => __('Closure Date'),
				'headerTooltip' => __('Mandatory if Closure Date Auto is set to "%s", set the date with the format YYYY-MM-DD when this exception was closed.', self::CLOSURE_DATE_TOGGLE_OFF)
			],
			'ComplianceException.status' => [
				'name' => __('Status'),
				'headerTooltip' => __(
					'This field is mandatory, you need to set status by inserting one of the following values: %s',
					ImportToolModule::formatList($this->statuses())
				)
			],
			'ComplianceException.Tag' => [
				'name' => __('Tags'),
				'model' => 'Tag',
				'callback' => [
					'beforeImport' => ['Tag', 'convertTagsImport']
				],
				'headerTooltip' => __('Optional and accepts tags separated by "|". For example "Critical|Approved"')
			],
		];
	}

	/*
     * static enum: Model::function()
     * @access static
     */
    public static function statuses($value = null) {
        $options = array(
            self::STATUS_CLOSED => __('Closed'),
            self::STATUS_OPEN => __('Open'),
        );
        return parent::enum($value, $options);
    }

    const STATUS_CLOSED = 0;
    const STATUS_OPEN = 1;

    /*
     * static enum: Model::function()
     * @access static
     */
    public static function closureDateToggles($value = null) {
        $options = array(
            self::CLOSURE_DATE_TOGGLE_ON => __('Enabled Auto Closure'),
			self::CLOSURE_DATE_TOGGLE_OFF => __('Disabled Auto Closure'),
        );
        return parent::enum($value, $options);
    }

    const CLOSURE_DATE_TOGGLE_ON = 1;
    const CLOSURE_DATE_TOGGLE_OFF = 0;

    public function getObjectStatusConfig() {
        return [
            'expired' => [
            	'title' => __('Expired'),
                'callback' => [$this, 'statusExpired'],
                'trigger' => [
                    [
                        'model' => $this->ComplianceManagement,
                        'trigger' => 'ObjectStatus.trigger.compliance_exception_expired'
                    ],
                ],
                'regularTrigger' => true,
            ],
        ];
    }

    public function statusExpired($conditions = null) {
        return parent::statusExpired([
			'ComplianceException.status !=' => COMPLIANCE_EXCEPTION_CLOSED,
			'DATE(ComplianceException.expiration) < DATE(NOW())'
        ]);
    }

    public function getReportsConfig()
	{
		return [
			'finder' => [
				'options' => [
					'contain' => Hash::merge($this->containList(), [
						'ComplianceManagement' => [
							'CompliancePackageItem' => [
								'CompliancePackage' => [
									'CompliancePackageRegulator'
								]
							]
						]
					])
				]
			],
			'table' => [
				'model' => [
					'ComplianceManagement'
				]
			],
			'chart' => [
                1 => [
                    'title' => __('Top Ten Exceptions by Compliance Package'),
                    'description' => __('This pie charts shows the top 10 compliance packages by their number of asociated exceptions.'),
                    'type' => ReportBlockChartSetting::TYPE_PIE,
                    'templateType' => ReportTemplate::TYPE_SECTION,
                    'dataFn' => 'topExceptionsChart'
                ],
                2 => [
                    'title' => __('Exceptions by Duration'),
                    'description' => __('This chart shows exceptions distributed by their duration from start to close date.'),
                    'type' => ReportBlockChartSetting::TYPE_BAR,
                    'templateType' => ReportTemplate::TYPE_SECTION,
                    'dataFn' => 'exceptionsByDurationChart'
                ]
            ]
		];
	}

	public function getSectionInfoConfig()
	{
		return [
			'map' => [
				'ComplianceManagement',
			]
		];
	}

	public function getStatuses() {
		if (isset($this->data['ComplianceException']['status'])) {
			$statuses = getCommonStatuses();

			return $statuses[$this->data['ComplianceException']['status']];
		}

		return false;
	}

	public function afterAuditProperty($Model, $propertyName, $oldValue, $newValue) {
		$this->propertyChangeNotification($propertyName, $oldValue, $newValue, 'status', 'StatusChange', self::statuses());
	}

	public function beforeValidate($options = array())
	{
		if (!empty($this->data[$this->alias]['closure_date_toggle']) && 
			$this->data[$this->alias]['closure_date_toggle'] == 1) {
			$this->validate['closure_date']['notEmpty']['required'] = false;
			$this->validate['closure_date']['notEmpty']['allowEmpty'] = true;
		}
		else {
			$this->validate['closure_date']['notEmpty']['required'] = true;
			$this->validate['closure_date']['notEmpty']['allowEmpty'] = false;
		}

		return true;
	}

	public function beforeSave($options = array()){
		// transforms the data array to save the HABTM relation
		// $this->transformDataToHabtm(array('Requestor'));

		//
		// Set closure date when status is set to CLOSED
		if (isset($this->data[$this->alias]['status']) &&
			isset($this->data[$this->alias]['closure_date_toggle'])) {
			if ($this->data[$this->alias]['status'] == self::STATUS_OPEN) {
				$this->data[$this->alias]['closure_date'] = null;
			} else {
				$id = $this->getId();
				$oldData = false;
				if ($id != false) {
					$oldData = $this->find('first', [
						'fields' => [
							'status'
						],
						'conditions' => [
							'id' => $id
						],
						'recursive' => -1
					]);
				}

				if ($this->data[$this->alias]['status'] == self::STATUS_CLOSED && 
					$this->data[$this->alias]['closure_date_toggle'] == 1 && 
					(!$oldData || $oldData[$this->alias]['status'] == self::STATUS_OPEN)) {
					$this->data[$this->alias]['closure_date'] = date('Y-m-d', time());
				}
			}
		}
		//

		return true;
	}

	public function getNotificationSystemConfig()
	{
		$config = parent::getNotificationSystemConfig();
		$config['notifications']['object_reminder'] = $this->_getModelObjectReminderNotification('Exception');
		
		$config['notifications'] = array_merge($config['notifications'], [
			'status_change' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.StatusChange',
				'key' => 'value',
				'label' => __('Status Change')
			],
			'compliance_exception_expiration_-1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => -1,
				'label' => __('Compliance Exception Expiring in (-1 day)'),
				'description' => __('Notifies 1 day before a Compliance Exception expires')
			],
			'compliance_exception_expiration_-5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => -5,
				'label' => __('Compliance Exception Expiring in (-5 days)'),
				'description' => __('Notifies 5 days before a Compliance Exception expires')
			],
			'compliance_exception_expiration_-10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => -10,
				'label' => __('Compliance Exception Expiring in (-10 days)'),
				'description' => __('Notifies 10 days before a Compliance Exception expires')
			],
			'compliance_exception_expiration_-20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => -20,
				'label' => __('Compliance Exception Expiring in (-20 days)'),
				'description' => __('Notifies 20 days before a Compliance Exception expires')
			],
			'compliance_exception_expiration_-30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => -30,
				'label' => __('Compliance Exception Expiring in (-30 days)'),
				'description' => __('Notifies 30 days before a Compliance Exception expires')
			],
			'compliance_exception_expiration_+1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => 1,
				'label' => __('Compliance Exception Expiring in (+1 day)'),
				'description' => __('Notifies 1 day after a Compliance Exception expires')
			],
			'compliance_exception_expiration_+5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => 5,
				'label' => __('Compliance Exception Expiring in (+5 days)'),
				'description' => __('Notifies 5 days after a Compliance Exception expires')
			],
			'compliance_exception_expiration_+10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => 10,
				'label' => __('Compliance Exception Expiring in (+10 days)'),
				'description' => __('Notifies 10 days after a Compliance Exception expires')
			],
			'compliance_exception_expiration_+20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => 20,
				'label' => __('Compliance Exception Expiring in (+20 days)'),
				'description' => __('Notifies 20 days after a Compliance Exception expires')
			],
			'compliance_exception_expiration_+30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => 30,
				'label' => __('Compliance Exception Expiring in (+30 days)'),
				'description' => __('Notifies 30 days after a Compliance Exception expires')
			]
		]);

		return $config;
	}

	public function getRequestors() {
		$data = $this->Requestor->find('list', array(
			'order' => array('Requestor.full_name' => 'ASC'),
			'fields' => array('Requestor.id', 'Requestor.full_name'),
			'recursive' => -1
		));
		return $data;
	}

	public function getPackages() {
		$data = $this->ComplianceManagement->CompliancePackageItem->CompliancePackage->find('list', array(
			'order' => array('CompliancePackage.name' => 'ASC'),
			'fields' => array('CompliancePackage.id', 'CompliancePackage.name'),
			'recursive' => -1
		));
		return $data;
	}

	public function getExceptionStatuses() {
		return getCommonStatuses();
	}

	/**
     * @deprecated status, in favor of ComplianceException::statusExpired()
     */
	public function statusIsExpired($id) {
		$today = date('Y-m-d', strtotime('now'));

		$isExpired = $this->find('count', array(
			'conditions' => array(
				'ComplianceException.id' => $id,
				'ComplianceException.status !=' => COMPLIANCE_EXCEPTION_CLOSED,
				'DATE(ComplianceException.expiration) <' => $today
			),
			'recursive' => -1
		));

		return $isExpired;
	}

	/**
	 * Callback used by Status Assessment to calculate expired field based on query data before saving and insert it into the query.
	 */
	public function editSaveQuery() {
		$this->expiredStatusToQuery('expired', 'expiration');
	}

	public function expiredStatusToQuery($expiredField = 'expired', $dateField = 'date') {
		if (!isset($this->data['ComplianceException']['expired']) && isset($this->data['ComplianceException']['expiration'])) {
			$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));

			if ($this->data['ComplianceException']['expiration'] < $today && $this->data['ComplianceException']['status'] == 1) {
				$this->data['ComplianceException']['expired'] = '1';
			}
			else {
				$this->data['ComplianceException']['expired'] = '0';
			}
		}
	}

	public function logExpirations($ids) {
		$this->logToModel('CompliancePackageItem', $ids);
	}

	public function logToModel($model, $ids = array()) {

		$data = $this->ComplianceManagement->find('all', array(
			'conditions' => array(
				'ComplianceManagement.compliance_exception_id' => $ids
			),
			'fields' => array('ComplianceManagement.compliance_package_item_id', 'ComplianceException.title'),
			'recursive' => 0
		));

		foreach ($data as $item) {
			$msg = __('Compliance Exception "%s" expired', $item['ComplianceException']['title']);

			$this->ComplianceManagement->CompliancePackageItem->id = $item['ComplianceManagement']['compliance_package_item_id'];
			$this->ComplianceManagement->CompliancePackageItem->addNoteToLog($msg);
			$this->ComplianceManagement->CompliancePackageItem->setSystemRecord($item['ComplianceManagement']['compliance_package_item_id'], 2);
		}

	}

	public function expiredConditions($data = array()){
		$conditions = array();
		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));
		if($data['expired'] == 1){
			$conditions = array(
				'ComplianceException.status' => 1,
				'ComplianceException.expiration <' => $today
			);
		}
		elseif($data['expired'] == 0){
			$conditions = array(
				'ComplianceException.status' => 0
			);
		}

		return $conditions;
	}

	public function findByRequestor($data = array()) {
		$this->ComplianceExceptionsUser->Behaviors->attach('Containable', array(
				'autoFields' => false
			)
		);
		$this->ComplianceExceptionsUser->Behaviors->attach('Search.Searchable');

		$query = $this->ComplianceExceptionsUser->getQuery('all', array(
			'conditions' => array(
				'ComplianceExceptionsUser.user_id' => $data['author_id']
			),
			'fields' => array(
				'ComplianceExceptionsUser.compliance_exception_id'
			)
		));

		return $query;
	}

	public function getThirdParties() {
		return $this->ComplianceManagement->getThirdParties();
	}

	public function hasSectionIndex()
	{
		return true;
	}

}
