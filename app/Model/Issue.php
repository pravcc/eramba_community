<?php
App::uses('AppModel', 'Model');
App::uses('InheritanceInterface', 'Model/Interface');

class Issue extends AppModel implements InheritanceInterface
{
	protected $issueParentModel = false;

	public $displayField = 'description';

	public $mapping = array(
		'titleColumn' => false,
		'logRecords' => true,
		'workflow' => false
	);

	public $workflow = array(
		// 'pullWorkflowData' => array('SecurityServiceIssue')
	);

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
        'ModuleDispatcher' => [
            'behaviors' => [
                'Reports.Report',
            ]
        ],
		'Visualisation.Visualisation',
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
        'SubSection' => [
            'parentField' => 'foreign_key'
        ],
		'AdvancedFilters.AdvancedFilters'
	);

	public $belongsTo = array(
		'User'
	);

	public $validate = array(
		'date_start' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Please enter a date'
			),
			'date' => array(
				'rule' => 'date',
				'message' => 'Enter a valid date'
			)
		),
		'date_end' => array(
			'date' => array(
				'required' => true,
				'allowEmpty' => true,
				'rule' => 'date',
				'message' => 'Enter a valid date'
			)
		)
	);

	public $hasMany = array(
	);

	public static function statuses($value = null) {
        $options = array(
            self::STATUS_CLOSED => __('Closed'),
            self::STATUS_OPEN => __('Open')
        );
        return parent::enum($value, $options);
    }

    const STATUS_CLOSED = ISSUE_CLOSED;
    const STATUS_OPEN = ISSUE_OPEN;

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Issues');

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
		];

		$this->fieldData = [
			'model' => [
				'label' => __('Model'),
				'editable' => false,
				'hidden' => true,
			],
			'foreign_key' => [
				'label' => __('Foreign Key'),
				'editable' => true,
				'empty' => __('Choose one ...'),
                'renderHelper' => ['Issues', 'foreignKeyField']
			],
			'date_start' => [
				'label' => __('Start Date'),
				'editable' => true,
			],
			'date_end' => [
				'label' => __('End Date'),
				'editable' => true,
			],
			'user_id' => [
				'label' => __('Owner'),
				'editable' => true,
			],
			'description' => [
				'label' => __('Description'),
				'editable' => true,
				'inlineEdit' => true
			],
			'status' => [
				'label' => __('Status'),
				'editable' => true,
				'options' => [$this, 'statuses']
			],
		];

		parent::__construct($id, $table, $ds);
	}

	public function getAdvancedFilterConfig()
    {
        $advancedFilterConfig = $this->createAdvancedFilterConfig()
            ->group('general', [
                'name' => __('General')
            ])
                ->nonFilterableField('id');

        if (!empty($this->issueParentModel)) {
        	$ParentModel = ClassRegistry::init($this->issueParentModel);
        	$advancedFilterConfig
        		->multipleSelectField('foreign_key', [$ParentModel, 'getList'], [
        			'label' => $ParentModel->label(['singular' => true])
        		]);
        }

        $advancedFilterConfig
     		->dateField('date_start', [
            	'showDefault' => true
            ])
			->dateField('date_end', [
            	'showDefault' => true
            ])
			->multipleSelectField('user_id', [$this, 'getUsers'], [
            	'showDefault' => true
            ])
			->textField('description', [
            	'showDefault' => true
            ])
			->multipleSelectField('status', [$this, 'statuses'], [
            	'showDefault' => true
            ]);

        $this->otherFilters($advancedFilterConfig);

        return $advancedFilterConfig->getConfiguration()->toArray();
    }

	public function getReportsConfig()
    {
		return [
			'table' => [
				'model' => [
					$this->issueParentModel
				]
			],
		];
	}

	public function getMacrosConfig()
	{
		return [
			'assoc' => [
				$this->issueParentModel
			],
		];
	}

	public function parentModel() {
        return $this->issueParentModel;
    }

    public function parentNode($type) {
    	return $this->visualisationParentNode('foreign_key');
    }

	public function afterSave($created, $options = array()) {
		parent::afterSave($created, $options);

		$this->triggerAssociatedObjectStatus($this->id);
	}

	public function beforeDelete($cascade = true) {
		$ret = true;
		if (!empty($this->id)) {
			// $this->bindIssueModel();
			$data = $this->getIssue();

			$this->parentModel = $data[$this->alias]['model'];
			$this->parentId = $data[$this->alias]['foreign_key'];
		}

		return $ret;
	}

	public function afterDelete() {
		$ret = true;
		if (isset($this->parentId) && isset($this->parentModel)) {
			$this->triggerAssociatedObjectStatus([
				$this->alias => [
					'model' => $this->parentModel,
					'foreign_key' => $this->parentId
				]
			]);
		}

		return $ret;
	}

	public function triggerAssociatedObjectStatus($id) {
		if (is_array($id) && !empty($id)) {
			$data = $id;
		}
		else {
			$data = $this->find('first', [
	            'conditions' => [
	                "{$this->alias}.id" => $id,
	            ],
	            'recursive' => -1
	        ]);
		}

        if (empty($data)) {
            return;
        }

        $AssocModel = ClassRegistry::init($data[$this->alias]['model']);
        if ($AssocModel->Behaviors->enabled('ObjectStatus.ObjectStatus')) {
            $AssocModel->triggerObjectStatus('control_with_issues', $data[$this->alias]['foreign_key']);
        }
    }

	protected function bindIssueModel() {
		// debug($this->issueParentModel);
		if (!empty($this->issueParentModel)) {
			$this->bindModel(array(
				'belongsTo' => array(
					$this->issueParentModel => array(
						'foreignKey' => 'foreign_key'
					)
				)
			));

			return true;
		}

		return false;
	}

	protected function getIssue() {
		$issue = $this->find('first', array(
			'conditions' => array(
				'id' => $this->id
			),
			'recursive' => -1
		));

		return $issue;
	}

	protected function getMapping() {
	  return $this->mapping;
	}

	public function hasSectionIndex()
	{
		return true;
	}
}