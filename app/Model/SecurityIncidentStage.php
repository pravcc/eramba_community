<?php
App::uses('AppModel', 'Model');

class SecurityIncidentStage extends AppModel
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
		'workflow' => false
	);

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'description'
			)
		),
		'ModuleDispatcher' => [
			'behaviors' => [
				'Reports.Report',
			]
		],
		'AuditLog.Auditable',
		'ObjectStatus.ObjectStatus',
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
		'AdvancedFilters.AdvancedFilters'
	);

	public $validate = array(
		'name' => array(
			'rule' => 'notBlank'
		),
		'description' => array(
			'rule' => 'notBlank'
		)
	);

	public $hasAndBelongsToMany = [
		'SecurityIncident'
	];

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Security Incident Stages');
		$this->_group = 'security-operations';

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
		];

		$this->fieldData = [
			'name' => [
				'label' => __('Name'),
				'editable' => true,
				'description' => __('Give a name to this stage. For example "Identification" or "Containment"'),
			],
			'description' => [
				'label' => __('Description'),
				'editable' => true,
				'description' => __('Give a description to this stage'),
			],
			'SecurityIncident' => [
				'label' => __('Security Incident'),
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
				->textField('name', [
					'showDefault' => true
				])
				->textField('description', [
					'showDefault' => true
				]);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getReportsConfig()
	{
		return [
			'table' => [
				'model' => [
					'SecurityIncident',
				]
			],
		];
	}

	public function getObjectStatusConfig() {
        return [
            'lifecycle_incomplete' => [
                'trigger' => [
                    $this->SecurityIncident,
                ],
                'hidden' => true
            ]
        ];
    }

	public function getStage($conditions = array()){
		$stage = $this->find('first', array(
			'conditions' => $conditions
		));

		return $stage;
	}

	public function getStagesList($conditions = array()){
		$stages = $this->find('list', array(
			'conditions' => $conditions
		));

		return $stages;
	}

	public function hasSectionIndex()
	{
		return true;
	}
}