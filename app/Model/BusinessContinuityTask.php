<?php
App::uses('AppModel', 'Model');
App::uses('UserFields', 'UserFields.Lib');
App::uses('InheritanceInterface', 'Model/Interface');

class BusinessContinuityTask extends AppModel implements InheritanceInterface
{
	public $displayField = 'step';

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $mapping = array(
		'indexController' => 'businessContinuityPlans',
		'indexController' => array(
			'basic' => 'businessContinuityTasks',
			'advanced' => 'businessContinuityTasks',
			'params' => array('goal_id')
		),
		'titleColumn' => false,
		'logRecords' => true,
		'notificationSystem' => true,
		'workflow' => false
	);

	public $actsAs = array(
		'Containable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'step', 'when', 'who', 'does',
				'where', 'how'
			)
		),
		'Visualisation.Visualisation',
		'AuditLog.Auditable',
		'Utils.SoftDelete',
		'UserFields.UserFields' => [
			'fields' => [
				'AwarenessRole' => [
					'mandatory' => false
				]
			]
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
		'SubSection' => [
			'parentField' => 'business_continuity_plan_id'
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
		'step' => array(
			'rule' => 'numeric',
			'required' => true,
			'allowEmpty' => false
		),
		'when' => array(
			'rule' => 'notBlank',
			'allowEmpty' => false,
			'required' => true
		),
		'who' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'user_id' => array(
			'rule' => 'notBlank'
		),
		'does' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'where' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'how' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		)
	);

	public $belongsTo = array(
		'BusinessContinuityPlan'
	);

	public $hasMany = array(
		'BusinessContinuityTaskReminder',
	);

	public function __construct($id = false, $table = null, $ds = null)
	{
		//
		// Init helper Lib for UserFields Module
		$UserFields = new UserFields();
		//
		
		$this->label = __('Business Continuity Tasks');
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
				'renderHelper' => ['BusinessContinuityTasks', 'businessContinuityPlanField'],
				'macro' => [
					'normal' => 'business_continuity_plan'
				]
			],
			'AwarenessRole' => $UserFields->getFieldDataEntityData($this, 'AwarenessRole', [
				'label' => __('Awareness Role'), 
				'description' => __('The individual selected can get notifications at regular points in time reminding him of his responsabilities in this plan.')
			]),
			'step' => [
				'label' => __('Plan Step'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('In this plan, where this step goes? Example: 1, 4, 6, Etc.')
			],
			'when' => [
				'label' => __('When'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('When reading an emergency procedure, is important to know who does what in particular when! Example: no longer than 5 minutes after declared the crisis.')
			],
			'who' => [
				'label' => __('Who'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Who is executing this task? This shoud be an individual, a group, Etc.')
			],
			'does' => [
				'label' => __('Does Something'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Valid examples: Warms up engines, Starts passive DC infrastructure. There\'s no point in writting how in details that is to be done since you shouldnt expect someone to learn to do something while in the middle of an emergency')
			],
			'where' => [
				'label' => __('Where'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Where is the task executed?')
			],
			'how' => [
				'label' => __('How'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('How is the task executed?')
			],
		];

		$this->advancedFilterSettings = [
			'pdf_title' => __('Business Continuity Task'),
			'pdf_file_name' => __('business_continuity_task'),
			'csv_file_name' => __('business_continuity_task'),
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
				->userField('AwarenessRole', 'AwarenessRole', [
					'showDefault' => true
				])
				->textField('step', [
					'showDefault' => true
				])
				->textField('when', [
					'showDefault' => true
				])
				->textField('does', [
					'showDefault' => true
				])
				->textField('where', [
					'showDefault' => true
				])
				->textField('how', [
					'showDefault' => true
				]);

		if (AppModule::loaded('CustomFields')) {
			$this->customFieldsFilters($advancedFilterConfig);
		}

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getDisplayFilterFields()
	{
		return ['business_continuity_plan_id', 'step'];
	}

	public function parentModel() {
		return 'BusinessContinuityPlan';
	}

	public function parentNode($type) {
        return $this->visualisationParentNode('business_continuity_plan_id');
    }
    
	public function getReportsConfig()
    {
		return [
			'table' => [
				'model' => [
					'BusinessContinuityPlan',
				]
			],
			'chart' => [
			]
		];
	}
	
	public function getRecordTitle($id) {
		$data = $this->find('first', array(
			'conditions' => array(
				'BusinessContinuityTask.id' => $id
			),
			'fields' => array(
				'BusinessContinuityTask.step',
				'BusinessContinuityPlan.title'
			),
			'recursive' => 0
		));

		if (empty($data)) {
			return '';
		}

		return sprintf('%s (%s)', $data['BusinessContinuityTask']['step'], $data['BusinessContinuityPlan']['title']);
	}

	public function hasSectionIndex()
	{
		return true;
	}
}
