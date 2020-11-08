<?php
/*
** Copyright (C) 2011-2015 www.eramba.org
** Author(s):	Esteban Ribicic <kisero@gmail.com>
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License Version 2 as
** published by the Free Software Foundation.  You may not use, modify or
** distribute this program under any other version of the GNU General
** Public License.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this progra
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
App::uses('AppModel', 'Model');
App::uses('UserFields', 'UserFields.Lib');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ReportBlockChartSetting', 'Reports.Model');
App::uses('NotificationSystem', 'NotificationSystem.Model');

class Project extends AppModel
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
				'title', 'goal', 'start', 'deadline', 'plan_budget', 'project_status_id'
			)
		),
		'AuditLog.Auditable',
		'Utils.SoftDelete',
		'Visualisation.Visualisation',
		'ObjectStatus.ObjectStatus',
		'ModuleDispatcher' => [
			'behaviors' => [
				'CustomFields.CustomFields',
				'Reports.Report',
			]
		],
		'UserFields.UserFields' => [
			'fields' => [
				'Owner' => [
					'mandatory' => false
				]
			]
		],
		'AssociativeDelete.AssociativeDelete' => [
			'associations' => ['ProjectAchievement', 'ProjectExpense']
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'AdvancedQuery.AdvancedFinder',
		'Macros.Macro',
		'AdvancedFilters.AdvancedFilters',
		'SubSection' => [
			'childModels' => true
		],
		'CustomLabels.CustomLabels'
	);

	/*public $virtualFields = array(
		'completion' => 'COUNT(ProjectAchievement.id)'
	);*/

	public $validate = array(
		'title' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'start' => array(
			'rule' => 'date'
		),
		'deadline' => array(
			'rule' => 'date'
		),
		'plan_budget' => array(
			'rule' => 'numeric'
		)
	);

	public $belongsTo = array(
		'ProjectStatus'
	);

	public $hasMany = array(
		'ProjectAchievement',
		'ProjectExpense',
		'Tag' => array(
			'className' => 'Tag',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'Tag.model' => 'Project'
			)
		)
	);

	public $hasAndBelongsToMany = array(
		'Risk',
		'ThirdPartyRisk',
		'BusinessContinuity' => array(
			'with' => 'BusinessContinuitiesProjects'
		),
		'SecurityService',
		'Goal',
		'SecurityPolicy',
		'ComplianceManagement',
		'DataAsset',
		'SecurityServiceAuditImprovement',
		'BusinessContinuityPlanAuditImprovement',
		'GoalAuditImprovement',
	);

	/**
	 * To calculate the completion of the Project based on its Tasks completion
	 * 
	 * @var string
	 */
	protected $_ultimateCompletionQuery;

	public static function statuses($value = null) {
        $options = array(
            self::STATUS_PLANNED => __('Planned'),
            self::STATUS_ONGOING => __('Ongoing'),
            self::STATUS_COMPLETED => __('Closed'),
        );
        return parent::enum($value, $options);
    }

    const STATUS_PLANNED = PROJECT_STATUS_PLANNED;
	const STATUS_ONGOING = PROJECT_STATUS_ONGOING;
	const STATUS_COMPLETED = PROJECT_STATUS_COMPLETED;

	public function __construct($id = false, $table = null, $ds = null)
	{
		//
		// Init helper Lib for UserFields Module
		$UserFields = new UserFields();
		//
		
		$this->label = __('Projects');
		$this->_group = 'security-operations';

		$this->_ultimateCompletionQuery = 'SUM(completion/100) / COUNT(id)';

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			),
		);

		$this->fieldData = [
			'title' => [
				'label' => __('Title'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Give the project a title, name or code so it\'s easily identified on the project list menu.')
			],
			'goal' => [
				'label' => __('Goal'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Describe the project Goal, it\'s roadmap and deliverables.')
			],
			'start' => [
				'label' => __('Project Start Date'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Insert the project kick-off date. The date format for this field is YYYY-MM-DD, the default is todays date.')
			],
			'deadline' => [
				'label' => __('Project Deadline'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Insert the project deadline. The date format for this field is YYYY-MM-DD, the default is todays date.')
			],
			'Owner' => $UserFields->getFieldDataEntityData($this, 'Owner', [
				'label' => __('Owner'), 
				'description' => __('Select the project owner. This is the person responsible for ensuring this project delivered as agreed within the timescales and budget.'),
				'quickAdd' => true,
				'inlineEdit' => true,
			]),
			'plan_budget' => [
				'label' => __('Planned Budget'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Document the planned and approved budget for this project.')
			],
			'Tag' => array(
                'label' => __('Tags'),
				'editable' => true,
				'type' => 'tags',
				'description' => __('Apply tags for this Project.'),
				'empty' => __('Add a tag')
            ),
			'project_status_id' => [
				'label' => __('Status'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Projects are first planned, then they are initiated (ongoing) and finally finished (closed).'),
				'options' => [$this, 'statuses'],
				'macro' => [
					'name' => 'status'
				]
			],
			'over_budget' => [
				'label' => __('Over Budget'),
				'editable' => false,
				'hidden' => true
			],
			'expired' => [
				'label' => __('Expired'),
				'editable' => false,
				'hidden' => true
			],
			'expired_tasks' => [
				'label' => __('Expired Tasks'),
				'editable' => false,
				'hidden' => true
			],
			'ProjectAchievement' => [
				'label' => __('Tasks'),
				'editable' => false,
				'macro' => [
					'name' => 'task'
				]
			],
			'ProjectExpense' => [
				'label' => __('Expenses'),
				'editable' => false,
				'macro' => [
					'name' => 'expense'
				]
			],
			'Risk' => [
				'label' => __('Asset Risks'),
				'editable' => false,
			],
			'ThirdPartyRisk' => [
				'label' => __('Third Party Risks'),
				'editable' => false,
			],
			'BusinessContinuity' => [
				'label' => __('Business Continuities'),
				'editable' => false,
			],
			'SecurityService' => [
				'label' => __('Internal Controls'),
				'editable' => false,
			],
			'Goal' => [
				'label' => __('Goals'),
				'editable' => false,
				'macro' => [
					'name' => 'related_goals'
				]
			],
			'SecurityPolicy' => [
				'label' => __('Security Policies'),
				'editable' => false,
			],
			'ComplianceManagement' => [
				'label' => __('Compliance Managements'),
				'editable' => false,
				'hidden' => true
			],
			'DataAsset' => [
				'label' => __('Data Assets Flows'),
				'editable' => false,
			],
			'SecurityServiceAuditImprovement' => [
				'label' => __('Security Service Audit Improvements'),
				'editable' => false,
				'hidden' => true
			],
			'BusinessContinuityPlanAuditImprovement' => [
				'label' => __('Business Continuity Plan Audit Improvements'),
				'editable' => false,
				'hidden' => true
			],
			'GoalAuditImprovement' => [
				'label' => __('Goal Audit Improvements'),
				'editable' => false,
				'hidden' => true
			],
			'ultimate_completion' => [
				'label' => __('Completion'),
				'editable' => false,
				'hidden' => true
			]
		];

		$this->notificationSystem = array(
			'macros' => array(
				'PROJECT_ID' => array(
					'field' => 'Project.id',
					'name' => __('Project ID')
				),
				'PROJECT_TITLE' => array(
					'field' => 'Project.title',
					'name' => __('Project Title')
				),
				'PROJECT_END' => array(
					'field' => 'Project.deadline',
					'name' => __('Project End')
				),
				'PROJECT_COMPLETION' => array(
					'type' => 'callback',
					'name' => __('Project Completion'),
					'callback' => array($this, 'getFormattedCompletion')
				),
			),
			'customEmail' =>  true
		);

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Projects'),
			'pdf_file_name' => __('projects'),
			'csv_file_name' => __('projects'),
			'additional_actions' => array(
				'ProjectAchievement' => __('Tasks'),
				'ProjectExpense' => __('Expenses')
			),
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
				->textField('goal')
				->dateField('start', [
					'showDefault' => true
				])
				->dateField('deadline', [
					'showDefault' => true
				])
				->userField('Owner', 'Owner', [
					'showDefault' => true
				])
				->nonFilterableField('ultimate_completion')
				->numberField('plan_budget', [
					'showDefault' => true
				])
				->multipleSelectField('Tag-title', [$this, 'getTags'], [
					'showDefault' => true,
					'fieldData' => 'Tag',
				])
				->multipleSelectField('project_status_id', [$this, 'statuses'], [
					'label' => __('Project Status'),
					'showDefault' => true,
				])
				->objectStatusField('ObjectStatus_expired', 'expired')
				->objectStatusField('ObjectStatus_expired_tasks', 'expired_tasks')
				->objectStatusField('ObjectStatus_no_updates', 'no_updates')
			->group('ProjectAchievement', [
				'name' => __('Tasks')
			])
				->textField('ProjectAchievement-description', [
					'label' => __('Task Description'),
					'showDefault' => true,
					'fieldData' => 'ProjectAchievement'
				])
			->group('ProjectExpense', [
				'name' => __('Expenses')
			])
				->textField('ProjectExpense-description', [
					'label' => __('Expense Description'),
					'showDefault' => true,
					'fieldData' => 'ProjectExpense'
				]);

		$this->SecurityService->relatedFilters($advancedFilterConfig);
		$this->Risk->relatedFilters($advancedFilterConfig);
		$this->ThirdPartyRisk->relatedFilters($advancedFilterConfig);
		$this->BusinessContinuity->relatedFilters($advancedFilterConfig);
		$this->ComplianceManagement->relatedFilters($advancedFilterConfig);
		$this->SecurityPolicy->relatedFilters($advancedFilterConfig);

		$advancedFilterConfig
			->group('DataAsset', [
				'name' => __('Data Flow Analysis')
			])
				->multipleSelectField('DataAsset', [ClassRegistry::init('DataAsset'), 'getList']);

		if (AppModule::loaded('CustomFields')) {
			$this->customFieldsFilters($advancedFilterConfig);
		}

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function relatedFilters($advancedFilterConfig)
	{
		$advancedFilterConfig
		    ->group('Project', [
				'name' => __('Project Management')
			])
				->multipleSelectField('Project', [$this, 'getList'], [
					'label' => __('Project'),
				])
				->textField('ProjectAchievement-description', [
	                'label' => __('Project Task'),
	                'findField' => 'Project.ProjectAchievement.description',
	                'fieldData' => 'Project.ProjectAchievement'
	            ]);

        $Model = $advancedFilterConfig->getModel();

		if ($Model->Behaviors->enabled('ObjectStatus.ObjectStatus')) {
			if (isset($Model->getObjectStatusConfig()['project_planned'])) {
				$advancedFilterConfig->objectStatusField('ObjectStatus_project_planned', 'project_planned');
			}
			if (isset($Model->getObjectStatusConfig()['project_planned'])) {
				$advancedFilterConfig->objectStatusField('ObjectStatus_project_ongoing', 'project_ongoing');
			}
			if (isset($Model->getObjectStatusConfig()['project_planned'])) {
				$advancedFilterConfig->objectStatusField('ObjectStatus_project_closed', 'project_closed');
			}
			if (isset($Model->getObjectStatusConfig()['project_planned'])) {
				$advancedFilterConfig->objectStatusField('ObjectStatus_project_expired', 'project_expired');
			}
			if (isset($Model->getObjectStatusConfig()['project_planned'])) {
				$advancedFilterConfig->objectStatusField('ObjectStatus_project_expired_tasks', 'project_expired_tasks');
			}
		}

		return $advancedFilterConfig;
	}

	public function childFilters($advancedFilterConfig)
	{
		$advancedFilterConfig
		    ->group('Project', [
				'name' => __('Project')
			])
				->multipleSelectField('project_id', [ClassRegistry::init('Project'), 'getList'], [
					'label' => __('Project'),
					'showDefault' => true
				])
				->userField('Project-Owner', 'Owner', [
					'label' => __('Project Owner'),
					'findField' => 'Project.UserFieldsObjectOwner.object_key',
					'showDefault' => true
				])
				->textField('Project-goal', [
					'label' => __('Project Goal'),
					'showDefault' => true
				])
				->dateField('Project-start', [
					'label' => __('Project Start Date'),
					'showDefault' => true
				])
				->dateField('Project-deadline', [
					'label' => __('Project Deadline'),
					'showDefault' => true
				])
				->selectField('Project-project_status_id', [ClassRegistry::init('Project'), 'statuses'], [
					'label' => __('Project Status'),
					'showDefault' => true
				]);

		return $advancedFilterConfig;
	}

	public function getNotificationSystemConfig()
	{
		$config = parent::getNotificationSystemConfig();
		$config['notifications']['object_reminder'] = $this->_getModelObjectReminderNotification();
		
		$config['notifications'] = array_merge($config['notifications'], [
			'project_deadline_-1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectDeadline',
				'days' => -1,
				'label' => __('Project Deadline in (-1 day)'),
				'description' => __('Notifies 1 day before a Project expires')
			],
			'project_deadline_-5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectDeadline',
				'days' => -5,
				'label' => __('Project Deadline in (-5 days)'),
				'description' => __('Notifies 5 days before a Project expires')
			],
			'project_deadline_-10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectDeadline',
				'days' => -10,
				'label' => __('Project Deadline in (-10 days)'),
				'description' => __('Notifies 10 days before a Project expires')
			],
			'project_deadline_-20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectDeadline',
				'days' => -20,
				'label' => __('Project Deadline in (-20 days)'),
				'description' => __('Notifies 20 days before a Project expires')
			],
			'project_deadline_-30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectDeadline',
				'days' => -30,
				'label' => __('Project Deadline in (-30 days)'),
				'description' => __('Notifies 30 days before a Project expires')
			],
			'project_deadline_+1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectDeadline',
				'days' => 1,
				'label' => __('Project Deadline in (+1 day)'),
				'description' => __('Notifies 1 day after a Project expires')
			],
			'project_deadline_+5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectDeadline',
				'days' => 5,
				'label' => __('Project Deadline in (+5 days)'),
				'description' => __('Notifies 5 days after a Project expires')
			],
			'project_deadline_+10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectDeadline',
				'days' => 10,
				'label' => __('Project Deadline in (+10 days)'),
				'description' => __('Notifies 10 days after a Project expires')
			],
			'project_deadline_+20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectDeadline',
				'days' => 20,
				'label' => __('Project Deadline in (+20 days)'),
				'description' => __('Notifies 20 days after a Project expires')
			],
			'project_deadline_+30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectDeadline',
				'days' => 30,
				'label' => __('Project Deadline in (+30 days)'),
				'description' => __('Notifies 30 days after a Project expires')
			],
			'project_no_activity_+5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectNoActivity',
				'days' => 5,
				'label' => __('Project Inactivity (5 days)'),
				'description' => __('No activity in the last 5 days')
			],
			'project_no_activity_+10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectNoActivity',
				'days' => 10,
				'label' => __('Project Inactivity (10 days)'),
				'description' => __('No activity in the last 10 days')
			],
			'project_no_activity_+15day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectNoActivity',
				'days' => 15,
				'label' => __('Project Inactivity (15 days)'),
				'description' => __('No activity in the last 15 days')
			],
			'project_no_activity_+20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectNoActivity',
				'days' => 20,
				'label' => __('Project Inactivity (20 days)'),
				'description' => __('No activity in the last 20 days')
			],
			'project_no_activity_+30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectNoActivity',
				'days' => 30,
				'label' => __('Project Inactivity (30 days)'),
				'description' => __('No activity in the last 30 days')
			]
		]);
		
		return $config;
	}

	public function getObjectStatusConfig() {
        return [
        	'over_budget' => [// this is only for calculating column value in DB // delete
                'title' => __('Over Budget'),
                'callback' => [$this, '_statusOverBudget'],
                'hidden' => true,
            ],
            'ongoing' => [
                'title' => __('Ongoing'),
                'callback' => [$this, 'statusOngoing'],
                'type' => 'success',
                'trigger' => [
                	[
                        'model' => $this->SecurityService,
                        'trigger' => 'ObjectStatus.trigger.project_ongoing'
                    ],
                    [
                        'model' => $this->DataAsset,
                        'trigger' => 'ObjectStatus.trigger.project_ongoing'
                    ],
                    [
                        'model' => $this->Risk,
                        'trigger' => 'ObjectStatus.trigger.project_ongoing'
                    ],
                    [
                        'model' => $this->ThirdPartyRisk,
                        'trigger' => 'ObjectStatus.trigger.project_ongoing'
                    ],
                    [
                        'model' => $this->BusinessContinuity,
                        'trigger' => 'ObjectStatus.trigger.project_ongoing'
                    ],
                    [
                        'model' => $this->ComplianceManagement,
                        'trigger' => 'ObjectStatus.trigger.project_ongoing'
                    ],
                    [
                        'model' => $this->SecurityPolicy,
                        'trigger' => 'ObjectStatus.trigger.project_ongoing'
                    ],
                    [
                        'model' => $this->Goal,
                        'trigger' => 'ObjectStatus.trigger.project_ongoing'
                    ],
                ]
            ],
        	'closed' => [
                'title' => __('Closed'),
                'callback' => [$this, 'statusClosed'],
                'type' => 'success',
                'trigger' => [
                	[
                        'model' => $this->SecurityService,
                        'trigger' => 'ObjectStatus.trigger.project_closed'
                    ],
                    [
                        'model' => $this->DataAsset,
                        'trigger' => 'ObjectStatus.trigger.project_closed'
                    ],
                    [
                        'model' => $this->Risk,
                        'trigger' => 'ObjectStatus.trigger.project_closed'
                    ],
                    [
                        'model' => $this->ThirdPartyRisk,
                        'trigger' => 'ObjectStatus.trigger.project_closed'
                    ],
                    [
                        'model' => $this->BusinessContinuity,
                        'trigger' => 'ObjectStatus.trigger.project_closed'
                    ],
                    [
                        'model' => $this->ComplianceManagement,
                        'trigger' => 'ObjectStatus.trigger.project_closed'
                    ],
                    [
                        'model' => $this->SecurityPolicy,
                        'trigger' => 'ObjectStatus.trigger.project_closed'
                    ],
                    [
                        'model' => $this->Goal,
                        'trigger' => 'ObjectStatus.trigger.project_closed'
                    ],
                ]
            ],
            'planned' => [
            	'title' => __('Planned'),
                'callback' => [$this, 'statusPlanned'],
                'type' => 'success',
                'trigger' => [
                	[
                        'model' => $this->SecurityService,
                        'trigger' => 'ObjectStatus.trigger.project_planned'
                    ],
                    [
                        'model' => $this->DataAsset,
                        'trigger' => 'ObjectStatus.trigger.project_planned'
                    ],
                    [
                        'model' => $this->Risk,
                        'trigger' => 'ObjectStatus.trigger.project_planned'
                    ],
                    [
                        'model' => $this->ThirdPartyRisk,
                        'trigger' => 'ObjectStatus.trigger.project_planned'
                    ],
                    [
                        'model' => $this->BusinessContinuity,
                        'trigger' => 'ObjectStatus.trigger.project_planned'
                    ],
                    [
                        'model' => $this->ComplianceManagement,
                        'trigger' => 'ObjectStatus.trigger.project_planned'
                    ],
                    [
                        'model' => $this->SecurityPolicy,
                        'trigger' => 'ObjectStatus.trigger.project_planned'
                    ],
                    [
                        'model' => $this->Goal,
                        'trigger' => 'ObjectStatus.trigger.project_planned'
                    ],
                ]
            ],
            'expired_tasks' => [
            	'title' => __('Task Expired'),
            	'callback' => [$this, 'statusExpiredTask'],
                'trigger' => [
                	[
                        'model' => $this->SecurityService,
                        'trigger' => 'ObjectStatus.trigger.project_expired_tasks'
                    ],
                    [
                        'model' => $this->DataAsset,
                        'trigger' => 'ObjectStatus.trigger.project_expired_tasks'
                    ],
                    [
                        'model' => $this->Risk,
                        'trigger' => 'ObjectStatus.trigger.project_expired_tasks'
                    ],
                    [
                        'model' => $this->ThirdPartyRisk,
                        'trigger' => 'ObjectStatus.trigger.project_expired_tasks'
                    ],
                    [
                        'model' => $this->BusinessContinuity,
                        'trigger' => 'ObjectStatus.trigger.project_expired_tasks'
                    ],
                    [
                        'model' => $this->ComplianceManagement,
                        'trigger' => 'ObjectStatus.trigger.project_expired_tasks'
                    ],
                    [
                        'model' => $this->SecurityPolicy,
                        'trigger' => 'ObjectStatus.trigger.project_expired_tasks'
                    ],
                    [
                        'model' => $this->Goal,
                        'trigger' => 'ObjectStatus.trigger.project_expired_tasks'
                    ],
                ],
                'regularTrigger' => true,
            ],
            'expired' => [
            	'title' => __('Expired'),
                'callback' => [$this, 'statusExpired'],
                'trigger' => [
                	[
                        'model' => $this->SecurityService,
                        'trigger' => 'ObjectStatus.trigger.project_expired'
                    ],
                    [
                        'model' => $this->DataAsset,
                        'trigger' => 'ObjectStatus.trigger.project_expired'
                    ],
                    [
                        'model' => $this->Risk,
                        'trigger' => 'ObjectStatus.trigger.project_expired'
                    ],
                    [
                        'model' => $this->ThirdPartyRisk,
                        'trigger' => 'ObjectStatus.trigger.project_expired'
                    ],
                    [
                        'model' => $this->BusinessContinuity,
                        'trigger' => 'ObjectStatus.trigger.project_expired'
                    ],
                    [
                        'model' => $this->ComplianceManagement,
                        'trigger' => 'ObjectStatus.trigger.project_expired'
                    ],
                    [
                        'model' => $this->SecurityPolicy,
                        'trigger' => 'ObjectStatus.trigger.project_expired'
                    ],
                    [
                        'model' => $this->Goal,
                        'trigger' => 'ObjectStatus.trigger.project_expired'
                    ],
                ],
                'regularTrigger' => true,
            ],
            'no_updates' => [
            	'title' => __('Missing Updates'),
                'callback' => [$this, '_statusNoUpdates'],
                'storageSelf' => false,
                'trigger' => [
                	[
                        'model' => $this->SecurityService,
                        'trigger' => 'ObjectStatus.trigger.project_no_updates'
                    ],
                    [
                        'model' => $this->DataAsset,
                        'trigger' => 'ObjectStatus.trigger.project_no_updates'
                    ],
                    [
                        'model' => $this->Risk,
                        'trigger' => 'ObjectStatus.trigger.project_no_updates'
                    ],
                    [
                        'model' => $this->ThirdPartyRisk,
                        'trigger' => 'ObjectStatus.trigger.project_no_updates'
                    ],
                    [
                        'model' => $this->BusinessContinuity,
                        'trigger' => 'ObjectStatus.trigger.project_no_updates'
                    ],
                    [
                        'model' => $this->ComplianceManagement,
                        'trigger' => 'ObjectStatus.trigger.project_no_updates'
                    ],
                    [
                        'model' => $this->SecurityPolicy,
                        'trigger' => 'ObjectStatus.trigger.project_no_updates'
                    ],
                    [
                        'model' => $this->Goal,
                        'trigger' => 'ObjectStatus.trigger.project_no_updates'
                    ],
                ],
                'regularTrigger' => true,
            ],
            'audits_improvements' => [// delete
            	'title' => __('Control Being Fixed'),
            	'trigger' => [
                    $this->SecurityService,
                ],
                'hidden' => true,
                'regularTrigger' => true,
            ],
        ];
    }

    public function statusOngoing()
    {
    	$count = $this->find('count', [
			'conditions' => [
				'Project.id' => $this->id,
				'Project.project_status_id' => self::STATUS_ONGOING
			],
			'recursive' => -1
		]);

		return (bool) $count;
    }

    public function statusClosed()
    {
    	$count = $this->find('count', [
			'conditions' => [
				'Project.id' => $this->id,
				'Project.project_status_id' => self::STATUS_COMPLETED
			],
			'recursive' => -1
		]);

		return (bool) $count;
    }

    public function statusPlanned()
    {
    	$count = $this->find('count', [
			'conditions' => [
				'Project.id' => $this->id,
				'Project.project_status_id' => self::STATUS_PLANNED
			],
			'recursive' => -1
		]);

		return (bool) $count;
    }

    /**
     * Check if Project, Expenses or Achievements were modified in last two weeks.
     */
    public function _statusNoUpdates() {
    	$dataProject = $this->find('all', [
			'conditions' => [
				'Project.id' => $this->id,
			],
			'fields' => [
				'Project.id',
				'COUNT(Comment.id) AS comments_count',
				'COUNT(Attachment.id) AS attachments_count',
				'IF(DATE(Project.modified) > CURDATE() - INTERVAL 14 DAY, 1, 0) as item_modified',
			],
			'joins' => [
                [
                    'table' => 'comments',
                    'alias' => 'Comment',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Comment.foreign_key = Project.id',
                        'Comment.model' => 'Project',
                        'DATE(Comment.created) > CURDATE() - INTERVAL 14 DAY'
                    ]
                ],
                [
                    'table' => 'attachments',
                    'alias' => 'Attachment',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Attachment.foreign_key = Project.id',
                        'Attachment.model' => 'Project',
                        'DATE(Attachment.created) > CURDATE() - INTERVAL 14 DAY'
                    ]
                ],
            ],
            'group' => [
            	'Project.id'
            ],
			'recursive' => -1
		]);

		$dataAchievement = $this->ProjectAchievement->find('all', [
			'conditions' => [
				'ProjectAchievement.project_id' => $this->id,
			],
			'fields' => [
				'ProjectAchievement.id',
				'COUNT(Comment.id) AS comments_count',
				'COUNT(Attachment.id) AS attachments_count',
				'IF(DATE(ProjectAchievement.modified) > CURDATE() - INTERVAL 14 DAY, 1, 0) as item_modified',
			],
			'joins' => [
                [
                    'table' => 'comments',
                    'alias' => 'Comment',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Comment.foreign_key = ProjectAchievement.id',
                        'Comment.model' => 'ProjectAchievement',
                        'DATE(Comment.created) > CURDATE() - INTERVAL 14 DAY'
                    ]
                ],
                [
                    'table' => 'attachments',
                    'alias' => 'Attachment',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Attachment.foreign_key = ProjectAchievement.id',
                        'Attachment.model' => 'ProjectAchievement',
                        'DATE(Attachment.created) > CURDATE() - INTERVAL 14 DAY'
                    ]
                ],
            ],
            'group' => [
            	'ProjectAchievement.id'
            ],
			'recursive' => -1
		]);

		$dataExpense = $this->ProjectExpense->find('all', [
			'conditions' => [
				'ProjectExpense.project_id' => $this->id,
			],
			'fields' => [
				'ProjectExpense.id',
				'COUNT(Comment.id) AS comments_count',
				'COUNT(Attachment.id) AS attachments_count',
				'IF(DATE(ProjectExpense.modified) > CURDATE() - INTERVAL 14 DAY, 1, 0) as item_modified',
			],
			'joins' => [
                [
                    'table' => 'comments',
                    'alias' => 'Comment',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Comment.foreign_key = ProjectExpense.id',
                        'Comment.model' => 'ProjectExpense',
                        'DATE(Comment.created) > CURDATE() - INTERVAL 14 DAY'
                    ]
                ],
                [
                    'table' => 'attachments',
                    'alias' => 'Attachment',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Attachment.foreign_key = ProjectExpense.id',
                        'Attachment.model' => 'ProjectExpense',
                        'DATE(Attachment.created) > CURDATE() - INTERVAL 14 DAY'
                    ]
                ],
            ],
            'group' => [
            	'ProjectExpense.id'
            ],
			'recursive' => -1
		]);

		$data = array_merge($dataProject, $dataAchievement, $dataExpense);

		foreach ($data as $item) {
			if (!empty($item[0]['comments_count']) || !empty($item[0]['attachments_count']) || !empty($item[0]['item_modified'])) {
				return false;
			}
		}

		return true;
	}

    public function _statusOverBudget() {
    	$data = $this->ProjectExpense->find('first', [
			'conditions' => [
				'ProjectExpense.project_id' => $this->id,
			],
			'fields' => [
				'ROUND(SUM(amount), 2) as expenses', 'Project.plan_budget'
			],
			'joins' => [
                [
                    'table' => 'projects',
                    'alias' => 'Project',
                    'type' => 'INNER',
                    'conditions' => [
                        'Project.id = ProjectExpense.project_id',
                    ]
                ],
            ],
            'having' => ['expenses > Project.plan_budget'],
			'recursive' => -1
		]);

		return (!empty($data));
	}

    public function statusExpired($conditions = null) {
        return parent::statusExpired([
        	'Project.project_status_id' => self::STATUS_ONGOING,
            'DATE(Project.deadline) < DATE(NOW())'
        ]);
    }

    public function statusExpiredTask()
    {
    	$ongoing = $this->find('count', [
			'conditions' => [
				'Project.id' => $this->id,
				'Project.project_status_id' => self::STATUS_ONGOING,
			],
			'recursive' => -1
		]);

		if (!$ongoing) {
			return false;
		}

		$count = $this->ProjectAchievement->find('count', [
			'conditions' => [
				'ProjectAchievement.project_id' => $this->id,
				'DATE(ProjectAchievement.date) < DATE(NOW())',
				'ProjectAchievement.completion !=' => 100,
			],
			'recursive' => -1
		]);

		return (bool) $count;
	}

	public function getSectionInfoConfig()
	{
		return [
			'map' => [
				'ProjectAchievement',
				'ProjectExpense',
				'Risk',
				'ThirdPartyRisk',
				'BusinessContinuity',
				'SecurityService' => [
					'SecurityServiceAudit',
				],
				'SecurityPolicy',
				'ComplianceManagement',
				'DataAssetInstance' => [
					'DataAsset',
				] 
			]
		];
	}

    public function getReportsConfig()
	{
		return [
			'finder' => [
				'options' => [
					'contain' => [
						'ProjectStatus',
						'Tag',
						'ProjectAchievement' => [
							'Project',
							'TaskOwner',
							'TaskOwnerGroup'
						],
						'ProjectExpense' => [
							'Project'
						],
						'CustomFieldValue',
						'BusinessContinuity',
						'Risk',
						'ThirdPartyRisk',
						'SecurityService',
						'Goal' => [
							'Owner',
							'GoalAudit',
							'GoalAuditDate',
							'CustomFieldValue',
							'SecurityService',
							'Risk',
							'ThirdPartyRisk',
							'BusinessContinuity',
							'Project',
							'SecurityPolicy',
							'ProgramIssue'
						],
						'SecurityPolicy',
						'ComplianceManagement' => [
							'CompliancePackageItem' => [
								'CompliancePackage' => [
									'CompliancePackageRegulator'
								]
							]
						],
						'DataAsset' => [
							'DataAssetInstance' => [
								'Asset'
							]
						],
						'SecurityServiceAuditImprovement',
						'BusinessContinuityPlanAuditImprovement',
						'GoalAuditImprovement',
						'Owner',
						'OwnerGroup',
					]
				]
			],
			'table' => [
				'model' => [
					'ProjectAchievement',
					'ProjectExpense',
					'Goal',
				]
			],
			'chart' => [
                1 => [
                    'title' => __('Project Relationships'),
                    'description' => __('This chart shows what GRC elements are associated with this project.'),
                    'type' => ReportBlockChartSetting::TYPE_TREE,
                    'templateType' => ReportTemplate::TYPE_ITEM,
                    'dataFn' => 'relatedObjectsChart'
                ],
            ]
		];
	}

	public function afterSave($created, $options = array()) {
		$ret = true;
		if ($this->id) {
			$projectTitle = $this->field($this->mapping['titleColumn']);

			$serviceIds = $this->ProjectsSecurityService->find('list', array(
				'conditions' => array(
					'ProjectsSecurityService.project_id' => $this->id
				),
				'fields' => array('id', 'security_service_id'),
				'recursive' => -1
			));


			$Improvement = $this->ProjectsSecurityServiceAuditImprovement;
			$_ids = $Improvement->find('list', array(
				'conditions' => array(
					'ProjectsSecurityServiceAuditImprovement.project_id' => $this->id,
					// 'Project.project_status_id !=' => PROJECT_STATUS_COMPLETED
				),
				'fields' => ['SecurityServiceAudit.security_service_id'],
				'joins' => $this->SecurityServiceAuditImprovement->getRelatedJoins(),
				'recursive' => -1
			));
			$serviceIds = am($serviceIds, $_ids);

			$goalIds = $this->GoalsProject->find('list', array(
				'conditions' => array(
					'GoalsProject.project_id' => $this->id
				),
				'fields' => array('id', 'goal_id'),
				'recursive' => -1
			));

			$this->triggerData = array(
				'mappedProjectsCompleted' => $projectTitle
			);

			$settings = array(
				'disableToggles' => array('mappedProjects', 'unmappedProjects'),
				'customToggles' => array('ProjectCompleted'),
				'customValues' => $this->triggerData
			);

			// $ret &= $this->resaveNotifications($this->id);
		}

		return $ret;
	}

	public function resaveNotifications($id) {
		$ret = true;

		$this->bindNotifications();
		$ret &= $this->NotificationObject->NotificationSystem->saveCustomUsersByModel($this->alias, $id);

		$taskIds = $this->ProjectAchievement->find('list', array(
			'conditions' => array(
				'ProjectAchievement.project_id' => $id
			),
			'fields' => array('id', 'id'),
			'recursive' => -1
		));

		$this->ProjectAchievement->bindNotifications();
		$ret &= $this->ProjectAchievement->NotificationObject->NotificationSystem->saveCustomUsersByModel('ProjectAchievement', $taskIds);

		$expenseIds = $this->ProjectExpense->find('list', array(
			'conditions' => array(
				'ProjectExpense.project_id' => $id
			),
			'fields' => array('id', 'id'),
			'recursive' => -1
		));

		$this->ProjectExpense->bindNotifications();
		$ret &= $this->ProjectExpense->NotificationObject->NotificationSystem->saveCustomUsersByModel('ProjectExpense', $expenseIds);

		return $ret;
	}

	public function getUltimateCompletion($id) {
		$completion = $this->ProjectAchievement->find('first', array(
			'conditions' => array(
				'ProjectAchievement.project_id' => $id
			),
			'fields' => array(
				$this->_ultimateCompletionQuery . ' as completion'
			),
			'recursive' => -1
		));

		return (float) $completion[0]['completion'];
	}

	public function formatUltimateCompletion($value) {
		$percentage = CakeNumber::toPercentage($value, 0, array(
			'multiply' => true
		));

		return $percentage;
	}

	public function getFormattedCompletion($id) {
		$completion = $this->getUltimateCompletion($id);

		return $this->formatUltimateCompletion($completion);
	}

	/**
	 * @deprecated status, in favor of Project::statusExpired()
	 */
	public function statusIsExpired($id) {
		$today = date('Y-m-d', strtotime('now'));

		$isExpired = $this->find('count', array(
			'conditions' => array(
				'Project.id' => $id,
				'Project.project_status_id !=' => PROJECT_STATUS_COMPLETED,
				'DATE(Project.deadline) <' => $today
			),
			'recursive' => -1
		));

		return $isExpired;
	}

	/**
	 * @deprecated status, in favor of Project::_statusOverBudget()
	 */
	public function statusOverBudget($id) {
		$budgets = $this->getBudgetValues($id);

		if ($budgets['expense'] > $budgets['planBudget']) {
			return PROJECT_OVER_BUDGET;
		}

		return PROJECT_NOT_OVER_BUDGET;
	}

	public function totalExpenses($id) {
		$budgets = $this->getBudgetValues($id);
		return CakeNumber::currency($budgets['expense']);
	}

	public function planBudget($id) {
		$budgets = $this->getBudgetValues($id);
		return CakeNumber::currency($budgets['planBudget']);
	}

	public function overBudgetMsgParams($id = null) {
		$budgets = $this->getBudgetValues($this->id);

		$planBudget = CakeNumber::currency($budgets['planBudget']);
		$expense = CakeNumber::currency($budgets['expense']);

		return array($expense, $planBudget);
	}

	public function saveOverBudget($id) {
		$budgets = $this->getBudgetValues($id);

		$over_budget = '0';
		if ($budgets['expense'] > $budgets['planBudget']) {
			$over_budget = '1';
		}

		$this->id = $id;
		$ret = $this->save(array('over_budget' => $over_budget), array(
			'validate' => false
		));

		return $ret;
	}

	public function getBudgetValues($id) {
		$expenses = $this->ProjectExpense->find('first', array(
			'conditions' => array(
				'ProjectExpense.project_id' => $id
			),
			'fields' => array('ROUND(SUM(amount), 2) as expenses', 'Project.plan_budget'),
			'recursive' => 0
		));

		$expense = $expenses[0]['expenses'];
		$planBudget = $expenses['Project']['plan_budget'];

		return array(
			'expense' => $expense,
			'planBudget' => $planBudget
		);
	}

	/* --- */

	public function expiredTasksMsgParams($id = null) {
		$expiredTasks = $this->getExpiredTasks($this->id);

		return implode(', ', $expiredTasks);
	}

	public function saveExpiredTasks($id) {
		$expiredTasks = $this->getExpiredTasks($id);

		$expired_tasks = '0';
		if (!empty($expiredTasks)) {
			$expired_tasks = '1';
		}

		$this->id = $id;
		$ret = $this->save(array('expired_tasks' => $expired_tasks), array('validate' => false, 'callbacks' => 'before'));

		return $ret;
	}

	public function getExpiredTasks($id) {
		$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));

		$expiredTasks = $this->ProjectAchievement->find('list', array(
			'conditions' => array(
				'ProjectAchievement.project_id' => $id,
				'DATE(ProjectAchievement.date) <' => $today,
				'ProjectAchievement.completion !=' => 100
			),
			'fields' => array('id', 'description'),
			'recursive' => -1
		));

		return $expiredTasks;
	}

	public function getDataAssets() {
        return $this->DataAsset->getList();
    }

	/**
	 * @deprecated
	 */
	public function expiredTaskConditions($data = array()){
		$conditions = array();
		if($data['expired_tasks'] == 1){
			$conditions = array(
				'Project.expired_tasks >' => 0
			);
		}
		elseif($data['expired_tasks'] == 0){
			$conditions = array(
				'Project.expired_tasks' => 0
			);
		}

		return $conditions;
	}

	public function findByTag($data = array(), $filter) {
		$query = $this->Tag->find('list', array(
			'conditions' => array(
				'Tag.title' => $data[$filter['name']],
				'Tag.model' => 'Project'
			),
			'fields' => array(
				'Tag.foreign_key'
			)
		));

		return $query;
	}

	public function getStatuses() {
		$data = $this->ProjectStatus->find('list', array(
		));

		return $data;
	}

	public function ultimateCompletionSubquery() {
		$query = $this->ProjectAchievement->getQuery('all', array(
			'conditions' => array(
				'ProjectAchievement.project_id = Project.id'
			),
			'fields' => array(
				'ROUND(' . $this->_ultimateCompletionQuery . ' * 100, 0) as completion'
			),
			'contain' => array()
		));
		return $query;
	}

    public function getList($excludeCompleted = true) {
    	$conditions = [];

    	// if ($excludeCompleted) {
    	// 	$conditions = [
    	// 		$this->alias . '.project_status_id !=' => self::STATUS_COMPLETED
    	// 	];
    	// }

        $data = $this->find('list', [
        	'conditions' => $conditions,
            'order' => [
                $this->alias . '.' . $this->displayField => 'ASC'
            ],
        ]);

        return $data;
    }

	/**
	 * Callback used by Status Assessment to calculate over budget field.
	 */
	/*public function queryOverBudget() {
		if ($this->id != null && !isset($this->data['Project']['over_budget'])) {
			$expenses = $this->ProjectExpense->find('first', array(
				'conditions' => array(
					'ProjectExpense.project_id' => $this->id
				),
				'fields' => array('SUM(amount) as expenses'),
				'recursive' => -1
			));

			$expense = $expenses[0]['expenses'];

			if (!isset($this->data['Project']['plan_budget'])) {
				$data = $this->find('first', array(
					'conditions' => array(
						'Project.id' => $this->id
					),
					'fields' => array('plan_budget'),
					'recursive' => -1
				));

				$planBudget = $data['Project']['plan_budget'];
			}
			else {
				$planBudget = $this->data['Project']['plan_budget'];
			}

			if ($expense > $planBudget) {
				$this->data['Project']['over_budget'] = '1';
			}
			else {
				$this->data['Project']['over_budget'] = '0';
			}

			$planBudget = CakeNumber::currency($planBudget);
			$expense = CakeNumber::currency($expense);
			return array($expense, $planBudget);
		}

		return true;
	}*/

	/**
	 * Check if a project is associated with one or more expired project tasks, then modify query.
	 */
	/*public function queryExpiredTasks() {
		if ($this->id != null && !isset($this->data['Project']['expired_tasks'])) {
			$today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));

			$expiredTasks = $this->ProjectAchievement->find('list', array(
				'conditions' => array(
					'ProjectAchievement.project_id' => $this->id,
					'ProjectAchievement.date <' => $today
				),
				'fields' => array('id', 'description'),
				'recursive' => -1
			));

			if (!empty($expiredTasks)) {
				$this->data['Project']['expired_tasks'] = '1';

				return array(implode(', ', $expiredTasks));
			}
			else {
				$this->data['Project']['expired_tasks'] = '0';
			}
		}

		return null;
	}*/

	public function hasSectionIndex()
	{
		return true;
	}

}
