<?php
App::uses('DashboardAppModel', 'Dashboard.Model');

class DashboardKpiAttribute extends DashboardAppModel {
	public $useTable = 'kpi_attributes';

	public $belongsTo = [
		'DashboardKpi' => [
			'className' => 'Dashboard.DashboardKpi',
			'foreignKey' => 'kpi_id',
			'counterCache' => true
		]
	];

	public $validate = array(
		'model' => array(
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field is required',
			],
			'inList' => [
				'rule' => ['inList', [
					'User',
					'CustomRoles.CustomRole',
					'CustomRoles.CustomUser',
					'Admin',
					'CustomQuery',
					'DynamicFilter',
					'AwarenessProgram',
					'AwarenessProgramUserModel',
					'ComplianceManagement',
					'ComplianceType',
					'AdvancedFilter'
				]]
			]
		),
		'foreign_key' => [
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field is required'
			]
		],
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Dashboard KPI Attributes');

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			)
		);

		$this->fieldData = array(
			'foreign_key' => array(
				'label' => __('Filter'),
				'options' => array($this, 'getFilters'),
				'editable' => true,
				'description' => __('Choose one filter from the dropdown to be used for this KPI'),
				'empty' => __('Choose one')
			),
			'model' => array(
				'label' => __('Model'),
				'editable' => true,
				'description' => __( 'TBD' ),
			)
		);

		parent::__construct($id, $table, $ds);
	}

	public function getFilters() {
		$data = ClassRegistry::init('AdvancedFilter')->find('all', [
			'conditions' => [
				'AdvancedFilter.user_id' => $this->currentUser('id'),
				'AdvancedFilter.model' => $this->DashboardKpi->instance()->allowedModels
			],
			'fields' => ['id', 'name', 'model'],
			'recursive' => -1
		]);

	
		$list = [];
		foreach ($data as $item) {
			$list[$item['AdvancedFilter']['id']] = sprintf(
				'%s - %s',
				ClassRegistry::init($item['AdvancedFilter']['model'])->label(),
				$item['AdvancedFilter']['name']);
		}

		return $list;
	}

}
