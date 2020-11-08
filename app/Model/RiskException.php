<?php
App::uses('AppModel', 'Model');
App::uses('UserFields', 'UserFields.Lib');
App::uses('ImportToolModule', 'ImportTool.Lib');
App::uses('Tag', 'Model');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlockChartSetting', 'Reports.Model');

class RiskException extends AppModel
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
		'notificationSystem' => true,
		'workflow' => false
	);

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'title', 'description', 'author_id', 'expiration', 'closure_date', 'status'
			)
		),
		'ModuleDispatcher' => [
			'behaviors' => [
				'CustomFields.CustomFields',
				'Reports.Report',
			]
		],
		'AuditLog.Auditable',
		'Utils.SoftDelete',
		'Visualisation.Visualisation',
		'ObjectStatus.ObjectStatus',
		'UserFields.UserFields' => [
			'fields' => [
				'Requester' => [
					'mandatory' => false
				]
			]
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
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
		'expiration' => array(
			'rule' => 'date',
			'required' => true
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
		)
	);

	public $hasMany = array(
		'Tag' => array(
			'className' => 'Tag',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'Tag.model' => 'RiskException'
			)
		)
	);

	public $hasAndBelongsToMany = array(
		'Risk',
		'ThirdPartyRisk',
		'BusinessContinuity'
	);

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

	public function __construct($id = false, $table = null, $ds = null)
	{
		//
		// Init helper Lib for UserFields Module
		$UserFields = new UserFields();
		//
		
		$this->label = __('Risk Exception');
		$this->_group = parent::SECTION_GROUP_RISK_MGT;

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
		];

		$this->fieldData = [
			'title' => [
				'label' => __('Title'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Provide a descriptive title. Example: Lack of budget for Antivirus for Mac-OS.')
			],
			'description' => [
				'label' => __('Description'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('OPTIONAL: Describe why this Exception is required, who authorized, Etc.')
			],
			'Requester' => $UserFields->getFieldDataEntityData($this, 'Requester', [
				'label' => __('Requester'), 
				'description' => __('Select one or more accounts that are asociated with this exception'),
				'quickAdd' => true,
				'inlineEdit' => true,
			]),
			'expiration' => [
				'label' => __('Expiration'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Set the deadline for this Exception')
			],
			'expired' => [
				'label' => __('Expired'),
				'editable' => false,
				'hidden' => true
			],
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
				'renderHelper' => ['RiskExceptions', 'closureDateField']
			],
			'status' => [
				'label' => __('Status'),
				'editable' => true,
				'inlineEdit' => true,
				'options' => [$this, 'statuses'],
				'description' => __('Defines if the exception is still valid or not.')
			],
			'Tag' => array(
                'label' => __('Tags'),
				'editable' => true,
				'type' => 'tags',
				'description' => __('OPTIONAL: Use tags for this exception, examples are: Approved, To be reviewed, Important, Etc.'),
				'empty' => __('Add a tag')
            ),
			'Risk' => [
				'label' => __('Asset Risk'),
				'editable' => false,
			],
			'ThirdPartyRisk' => [
				'label' => __('Third Party Risk'),
				'editable' => false,
			],
			'BusinessContinuity' => [
				'label' => __('Business Continuity'),
				'editable' => false,
			],
		];

		$this->notificationSystem = array(
			'macros' => array(
				'EXCEPTION_ID' => array(
					'field' => 'RiskException.id',
					'name' => __('Risk Exception ID')
				),
				'EXCEPTION_TITLE' => array(
					'field' => 'RiskException.title',
					'name' => __('Risk Exception Title')
				),
				'EXCEPTION_REQUESTER' => $UserFields->getNotificationSystemData('Requester', [
					'name' => __('Risk Exception Requester')
				]),
				'EXCEPTION_EXPIRATION' => array(
					'field' => 'RiskException.expiration',
					'name' => __('Risk Exception Expiration')
				),
				'EXCEPTION_CLOSURE_DATE' => array(
					'field' => 'RiskException.closure_date',
					'name' => __('Risk Exception Closure Date')
				),
				'EXCEPTION_STATUS' => array(
					'type' => 'status',
					'name' => __('Risk Exception Status'),
					'status' => array(
						'model' => 'RiskException'
					)
				),
			),
			'customEmail' =>  true
		);

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Risk Exceptions'),
			'pdf_file_name' => __('risk_exceptions'),
			'csv_file_name' => __('risk_exceptions'),
			'bulk_actions' => true,
			'history' => true,
            'trash' => true,
			'use_new_filters' => true,
			'add' => true,
			'bulk_actions' => true,
			'history' => true,
            'trash' => true,
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
				->textField('title', [
					'showDefault' => true
				])
				->userField('Requester', 'Requester', [
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

		$this->Risk->relatedFilters($advancedFilterConfig)
			->field('Risk', ['showDefault' => true]);

		$this->ThirdPartyRisk->relatedFilters($advancedFilterConfig)
			->field('ThirdPartyRisk', ['showDefault' => true]);

		$this->BusinessContinuity->relatedFilters($advancedFilterConfig)
			->field('BusinessContinuity', ['showDefault' => true]);
				
		if (AppModule::loaded('CustomFields')) {
			$this->customFieldsFilters($advancedFilterConfig);
		}

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getImportToolConfig()
	{
		return [
			'RiskException.title' => [
				'name' => __('Title'),
				'headerTooltip' => __('This field is mandatory, provide a descriptive title.')
			],
			'RiskException.description' => [
				'name' => __('Description'),
				'headerTooltip' => __('Optional, describe why this Exception is required, who authorized, Etc.')
			],
			'RiskException.Requester' => UserFields::getImportArgsFieldData('Requester', [
				'name' => __('Requester'),
			], true),
			'RiskException.expiration' => [
				'name' => __('Expiration'),
				'headerTooltip' => __('This field is mandatory, set the deadline for this Risk Exception. At the expiration day, a full re-assessment on this exception is usually done. Set the date with the format YYYY-MM-DD.')
			],
			'RiskException.closure_date_toggle' => [
				'name' => __('Closure Date Auto'),
				'headerTooltip' => __('Optional, set "%s" if you want to manually set a closure date, otherwise set "%s" or left this field blank and the date will be set when the status of the exception is changed from “open” to “close”.', self::CLOSURE_DATE_TOGGLE_OFF, self::CLOSURE_DATE_TOGGLE_ON)
			],
			'RiskException.closure_date' => [
				'name' => __('Closure Date'),
				'headerTooltip' => __('Mandatory if Closure Date Auto is set to "%s", set the date with the format YYYY-MM-DD when this exception was closed.', self::CLOSURE_DATE_TOGGLE_OFF)
			],
			'RiskException.status' => [
				'name' => __('Status'),
				'headerTooltip' => __(
					'This field is mandatory, you need to set status by inserting one of the following values: %s',
					ImportToolModule::formatList($this->statuses())
				)
			],
			'RiskException.Tag' => [
				'name' => __('Tags'),
				'model' => 'Tag',
				'callback' => [
					'beforeImport' => ['Tag', 'convertTagsImport']
				],
				'headerTooltip' => __('Optional and accepts tags separated by "|". For example "Critical|Approved"')
			],
		];
	}

	public function getReportsConfig()
	{
		return [
			'table' => [
				'model' => [
					'Risk', 'ThirdPartyRisk', 'BusinessContinuity'
				]
			],
			'chart' => [
                1 => [
                    'title' => __('Top 10 Exceptions by Risk'),
                    'description' => __('This pie charts shows the top 10 risks by their number of asociated exceptions.'),
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

	public function beforeSave($options = array())
	{
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
			'risk_exception_expiration_-1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => -1,
				'label' => __('Risk Exception Expiring in (-1 day)'),
				'description' => __('Notifies 1 day before a Risk Exception expires')
			],
			'risk_exception_expiration_-5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => -5,
				'label' => __('Risk Exception Expiring in (-5 days)'),
				'description' => __('Notifies 5 days before a Risk Exception expires')
			],
			'risk_exception_expiration_-10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => -10,
				'label' => __('Risk Exception Expiring in (-10 days)'),
				'description' => __('Notifies 10 days before a Risk Exception expires')
			],
			'risk_exception_expiration_-20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => -20,
				'label' => __('Risk Exception Expiring in (-20 days)'),
				'description' => __('Notifies 20 days before a Risk Exception expires')
			],
			'risk_exception_expiration_-30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => -30,
				'label' => __('Risk Exception Expiring in (-30 days)'),
				'description' => __('Notifies 30 days before a Risk Exception expires')
			],
			'risk_exception_expiration_+1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => 1,
				'label' => __('Risk Exception Expiring in (+1 day)'),
				'description' => __('Notifies 1 day after a Risk Exception expires')
			],
			'risk_exception_expiration_+5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => 5,
				'label' => __('Risk Exception Expiring in (+5 days)'),
				'description' => __('Notifies 5 days after a Risk Exception expires')
			],
			'risk_exception_expiration_+10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => 10,
				'label' => __('Risk Exception Expiring in (+10 days)'),
				'description' => __('Notifies 10 days after a Risk Exception expires')
			],
			'risk_exception_expiration_+20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => 20,
				'label' => __('Risk Exception Expiring in (+20 days)'),
				'description' => __('Notifies 20 days after a Risk Exception expires')
			],
			'risk_exception_expiration_+30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => 30,
				'label' => __('Risk Exception Expiring in (+30 days)'),
				'description' => __('Notifies 30 days after a Risk Exception expires')
			]
		]);
		
		return $config;
	}

	public function getObjectStatusConfig() {
        return [
            'expired' => [
            	'title' => __('Expired'),
                'callback' => [$this, 'statusExpired'],
                'trigger' => [
                    [
                        'model' => $this->Risk,
                        'trigger' => 'ObjectStatus.trigger.risk_exception_expired'
                    ],
                    [
                        'model' => $this->ThirdPartyRisk,
                        'trigger' => 'ObjectStatus.trigger.risk_exception_expired'
                    ],
                    [
                        'model' => $this->BusinessContinuity,
                        'trigger' => 'ObjectStatus.trigger.risk_exception_expired'
                    ],
                    [
                        'model' => $this->Risk,
                        'trigger' => 'ObjectStatus.trigger.exceptions_issues'
                    ],
                    [
                        'model' => $this->ThirdPartyRisk,
                        'trigger' => 'ObjectStatus.trigger.exceptions_issues'
                    ],
                    [
                        'model' => $this->BusinessContinuity,
                        'trigger' => 'ObjectStatus.trigger.exceptions_issues'
                    ],
                ],
                'regularTrigger' => true,
            ],
        ];
    }

    public function statusExpired($conditions = null) {
        return parent::statusExpired([
            'RiskException.status !=' => RISK_EXCEPTION_CLOSED,
			'DATE(RiskException.expiration) < DATE(NOW())'
        ]);
    }

	public function afterSave($created, $options = array()) {
		if (!empty($this->id)) {
			return $this->updateRiskIssues();
		}

		return true;
	}

	/**
     * @deprecated status, in favor of RiskException::statusExpired()
     */
	public function statusIsExpired($id) {
		$today = date('Y-m-d', strtotime('now'));

		$isExpired = $this->find('count', array(
			'conditions' => array(
				'RiskException.id' => $id,
				'RiskException.status !=' => RISK_EXCEPTION_CLOSED,
				'DATE(RiskException.expiration) <' => $today
			),
			'recursive' => -1
		));

		return $isExpired;
	}

	public function afterAuditProperty($Model, $propertyName, $oldValue, $newValue) {
		$this->propertyChangeNotification($propertyName, $oldValue, $newValue, 'status', 'StatusChange', self::statuses());
	}

	public function getRisks() {
		$data = $this->Risk->find('list', array(
			'order' => array('Risk.title' => 'ASC'),
			'fields' => array('Risk.id', 'Risk.title'),
			'recursive' => -1
		));

		return $data;
	}

	public function getThirdPartyRisks() {
		$data = $this->ThirdPartyRisk->find('list', array(
			'order' => array('ThirdPartyRisk.title' => 'ASC'),
			'fields' => array('ThirdPartyRisk.id', 'ThirdPartyRisk.title'),
			'recursive' => -1
		));

		return $data;
	}

	public function getBusinessContinuities() {
		$data = $this->BusinessContinuity->find('list', array(
			'order' => array('BusinessContinuity.title' => 'ASC'),
			'fields' => array('BusinessContinuity.id', 'BusinessContinuity.title'),
			'recursive' => -1
		));

		return $data;
	}

	public function getExceptionStatuses() {
		return getCommonStatuses();
	}

	public function getStatuses() {
		if (isset($this->data['RiskException']['status'])) {
			$statuses = getCommonStatuses();

			return $statuses[$this->data['RiskException']['status']];
		}

		return false;
	}

	/**
	 * Trigger updating exception issues field for Risks.
	 */
	private function updateRiskIssues() {
		$ret = $this->Risk->saveExceptionIssues($this->getRelatedRisks('Risk', $this->id));
		$ret &= $this->ThirdPartyRisk->saveExceptionIssues($this->getRelatedRisks('ThirdPartyRisk', $this->id));
		$ret &= $this->BusinessContinuity->saveExceptionIssues($this->getRelatedRisks('BusinessContinuity', $this->id));

		return $ret;
	}

	public function getRelatedRisks($model, $riskId) {
		$assocId = $this->hasAndBelongsToMany[$model]['associationForeignKey'];
		$with = $this->hasAndBelongsToMany[$model]['with'];
		$this->{$with}->bindModel(array(
			'belongsTo' => array($model)
		));

		//risk_exception_id
		$foreignKey = $this->hasAndBelongsToMany[$model]['foreignKey'];
		$data = $this->{$with}->find('list', array(
			'conditions' => array(
				$with . '.' . $foreignKey => $riskId
			),
			'fields' => array($with . '.' . $assocId),
			'recursive' => 0
		));

		return $data;
	}

	public function editSaveQuery() {
		$this->expiredStatusToQuery('expired', 'expiration');
	}

	public function expiredStatusToQuery($expiredField = 'expired', $dateField = 'date') {
		if (!isset($this->data['RiskException']['expired']) && isset($this->data['RiskException']['expiration'])) {
			$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));
			if ($this->data['RiskException']['expiration'] < $today && $this->data['RiskException']['status'] == 1) {
				$this->data['RiskException']['expired'] = '1';
			}
			else {
				$this->data['RiskException']['expired'] = '0';
			}
		}
	}

	public function logExpirations($ids) {
		$this->logToModel('Risk', $ids);
		$this->logToModel('ThirdPartyRisk', $ids);
		$this->logToModel('BusinessContinuity', $ids);
	}

	public function logToModel($model, $ids = array()) {
		$assocId = $this->hasAndBelongsToMany[$model]['associationForeignKey'];

		$habtmModel = $this->hasAndBelongsToMany[$model]['with'];

		$this->{$habtmModel}->bindModel(array(
			'belongsTo' => array('RiskException')
		));

		//risk_exception_id
		$foreignKey = $this->hasAndBelongsToMany[$model]['foreignKey'];
		$data = $this->{$habtmModel}->find('all', array(
			'conditions' => array(
				$habtmModel . '.' . $foreignKey => $ids
			),
			'fields' => array($habtmModel . '.' . $assocId, 'RiskException.title'),
			'recursive' => 0
		));

		foreach ($data as $item) {
			$msg = __('Risk Exception "%s" expired', $item['RiskException']['title']);

			$this->{$model}->id = $item[$habtmModel][$assocId];
			$this->{$model}->addNoteToLog($msg);
			$this->{$model}->setSystemRecord($item[$habtmModel][$assocId], 2);
		}
	}

	/*public function getIssues($id = array()) {
		if (empty($id)) {
			return false;
		}

		$data = $this->find('list', array(
			'conditions' => array(
				'OR' => array(
					array(
						'SecurityService.id' => $id,
						'SecurityService.audits_all_done' => 0
					),
					array(
						'SecurityService.id' => $id,
						'SecurityService.audits_last_passed' => 0
					)
				)
			),
			'fields' => array('SecurityService.id', 'SecurityService.name'),
			'recursive' => 0
		));

		return $data;
	}*/

	public function hasSectionIndex()
	{
		return true;
	}
}
