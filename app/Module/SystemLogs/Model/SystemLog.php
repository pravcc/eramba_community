<?php
App::uses('AppModel', 'Model');
App::uses('Hash', 'Utility');
App::uses('Inflector', 'Utility');

class SystemLog extends AppModel
{
	public $displayField = 'id';
	public $useTable = 'system_logs';

	public $relatedModel = null;

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];
	
	public $actsAs = [
		'Search.Searchable',
		'AdvancedFilters.AdvancedFilters'
	];

	public $belongsTo = [
		'User' => [
			'className' => 'User',
			'foreignKey' => 'user_id',
		],
	];

	public $validate = [

	];

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('System Logs');

		$Model = ClassRegistry::init($this->relatedModel);
		$this->bindSubjectModel($Model);

		$config = $Model->getSystemLogsConfig();

		$SubModel = null;
		if (!empty($config['subModel']['class'])) {
			$SubModel = ClassRegistry::init($config['subModel']['class']);
			$this->bindSubjectModel($SubModel, 'sub_foreign_key');
		}

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
		];

		$this->fieldData = [
			'model' => [
				'label' => __('Model'),
				'editable' => false,
			],
			'foreign_key' => [
				'label' => $Model->label(['singular' => true]),
				'editable' => false,
			],
			'sub_foreign_key' => [
				'label' => ($SubModel !== null) ? $SubModel->label(['singular' => true]) : __('Sub Subject'),
				'editable' => false,
			],
			'action' => [
				'label' => __('Action'),
				'editable' => false,
				'type' => 'select',
				'options' => [$Model, 'listSystemLogActions']
			],
			'result' => [
				'label' => __('Result'),
				'editable' => false,
			],
			'ip' => [
				'label' => __('Ip'),
				'editable' => false,
			],
			'uri' => [
				'label' => __('Uri'),
				'editable' => false,
			],
			'request_id' => [
				'label' => __('Request ID'),
				'editable' => false,
			],
			'message' => [
				'label' => __('Message'),
				'editable' => false,
			],
			'user_id' => [
				'label' => __('User'),
				'editable' => false,
			],
		];

		parent::__construct($id, $table, $ds);

		$this->advancedFilterSettings = [
			'pdf_title' => __('System Logs'),
			'pdf_file_name' => __('system_logs'),
			'csv_file_name' => __('system_logs'),
			'max_selection_size' => 10,
			'bulk_actions' => false,
			'history' => false,
			'trash' => false,
			'use_new_filters' => true,
			'actions' => false,
			'url' => [
				'plugin' => 'system_logs',
                'controller' => 'systemLogs',
                'action' => 'index',
                '?' => [
                    'advanced_filter' => 1
                ]
            ],
            'include_timestamps' => false
		];
	}

	protected function _getAdvancedFilterConfig()
	{
		$advancedFilterConfig = $this->createAdvancedFilterConfig()
			->group('general', [
				'name' => __('General')
			])
				->nonFilterableField('id')
				->selectField('action', [ClassRegistry::init($this->relatedModel), 'listSystemLogActions'], [
					'showDefault' => true
				])
				->multipleSelectField('user_id', [$this, 'getUsers'], [
					'label' => __('User'),
					'showDefault' => true,
				])
				->textField('message', [
					'showDefault' => true,
				])
				->dateField('created', [
					'label' => __('Date'),
					'showDefault' => true,
				]);

		return $advancedFilterConfig;
	}

	public function getAdvancedFilterConfig()
	{
		$advancedFilterConfig = $this->_getAdvancedFilterConfig();

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function bindSubjectModel($Model, $foreignKey = 'foreign_key') {
		$this->bindModel([
            'belongsTo' => [
                $Model->name => [
                    'className' => $Model->modelFullName(),
                    'foreignKey' => $foreignKey,
                ]
            ]
        ], false);
	}
}