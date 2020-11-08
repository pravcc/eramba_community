<?php
App::uses('AppModel', 'Model');
App::uses('UserFields', 'UserFields.Lib');
App::uses('ImportToolModule', 'ImportTool.Lib');
App::uses('Tag', 'Model');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlockChartSetting', 'Reports.Model');

class PolicyException extends AppModel {
	public $displayField = 'title';

	public $mapping = array(
		'titleColumn' => 'title',
		'logRecords' => true,
		'notificationSystem' => array('index'),
		'workflow' => false
	);

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
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
		'AuditLog.Auditable',
		'Utils.SoftDelete',
		'Visualisation.Visualisation',
		'ObjectStatus.ObjectStatus',
		'UserFields.UserFields' => [
			'fields' => ['Requestor']
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
		'Classification' => array(
			'className' => 'PolicyExceptionClassification'
		),
	);

	public $hasAndBelongsToMany = array(
		'SecurityPolicy',
		'ThirdParty',
		'Asset'
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
		
		$this->label = __('Policy Exception');
		$this->_group = parent::SECTION_GROUP_CONTROL_CATALOGUE;

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
			'asset' => [
				'label' => __('Asset')
			],
		];

		$this->fieldData = [
			'title' => [
				'label' => __('Title'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Give a descriptive title to this Exception')
			],
			'description' => [
				'label' => __('Description'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('OPTIONAL: Describe the Policy Exception in detail (when, what, where, why, whom, how).')
			],
			'expiration' => [
				'label' => __('Expiration'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Select the date when this exception expires'),
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
				'renderHelper' => ['PolicyExceptions', 'closureDateField']
			],
			'status' => [
				'label' => __('Status'),
				'editable' => true,
				'inlineEdit' => true,
				'options' => [$this, 'statuses'],
				'description' => __('Select the status for this exception')
			],
			'SecurityPolicy' => [
				'label' => __('Security Policy Items'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('OPTIONAL: If a policy exception is requested is expected that you have a policy documented in eramba. Being able to link your exceptions to your policies allows you further analysis on which policies are not business aligned.'),
				'quickAdd' => true,
			],
			'ThirdParty' => [
				'label' => __('Third Parties'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('OPTIONAL: Select any third parties affected by this policy exception.'),
				'quickAdd' => true,
			],
			'Requestor' => $UserFields->getFieldDataEntityData($this, 'Requestor', [
				'label' => __('Requestor'),
				'description' => __('This is usually the individual who requested the exception.'),
				'quickAdd' => true,
				'inlineEdit' => true,
			]),
			'Classification' => array(
                'label' => __('Tag'),
				'editable' => true,
				'type' => 'tags',
				'options' => [$this, 'getClassifications'],
				'description' => __('OPTIONAL: Use tags to classify your exceptions (high relevance, etc).'),
				'empty' => __('Add a tag'),
				'macro' => [
					'name' => 'Tag'
				]
            ),
            'Asset' => array(
                'label' => __('Assets'),
                'group' => 'asset',
				'editable' => true,
				'description' => __('OPTIONAL: select one or more assets that are affected by this policy exception.'),
				'quickAdd' => true,
				'inlineEdit' => true
            ),
		];

		$this->notificationSystem = array(
			'macros' => array(
				'EXCEPTION_ID' => array(
					'field' => 'PolicyException.id',
					'name' => __('Policy Exception ID')
				),
				'EXCEPTION_TITLE' => array(
					'field' => 'PolicyException.title',
					'name' => __('Policy Exception Title')
				),
				'EXCEPTION_DESCRIPTION' => array(
					'field' => 'PolicyException.description',
					'name' => __('Policy Exception Description')
				),
				'EXCEPTION_REQUESTER' => $UserFields->getNotificationSystemData('Requestor', [
					'name' => __('Policy Exception Requester')
				]),
				'EXCEPTION_EXPIRATION' => array(
					'field' => 'PolicyException.expiration',
					'name' => __('Policy Exception Expiration')
				),
				'EXCEPTION_CLOSURE_DATE' => array(
					'field' => 'PolicyException.closure_date',
					'name' => __('Policy Exception Closure Date')
				),
				'EXCEPTION_STATUS' => array(
					'type' => 'status',
					'name' => __('Policy Exception Status'),
					'status' => array(
						'model' => 'PolicyException'
					)
				),
			),
			'customEmail' =>  true
		);

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Policy Exceptions'),
			'pdf_file_name' => __('policy_exceptions'),
			'csv_file_name' => __('policy_exceptions'),
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
				->userField('Requestor', 'Requestor', [
					'showDefault' => true
				])
				->dateField('expiration', [
					'showDefault' => true
				])
				->multipleSelectField('Classification-name', [$this, 'getClassifications'], [
					'label' => __('Tags')
				])
				->dateField('closure_date', [
					'showDefault' => true
				])
				->selectField('status', [$this, 'statuses'], [
					'showDefault' => true
				])
				->objectStatusField('ObjectStatus_expired', 'expired');

		$this->SecurityPolicy->relatedFilters($advancedFilterConfig)
			->field('SecurityPolicy', ['showDefault' => true]);

		$this->Asset->relatedFilters($advancedFilterConfig)
			->field('Asset', ['showDefault' => true]);

		$this->ThirdParty->relatedFilters($advancedFilterConfig);

		if (AppModule::loaded('CustomFields')) {
			$this->customFieldsFilters($advancedFilterConfig);
		}

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getImportToolConfig()
	{
		return [
			'PolicyException.title' => [
				'name' => __('Title'),
				'headerTooltip' => __('This field is mandatory, provide a descriptive title.')
			],
			'PolicyException.description' => [
				'name' => __('Description'),
				'headerTooltip' => __('Optional, describe the Policy Exception in detail (when, what, where, why, whom, how).')
			],
			'PolicyException.Requestor' => UserFields::getImportArgsFieldData('Requestor', [
				'name' => __('Requester'),
			], false),
			'PolicyException.expiration' => [
				'name' => __('Expiration'),
				'headerTooltip' => __('This field is mandatory, exceptions are not eternal, they must expire at some point time. This setting will let you define notifications before this date to be sent to the requestor or anyone you define. Set the date with the format YYYY-MM-DD.')
			],
			'PolicyException.SecurityPolicy' => [
				'name' => __('Security Policy Items'),
				'model' => 'SecurityPolicy',
				'headerTooltip' => __('Optional, accepts multiple names separated by "|". You need to enter the name of a Security Policy, you can find them at Controls Catalogue / Security Policies'),
				'objectAutoFind' => true
			],
			'PolicyException.ThirdParty' => [
				'name' => __('Third Parties'),
				'model' => 'ThirdParty',
				'headerTooltip' => __('Optional, accepts multiple names separated by "|". You need to enter the name of a Third Party, you can find them at Organization / Third Parties'),
				'objectAutoFind' => true
			],
			'PolicyException.closure_date_toggle' => [
				'name' => __('Closure Date Auto'),
				'headerTooltip' => __('Optional, set "%s" if you want to manually set a closure date, otherwise set "%s" or left this field blank and the date will be set when the status of the exception is changed from “open” to “close”.', self::CLOSURE_DATE_TOGGLE_OFF, self::CLOSURE_DATE_TOGGLE_ON)
			],
			'PolicyException.closure_date' => [
				'name' => __('Closure Date'),
				'headerTooltip' => __('Mandatory if Closure Date Auto is set to "%s", set the date with the format YYYY-MM-DD when this exception was closed.', self::CLOSURE_DATE_TOGGLE_OFF)
			],
			'PolicyException.status' => [
				'name' => __('Status'),
				'headerTooltip' => __(
					'This field is mandatory, you need to set status by inserting one of the following values: %s',
					ImportToolModule::formatList($this->statuses())
				)
			],
			'PolicyException.Classification' => [
				'name' => __('Tags'),
				'model' => 'Classification',
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
					'SecurityPolicy'
				]
			],
			'chart' => [
                1 => [
                    'title' => __('Top 10 Exceptions by Policy'),
                    'description' => __('This pie charts shows the top 10 policies by their number of asociated exceptions.'),
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

	public function getMacrosConfig()
	{
		return [
			'assoc' => [
			],
		];
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

		if (isset($this->data['PolicyException']['ThirdParty'])) {
			$this->invalidateRelatedNotExist('ThirdParty', 'ThirdParty', $this->data['PolicyException']['ThirdParty']);
		}

		if (isset($this->data['PolicyException']['SecurityPolicy'])) {
			$this->invalidateRelatedNotExist('SecurityPolicy', 'SecurityPolicy', $this->data['PolicyException']['SecurityPolicy']);
		}

		return true;
	}

	public function beforeSave($options = array()) {
		// $this->transformDataToHabtm(['SecurityPolicy', 'ThirdParty', 'Asset']);

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

	public function afterSave($created, $options = array()) {
		if (isset($this->data['PolicyException']['Classification'])) {
			$this->Classification->deleteAll(['Classification.policy_exception_id' => $this->id]);
			$this->saveClassifications($this->data['PolicyException']['Classification'], $this->id);
		}
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
			'policy_exception_expiration_-1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => -1,
				'label' => __('Policy Exception Expiring in (-1 day)'),
				'description' => __('Notifies 1 day before a Policy Exception expires')
			],
			'policy_exception_expiration_-5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => -5,
				'label' => __('Policy Exception Expiring in (-5 days)'),
				'description' => __('Notifies 5 days before a Policy Exception expires')
			],
			'policy_exception_expiration_-10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => -10,
				'label' => __('Policy Exception Expiring in (-10 days)'),
				'description' => __('Notifies 10 days before a Policy Exception expires')
			],
			'policy_exception_expiration_-20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => -20,
				'label' => __('Policy Exception Expiring in (-20 days)'),
				'description' => __('Notifies 20 days before a Policy Exception expires')
			],
			'policy_exception_expiration_-30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => -30,
				'label' => __('Policy Exception Expiring in (-30 days)'),
				'description' => __('Notifies 30 days before a Policy Exception expires')
			],
			'policy_exception_expiration_+1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => 1,
				'label' => __('Policy Exception Expiring in (+1 day)'),
				'description' => __('Notifies 1 day after a Policy Exception expires')
			],
			'policy_exception_expiration_+5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => 5,
				'label' => __('Policy Exception Expiring in (+5 days)'),
				'description' => __('Notifies 5 days after a Policy Exception expires')
			],
			'policy_exception_expiration_+10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => 10,
				'label' => __('Policy Exception Expiring in (+10 days)'),
				'description' => __('Notifies 10 days after a Policy Exception expires')
			],
			'policy_exception_expiration_+20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => 20,
				'label' => __('Policy Exception Expiring in (+20 days)'),
				'description' => __('Notifies 20 days after a Policy Exception expires')
			],
			'policy_exception_expiration_+30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ExceptionExpiration',
				'days' => 30,
				'label' => __('Policy Exception Expiring in (+30 days)'),
				'description' => __('Notifies 30 days after a Policy Exception expires')
			]
		]);
		
		return $config;
	}

	private function saveClassifications($labels, $id) {
		if (empty($labels)) {
			return true;
		}

		$labels = explode(',', $labels);

		foreach ($labels as $name) {
			$tmp = array(
				'policy_exception_id' => $id,
				'name' => $name
			);

			$this->Classification->create();
			if (!$this->Classification->save($tmp)) {
				return false;
			}
		}

		return true;
	}

	public function getSectionInfoConfig()
	{
		return [
			'map' => [
				'SecurityPolicy',
				'ThirdParty',
				'Asset',
			]
		];
	}

	public function getObjectStatusConfig() {
        return [
            'expired' => [
            	'title' => __('Expired'),
                'callback' => [$this, 'statusExpired'],
                'regularTrigger' => true,
            ],
        ];
    }

    public function statusExpired($conditions = null) {
        return parent::statusExpired([
            'PolicyException.status !=' => POLICY_EXCEPTION_CLOSED,
			'DATE(PolicyException.expiration) < DATE(NOW())'
        ]);
    }

    /**
     * @deprecated status, in favor of PolicyException::statusExpired()
     */
	public function statusIsExpired($id) {
		$today = date('Y-m-d', strtotime('now'));

		$isExpired = $this->find('count', array(
			'conditions' => array(
				'PolicyException.id' => $id,
				'PolicyException.status !=' => POLICY_EXCEPTION_CLOSED,
				'DATE(PolicyException.expiration) <' => $today
			),
			'recursive' => -1
		));

		return $isExpired;
	}

	public function getStatuses() {
		if (isset($this->data['PolicyException']['status'])) {
			$statuses = getCommonStatuses();

			return $statuses[$this->data['PolicyException']['status']];
		}

		return false;
	}

	public function getSecurityPolicies() {
		return $this->SecurityPolicy->getListWithType();
	}

	public function getExceptionStatuses() {
		return static::statuses();
	} 

	public function getClassifications() {
		$rawData = $this->Classification->find('list', array(
			'order' => array('Classification.name' => 'ASC'),
			'fields' => array('Classification.id', 'Classification.name'),
			'group' => array('Classification.name'),
			'recursive' => -1
		));

		$data = array();
		foreach ($rawData as $item) {
			$data[$item] = $item;
		}

		return $data;
	}

	public function findByClassifications($data) {
		$this->Classification->Behaviors->attach('Containable', array('autoFields' => false));
		$this->Classification->Behaviors->attach('Search.Searchable');

		$query = $this->Classification->getQuery('all', array(
			'conditions' => array(
				'Classification.name' => $data['classification_name']
			),
			'contain' => array(),
			'fields' => array(
				'Classification.policy_exception_id'
			),
		));

		return $query;
	}

	public function editSaveQuery() {
		$this->expiredStatusToQuery('expired', 'expiration');
	}

	public function expiredStatusToQuery($expiredField = 'expired', $dateField = 'date') {
		if (!isset($this->data['PolicyException']['expired']) && isset($this->data['PolicyException']['expiration'])) {
			$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));
			if ($this->data['PolicyException']['expiration'] < $today && $this->data['PolicyException']['status'] == 1) {
				$this->data['PolicyException']['expired'] = '1';
			}
			else {
				$this->data['PolicyException']['expired'] = '0';
			}
		}
	}

	public function findByThirdParty($data = array()) {
		$this->PolicyExceptionsThirdParty->Behaviors->attach('Containable', array(
				'autoFields' => false
			)
		);
		$this->PolicyExceptionsThirdParty->Behaviors->attach('Search.Searchable');

		$query = $this->PolicyExceptionsThirdParty->getQuery('all', array(
			'conditions' => array(
				'PolicyExceptionsThirdParty.third_party_id' => $data['third_party_id']
			),
			'fields' => array(
				'PolicyExceptionsThirdParty.policy_exception_id'
			)
		));

		return $query;
	}

	public function findByRequestor($data = array()) {
		$this->PolicyExceptionsUser->Behaviors->attach('Containable', array(
				'autoFields' => false
			)
		);
		$this->PolicyExceptionsUser->Behaviors->attach('Search.Searchable');

		$query = $this->PolicyExceptionsUser->getQuery('all', array(
			'conditions' => array(
				'PolicyExceptionsUser.user_id' => $data['user_id']
			),
			'fields' => array(
				'PolicyExceptionsUser.policy_exception_id'
			)
		));

		return $query;
	}

	public function logExpirations($ids) {
		$this->logToModel('SecurityPolicy', $ids);
	}

	public function logToModel($model, $ids = array()) {
		$assocId = $this->hasAndBelongsToMany[$model]['associationForeignKey'];

		$habtmModel = $this->hasAndBelongsToMany[$model]['with'];

		$this->{$habtmModel}->bindModel(array(
			'belongsTo' => array('PolicyException')
		));

		//risk_exception_id
		$foreignKey = $this->hasAndBelongsToMany[$model]['foreignKey'];
		$data = $this->{$habtmModel}->find('all', array(
			'conditions' => array(
				$habtmModel . '.' . $foreignKey => $ids
			),
			'fields' => array($habtmModel . '.' . $assocId, 'PolicyException.title'),
			'recursive' => 0
		));
// debug($ids);
// exit;
		foreach ($data as $item) {
			$msg = __('Policy Exception "%s" expired', $item['PolicyException']['title']);

			$this->{$model}->id = $item[$habtmModel][$assocId];
			$this->{$model}->addNoteToLog($msg);
			$this->{$model}->setSystemRecord($item[$habtmModel][$assocId], 2);
		}

	}

	public function expiredConditions($data = array()){
		$conditions = array();
		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));
		if($data['expired'] == 1){
			$conditions = array(
				'PolicyException.status' => 1,
				'PolicyException.expiration <' => $today
			);
		}
		elseif($data['expired'] == 0){
			$conditions = array(
				'PolicyException.status' => 0
			);
		}

		return $conditions;
	}

	public function getAssets() {
		return $this->Asset->getList();
	}

	public function hasSectionIndex()
	{
		return true;
	}

}
