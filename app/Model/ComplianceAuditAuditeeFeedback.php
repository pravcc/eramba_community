<?php
App::uses('AppModel', 'Model');

class ComplianceAuditAuditeeFeedback extends AppModel
{
	public $mapController = 'complianceAudits';

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array()
		)
	);

	public $belongsTo = array(
		'User',
		'ComplianceAuditSetting',
		'ComplianceAuditFeedbackProfile',
		'ComplianceAuditFeedback'
	);

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Compliance Audit Auditee Feedbacks');

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
		];

		$this->fieldData = [
			'user_id' => [
				'label' => __('User'),
				'editable' => false,
			],
			'compliance_audit_setting_id' => [
				'label' => __('Compliance Audit Setting'),
				'editable' => false,
			],
			'compliance_audit_feedback_id' => [
				'label' => __('Compliance Audit Feedback'),
				'editable' => false,
			],
			'compliance_audit_feedback_profile id' => [
				'label' => __('Compliance Audit Feedback Profile'),
				'editable' => false,
			],
		];

		$this->advancedFilter = array(
			__('General') => array(
				'id' => array(
					'type' => 'text',
					'name' => __('ID'),
					'filter' => false
				),
				/*'compliance_audit_setting_id' => array(
					'type' => 'text',
					'name' => __('Compliance Audit Analysis ID'),
					'show_default' => false,
					'filter' => array(
						'type' => 'value',
					),
				),*/
				'compliance_audit_id' => array(
					'type' => 'multiple_select',
					'name' => __('Compliance Audit'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findByComplianceAudit',
						'field' => 'ComplianceAuditAuditeeFeedback.compliance_audit_setting_id'
					),
					'data' => array(
						'method' => 'getComplianceAudits',
					),
					'field' => 'ComplianceAuditSetting.ComplianceAudit.name',
					'containable' => array(
						'ComplianceAuditSetting' => array(
							'fields' => array('id'),
							'ComplianceAudit' => array(
								'fields' => array('name'),
							)
						)
					),
				),
				'compliance_package_item_item_id' => array(
					'type' => 'text',
					'name' => __('Compliance Package Item ID'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findByPackageItemId',
						'field' => 'ComplianceAuditAuditeeFeedback.compliance_audit_setting_id'
					),
					'field' => 'ComplianceAuditSetting.CompliancePackageItem.item_id',
					'containable' => array(
						'ComplianceAuditSetting' => array(
							'fields' => array('id'),
							'CompliancePackageItem' => array(
								'fields' => array('item_id'),
							)
						)
					),
				),
				'compliance_audit_feedback_profile_id' => array(
					'type' => 'multiple_select',
					'name' => __('Feedback Profile'),
					'show_default' => true,
					'filter' => array(
						'type' => 'value',
					),
					'data' => array(
						'method' => 'getFeedbackProfiles',
					),
					'contain' => array(
						'ComplianceAuditFeedbackProfile' => array(
							'name'
						)
					)
				),
				'compliance_audit_feedback_id' => array(
					'type' => 'multiple_select',
					'name' => __('Feedback Answer'),
					'show_default' => true,
					'filter' => array(
						'type' => 'value',
					),
					'data' => array(
						'method' => 'getFeedbacks',
					),
					'contain' => array(
						'ComplianceAuditFeedback' => array(
							'name'
						)
					)
				)
			)
		);

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Compliance Audit Auditee Feedbacks'),
			'pdf_file_name' => __('compliance_audit_auditee_feedbacks'),
			'csv_file_name' => __('compliance_audit_auditee_feedbacks'),
			'actions' => false,
			'reset' => array(
				'controller' => 'complianceAudits',
				'action' => 'index',
			)
		);

		parent::__construct($id, $table, $ds);
	}

	public function getComplianceAudits() {
		return $this->ComplianceAuditSetting->ComplianceAudit->getComplianceAudits();
	}

	public function getFeedbacks() {
		$data = $this->ComplianceAuditFeedback->find('list', array(
			'order' => array('ComplianceAuditFeedback.name' => 'ASC'),
			'fields' => array('ComplianceAuditFeedback.id', 'ComplianceAuditFeedback.name'),
			'recursive' => -1
		));

		return $data;
	}

	public function getFeedbackProfiles() {
		$data = $this->ComplianceAuditFeedbackProfile->find('list', array(
			'order' => array('ComplianceAuditFeedbackProfile.name' => 'ASC'),
			'fields' => array('ComplianceAuditFeedbackProfile.id', 'ComplianceAuditFeedbackProfile.name'),
			'recursive' => -1
		));

		return $data;
	}

	public function findByComplianceAudit($data = array(), $filter) {
		$this->ComplianceAuditSetting->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceAuditSetting->Behaviors->attach('Search.Searchable');

		$query = $this->ComplianceAuditSetting->getQuery('all', array(
			'conditions' => array(
				'ComplianceAuditSetting.compliance_audit_id' => $data[$filter['name']]
			),
			'fields' => array(
				'ComplianceAuditSetting.id'
			),
			'contain' => array()
		));

		return $query;
	}

	public function findByPackageItemId($data = array(), $filter) {
		$this->ComplianceAuditSetting->CompliancePackageItem->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceAuditSetting->CompliancePackageItem->Behaviors->attach('Search.Searchable');

		$subQuery = $this->ComplianceAuditSetting->CompliancePackageItem->getQuery('all', array(
			'conditions' => array(
				'CompliancePackageItem.item_id' => $data[$filter['name']]
			),
			'fields' => array(
				'CompliancePackageItem.id'
			),
			'recursive' => -1
		));

		$this->ComplianceAuditSetting->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceAuditSetting->Behaviors->attach('Search.Searchable');

		$query = $this->ComplianceAuditSetting->getQuery('all', array(
			'conditions' => array(
				'ComplianceAuditSetting.compliance_package_item_id IN(' . $subQuery . ')'
			),
			'fields' => array(
				'ComplianceAuditSetting.id'
			)
		));

		return $query;
	}

	public function dataToList($data) {
		$list = array();
		foreach ($data as $data) {
			$list[$data['id']] = $data['name'];
		}
		return $list;
	}
}
