<?php
App::uses('AppAudit', 'Model');
App::uses('InheritanceInterface', 'Model/Interface');

class BusinessContinuityPlanAudit extends AppAudit implements InheritanceInterface {
	protected $auditParentModel = 'BusinessContinuityPlan';
	public $displayField = 'planned_date';
	
	public $mapping = array(
		'indexController' => array(
			'basic' => 'businessContinuityPlanAudits',
			'advanced' => 'businessContinuityPlanAudits',
			'params' => array('business_continuity_plan_id')
		),
		'titleColumn' => false,
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
				'audit_metric_description', 'audit_success_criteria', 'result_description'
			)
		),
		'AuditLog.Auditable',
		'Utils.SoftDelete',
		'ObjectStatus.ObjectStatus',
		'UserFields.UserFields',
		'ModuleDispatcher' => [
			'behaviors' => [
				'NotificationSystem.NotificationSystem',
				'Reports.Report',
			]
		],
		'Visualisation.Visualisation',
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
		'SubSection' => [
			'parentField' => 'business_continuity_plan_id'
		],
		'AdvancedFilters.AdvancedFilters'
	);

	public $validate = [
		'start_date' => array(
			'date' => array(
				'rule' => array('date', 'ymd'),
				'required' => true,
				'allowEmpty' => false,
				'on' => 'update',
				'message' => 'Enter a valid date.'
			),
		),
		'end_date' => array(
			'date' => array(
				'rule' => array('date', 'ymd'),
				'required' => true,
				'allowEmpty' => false,
				'on' => 'update',
				'message' => 'Enter a valid date.'
			),
			'afterStartDate' => array(
				'rule' => array('checkEndDate', 'start_date'),
				'message' => 'End date must happen after the start date.'
			)
		),
	];

	public $createValidate = [
		'planned_date' => [
			'date' => [
				'rule' => ['date', 'ymd'],
				'required' => true,
				'message' => 'Enter a valid date.'
			],
		],
	];

	public $belongsTo = array(
		'BusinessContinuityPlan',
		'User'
	);

	public $hasMany = array(
		// 'Asset',
	);

	public $hasOne = array(
		'BusinessContinuityPlanAuditImprovement'
	);

	/*
     * static enum: Model::function()
     * @access static
     */
    public static function results($value = null) {
        $options = array(
            self::RESULT_FAILED => __('Fail'),
            self::RESULT_SUCCESS => __('Pass'),
        );
        return parent::enum($value, $options);
    }

    const RESULT_FAILED = 0;
    const RESULT_SUCCESS = 1;

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Business Continuity Plan Audits');
		$this->_group = parent::SECTION_GROUP_CONTROL_CATALOGUE;

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
		];

		$this->fieldData = [
			'business_continuity_plan_id' => [
				'label' => __('Business Continuity Plan'),
				'editable' => true,
				'empty' => __('Choose one ...'),
				'renderHelper' => ['BusinessContinuityPlanAudits', 'businessContinuityPlanField'],
				'macro' => [
					'name' => 'business_continuity_plan'
				]
			],
			'audit_metric_description' => [
				'label' => __('Audit Metric'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('At the time of creating the Security Service, a metric was defined in order to be able to measure the level of efficacy of the control. This should be utilized as the base for this audit review.'),
				'macro' => [
					'name' => 'metric_description'
				]
			],
			'audit_success_criteria' => [
				'label' => __('Metric Success Criteria'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('At the time of creating the Security Service, a success criteria was defined in order to evaluate if the metric results are within acceptable threasholds (audit pass) or not (audit not pass).'),
				'macro' => [
					'name' => 'success_criteria'
				]
			],
			'result' => [
				'label' => __('Audit Result'),
				'editable' => true,
				'inlineEdit' => true,
				'options' => [$this, 'results'],
				'description' => __('After evluating the audit evidence, success criteria, etc you are able to conclude with the audit result. Pass or Fail are the available options.')
			],
			'result_description' => [
				'label' => __('Audit Conclusion'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Describe what evidence was avilable, the accuracy and integrity of the metrics taken and if the metrics are within the expected threasholds or not.'),
				'macro' => [
					'name' => 'conclusion'
				]
			],
			'user_id' => [
				'label' => __('Audit Owner'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Register the person who has worked on this audit (the auditor name)'),
				'macro' => [
					'name' => 'owner'
				]
			],
			'planned_date' => [
				'label' => __('Planned Date'),
				'editable' => true,
				'inlineEdit' => true,
			],
			'start_date' => [
				'label' => __('Audit Start Date'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Register the date at which this audit started.')
			],
			'end_date' => [
				'label' => __('Audit End Date'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Register the date at which this audit ended.')
			],
			'BusinessContinuityPlanAuditImprovement' => [
				'label' => __('Business Continuity Plan Audit Improvements'),
				'editable' => false,
				'usable' => false
			]
		];

		$this->advancedFilterSettings = [
			'pdf_title' => __('Business Continuity Plan Audit'),
			'pdf_file_name' => __('business_continuity_plan_audit'),
			'csv_file_name' => __('business_continuity_plan_audit'),
			'bulk_actions' => true,
			'history' => true,
            'trash' => true,
            'use_new_filters' => true
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
				->multipleSelectField('business_continuity_plan_id', [ClassRegistry::init('BusinessContinuityPlan'), 'getList'], [
					'showDefault' => true
				])
				->textField('audit_metric_description', [
					'showDefault' => true
				])
				->textField('audit_success_criteria', [
					'showDefault' => true
				])
				->dateField('planned_date', [
					'showDefault' => true
				])
				->dateField('start_date', [
					'showDefault' => true
				])
				->dateField('end_date', [
					'showDefault' => true
				])
				->multipleSelectField('result', [$this, 'results'], [
					'showDefault' => true
				])
				->textField('result_description', [
					'showDefault' => true
				])
				->objectStatusField('ObjectStatus_audit_missing', 'audit_missing');

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getDisplayFilterFields()
    {
        return ['business_continuity_plan_id', 'planned_date'];
    }

	public function getObjectStatusConfig() {
        return [
        	'audit_failed' => [
        		'title' => __('Audit Failed'),
                'callback' => [$this, 'statusAuditFailed'],
                'type' => 'danger',
                'storageSelf' => false,
            	'trigger' => [
                    [
                        'model' => $this->BusinessContinuityPlan,
                        'trigger' => 'ObjectStatus.trigger.audits_last_passed'
                    ],
                ],
        	],
        	'audit_missing' => [
        		'title' => __('Audit Expired'),
                'callback' => [$this, 'statusAuditMissing'],
                'storageSelf' => false,
                'trigger' => [
                    [
                        'model' => $this->BusinessContinuityPlan,
                        'trigger' => 'ObjectStatus.trigger.audits_last_missing'
                    ],
                ],
                'regularTrigger' => true,
        	],
        ];
    }

    public function statusAuditFailed() {
		$data = $this->find('count', [
			'conditions' => [
				'BusinessContinuityPlanAudit.id' => $this->id,
				'BusinessContinuityPlanAudit.result' => self::RESULT_FAILED,
			],
			'fields' => [
				'BusinessContinuityPlanAudit.id'
			],
			'recursive' => -1
		]);

    	return (boolean) $data;
    }

    public function statusAuditMissing() {
		$data = $this->find('count', [
			'conditions' => [
				'BusinessContinuityPlanAudit.id' => $this->id,
				'BusinessContinuityPlanAudit.result IS NULL',
				'DATE(BusinessContinuityPlanAudit.planned_date) < DATE(NOW())'
			],
			'fields' => [
				'BusinessContinuityPlanAudit.id'
			],
			'recursive' => -1
		]);

    	return (boolean) $data;
    }

    public function statusOngoingCorrectiveActions() {
		$data = $this->BusinessContinuityPlanAuditImprovement->find('count', [
			'conditions' => [
				'BusinessContinuityPlanAuditImprovement.business_continuity_plan_audit_id' => $this->id
			],
			'recursive' => -1
		]);

		return (boolean) $data;
	}

	public function getNotificationSystemConfig()
	{
		$config = parent::getNotificationSystemConfig();
		$config['notifications']['object_reminder'] = $this->_getModelObjectReminderNotification();

		$config['notifications'] = array_merge($config['notifications'], [
			'business_continuity_plan_audit_begin_-1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.BusinessContinuityPlanAuditBegin',
				'days' => -1,
				'label' => __('Scheduled Audit in (-1 day)'),
				'description' => __('Notifies 1 day before a scheduled Business Continuity Plan Audit begins')
			],
			'business_continuity_plan_audit_begin_-5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.BusinessContinuityPlanAuditBegin',
				'days' => -5,
				'label' => __('Scheduled Audit in (-5 days)'),
				'description' => __('Notifies 5 days before a scheduled Business Continuity Plan Audit begins')
			],
			'business_continuity_plan_audit_begin_-10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.BusinessContinuityPlanAuditBegin',
				'days' => -10,
				'label' => __('Scheduled Audit in (-10 days)'),
				'description' => __('Notifies 10 days before a scheduled Business Continuity Plan Audit begins')
			],
			'business_continuity_plan_audit_begin_-20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.BusinessContinuityPlanAuditBegin',
				'days' => -20,
				'label' => __('Scheduled Audit in (-20 days)'),
				'description' => __('Notifies 20 days before a scheduled Business Continuity Plan Audit begins')
			],
			'business_continuity_plan_audit_begin_-30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.BusinessContinuityPlanAuditBegin',
				'days' => -30,
				'label' => __('Scheduled Audit in (-30 days)'),
				'description' => __('Notifies 30 days before a scheduled Business Continuity Plan Audit begins')
			],
			'business_continuity_plan_audit_begin_+1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.BusinessContinuityPlanAuditBegin',
				'days' => 1,
				'label' => __('Scheduled Audit in (+1 day)'),
				'description' => __('Notifies 1 day after a scheduled Business Continuity Plan Audit begins')
			],
			'business_continuity_plan_audit_begin_+5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.BusinessContinuityPlanAuditBegin',
				'days' => 5,
				'label' => __('Scheduled Audit in (+5 days)'),
				'description' => __('Notifies 5 days after a scheduled Business Continuity Plan Audit begins')
			],
			'business_continuity_plan_audit_begin_+10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.BusinessContinuityPlanAuditBegin',
				'days' => 10,
				'label' => __('Scheduled Audit in (+10 days)'),
				'description' => __('Notifies 10 days after a scheduled Business Continuity Plan Audit begins')
			],
			'business_continuity_plan_audit_begin_+20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.BusinessContinuityPlanAuditBegin',
				'days' => 20,
				'label' => __('Scheduled Audit in (+20 days)'),
				'description' => __('Notifies 20 days after a scheduled Business Continuity Plan Audit begins')
			],
			'business_continuity_plan_audit_begin_+30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.BusinessContinuityPlanAuditBegin',
				'days' => 30,
				'label' => __('Scheduled Audit in (+30 days)'),
				'description' => __('Notifies 30 days after a scheduled Business Continuity Plan Audit begins')
			],
		]);

		return $config;
	}

    public function getReportsConfig()
	{
		return [
			'table' => [
				'model' => [
					'BusinessContinuityPlan',
				]
			],
		];
	}

    public function afterSave($created, $options = array()) {
    	parent::afterSave($created, $options);

		if ($created) {
			$this->triggerObjectStatus();
		}
	}

	public function parentModel() {
		return 'BusinessContinuityPlan';
	}

	public function parentNode($type) {
        return $this->visualisationParentNode('business_continuity_plan_id');
    }

    public function checkEndDate($endDate, $startDate) {
		if (!isset($this->data[$this->name][$startDate])) {
			return true;
		}

		return $this->data[$this->name][$startDate] <= $endDate['end_date'];
	}

	public function setCreateValidation() {
		$this->validate = array_merge($this->validate, $this->createValidate);
	}

	public function getRecordTitle($id) {
		$data = $this->find('first', array(
			'conditions' => array(
				'BusinessContinuityPlanAudit.id' => $id,
				'BusinessContinuityPlanAudit.deleted' => [0, 1],
			),
			'fields' => array(
				'BusinessContinuityPlanAudit.planned_date',
				'BusinessContinuityPlan.title'
			),
			'recursive' => 0
		));

		if (empty($data)) {
			return '';
		}
		
		return sprintf('%s (%s)', $data['BusinessContinuityPlanAudit']['planned_date'], $data['BusinessContinuityPlan']['title']);
	}

	private function logStatusToPlan() {
		$record = $this->find('first', array(
			'conditions' => array(
				'id' => $this->id
			),
			'fields' => array('result'),
			'recursive' => -1
		));

		if ($record['BusinessContinuityPlanAudit']['result'] != $this->data['BusinessContinuityPlanAudit']['result']) {
			$statuses = getAuditStatuses();
			$this->BusinessContinuityPlan->addNoteToLog(__('Audit status changed to %s', $statuses[$this->data['BusinessContinuityPlanAudit']['result']]));
			$this->BusinessContinuityPlan->setSystemRecord($this->data['BusinessContinuityPlanAudit']['business_continuity_plan_id'], 2);
		}
	}

	/**
	 * Get audits completion statuses.
	 * @param  int $id   Security Service ID.
	 */
	public function getStatuses($id) {
		$audits = $this->find('count', array(
			'conditions' => array(
				'BusinessContinuityPlanAudit.business_continuity_plan_id' => $id,
				'BusinessContinuityPlanAudit.result' => null,
				'BusinessContinuityPlanAudit.planned_date <' => date('Y-m-d', strtotime('now'))
			),
			'recursive' => -1
		));

		$all_done = false;
		if (empty($audits)) {
			$all_done = true;
		}

		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));

		$audit = $this->find('first', array(
			'conditions' => array(
				'BusinessContinuityPlanAudit.business_continuity_plan_id' => $id,
				'BusinessContinuityPlanAudit.planned_date <=' => $today
			),
			'fields' => array(
				'BusinessContinuityPlanAudit.id',
				'BusinessContinuityPlanAudit.result',
				'BusinessContinuityPlanAudit.planned_date',
				'BusinessContinuityPlanAuditImprovement.id'
			),
			'order' => array('BusinessContinuityPlanAudit.planned_date' => 'DESC'),
			'contain' => array(
				'BusinessContinuityPlanAuditImprovement'
			)
		));

		$last_passed = false;
		if (empty($audit) ||
			(!empty($audit) && in_array($audit['BusinessContinuityPlanAudit']['result'], array(1, null)))) {
			$last_passed = true;
		}
		elseif (!empty($audit)) {
			$this->BusinessContinuityPlan->lastAuditFailed = $audit['BusinessContinuityPlanAudit']['planned_date'];
		}

		$improvements = false;
		$audit = $this->find('first', array(
			'conditions' => array(
				'BusinessContinuityPlanAudit.business_continuity_plan_id' => $id,
				'BusinessContinuityPlanAudit.planned_date <=' => $today,
				'BusinessContinuityPlanAudit.result' => array(1, 0)
			),
			'fields' => array(
				'BusinessContinuityPlanAudit.id',
				'BusinessContinuityPlanAudit.result',
				'BusinessContinuityPlanAuditImprovement.id'
			),
			'order' => array('BusinessContinuityPlanAudit.planned_date' => 'DESC'),
			'contain' => array(
				'BusinessContinuityPlanAuditImprovement'
			)
		));

		if (isset($audit['BusinessContinuityPlanAuditImprovement']['id']) && $audit['BusinessContinuityPlanAuditImprovement']['id'] != null) {
			$improvements = true;
		}

		$audit = $this->find('first', array(
			'conditions' => array(
				'BusinessContinuityPlanAudit.business_continuity_plan_id' => $id,
				'BusinessContinuityPlanAudit.planned_date <' => $today
			),
			'fields' => array(
				'BusinessContinuityPlanAudit.id',
				'BusinessContinuityPlanAudit.result',
				'BusinessContinuityPlanAudit.planned_date'
			),
			'order' => array('BusinessContinuityPlanAudit.planned_date' => 'DESC'),
			'recursive' => -1
		));

		$lastMissing = false;
		if (!empty($audit) && $audit['BusinessContinuityPlanAudit']['result'] == null) {
			$this->BusinessContinuityPlan->lastAuditMissing = $audit['BusinessContinuityPlanAudit']['planned_date'];
			$lastMissing = true;
		}
		
		$arr = array(
			'audits_all_done' => (string) (int) $all_done,
			'audits_last_missing' => (string) (int) $lastMissing,
			'audits_last_passed' => (string) (int) $last_passed,
			'audits_improvements' =>(int) $improvements
		);

		return $arr;
	}

	public function logMissingAudits() {
		$yesterday = CakeTime::format('Y-m-d', CakeTime::fromString('-1 day'));

		$audits = $this->find('all', array(
			'conditions' => array(
				'BusinessContinuityPlanAudit.planned_date' => $yesterday
			),
			'fields' => array(
				'BusinessContinuityPlanAudit.id',
				'BusinessContinuityPlanAudit.result',
				'BusinessContinuityPlanAudit.planned_date',
				'BusinessContinuityPlanAudit.business_continuity_plan_id'
			),
			'order' => array('BusinessContinuityPlanAudit.planned_date' => 'DESC'),
			'recursive' => -1
		));

		foreach ($audits as $item) {
			$msg = __('Last audit missing (%s)', $item['BusinessContinuityPlanAudit']['planned_date']);

			if ($item['BusinessContinuityPlanAudit']['result'] == null) {
				$bcpId = $item['BusinessContinuityPlanAudit']['business_continuity_plan_id'];

				$this->BusinessContinuityPlan->id = $bcpId;
				$this->BusinessContinuityPlan->addNoteToLog($msg);
				$this->BusinessContinuityPlan->setSystemRecord($bcpId, 2);
			}
		}
	}

	public function hasSectionIndex()
	{
		return true;
	}
}
