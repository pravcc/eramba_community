<?php
App::uses('AppModel', 'Model');
App::uses('ImportToolModule', 'ImportTool.Lib');

class ProgramScope extends AppModel
{
	public $displayField = 'version';

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $mapping = array(
		'titleColumn' => 'version',
		'logRecords' => true,
		'notificationSystem' => array('index'),
		'workflow' => false,
	);

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'version', 'description', 'status'
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
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
		'AdvancedFilters.AdvancedFilters'
	);

	public $validate = array(
		'version' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Version is a required field'
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
					PROGRAM_SCOPE_DRAFT,
					PROGRAM_SCOPE_DISCARDED,
					PROGRAM_SCOPE_CURRENT
				)),
				'message' => 'Please select one of the statuses'
			),
			'unique' => array(
				'rule' => 'validateSingleCurrentStatus',
				'message' => 'Current status can be used only once and is already used by another Scope'
			)
		)
	);

	public $hasMany = array(
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

    const STATUS_DRAFT = PROGRAM_SCOPE_DRAFT;
    const STATUS_DISCARDED = PROGRAM_SCOPE_DISCARDED;
    const STATUS_CURRENT = PROGRAM_SCOPE_CURRENT;

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Program Scopes');
		$this->_group = 'program';

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			]
		];

		$this->fieldData = [
			'version' => [
				'label' => __('Version'),
				'editable' => true,
				'description' => __('The version of the scope. As you update the version of your scope you should update this number.'),
				'inlineEdit' => true,
			],
			'description' => [
				'label' => __('Description'),
				'editable' => true,
				'description' => __('The purpose of this document is to clearly define the boundaries of the Information Security Management System (ISMS)'),
				'inlineEdit' => true,
			],
			'status' => [
				'label' => __('Status'),
				'editable' => true,
				'options' => [$this, 'statuses'],
				'description' => __('The status of the scope can be "Draft" (in development), "Current" (its the actual scope of your program. n.b. There can only be one active scope) and "Discarded" (the scope is no longer in use).'),
				'inlineEdit' => true,
				'renderHelper' => ['ProgramScopes', 'statusField']
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
				->nonFilterableField('id')
				->textField('version', [
					'showDefault' => true
				])
				->textField('description', [
					'showDefault' => true
				])
				->selectField('status', [$this, 'getStatuses'], [
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
				]
			],
		];
	}

	/**
	 * Validation to have only one Scope with Current status.
	 */
	public function validateSingleCurrentStatus($check) {
		if ($check['status'] != PROGRAM_SCOPE_CURRENT) {
			return true;
		}

		return !$this->hasCurrentStatus($this->id);
	}

	/**
	 * Checks if there already is a Scope with a Current status.
	 * 
	 * @param  int  $id Scope ID of an item not to be checked
	 */
	public function hasCurrentStatus($id = null) {
		$conds = array(
			'ProgramScope.status' => PROGRAM_SCOPE_CURRENT
		);

		if (!empty($id)) {
			$conds['ProgramScope.id !='] = $id;
		}

		$hasCurrent = $this->find('count', array(
			'conditions' => $conds,
			'recursive' => -1
		));

		return $hasCurrent;
	}

	/**
	 * Get a label for a changed Scope status.
	 */
	public function getProgramScopeStatuses() {
		if (isset($this->data['ProgramScope']['status'])) {
			return getProgramScopeStatuses($this->data['ProgramScope']['status']);
		}

		return false;
	}

	public function getStatuses() {
		return static::statuses();
	}

	public function hasSectionIndex()
	{
		return true;
	}
}
