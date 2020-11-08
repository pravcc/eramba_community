<?php
App::uses('AppModel', 'Model');
App::uses('InheritanceInterface', 'Model/Interface');
App::uses('UserFields', 'UserFields.Lib');
App::uses('NotificationSystem', 'NotificationSystem.Model');

class ProjectAchievement extends AppModel implements InheritanceInterface
{
	public $displayField = 'description';

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'description'
			)
		),
		'ModuleDispatcher' => [
			'behaviors' => [
				'Reports.Report',
			]
		],
		'AuditLog.Auditable',
		'Utils.SoftDelete',
		'Visualisation.Visualisation',
		'ObjectStatus.ObjectStatus',
		'UserFields.UserFields' => [
			'fields' => ['TaskOwner']
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
		'ImportTool.ImportTool',
		'AdvancedFilters.AdvancedFilters',
		'SubSection' => [
			'parentField' => 'project_id'
		],
		'CustomLabels.CustomLabels'
	);

	public $mapping = array(
		'indexController' => array(
			'basic' => 'projects',
			'advanced' => 'projectAchievements',
			'params' => array('project_id')
		),
		'titleColumn' => 'description',
		'logRecords' => true,
		'notificationSystem' => true,
		'workflow' => false

	);

	public $validate = array(
		'date' => array(
			'rule' => 'date',
			'required' => true
		),
		'completion' => array(
			'validateCompletion' => [
				'rule' => 'validateCompletion',
				'message' => 'This field cannot be left empty',
				'required' => true
			]
		),
		'task_order' => array(
			'naturalNumber' => [
				'rule' => ['naturalNumber', true],
				'message' => 'Only natural numbers in range 0 - 100 are allowed',
				'required' => true
			],
			'range' => [
				'rule' => ['range', -1, 101],
				'message' => 'Only natural numbers in range 0 - 100 are allowed',
				'required' => true
			],
		)
	);

	public $belongsTo = array(
		'Project'
	);

	public $virtualFields = array(
		'task_duration' => 'DATEDIFF(ProjectAchievement.date, ProjectAchievement.created)',
		// 'completion_total' => 'SUM(DATEDIFF(ProjectAchievement.date, ProjectAchievement.created))'
	);
	
	public function __construct($id = false, $table = null, $ds = null)
	{
		//
		// Init helper Lib for UserFields Module
		$UserFields = new UserFields();
		//
		
		$this->label = __('Project Tasks');
		$this->_group = 'security-operations';

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			),
		);

		$this->fieldData = [
			'project_id' => [
				'label' => __('Project'),
				'editable' => true,
				'macro' => [
					'name' => 'project'
				],
				'empty' => __('Choose one ...'),
				'renderHelper' => ['ProjectAchievements', 'projectField']
			],
			'TaskOwner' => $UserFields->getFieldDataEntityData($this, 'TaskOwner', [
				'label' => __('Task Owner'), 
				'description' => __('Typically the individual responsible to ensure the task is completed'),
				'quickAdd' => true,
			]),
			'date' => [
				'label' => __('Task Deadline'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('The deadline of the task'),
				'renderHelper' => ['ProjectAchievements', 'dateField'],
				'macro' => [
					'name' => 'deadline'
				]
			],
			'description' => [
				'label' => __('Description'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('A brief description of what the task goal is')
			],
			'completion' => [
				'label' => __('How completed is this task?'),
				'editable' => true,
				'inlineEdit' => true,
				'options' => 'getPercentageOptions',
				'description' => __('Percentage of completion of the task')
			],
			'task_order' => [
				'label' => __('Task Order'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('The task number dictates the order in which tasks must be executed'),
				'renderHelper' => ['ProjectAchievements', 'taskOrderField']
			],
			'expired' => [
				'label' => __('Expired'),
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
					'callback' => array($this, 'getProjectFormattedCompletion')
				),
				'TASK_ID' => array(
					'field' => 'ProjectAchievement.id',
					'name' => __('Project Task ID')
				),
				'TASK_DESCRIPTION' => array(
					'field' => 'ProjectAchievement.description',
					'name' => __('Project Task Description')
				),
				'TASK_END' => array(
					'field' => 'ProjectAchievement.date',
					'name' => __('Project Task End')
				),
			),
			'customEmail' =>  true
		);

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Project Tasks'),
			'pdf_file_name' => __('project_tasks'),
			'csv_file_name' => __('project_tasks'),
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
			->userField('TaskOwner', 'TaskOwner', [
				'showDefault' => true
			])
			->numberField('completion', [
				'label' => __('Completion'),
				'showDefault' => true
			])
			->dateField('date', [
				'showDefault' => true
			])
			->numberField('task_order', [
				'showDefault' => true
			])
			->textField('description', [
				'showDefault' => true
			])
			->objectStatusField('ObjectStatus_expired', 'expired', [
				'showDefault' => true
			]);

		$this->Project->childFilters($advancedFilterConfig);

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getImportToolConfig()
	{
		return [
			'ProjectAchievement.project_id' => [
				'name' => __('Project'),
				'model' => 'Project',
				'headerTooltip' => __('This field is mandatory, you need to input one Project name. You can get the name of a Project from Security Operations / Project Management.'),
				'porject',
				'objectAutoFind' => true
			],
			'ProjectAchievement.TaskOwner' => UserFields::getImportArgsFieldData('TaskOwner', [
				'name' => __('Task Owner')
			], true),
			'ProjectAchievement.description' => [
				'name' => __('Description'),
				'headerTooltip' => __('This field is optional, you may leave it blank.')
			],
			'ProjectAchievement.date' => [
				'name' => __('Task Deadline'),
				'headerTooltip' => __('This field is mandatory, it requires a date value with the format YYYY-MM-DD.')
			],
			'ProjectAchievement.completion' => [
				'name' => __('How completed is this task?'),
				'headerTooltip' => __(
					'Mandatory, set one of the following values: %s',
					ImportToolModule::formatList(getPercentageOptions(), false)
				)
			],
			'ProjectAchievement.task_order' => [
				'name' => __('Task Order'),
				'headerTooltip' => __('This field is mandatory, it requires a natural number value in range 0 - 100.')
			],
		];
	}

	public function getReportsConfig()
	{
		return [
			'table' => [
				'model' => [
					'Project',
				]
			],
		];
	}

	public function getMacrosConfig()
	{
		return [
			'assoc' => [
				'Project',
			],
		];
	}

	public function parentModel() {
        return 'Project';
    }

    public function parentNode($type) {
    	return $this->visualisationParentNode('project_id');
    }

    public function getNotificationSystemConfig()
	{
		$config = parent::getNotificationSystemConfig();
		$config['notifications']['object_reminder'] = $this->_getModelObjectReminderNotification();
		
		$config['notifications'] = array_merge($config['notifications'], [
			'project_achievement_deadline_-1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectAchievementDeadline',
				'days' => -1,
				'label' => __('Project Task Deadline (-1 day)'),
				'description' => __('Notifies 1 day before a Project Task expires')
			],
			'project_achievement_deadline_-5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectAchievementDeadline',
				'days' => -5,
				'label' => __('Project Task Deadline (-5 days)'),
				'description' => __('Notifies 5 days before a Project Task expires')
			],
			'project_achievement_deadline_-10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectAchievementDeadline',
				'days' => -10,
				'label' => __('Project Task Deadline (-10 days)'),
				'description' => __('Notifies 10 days before a Project Task expires')
			],
			'project_achievement_deadline_-20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectAchievementDeadline',
				'days' => -20,
				'label' => __('Project Task Deadline (-20 days)'),
				'description' => __('Notifies 20 days before a Project Task expires')
			],
			'project_achievement_deadline_-30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectAchievementDeadline',
				'days' => -30,
				'label' => __('Project Task Deadline (-30 days)'),
				'description' => __('Notifies 30 days before a Project Task expires')
			],
			'project_achievement_deadline_+1day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectAchievementDeadline',
				'days' => 1,
				'label' => __('Project Task Deadline (+1 day)'),
				'description' => __('Notifies 1 day after a Project Task expires')
			],
			'project_achievement_deadline_+5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectAchievementDeadline',
				'days' => 5,
				'label' => __('Project Task Deadline (+5 days)'),
				'description' => __('Notifies 5 days after a Project Task expires')
			],
			'project_achievement_deadline_+10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectAchievementDeadline',
				'days' => 10,
				'label' => __('Project Task Deadline (+10 days)'),
				'description' => __('Notifies 10 days after a Project Task expires')
			],
			'project_achievement_deadline_+20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectAchievementDeadline',
				'days' => 20,
				'label' => __('Project Task Deadline (+20 days)'),
				'description' => __('Notifies 20 days after a Project Task expires')
			],
			'project_achievement_deadline_+30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectAchievementDeadline',
				'days' => 30,
				'label' => __('Project Task Deadline (+30 days)'),
				'description' => __('Notifies 30 days after a Project Task expires')
			],
			'project_achievement_no_activity_+5day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectAchievementNoActivity',
				'days' => 5,
				'label' => __('Task Inactivity (5 days)'),
				'description' => __('No activity in the last 5 days')
			],
			'project_achievement_no_activity_+10day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectAchievementNoActivity',
				'days' => 10,
				'label' => __('Task Inactivity (10 days)'),
				'description' => __('No activity in the last 10 days')
			],
			'project_achievement_no_activity_+15day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectAchievementNoActivity',
				'days' => 15,
				'label' => __('Task Inactivity (15 days)'),
				'description' => __('No activity in the last 15 days')
			],
			'project_achievement_no_activity_+20day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectAchievementNoActivity',
				'days' => 20,
				'label' => __('Task Inactivity (20 days)'),
				'description' => __('No activity in the last 20 days')
			],
			'project_achievement_no_activity_+30day' => [
				'type' => NOTIFICATION_TYPE_WARNING,
				'className' => '.ProjectAchievementNoActivity',
				'days' => 30,
				'label' => __('Task Inactivity (30 days)'),
				'description' => __('No activity in the last 30 days')
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
                        'model' => $this->Project,
                        'trigger' => 'ObjectStatus.trigger.expired_tasks'
                    ],
                ],
                'regularTrigger' => true,
            ],
            'no_updates' => [
                'trigger' => [
                    $this->Project,
                ],
                'hidden' => true
            ],
        ];
    }

    public function statusExpired($conditions = null) {
        return parent::statusExpired([
			'DATE(ProjectAchievement.date) < DATE(NOW())',
			'ProjectAchievement.completion !=' => 100
        ]);
    }

	public function getRecordTitle($id) {
		$title = parent::getRecordTitle($id);
		$textHelper = _getHelperInstance('Text');

		return $textHelper->truncate($title, 50);
	}

	public function validateCompletion($check) {
		return in_array($check['completion'], array_keys(getPercentageOptions()));
	}

	public function beforeValidate($options = array()) {
		$ret = true;

		if (isset($this->data['ProjectAchievement']['project_id'])) {
			$this->invalidateRelatedNotExist('Project', 'project_id', $this->data['ProjectAchievement']['project_id']);
		}

		return $ret;
	}

	public function beforeSave($options = array()) {
		// $this->logStatusToProject();

		return true;
	}

	public function afterSave($created, $options = array()) {
		// if (!empty($this->data['ProjectAchievement']['project_id'])) {
		// 	return $this->Project->saveExpiredTasks($this->data['ProjectAchievement']['project_id']);
		// }

		$ret = true;

		return $ret;
	}

	public function beforeDelete($cascade = true) {
		$this->data['ProjectAchievement']['project_id'] = $this->getProjectId($this->id);
		return true;
	}

	public function afterDelete() {
		$ret = true;

		return $ret;
	}

	public function getProjects()
    {
    	return $this->Project->getList(false);
    }

	public function getProjectStatuses() {
		return $this->Project->getStatuses();
	}

	public function getProjectFormattedCompletion($id) {
		$this->id = $id;
		$projectId = $this->field('project_id');

		return $this->Project->getFormattedCompletion($projectId);
	}

	/**
	 * When a task is created or modified as expired, create a record for related project.
	 */
	private function logStatusToProject() {
		if (!empty($this->data['ProjectAchievement']['expired']) && $this->data['ProjectAchievement']['expired'] == 1) {
			if ($this->id == null) {
				$this->createProjectLog($this->data['ProjectAchievement']['project_id'], $this->data['ProjectAchievement']['description']);
			}
			else {
				$record = $this->find('first', array(
					'conditions' => array(
						'id' => $this->id
					),
					'fields' => array('expired', 'description', 'project_id'),
					'recursive' => -1
				));

				if ($record['ProjectAchievement']['expired'] != $this->data['ProjectAchievement']['expired']) {
					$this->createProjectLog($record['ProjectAchievement']['project_id'], $record['ProjectAchievement']['description']);
				}
			}
		}
	}

	private function createProjectLog($projectId, $description) {
		$this->Project->id = $projectId;
		$this->Project->addNoteToLog(__('One or more tasks for this project has expired: %s', $description));
		$this->Project->setSystemRecord($projectId, 2);
	}

	private function getProjectId($id) {
		$data = $this->find('first', array(
			'conditions' => array(
				'ProjectAchievement.id' => $id
			),
			'fields' => array('ProjectAchievement.project_id'),
			'recursive' => -1,
			'softDelete' => false
		));

		return $data['ProjectAchievement']['project_id'];
	}

	public function editSaveQuery() {
		$this->expiredStatusToQuery('expired', 'date');
	}

	public function hasSectionIndex()
	{
		return true;
	}
}
