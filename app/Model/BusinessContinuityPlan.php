<?php
App::uses('AppModel', 'Model');
App::uses('UserFields', 'UserFields.Lib');

class BusinessContinuityPlan extends AppModel
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
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'title', 'objective', 'audit_metric', 'audit_success_criteria', 'launch_criteria', 'security_service_type_id', 'opex', 'capex', 'resource_utilization', 'regular_review', 'awareness_recurrence'
			)
		),
		'AuditLog.Auditable',
		'Utils.SoftDelete',
		'Visualisation.Visualisation',
		'ObjectStatus.ObjectStatus',
		'UserFields.UserFields' => [
			'fields' => [
				'LaunchInitiator' => [
					'mandatory' => false
				],
				'Sponsor' => [
					'mandatory' => false
				],
				'Owner' => [
					'mandatory' => false
				]
			]
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
		'SubSection' => [
			'childModels' => true
		],
		'AdvancedFilters.AdvancedFilters',
		'ModuleDispatcher' => [
			'behaviors' => [
				'CustomFields.CustomFields',
				'Reports.Report',
			]
		],
		'CustomLabels.CustomLabels'
	);

	public $validate = array(
		'title' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'objective' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'audit_metric' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'audit_success_criteria' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'security_service_type_id' => array(
			'rule' => 'notBlank',
			'required' => true
		),
		'opex' => array(
			'rule' => 'numeric',
			'required' => true
		),
		'capex' => array(
			'rule' => 'numeric',
			'required' => true
		),
		'resource_utilization' => array(
			'rule' => 'numeric',
			'required' => true
		)
	);

	public $belongsTo = array(
		'SecurityServiceType'
	);

	public $hasMany = array(
		'BusinessContinuityTask',
		'BusinessContinuityPlanAudit',
		'BusinessContinuityPlanAuditDate',
	);

	public $hasAndBelongsToMany = array(
		'BusinessContinuity'
	);

	public function __construct($id = false, $table = null, $ds = null)
	{
		//
		// Init helper Lib for UserFields Module
		$UserFields = new UserFields();
		//
		
		$this->label = __('Business Continuity Plans');
		$this->_group = parent::SECTION_GROUP_CONTROL_CATALOGUE;

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
			'audits' => [
				'label' => __('Audits')
			]
		];

		$this->fieldData = [
			'title' => [
				'label' => __('Title'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('The name for this Continuity Plan')
			],
			'objective' => [
				'label' => __('Objective'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Describe the plan objective, it should be something short and straightforward to understand')
			],
			'launch_criteria' => [
				'label' => __('Launch Criteria'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('OPTIONAL: Describe the criteria the plan initiator should use to trigger this continuity plan.')
			],
			'Owner' => $UserFields->getFieldDataEntityData($this, 'Owner', [
				'label' => __('Owner'), 
				'description' => __('The owner of the plan is usually the individual that is held responsible for the plan management.'),
				'quickAdd' => true,
				'inlineEdit' => true,
			]),
			'Sponsor' => $UserFields->getFieldDataEntityData($this, 'Sponsor', [
				'label' => __('Sponsor'), 
				'description' => __('Who is responsible for keeping this plan realitistic, communicated and applicable?'),
				'quickAdd' => true,
				'inlineEdit' => true,
			]),
			'LaunchInitiator' => $UserFields->getFieldDataEntityData($this, 'LaunchInitiator', [
				'label' => __('Launch Initiator'), 
				'description' => __('The Launch Initiator is the person who is authorized to launch or declare the need for the plan.'),
				'quickAdd' => true,
				'inlineEdit' => true,
			]),
			'opex' => [
				'label' => __('Cost (OPEX)'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Describe the associated OPEX for of this Control')
			],
			'capex' => [
				'label' => __('Cost (CAPEX)'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Describe the associated CAPEX for this Control')
			],
			'resource_utilization' => [
				'label' => __('Resource Utilization'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('The amount of days required to keep the plan operative. For example, 4 people need to work on the plan at least 5 days to ensure is audited, operational, Etc. That would make 20 days of effort (in terms of cost).')
			],
			'security_service_type_id' => [
				'label' => __('Status'),
				'editable' => true,
				'description' => __('The plan can be either in "Design" or "Production" phases. If the plan is set to "Design" it will not be shown on the rest of the system and audits will not be available'),
				'renderHelper' => ['BusinessContinuityPlans', 'securityServiceTypeField']
			],
			'regular_review' => [
				'label' => __('Regular Review'),
				'editable' => false,
				'hidden' => true
			],
			'awareness_recurrence' => [
				'label' => __('Awareness Recurrence'),
				'editable' => false,
				'hidden' => true
			],
			'audits_all_done' => [
				'label' => __('Audits All Done'),
				'editable' => false,
				'hidden' => true
			],
			'audits_last_missing' => [
				'label' => __('Audits Last missing'),
				'editable' => false,
				'hidden' => true
			],
			'audits_last_passed' => [
				'label' => __('Audits Last passed'),
				'editable' => false,
				'hidden' => true
			],
			'audits_improvements' => [
				'label' => __('Audits Improvements'),
				'editable' => false,
				'hidden' => true
			],
			'BusinessContinuityPlanAuditDate' => [
				'label' => __('Business Continuity Plan Audit Date'),
				'editable' => true,
				'usable' => false,
				'group' => 'audits',
				'description' => __('Insert dates when this plan needs to be tested every year.'),
				'renderHelper' => ['BusinessContinuityPlans', 'auditsField']
			],
			'audit_success_criteria' => [
				'label' => __('Audit Methodology'),
				'editable' => true,
				'group' => 'audits',
				'description' => __('Define how this continiuty plan will be tested at regular point in time.)'),
				'renderHelper' => ['BusinessContinuityPlans', 'auditSuccessCriteriaField']
			],
			'audit_metric' => [
				'label' => __('Audit Success Metric Criteria'),
				'editable' => true,
				'group' => 'audits',
				'description' => __('What criteria will be used to determine if the plan worked or not.'),
				'renderHelper' => ['BusinessContinuityPlans', 'auditMetricField']
			],
			'BusinessContinuityTask' => [
				'label' => __('Business Continuity Task'),
				'editable' => false,
			],
			'BusinessContinuityPlanAudit' => [
				'label' => __('Business Continuity Plan Audit'),
				'editable' => false,
			],
			'BusinessContinuity' => [
				'label' => __('Business Continuity'),
				'editable' => false,
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
				->textField('title', [
					'showDefault' => true
				])
				->textField('objective', [
					'showDefault' => true
				])
				->textField('launch_criteria', [
					'showDefault' => true
				])
				->userField('Owner', 'Owner', [
					'showDefault' => true
				])
				->userField('Sponsor', 'Sponsor', [
					'showDefault' => true
				])
				->userField('LaunchInitiator', 'LaunchInitiator', [
					'showDefault' => true
				])
				->numberField('opex', [
					'showDefault' => true
				])
				->numberField('capex', [
					'showDefault' => true
				])
				->numberField('resource_utilization', [
					'showDefault' => true
				])
				->multipleSelectField('security_service_type_id', [ClassRegistry::init('SecurityServiceType'), 'getList'], [
					'showDefault' => true
				])
				->textField('audit_success_criteria', [
					'showDefault' => true
				])
				->textField('audit_metric', [
					'showDefault' => true
				])
				->objectStatusField('ObjectStatus_audits_last_passed', 'audits_last_passed')
				->objectStatusField('ObjectStatus_audits_last_missing', 'audits_last_missing');

		if (AppModule::loaded('CustomFields')) {
			$this->customFieldsFilters($advancedFilterConfig);
		}

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getSectionInfoConfig()
	{
		return [
			'map' => [
				'BusinessContinuity',
				'BusinessContinuityPlanAudit',
				'Project' => [
					'ProjectAchievement',
				],
				'SecurityPolicy',
			]
		];
	}

	public function getObjectStatusConfig() {
        return [
            'audits_last_passed' => [
            	'title' => __('Last Audit Failed'),
                'callback' => [$this, 'statusAuditsLastFailed'],
                'type' => 'danger',
                'regularTrigger' => true,
            ],
            'audits_last_missing' => [
            	'title' => __('Last Audit Expired'),
            	'callback' => [$this, 'statusAuditsLastMissing'],
            	'regularTrigger' => true,
            ],
        ];
    }

    public function statusAuditsLastFailed() {
    	$data = $this->BusinessContinuityPlanAudit->find('first', [
			'conditions' => [
				'BusinessContinuityPlanAudit.business_continuity_plan_id' => $this->id,
				'BusinessContinuityPlanAudit.planned_date < DATE(NOW())'
			],
			'fields' => [
				'BusinessContinuityPlanAudit.id',
				'BusinessContinuityPlanAudit.result',
				'BusinessContinuityPlanAudit.planned_date',
			],
			'order' => ['BusinessContinuityPlanAudit.planned_date' => 'DESC'],
			'recursive' => -1
		]);

		return (!empty($data) && $data['BusinessContinuityPlanAudit']['result'] !== null && $data['BusinessContinuityPlanAudit']['result'] == 0);
    }

    public function statusAuditsLastMissing() {
    	$data = $this->BusinessContinuityPlanAudit->find('first', [
			'conditions' => [
				'BusinessContinuityPlanAudit.business_continuity_plan_id' => $this->id,
				'BusinessContinuityPlanAudit.planned_date < DATE(NOW())'
			],
			'fields' => [
				'BusinessContinuityPlanAudit.id',
				'BusinessContinuityPlanAudit.result',
				'BusinessContinuityPlanAudit.planned_date'
			],
			'order' => ['BusinessContinuityPlanAudit.planned_date' => 'DESC'],
			'recursive' => -1
		]);

		return (!empty($data) && $data['BusinessContinuityPlanAudit']['result'] === null);
    }

    public function getReportsConfig()
	{
		return [
			'table' => [
				'model' => [
					'BusinessContinuityTask', 'BusinessContinuityPlanAudit', 'BusinessContinuity'
				]
			],
		];
	}

	public function beforeValidate($options = array())
	{
		$ret = true;
		// Audits date validation
		$dateFields = array('BusinessContinuityPlanAuditDate');
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
		$this->updateAuditMetricAndCriteria();

		$conds = isset($this->data['BusinessContinuityPlan']['security_service_type_id']);
		$conds = $conds && $this->data['BusinessContinuityPlan']['security_service_type_id'] == SECURITY_SERVICE_DESIGN;
		$conds &= isset($this->data['BusinessContinuityPlan']['id']);

		if ($conds) {
			$this->deleteProductionJoins();
		}

		return true;
	}

	public function afterSave($created, $options = array())
	{
		// Temporary save data so they can be set again (in some cases data are removed becouse some other functionality)
		$tempData = $this->data;

		if (!empty($this->id)) {
			// $this->BusinessContinuity->pushStatusRecords();
			// $ret = $this->BusinessContinuity->saveCustomStatuses($this->getBusinessPlansBusinessContinuities($this->id));
			// $this->BusinessContinuity->holdStatusRecords();

			// return $ret;
		}

		if (isset($this->data['BusinessContinuityPlanAuditDate'])) {
			$this->BusinessContinuityPlanAuditDate->deleteAll(array(
				'BusinessContinuityPlanAuditDate.business_continuity_plan_id' => $this->id
			));
			if (!empty($this->data['BusinessContinuityPlanAuditDate'])) {
				$this->saveAuditsJoins($this->data['BusinessContinuityPlanAuditDate'], $this->id);
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

		$auditDates = isset($this->data['BusinessContinuityPlanAuditDate']) ? $this->data['BusinessContinuityPlanAuditDate'] : [];

		//
		// Delete previosly added Audits for next year which are not needed anymore
		$existingAudits = $this->BusinessContinuityPlanAudit->find('all', [
			'conditions' => [
				'business_continuity_plan_id' => $this->id
			],
			'recursive' => -1
		]);

		$this->BusinessContinuityPlanAudit->softDelete(false);
		foreach ($existingAudits as $item) {
			$plannedDate = $item['BusinessContinuityPlanAudit']['planned_date'];
			if ($this->isNextYearDate($plannedDate) &&
				!$this->dateExists($plannedDate, $auditDates)) {
				$this->BusinessContinuityPlanAudit->delete($item['BusinessContinuityPlanAudit']['id']);
			}
		}
		$this->BusinessContinuityPlanAudit->softDelete(true);
		// 

		//
		// Add new Audits for next year
		$this->saveAuditsJoins($auditDates, $this->data['BusinessContinuityPlan']['id'], true);
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

	public function deleteProductionJoins() {
		$this->BusinessContinuitiesBusinessContinuityPlan->deleteAll(array(
			'BusinessContinuitiesBusinessContinuityPlan.business_continuity_plan_id' => $this->data['BusinessContinuityPlan']['id']
		));
	}

	public function statusProcess($id, $column) {
		if ($column == 'audits_last_passed' || $column == 'audits_last_missing') {
			$statuses = $this->BusinessContinuityPlanAudit->getStatuses($id);
		}

		return $statuses[$column];
	}

	public function _statusOngoingCorrectiveActions() {
		$data = $this->BusinessContinuityPlanAudit->BusinessContinuityPlanAuditImprovement->find('count', [
			'conditions' => [
				'BusinessContinuityPlanAudit.id' => $this->id
			],
			'joins' => [
				[
					'table' => 'business_continuity_plan_audits',
					'alias' => 'BusinessContinuityPlanAudit',
					'type' => 'INNER',
					'conditions' => [
						'BusinessContinuityPlanAudit.id = BusinessContinuityPlanAuditImprovement.business_continuity_plan_audit_id'
					]
				],
			],
			'recursive' => -1
		]);

		return (boolean) $data;
	}

	/**
	 * @deprecated status, in favor of BusinessContinuityPlan::_statusOngoingCorrectiveActions()
	 */
	public function statusOngoingCorrectiveActions($id) {
		$auditIds = $this->BusinessContinuityPlanAudit->find('list', array(
			'conditions' => array(
				'BusinessContinuityPlanAudit.business_continuity_plan_id' => $id
			),
			'fields' => array('id', 'id'),
			'recursive' => -1
		));

		$ret = $this->BusinessContinuityPlanAudit->BusinessContinuityPlanAuditImprovement->find('count', array(
			'conditions' => array(
				'BusinessContinuityPlanAuditImprovement.business_continuity_plan_audit_id' => $auditIds
			),
			'recursive' => -1
		));

		if ($ret) {
			return 1;
		}

		return 0;
	}

	private function updateAuditMetricAndCriteria() {
		if (!empty($this->id)) {
			if (isset($this->data['BusinessContinuityPlan']['audit_metric']) && isset($this->data['BusinessContinuityPlan']['audit_success_criteria'])) {
				$data = $this->find('first', array(
					'conditions' => array(
						'id' => $this->id
					),
					'fields' => array('audit_metric', 'audit_success_criteria'),
					'recursive' => -1
				));

				$updateFields = array();
				if ($this->data['BusinessContinuityPlan']['audit_metric'] != $data['BusinessContinuityPlan']['audit_metric']) {
					$updateFields['BusinessContinuityPlanAudit.audit_metric_description'] = '"' . $this->data['BusinessContinuityPlan']['audit_metric'] . '"';
				}
				if ($this->data['BusinessContinuityPlan']['audit_success_criteria'] != $data['BusinessContinuityPlan']['audit_success_criteria']) {
					$updateFields['BusinessContinuityPlanAudit.audit_success_criteria'] = '"' . $this->data['BusinessContinuityPlan']['audit_success_criteria'] . '"';
				}

				if (!empty($updateFields)) {
					return $this->BusinessContinuityPlanAudit->updateAll($updateFields, array(
						'BusinessContinuityPlanAudit.planned_date >' => date('Y-m-d'),
						'BusinessContinuityPlanAudit.business_continuity_plan_id' => $this->id
					));
				}

			}
		}
	}

	public function getSecurityServiceTypes() {
		if (isset($this->data['BusinessContinuityPlan']['security_service_type_id'])) {
			$type = $this->SecurityServiceType->find('first', array(
				'conditions' => array(
					'SecurityServiceType.id' => $this->data['BusinessContinuityPlan']['security_service_type_id']
				),
				'fields' => array('name'),
				'recursive' => -1
			));

			return $type['SecurityServiceType']['name'];
		}

		return false;
	}

	public function getLastAuditFailedDate() {
		if (!empty($this->lastAuditFailed)) {
			return $this->lastAuditFailed;
		}

		return false;
	}

	public function getLastAuditMissingDate() {
		if (!empty($this->lastAuditMissing)) {
			return $this->lastAuditMissing;
		}

		return false;
	}

	/**
	 * Calculates and saves current audit statuses for given plan.
	 * @param  int $id Business continuity plan ID.
	 */
	public function saveAudits($id, $processType = null) {
		/*$audits = $this->BusinessContinuityPlanAudit->getStatuses($id);

		$saveData = $audits;

		$this->id = $id;
		return $this->save($saveData, array('validate' => false, 'callbacks' => 'before'));*/

		return true;
	}

	public function saveAuditsJoins($list, $bcm_id, $nextYear = false)
	{
		$year = date('Y');
		if ($nextYear) {
			$year = date('Y', strtotime(date('Y') . " + 365 day"));
		}

		// $user = $this->currentUser();
		$data = $this->data;
		foreach ( $list as $date ) {
			$tmp = array(
				'business_continuity_plan_id' => $bcm_id,
				'planned_date' =>  $year . '-' . $date['month'] . '-' . $date['day'],
				'audit_metric_description' => $data['BusinessContinuityPlan']['audit_metric'],
				'audit_success_criteria' => $data['BusinessContinuityPlan']['audit_success_criteria'],
				// 'workflow_owner_id' => $user['id']
			);

			$exist = $this->BusinessContinuityPlanAudit->find( 'count', array(
				'conditions' => array(
					'BusinessContinuityPlanAudit.business_continuity_plan_id' => $bcm_id,
					'BusinessContinuityPlanAudit.planned_date' => $year . '-' . $date['month'] . '-' . $date['day']
				),
				'recursive' => -1
			) );

			if ( ! $exist ) {
				$this->BusinessContinuityPlanAudit->create();
				$save = $this->BusinessContinuityPlanAudit->save($tmp, array(
					'validate' => false,
				));

				if (!$save) {
					return false;
				}
			}
		}

		return true;
	}

	private function getBusinessPlansBusinessContinuities($id) {
		$data = $this->BusinessContinuitiesBusinessContinuityPlan->find('list', array(
			'conditions' => array(
				'BusinessContinuitiesBusinessContinuityPlan.business_continuity_plan_id' => $id
			),
			'fields' => array('BusinessContinuitiesBusinessContinuityPlan.business_continuity_id'),
			'recursive' => -1
		));

		return $data;
	}

	public function getIssues($id = array(), $find = 'list') {
		if (empty($id)) {
			return false;
		}

		if ($find == 'all') {
			$data = $this->find($find, array(
				'conditions' => array(
					'BusinessContinuityPlan.id' => $id
				),
				'fields' => array(
					'MIN(BusinessContinuityPlan.audits_last_passed) AS LastAuditPassed',
					'MAX(BusinessContinuityPlan.audits_last_missing) AS LastAuditMissing',
					'MAX(BusinessContinuityPlan.audits_improvements) AS AuditImprovements',
					'MIN(BusinessContinuityPlan.security_service_type_id) AS SecurityServiceTypeId',

				),
				'recursive' => -1
			));

			$data = $data[0][0];
		}
		else {
			$data = $this->find('list', array(
				'conditions' => array(
					'OR' => array(
						array(
							'BusinessContinuityPlan.id' => $id,
							'BusinessContinuityPlan.audits_all_done' => 0
						),
						array(
							'BusinessContinuityPlan.id' => $id,
							'BusinessContinuityPlan.audits_last_passed' => 0
						)
					)
				),
				'fields' => array('BusinessContinuityPlan.id', 'BusinessContinuityPlan.title'),
				'recursive' => 0
			));
		}

		return $data;
	}

	public function hasSectionIndex()
	{
		return true;
	}
}
