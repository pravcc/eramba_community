<?php
App::uses('AppModel', 'Model');
App::uses('InheritanceInterface', 'Model/Interface');

class ProjectExpense extends AppModel implements InheritanceInterface
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
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
		'AdvancedFilters.AdvancedFilters',
		'SubSection' => [
			'parentField' => 'project_id'
		]
	);

	public $mapping = array(
		'indexController' => array(
			'basic' => 'projects',
			'advanced' => 'projectExpenses',
			'params' => array('project_id')
		),
		'titleColumn' => 'description',
		'logRecords' => true,
		'notificationSystem' => true,
		'workflow' => false
	);

	public $validate = array(
		'amount' => array(
			'rule' => 'numeric',
			'required' => true
		),
		'date' => array(
			'rule' => 'date',
			'required' => true
		)
	);

	public $belongsTo = array(
		'Project'
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Project Expenses');
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
			'amount' => [
				'label' => __('Expense Amount'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('The amount of money involved in this expense')
			],
			'description' => [
				'label' => __('Description'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('A brief description of what was purchased')
			],
			'date' => [
				'label' => __('Expense Date'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('The day the expense was executed')
			]
		];
		
		$this->advancedFilterSettings = array(
			'pdf_title' => __('Project Expeneses'),
			'pdf_file_name' => __('project_expeneses'),
			'csv_file_name' => __('project_expeneses'),
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
			->numberField('amount', [
				'showDefault' => true
			])
			->textField('description', [
				'showDefault' => true
			])
			->dateField('date', [
				'showDefault' => true
			]);

		$this->Project->childFilters($advancedFilterConfig);

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getObjectStatusConfig() {
        return [
            'no_updates' => [
                'trigger' => [
                    $this->Project,
                ],
                'hidden' => true
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

	public function getRecordTitle($id) {
		$title = parent::getRecordTitle($id);
		$textHelper = _getHelperInstance('Text');

		return $textHelper->truncate($title, 50);
	}

	public function afterSave($created, $options = array()) {
		$ret = true;
		return $ret;
	}

	public function beforeDelete($cascade = true) {
		$this->data['ProjectExpense']['project_id'] = $this->getProjectId($this->id);
		return true;
	}

	public function afterDelete() {
		$ret = true;
		return $ret;
	}

	private function getProjectId($id) {
		$data = $this->find('first', array(
			'conditions' => array(
				'ProjectExpense.id' => $id
			),
			'softDelete' => false,
			'fields' => array('ProjectExpense.project_id'),
			'recursive' => -1
		));

		return $data['ProjectExpense']['project_id'];
	}

	public function getProjects()
    {
    	return $this->Project->getList(false);
    }

    public function hasSectionIndex()
	{
		return true;
	}
}
