<?php
App::uses('AppModel', 'Model');
App::uses('ImportToolModule', 'ImportTool.Lib');

class TeamRole extends AppModel
{
	public $displayField = 'role';

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];
	
	public $mapping = array(
		'titleColumn' => 'role',
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
				'user_id', 'role', 'status', 'responsibilities', 'competences'
			)
		),
		'AuditLog.Auditable',
		'Utils.SoftDelete',
		'Visualisation.Visualisation',
		'ModuleDispatcher' => [
			'behaviors' => [
				'CustomFields.CustomFields',
				'Reports.Report',
			]
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
		'AdvancedFilters.AdvancedFilters'
	);

	public $validate = array(
		'user_id' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'role' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Role is a required field'
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
					TEAM_ROLE_ACTIVE,
					TEAM_ROLE_DISCARDED
				)),
				'message' => 'Please select one of the statuses'
			)
		)
	);

	public $belongsTo = array(
		'User'
	);

	public $hasMany = array(
	);

	/*
     * static enum: Model::function()
     * @access static
     */
    public static function statuses($value = null) {
        $options = array(
            self::STATUS_ACTIVE => __('Active'),
			self::STATUS_DISCARDED => __('Inactive'),
        );
        return parent::enum($value, $options);
    }

    const STATUS_ACTIVE = TEAM_ROLE_ACTIVE;
    const STATUS_DISCARDED = TEAM_ROLE_DISCARDED;

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Team Roles');
		$this->_group = parent::SECTION_GROUP_PROGRAM;

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			]
		];

		$this->fieldData = [
			'user_id' => [
				'label' => __('Name'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Select a user that is a team member'),
				'quickAdd' => true
			],
			'role' => [
				'label' => __('Role'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Describe the role this team member has within the program'),
			],
			'responsibilities' => [
				'label' => __('Responsibilities'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Describe the responsibilities of this team member or role'),
			],
			'competences' => [
				'label' => __('Competences'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Describe the competences (skills, etc) that this team member has or plans to acquire'),
			],
			'status' => [
				'label' => __('Status'),
				'editable' => true,
				'inlineEdit' => true,
				'options' => [$this, 'statuses'],
				'description' => __('Select the status for this team member as Active or Inactive (no longer part of the program)'),
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
				->selectField('user_id', [$this, 'getUsers'], [
					'showDefault' => true
				])
				->textField('role', [
					'showDefault' => true
				])
				->textField('responsibilities', [
					'showDefault' => true
				])
				->textField('competences', [
					'showDefault' => true
				])
				->selectField('status', [$this, 'statuses'], [
					'showDefault' => true
				]);

		if (AppModule::loaded('CustomFields')) {
			$this->customFieldsFilters($advancedFilterConfig);
		}

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getStatuses() {
		return static::statuses();
	}

	/**
	 * Get a label for a changed Scope status.
	 */
	public function getTeamRoleStatuses() {
		if (isset($this->data['TeamRole']['status'])) {
			return getTeamRoleStatuses($this->data['TeamRole']['status']);
		}

		return false;
	}

	public function hasSectionIndex()
	{
		return true;
	}
}
