<?php
App::uses('AppModel', 'Model');
App::uses('ImportToolModule', 'ImportTool.Lib');

class Goal extends AppModel
{
	public $displayField = 'name';

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
		'Search.Searchable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'owner_id', 'description', 'status', 'audit_metric', 'audit_criteria'
			)
		),
		'AuditLog.Auditable',
		'Utils.SoftDelete',
		'ModuleDispatcher' => [
			'behaviors' => [
				'CustomFields.CustomFields',
				'Reports.Report',
			]
		],
		'ObjectStatus.ObjectStatus',
		'Visualisation.Visualisation',
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
		'SubSection' => [
			'childModels' => true
		],
		'AdvancedFilters.AdvancedFilters'
	);

	public $validate = array(
		'name' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Name is a required field'
		),
		'owner_id' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'status' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Status is required'
			),
			'inList' => array(
				'rule' => array('inList', array(
					GOAL_DRAFT,
					GOAL_DISCARDED,
					GOAL_CURRENT
				)),
				'message' => 'Please select one of the statuses'
			)
		)
	);

	public $belongsTo = array(
		'Owner' => array(
			'className' => 'User',
			'foreignKey' => 'owner_id',
		)
	);

	public $hasMany = array(
		'GoalAudit',
		'GoalAuditDate',
	);

	public $hasAndBelongsToMany = array(
		'SecurityService',
		'Risk',
		'ThirdPartyRisk',
		'BusinessContinuity',
		'Project',
		'SecurityPolicy',
		'ProgramIssue'
	);

	/*
     * static enum: Model::function()
     * @access static
     */
    public static function statuses($value = null) {
        $options = array(
            self::STATUS_DRAFT => __('Draft'),
			self::STATUS_DISCARDED => __('Discarded'),
			self::STATUS_CURRENT => __('Current')
        );
        return parent::enum($value, $options);
    }

    const STATUS_DRAFT = GOAL_DRAFT;
    const STATUS_DISCARDED = GOAL_DISCARDED;
    const STATUS_CURRENT = GOAL_CURRENT;

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Goals');
		$this->_group = 'program';

		$this->fieldGroupData = array(
            'default' => array(
                'label' => __('General')
            ),
            'performance' => array(
                'label' => __('Performance')
            ),
            'activities' => array(
                'label' => __('Activities')
            ),
            'issues' => array(
                'label' => __('Issues')
            )
        );

		$this->fieldData = [
			'name' => [
				'label' => __('Name'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('The name of the goal'),
			],
			'owner_id' => [
				'label' => __('Owner'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('The individual accountable for planning, monitoring and ultimately acheving this goal'),
				'quickAdd' => true
			],
			'description' => [
				'label' => __('Description'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('A brief description of the goal'),
			],
			'status' => [
				'label' => __('Status'),
				'editable' => true,
				'inlineEdit' => true,
				'options' => [$this, 'statuses'],
				'description' => __('Select the current status of this goal'),
			],
			'audit_metric' => [
				'label' => __('Metric'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('What and how evidence will be used and collected to determine if the goal has been achieved'),
				'group' => 'performance'
			],
			'audit_criteria' => [
				'label' => __('Success Criteria'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('How that metric will need to look in order to determine if it was achieved or not?'),
				'group' => 'performance'
			],
			'GoalAudit' => [
				'label' => __('Goal Audit'),
				'editable' => false,
				'inlineEdit' => true,
				'group' => 'performance'
			],
			'GoalAuditDate' => [
				'label' => __('Audit Calendar'),
				'description' => __('Select the months in the year where this audit must take place'),
				'editable' => true,
				'inlineEdit' => false,
				'renderHelper' => ['Goals', 'auditsField'],
				'group' => 'performance',
				'usable' => false,
			],
			'SecurityService' => [
				'label' => __('Security Services'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Select controls that are being (or have been) developed and put into production to support the completion of this goal'),
				'group' => 'activities',
				'quickAdd' => true
			],
			'Risk' => [
				'label' => __('Asset Risks'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Select asset based risks that have emerged or have mitigated as part of this goal'),
				'group' => 'activities',
				'quickAdd' => true
			],
			'ThirdPartyRisk' => [
				'label' => __('Third Party Risks'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Select third party based risks that have emerged or have mitigated as part of this goal'),
				'group' => 'activities',
				'quickAdd' => true
			],
			'BusinessContinuity' => [
				'label' => __('Business Continuities'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Select business risks that have emerged or have mitigated as part of this goal'),
				'group' => 'activities',
				'quickAdd' => true
			],
			'Project' => [
				'label' => __('Projects'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Select projects that have been createed to meet this goal'),
				'group' => 'activities',
				'quickAdd' => true
			],
			'SecurityPolicy' => [
				'label' => __('Security Policies'),
				'editable' => true,
				'inlineEdit' => true,
				'options' => [$this, 'getSecurityPolicies'],
				'description' => __('Select Security Policies that have been developed and implemented as part of this goal'),
				'group' => 'activities',
				'quickAdd' => true
			],
			'ProgramIssue' => [
				'label' => __('Program Issues'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Select any issues that will be mitigated with the achievement of this goal'),
				'group' => 'issues',
				'quickAdd' => true
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
				->textField('description', [
					'showDefault' => true
				])
				->selectField('owner_id', [$this, 'getUsers'], [
					'showDefault' => true
				])
				->selectField('status', [$this, 'getStatuses'], [
					'showDefault' => true
				])
				->textField('audit_metric', [
					'showDefault' => true
				])
				->textField('audit_criteria', [
					'showDefault' => true
				])
				->multipleSelectField('SecurityService', [ClassRegistry::init('SecurityService'), 'getList'], [
					'showDefault' => true
				])
				->multipleSelectField('Risk', [ClassRegistry::init('Risk'), 'getList'], [
					'showDefault' => true
				])
				->multipleSelectField('ThirdPartyRisk', [ClassRegistry::init('ThirdPartyRisk'), 'getList'], [
					'showDefault' => true
				])
				->multipleSelectField('BusinessContinuity', [ClassRegistry::init('BusinessContinuity'), 'getList'], [
					'showDefault' => true
				])
				->multipleSelectField('Project', [ClassRegistry::init('Project'), 'getList'], [
					'showDefault' => true
				])
				->multipleSelectField('SecurityPolicy', [ClassRegistry::init('SecurityPolicy'), 'getList'], [
					'showDefault' => true
				])
				->multipleSelectField('ProgramIssue', [ClassRegistry::init('ProgramIssue'), 'getList'], [
					'showDefault' => true
				]);

		if (AppModule::loaded('CustomFields')) {
			$this->customFieldsFilters($advancedFilterConfig);
		}

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getObjectStatusConfig() {
        return [
            'metrics_last_missing' => [
            	'title' => __('Review Expired'),
                'callback' => [$this, '_metricsLastMissing'],
                'regularTrigger' => true,
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
		return [
			'table' => [
				'model' => [
					'GoalAudit',
				]
			],
		];
	}

	public function beforeValidate($options = array())
	{
		$ret = true;
		// Audits date validation
		$dateFields = array('GoalAuditDate');
		foreach ($dateFields as $field) {
			if (!empty($this->data[$field])) {
				foreach ($this->data[$field] as $key => $date) {
					$formattedDate = sprintf('%s-%s-%s', $date['day'], $date['month'], date('Y'));
					$ret &= $valid = Validation::date($formattedDate, array('dmy'));

					if (empty($valid)) {
						$this->invalidate($field . '_' . $key, __('This date is not valid.'));
						$this->invalidate($field, __('One or more dates are not valid.'));
					}
				}
			}
		}

		return $ret;
	}

	public function beforeSave($options = array()) {
		// $this->transformDataToHabtm(['SecurityService', 'Risk',	'ThirdPartyRisk', 'BusinessContinuity', 'Project', 'SecurityPolicy', 'ProgramIssue'
		// ]);

		return true;
	}

	public function afterSave($created, $options = array())
	{
		// Temporary save data so they can be set again (in some cases data are removed becouse some other functionality)
		$tempData = $this->data;

		if (isset($this->data['GoalAuditDate'])) {
			$this->GoalAuditDate->deleteAll(array(
				'GoalAuditDate.goal_id' => $this->id
			));
			if (!empty($this->data['GoalAuditDate'])) {
				$this->saveAuditsJoins($this->data['GoalAuditDate'], $this->id);
			}
		}


		$this->set($tempData);

		// Add new and remove unused Audits for next year
		$this->updateNextYearAudits();

		return true;
	}

	protected function updateNextYearAudits()
	{
		if (empty($this->id)) {
			return false;
		}

		$auditDates = isset($this->data['GoalAuditDate']) ? $this->data['GoalAuditDate'] : [];

		//
		// Delete previosly added Audits for next year which are not needed anymore
		$existingAudits = $this->GoalAudit->find('all', [
			'conditions' => [
				'goal_id' => $this->id
			],
			'recursive' => -1
		]);

		$this->GoalAudit->softDelete(false);
		foreach ($existingAudits as $item) {
			$plannedDate = $item['GoalAudit']['planned_date'];
			if ($this->isNextYearDate($plannedDate) &&
				!$this->dateExists($plannedDate, $auditDates)) {
				$this->GoalAudit->delete($item['GoalAudit']['id']);
			}
		}
		$this->GoalAudit->softDelete(true);
		// 

		//
		// Add new Audits for next year
		$this->saveAuditsJoins($auditDates, $this->data['Goal']['id'], true);
		//
	}

	protected function isNextYearDate($date)
	{
		$isNextYearDate = false;
		$nextYear = date('Y', strtotime(date('Y') . " + 365 day"));
		$dateYear = date('Y', strtotime($date));

		if ($dateYear === $nextYear) {
			$isNextYearDate = true;
		}

		return $isNextYearDate;
	}

	protected function dateExists($date, $dates)
	{
		$dateExists = false;
		$dateMonth = date('n', strtotime($date));
		$dateDay = date('j', strtotime($date));

		foreach ($dates as $d) {
			if (intval($d['month']) == intval($dateMonth) &&
				intval($d['day']) == intval($dateDay)) {
				$dateExists = true;
				break;
			}
		}

		return $dateExists;
	}

	/**
	 * @deprecated status, in favor of Goal::_statusOngoingCorrectiveActions()
	 */
	public function statusOngoingCorrectiveActions($id) {
		$this->GoalsProject->bindModel(array(
			'belongsTo' => array('Project')
		));
		
		$ret = $this->GoalsProject->find('count', array(
			'conditions' => array(
				'GoalsProject.goal_id' => $id,
				'Project.project_status_id !=' => PROJECT_STATUS_COMPLETED
			),
			'recursive' => 0
		));

		$auditIds = $this->GoalAudit->find('list', array(
			'conditions' => array(
				'GoalAudit.goal_id' => $id
			),
			'fields' => array('id', 'id'),
			'recursive' => -1
		));

		$ret = $ret || $this->GoalAudit->GoalAuditImprovement->find('count', array(
			'conditions' => array(
				'GoalAuditImprovement.goal_audit_id' => $auditIds
			),
			'recursive' => -1
		));

		if ($ret) {
			return 1;
		}

		return 0;
	}

	public function _metricsLastMissing() {
		$data = $this->GoalAudit->find('first', [
			'conditions' => [
				'GoalAudit.goal_id' => $this->id,
				'GoalAudit.planned_date < DATE(NOW())'
			],
			'fields' => ['GoalAudit.id', 'GoalAudit.result', 'GoalAudit.planned_date'],
			'order' => ['GoalAudit.planned_date' => 'DESC'],
			'recursive' => -1
		]);

		return (!empty($data) && $data['GoalAudit']['result'] === null);
	}

	/**
	 * @deprecated status, in favor of Goal::_metricsLastMissing()
	 */
	public function metricsLastMissing($id) {
		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));

		$audit = $this->GoalAudit->find('first', array(
			'conditions' => array(
				'GoalAudit.goal_id' => $id,
				'GoalAudit.planned_date <' => $today
			),
			'fields' => array('GoalAudit.id', 'GoalAudit.result', 'GoalAudit.planned_date'),
			'order' => array('GoalAudit.planned_date' => 'DESC'),
			'recursive' => -1
		));

		$lastMissing = false;
		if (!empty($audit) && $audit['GoalAudit']['result'] == null) {
			$lastMissing = true;
		}

		if (!$lastMissing) {
			return 0;
		}

		return 1;
	}

	/*public function lastMissingMetric($id) {
		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));

		$audit = $this->GoalAudit->find('first', array(
			'conditions' => array(
				'GoalAudit.goal_id' => $id,
				'GoalAudit.planned_date <=' => $today,
				'GoalAudit.result' => null
			),
			'order' => array('GoalAudit.modified' => 'DESC'),
			'recursive' => -1
		));

		if (!empty($audit)) {
			$this->lastMissingAuditId = $audit['GoalAudit']['id'];
			return $audit['GoalAudit']['planned_date'];
		}

		return false;
	}

	public function lastMissingMetricResult($id) {
		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));

		$audit = $this->GoalAudit->find('first', array(
			'conditions' => array(
				'GoalAudit.goal_id' => $id,
				'GoalAudit.planned_date <=' => $today,
				'GoalAudit.result' => array(1,0)
			),
			'order' => array('GoalAudit.modified' => 'DESC'),
			'recursive' => -1
		));

		if (!empty($audit)) {
			if ($audit['GoalAudit']['result']) {
				return __('Pass');
			}

			return __('Fail');

		}

		return false;
	}*/

	public function saveAudits($id, $processType = null) {
		$ret = true;
		return $ret;
	}

	public function saveAuditsJoins($list, $goal_id, $nextYear = false)
	{
		$year = date('Y');
		if ($nextYear) {
			$year = date('Y', strtotime(date('Y') . " + 365 day"));
		}

		$user = $this->currentUser();
		foreach ( $list as $date ) {
			$tmp = array(
				'goal_id' => $goal_id,
				'planned_date' =>  $year . '-' . $date['month'] . '-' . $date['day'],
				'audit_metric_description' => !empty($this->data['Goal']['audit_metric']) ? $this->data['Goal']['audit_metric'] : "",
				'audit_success_criteria' => !empty($this->data['Goal']['audit_criteria']) ? $this->data['Goal']['audit_criteria'] : ""
			);

			$exist = $this->GoalAudit->find( 'count', array(
				'conditions' => array(
					'GoalAudit.goal_id' => $goal_id,
					'GoalAudit.planned_date' => $year . '-' . $date['month'] . '-' . $date['day']
				),
				'recursive' => -1
			) );

			if (!$exist) {
				$this->GoalAudit->create();
				$save = $this->GoalAudit->save($tmp, array(
					'validate' => false,
					'forceRecheck' => true
				));

				if (!$save) {
					return false;
				}
			}
		}

		return true;
	}

	public function getSecurityPolicies() {
        return $this->SecurityPolicy->getListWithType();
    }

    public function getStatuses()
    {
		return static::statuses();
	}

	public function getSecurityServices()
	{
		$data = $this->SecurityService->find('list', array(
			'order' => array('SecurityService.name' => 'ASC'),
			'recursive' => -1
		));
		return $data;
	}

	public function getRisks()
	{
		$data = $this->Risk->find('list', array(
			'order' => array('Risk.title' => 'ASC'),
			'recursive' => -1
		));
		return $data;
	}

	public function getThirdPartyRisks()
	{
		$data = $this->ThirdPartyRisk->find('list', array(
			'order' => array('ThirdPartyRisk.title' => 'ASC'),
			'recursive' => -1
		));
		return $data;
	}

	public function getBusinessContinuities()
	{
		$data = $this->BusinessContinuity->find('list', array(
			'order' => array('BusinessContinuity.title' => 'ASC'),
			'recursive' => -1
		));
		return $data;
	}

	public function getProjects()
	{
		$data = $this->Project->find('list', array(
			'order' => array('Project.title' => 'ASC'),
			'recursive' => -1
		));
		return $data;
	}

	public function getSecurityPoliciesList()
	{
		$data = $this->SecurityPolicy->find('list', array(
			'order' => array('SecurityPolicy.index' => 'ASC'),
			'recursive' => -1
		));
		return $data;
	}

	public function getProgramIssues()
	{
		$data = $this->ProgramIssue->find('list', array(
			'order' => array('ProgramIssue.name' => 'ASC'),
			'recursive' => -1
		));
		return $data;
	}

	public function hasSectionIndex()
	{
		return true;
	}
}
