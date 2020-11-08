<?php
App::uses('AppModel', 'Model');
App::uses('InheritanceInterface', 'Model/Interface');

class SecurityIncidentStagesSecurityIncident extends AppModel implements InheritanceInterface
{
	public $mapping = array(
		'logRecords' => true,
		// 'indexController' => 'securityIncidents',
		'indexController' => array(
			'action' => 'index',
			'advanced' => 'securityIncidents',
			'basic' => 'securityIncidents'
		),
		'workflow' => false
	);

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'ModuleDispatcher' => [
			'behaviors' => [
				'Reports.Report',
			]
		],
		'Containable',
		'ObjectStatus.ObjectStatus',
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
		'SubSection' => [
			'parentField' => 'security_incident_id'
		],
		'AdvancedFilters.AdvancedFilters'
	);

	public $belongsTo = array(
		'SecurityIncidentStage',
		'SecurityIncident'
	);

	public $hasMany = array(
	);

	public function __construct($id = false, $table = null, $ds = null)
	{
		//
		// Init helper Lib for UserFields Module
		// $UserFields = new UserFields();
		//
		
		$this->label = __('Stages');
        $this->_group = 'security-operations';

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			),
		);

		$this->fieldData = array(
			'security_incident_id' => array(
				'type' => 'hidden',
				'label' => __('Security Incident'),
				'editable' => true,
				'macro' => [
					'name' => 'incident'
				]
				// 'renderHelper' => ['SecurityServiceAudits', 'securityServiceField']
				// 'hidden' => true
			),
			'security_incident_stage_id' => array(
				'type' => 'hidden',
				'label' => __('Stage'),
				'editable' => false,
				'macro' => [
					'name' => 'stage'
				]
			),
			'status' => [
				'type' => 'toggle',
				'label' => __('Status'),
				'options' => [$this, 'statuses'],
				'renderHelper' => ['SecurityIncidentStages', 'statusField'],
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Is important that incidents are systematically analysed and documented. Is expected that each stage has comments, attachments with incidents and tagged as "complete" once the stage requirements are completed.'),
				// 'dependency' => true
			],
			'stage_name' => [
				'label' => __('Name'),
				'editable' => false,
				'description' => __('Give a name to this stage. For example "Identification" or "Containment"'),
			],
			'stage_description' => [
				'label' => __('Description'),
				'editable' => false,
				'description' => __('Give a description to this stage'),
			],
		);

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Security Incident Stages'),
			'pdf_file_name' => __('security_incident_stages'),
			'csv_file_name' => __('security_incident_stages'),
			'use_new_filters' => true,
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
				->selectField('status', [$this, 'statuses'], [
					'showDefault' => true,
				])
				->multipleSelectField('security_incident_id', [ClassRegistry::init('SecurityIncident'), 'getList'], [
					'label' => __('Security Incident'),
					'showDefault' => true,
				])
				->textField('SecurityIncidentStage-name', [
					'label' => __('Stage'),
					'showDefault' => true,
				])
				->textField('SecurityIncidentStage-description', [
					'showDefault' => true,
				]);

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getDisplayFilterFields()
	{
		return ['security_incident_id', 'stage_name'];
	}

	public function parentModel()
	{
		return 'SecurityIncident';
	}

	public function getObjectStatusConfig() {
		return [
			'initiated' => [
                'title' => __('Incomplete'),
                'type' => 'danger',
                'callback' => [$this, 'statusInitiated'],
                'storageSelf' => false,
                'hidden' => true
            ],
            'completed' => [
                'title' => __('Completed'),
                'type' => 'success',
                'callback' => [$this, 'statusCompleted'],
                'storageSelf' => false,
                'hidden' => true
            ],
		];
	}

    public function statusInitiated() {
    	$data = $this->find('count', [
			'conditions' => [
				'SecurityIncidentStagesSecurityIncident.id' => $this->id,
				'SecurityIncidentStagesSecurityIncident.status' => self::STATUS_INITIATED,
			],
			'recursive' => -1
		]);

    	return (boolean) $data;
    }

    public function statusCompleted() {
    	$data = $this->find('count', [
			'conditions' => [
				'SecurityIncidentStagesSecurityIncident.id' => $this->id,
				'SecurityIncidentStagesSecurityIncident.status' => self::STATUS_COMPLETED
			],
			'recursive' => -1
		]);

    	return (boolean) $data;
    }

	public function getReportsConfig()
	{
		return [
			'table' => [
				'model' => [
					'SecurityIncident', 'SecurityIncidentStage',
				]
			],
		];
	}

	public function getMacrosConfig()
	{
		return [
			'assoc' => [
				'SecurityIncidentStage'
			],
		];
	}

	public static function statuses($value = null) {
		$options = array(
			self::STATUS_INITIATED => __('Incomplete'),
			self::STATUS_COMPLETED => __('Completed')
		);
		return parent::enum($value, $options);
	}
    // default is not used anymore, its here only for backwards compatibility
	const STATUS_INITIATED = 0;
	const STATUS_COMPLETED = 1;

	public function getItem($conditions){
		$item = $this->find('first', array(
			'conditions' => $conditions
		));

		return $item;
	}

	public function getIncidents() {
        $data = $this->SecurityIncident->find('list', array(
            'fields' => array('SecurityIncident.id', 'SecurityIncident.title'),
        ));
        return $data;
    }
}
