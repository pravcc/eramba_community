<?php
App::uses('AppModel', 'Model');
App::uses('ImportToolModule', 'ImportTool.Lib');
App::uses('InheritanceInterface', 'Model/Interface');

class Process extends AppModel implements InheritanceInterface
{
	public $displayField = 'name';

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];
	
	public $actsAs = array(
		'Search.Searchable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'description'
			)
		),
		'ModuleDispatcher' => [
			'behaviors' => [
				'CustomFields.CustomFields',
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
		'SubSection' => [
			'parentField' => 'business_unit_id'
		],
		'AdvancedFilters.AdvancedFilters'
	);

	public $mapping = array(
		'indexController' => 'processes',
		/*'indexController' => array(
			'basic' => 'processes',
			'advanced' => 'process',
			'action' => 'index',
			'params' => array('business_unit_id')
		),*/
		'titleColumn' => 'name',
		'logRecords' => true,
		'notificationSystem' => true,
		'workflow' => false
	);

	public $validate = array(
		'name' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'description' => array(
		),
		'rto' => array(
			'rule' => 'numeric',
			'required' => true,
			'allowEmpty' => false
		),
		'rpo' => array(
			'rule' => 'numeric',
			'required' => true,
			'allowEmpty' => false
		)
	);

	public $belongsTo = array(
		'BusinessUnit'
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Processes');
		$this->_group = 'organization';

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			]
		];

		$this->fieldData = [
			'business_unit_id' => [
				'label' => __('Business Unit'),
				'editable' => true,
				'empty' => __('Choose one ...'),
				'renderHelper' => ['Processes', 'businessUnitField']
			],
			'name' => [
				'label' => __('Name'),
				'editable' => true,
				'description' => __('The name of the process in the scope of this business unit. For example "Payroll", "Hiring".'),
				'inlineEdit' => true
			],
			'description' => [
				'label' => __('Description'),
				'editable' => true,
				'inlineEdit' => true
				// 'description' => __(''),
			],
			'rto' => [
				'label' => __('RTO'),
				'editable' => true,
				'description' => __('OPTIONAL: This value is only useful if you plan to BIA (Business Impact Analysis) your organisation with the Risk Management / Business Impact Analysis module. How long (in days, hours, etc) should it take to get things back on (this should be less or equal to MTO). Most business people usually reply ASAP, but in practice that might not be accurate.'),
				'inlineEdit' => true
			],
			'rpo' => [
				'label' => __('MTO'),
				'editable' => true,
				'description' => __('OPTIONAL: This value is only useful if you plan to BIA (Business Impact Analysis) your organisation with the Risk Management / Business Impact Analysis module. How long things can be broken before they become a serious business problem.'),
				'inlineEdit' => true
			],
			'rpd' => [
				'label' => __('Revenue per Hour'),
				'editable' => true,
				'description' => __('OPTIONAL: This value is only useful if you plan to BIA (Business Impact Analysis) your organisation with the Risk Management / Business Impact Analysis module. This should be used as a prioritization tool in order to Risk analyse those business that loss is too high and a mitigation -continuity plan- is cost effective.'),
				'inlineEdit' => true
			],
		];

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Processes'),
			'pdf_file_name' => __('processes'),
			'csv_file_name' => __('processes'),
			'bulk_actions' => true,
			'history' => true,
            'trash' => true,
            'use_new_filters' => true
		);

		parent::__construct($id, $table, $ds);
	}

	public function beforeValidate($options = array()) {
		if (isset($this->data['Process']['business_unit_id'])) {
			$this->invalidateRelatedNotExist('BusinessUnit', 'business_unit_id', $this->data['Process']['business_unit_id']);
		}
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
				])
				->multipleSelectField('business_unit_id', [ClassRegistry::init('BusinessUnit'), 'getList'], [
					'showDefault' => true
				])
				->numberField('rto', [
					'showDefault' => true
				])
				->numberField('rpo', [
					'showDefault' => true
				])
				->numberField('rpd', [
					'showDefault' => true
				]);

		if (AppModule::loaded('CustomFields')) {
			$this->customFieldsFilters($advancedFilterConfig);
		}

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getReportsConfig()
	{
		return [
			'table' => [
				'model' => [
					'BusinessUnit'
				]
			],
		];
	}

	public function getMacrosConfig()
	{
		return [
			'assoc' => [
				'BusinessUnit',
			],
		];
	}

	public function getNotificationSystemConfig()
	{
		return parent::getNotificationSystemConfig();
	}

	public function parentModel() {
		return 'BusinessUnit';
	}

	public function parentNode($type) {
        return $this->visualisationParentNode('business_unit_id');
    }

	public function getBusinessUnits() {
		$data = $this->BusinessUnit->find('list', array(
			'order' => array('BusinessUnit.name' => 'ASC'),
			'recursive' => -1
		));
		return $data;
	}

	public function hasSectionIndex()
	{
		return true;
	}
}
