<?php
App::uses('AppModel', 'Model');
App::uses('InheritanceInterface', 'Model/Interface');

class ComplianceFinding extends AppModel implements InheritanceInterface
{
	public $displayField = 'title';

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'AuditLog.Auditable',
		'Utils.SoftDelete',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'title', 'description'
			)
		),
		'Visualisation.Visualisation',
		'ObjectStatus.ObjectStatus',
		'Comments.Comments',
		'Widget.Widget',
		'Attachments.Attachments',
	);

	public $workflow;

	public $mapping = array(
		'indexController' => array(
			'basic' => 'complianceAudits',
			'advanced' => 'complianceFindings',
			'params' => false
		),
		'titleColumn' => 'title',
		'logRecords' => true,
		'notificationSystem' => true,
		'workflow' => false
		/*'workflow' => array(
			'modifyQuery' => array('index')
		)*/
	);

	public $validate = array(
		'title' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'description' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'deadline' => array(
			'rule' => 'date'
		)
	);

	public $belongsTo = array(
		'ComplianceFindingStatus',
		'CompliancePackageItem',
		'ComplianceAudit' => array(
			'counterCache' => true
		)
	);

	public $hasMany = array(
		'Classification' => array(
			'className' => 'ComplianceFindingClassification'
		)
	);

	public $hasAndBelongsToMany = array(
		'ComplianceException',
		'ThirdPartyRisk'
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Compliance Findings');
        $this->_group = parent::SECTION_GROUP_COMPLIANCE_MGT;

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			),
			'compliance-exception' => array(
				'label' => __('Compliance Exception')
			),
			'third-party-risks' => array(
				'label' => __('Third Party Risks')
			)
		);

		$this->fieldData = array(
			'title' => array(
				'label' => __('Title'),
				'editable' => true
			),
			'type' => array(
				'label' => __('Type'),
				'options' => array($this, 'getFindingTypes'),
				'editable' => true
			),
			'deadline' => array(
				'label' => __('Deadline'),
				'editable' => true
			),
			'compliance_finding_status_id' => array(
				'label' => __('Status'),
				'editable' => true
			),
			'description' => array(
				'label' => __('Description'),
				'editable' => true
			),
			'ComplianceException' => array(
				'label' => __('Compliance Exception'),
				'editable' => true
			),
			'ThirdPartyRisk' => array(
				'label' => __('Third Party Risk'),
				'editable' => true
			),
			'compliance_package_item_id' => array(
				'label' => __('Complinace Package Item')
			),
			'compliance_audit_id' => array(
				'label' => __('Complinace Audit')
			),
			'expired' => array(
				'label' => __('Expired'),
				'type' => 'toggle',
				'hidden' => true
			)
		);

		$this->notificationSystem = array(
			'macros' => array(
				'FINDING_ID' => array(
					'field' => 'ComplianceFinding.id',
					'name' => __('Compliance Finding ID')
				),
				'FINDING_TITLE' => array(
					'field' => 'ComplianceFinding.title',
					'name' => __('Compliance Finding Title')
				),
				'FINDING_DEADLINE' => array(
					'field' => 'ComplianceFinding.deadline',
					'name' => __('Compliance Finding Deadline')
				),
				'FINDING_STATUS' => array(
					'field' => 'ComplianceFindingStatus.name',
					'name' => __('Compliance Finding Status')
				),
				'FINDING_DESCRIPTION' => array(
					'field' => 'ComplianceFinding.description',
					'name' => __('Compliance Finding Description')
				),
				'FINDING_CLASSIFICATION' => array(
					'field' => 'Classification.{n}.name',
					'name' => __('Compliance Finding Tag')
				),
				'TP_NAME' => array(
					'field' => 'ComplianceAudit.ThirdParty.name',
					'name' => __('Third Party Name')
				)
			),
			'customEmail' =>  true
		);

		$this->advancedFilter = array(

			__('General') => array(
				'compliance_audit_id' => array(
					'type' => 'multiple_select',
					'name' => __('Name'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'ComplianceFinding.compliance_audit_id',
						'field' => 'ComplianceFinding.id',
					),
					'data' => array(
						'method' => 'getComplianceAudits'
					),
					'contain' => array(
						'ComplianceAudit' => array(
							'name'
						)
					)
				),
				'third_party' => array(
					'type' => 'multiple_select',
					'name' => __('Compliance Package'),
					'filter' => array(
						'type' => 'value',
						'field' => array('ThirdParty.id'),
					),
					'data' => array(
						'method' => 'getThirdParties',
					),
					'containable' => array(
						'ComplianceAudit' => array(
							'fields' => array('third_party_id')
						)
					),
					'field' => 'ThirdParty.name',
					'joins' => array(
						array(
							'table' => 'third_parties',
							'alias' => 'ThirdParty',
							'type' => 'LEFT',
							'conditions' => array(
								'ThirdParty.id = ComplianceAudit.third_party_id'
							)
						)
					)
				),
				'compliance_package_item_item_id' => array(
					'type' => 'text',
					'name' => __('Item ID'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'CompliancePackageItem.item_id',
						'field' => 'ComplianceFinding.compliance_package_item_id',
					),
					'field' => 'CompliancePackageItem.item_id',
					'containable' => array('CompliancePackageItem')
				),
				'compliance_package_item_name' => array(
					'type' => 'text',
					'name' => __('Name'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'CompliancePackageItem.name',
						'field' => 'ComplianceFinding.compliance_package_item_id',
					),
					'field' => 'CompliancePackageItem.name',
					'containable' => array('CompliancePackageItem')			
				),
				'compliance_package_item_description' => array(
					'type' => 'text',
					'name' => __('Compliance Requirement Description'),
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'CompliancePackageItem.description',
						'field' => 'ComplianceFinding.compliance_package_item_id',
					),
					'field' => 'CompliancePackageItem.description',
					'containable' => array('CompliancePackageItem')
				),
				'auditor' => array(
					'type' => 'multiple_select',
					'name' => __('Auditor'),
					'show_default' => false,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'ComplianceAudit.auditor_id',
						'field' => 'ComplianceFinding.compliance_audit_id',
					),
					'data' => array(
						'method' => 'getUsers',
						'result_key' => true,
					),
					'containable' => array(
						'ComplianceAudit' => array(
							'fields' => array('auditor_id')
						)
					),
					'field' => 'ComplianceAudit.auditor_id'
				),
				'third_party_contact' => array(
					'type' => 'multiple_select',
					'name' => __('Third Party Contact'),
					'show_default' => false,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'ComplianceAudit.third_party_contact_id',
						'field' => 'ComplianceFinding.compliance_audit_id',
					),
					'containable' => array(
						'ComplianceAudit' => array(
							'fields' => array('third_party_contact_id')
						)
					),
					'field' => 'ComplianceAudit.third_party_contact_id',
					'data' => array(
						'method' => 'getUsers',
						'result_key' => true,
					),
				),
			),

			//////////////
			

            __('Findings') => array(
                'id' => array(
                    'type' => 'text',
                    'name' => __('ID'),
                    'show_default' => true,
                    'filter' => false
                ),
                'title' => array(
					'type' => 'text',
					'name' => __('Title'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'ComplianceFinding.title',
						'field' => 'ComplianceFinding.id',
					),
					'editable' => 'title'
				),
                'type' => array(
					'type' => 'select',
					'name' => __('Type'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'ComplianceFinding.type',
						'field' => 'ComplianceFinding.id',
					),
					'data' => array(
						'method' => 'getFindingTypes',
						'empty' => __('All'),
						'result_key' => true
					),
					'editable' => 'type'
				),
				'classification_id' => array(
					'type' => 'multiple_select',
					'name' => __('Tags'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findByFindingClassification',
						'field' => 'ComplianceFinding.id',

						// 'type' => 'subquery',
						// 'method' => 'findComplexType',
						// 'findField' => 'Classification.id',
						// 'field' => 'ComplianceFinding.id',
					),
					'data' => array(
						'method' => 'getFindingClassification',
					),
					'many' => true,
					'field' => 'Classification.{n}.name',
					'containable' => array(
						'Classification' => array(
							'fields' => array('name')
						)
					),
				),
				'deadline' => array(
					'type' => 'date',
					'comparison' => false,
					'name' => __('Deadline'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'ComplianceFinding.deadline',
						'field' => 'ComplianceFinding.id',
					),
					'editable' => 'deadline'
				),
				'description' => array(
					'type' => 'text',
					'name' => __('Description'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'ComplianceFinding.description',
						'field' => 'ComplianceFinding.id',
					),
					'editable' => 'description'
				),
				'compliance_finding_status_id' => array(
					'type' => 'select',
					'name' => __('Status'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'ComplianceFinding.compliance_finding_status_id',
						'field' => 'ComplianceFinding.id',
					),
					'data' => array(
						'method' => 'getFindingStatuses',
						'empty' => __('All'),
					),
					'field' => 'ComplianceFindingStatus.name',
					'containable' => array(
						'ComplianceFindingStatus' => array(
							'fields' => array('name')
						)
					),
					'editable' => 'compliance_finding_status_id'
				),
				// 'exceptions' => array(
				// 	'type' => 'text',
				// 	'name' => __('Compliance Exception'),
				// 	'show_default' => false,
				// 	'filter' => false,
				// 	'field' => 'ComplianceFinding.id',
				// 	'editable' => 'ComplianceException'
				// ),
            ),
        );

        $this->advancedFilterSettings = array(
            'pdf_title' => __('Compliance Findings'),
            'pdf_file_name' => __('compliance_findings'),
            'csv_file_name' => __('compliance_findings'),
            'reset' => array(
                'controller' => 'complianceAudits',
                'action' => 'index',
            ),
            'history' => true,
            'bulk_actions' => true,
            'trash' => true,
            'view_item' => false,
            'use_new_filters' => true
        );

		parent::__construct($id, $table, $ds);
	}

	public function getObjectStatusConfig() {
        return [
            'expired' => [
            	'title' => __('Expired'),
                'callback' => [$this, 'statusExpired'],
                'regularTrigger' => true,
            ],
        ];
    }

    public function statusExpired($conditions = null) {
        return parent::statusExpired([
            'ComplianceFinding.deadline < DATE(NOW())'
        ]);
    }

	public function parentModel() {
		return 'ComplianceAudit';
	}

	public function parentNode($type) {
        return $this->visualisationParentNode('compliance_audit_id');
    }

	public function getFindingTypes() {
		return getFindingTypes();
	}

	public function getComplianceAudits() {
		return $this->ComplianceAudit->getComplianceAudits();
	}

	public function getThirdParties() {
		return $this->ComplianceAudit->getThirdParties();
	}

	public function getUsers() {
		return $this->ComplianceAudit->getUsers();
	}

	public function getFindingStatuses() {
		$data = $this->ComplianceFindingStatus->find('list', array(
			'fields' => array('ComplianceFindingStatus.id', 'ComplianceFindingStatus.name'),
			'recursive' => 0
		));
		return $data;
	} 

	public function getFindingClassification() {
		$data = $this->Classification->find('list', array(
			'order' => array('Classification.name' => 'ASC'),
			'fields' => array('Classification.id', 'Classification.name'),
			'recursive' => -1
		));
		return $data;
	}

	public function getAuditeeIds($id) {
		$auditData = $this->find('first', array(
			'conditions' => array(
				'ComplianceFinding.id' => $id
			),
			'fields' => array('ComplianceFinding.compliance_audit_id'),
			'recursive' => -1
		));

		$auditId = $auditData['ComplianceFinding']['compliance_audit_id'];

		$data = $this->find('first', array(
			'conditions' => array(
				'ComplianceFinding.id' => $id
			),
			'contain' => array(
				'CompliancePackageItem' => array(
					'fields' => array('id'),
					'ComplianceAuditSetting' => array(
						'conditions' => array(
							'ComplianceAuditSetting.compliance_audit_id' => $auditId
						),
						'fields' => array('id'),
						'Auditee' => array(
							// 'fields' => array('auditee_id')
						)
					)
				)
			),
			'recursive' => -1
		));

		$ids = array();
		if (!empty($data['CompliancePackageItem']['ComplianceAuditSetting'])) {
			foreach ($data['CompliancePackageItem']['ComplianceAuditSetting'] as $setting) {
				if (!empty($setting['Auditee'])) {
					foreach ($setting['Auditee'] as $auditee) {
						$ids[] = $auditee['id'];
					}
				}
			}
		}

		return $ids;
	}

	public function getAuditorId($id) {
		$data = $this->find('first', array(
			'conditions' => array(
				'ComplianceFinding.id' => $id
			),
			'fields' => array('ComplianceAudit.auditor_id'),
			'recursive' => 0
		));

		return $data['ComplianceAudit']['auditor_id'];
	}

	public function findByFindingClassification($data = array(), $filter) {
		$this->Classification->Behaviors->attach('Containable', array('autoFields' => false));
		$this->Classification->Behaviors->attach('Search.Searchable');

		$query = $this->Classification->getQuery('all', array(
			'conditions' => array(
				'Classification.id' => $data[$filter['name']],
			),
			'contain' => array(),
			'fields' => array(
				'Classification.compliance_finding_id',
			)
		));

		return $query;
	}

	public function beforeFind($query){
		if (isset($query['contain']['ComplianceAudit'])) {
			$query['conditions']['ComplianceAudit.deleted'] = 0;
		}

		return $query;
	}

	public function beforeSave($options = array()){
		// transforms the data array to save the HABTM relation
		// $this->transformDataToHabtm(array('ComplianceException', 'ThirdPartyRisk'));

		return true;
	}

	public function afterSave($created, $options = array()) {
		$finding = $this->find('count', array(
			'conditions' => array(
				'ComplianceFinding.id' => $this->id
			),
			'recursive' => -1
		));

		// if deleted
		if (empty($finding)) {
			return true;
		}

		$this->resaveNotifications($this->id);

		//create a audit setting record that finding was created.
		if ($created) {
			$data = $this->ComplianceAudit->ComplianceAuditSetting->find('first', array(
				'conditions' => array(
					'ComplianceAuditSetting.compliance_audit_id' => $this->data[$this->name]['compliance_audit_id'],
					'ComplianceAuditSetting.compliance_package_item_id' => $this->data[$this->name]['compliance_package_item_id']
				),
				'fields' => array('id'),
				'recursive' => -1
			));

			if (empty($data)) {
				return false;
			}

			if (!empty($this->data[$this->name]['title'])) {
				$this->ComplianceAudit->ComplianceAuditSetting->addNoteToLog(__('Compliance Finding item <b>%s</b> created', $this->data[$this->name]['title']));
			}
			else {
				$this->ComplianceAudit->ComplianceAuditSetting->addNoteToLog(__('Compliance Finding item created'));
			}

			return $this->ComplianceAudit->ComplianceAuditSetting->setSystemRecord($data['ComplianceAuditSetting']['id'], 2);
		}
	}

	public function resaveNotifications($id) {
		$ret = true;

		$this->bindNotifications();
		$ret &= $this->NotificationObject->NotificationSystem->saveCustomUsersByModel($this->alias, $id);

		$auditData = $this->find('first', array(
			'conditions' => array(
				'ComplianceFinding.id' => $id
			),
			'fields' => array('compliance_audit_id'),
			'recursive' => -1
		));

		$this->ComplianceAudit->bindNotifications();
		$ret &= $this->ComplianceAudit->NotificationObject->NotificationSystem->saveCustomUsersByModel('ComplianceAudit', $auditData['ComplianceFinding']['compliance_audit_id']);

		return $ret;
	}
}
