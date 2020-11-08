<?php
App::uses('AppModel', 'Model');

class ComplianceAudit extends AppModel
{
	public $displayField = 'name';

	const FILTER_AUDIT = 1;
	const FILTER_FINDINGS = 2;
	const FILTER_ITEMS = 3;

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $mapping = array(
		'titleColumn' => 'name',
		'logRecords' => true,
		'notificationSystem' => array('index'),
		'workflow' => false
	);

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'AuditLog.Auditable' => array(
			'ignore' => array('compliance_finding_count', 'created', 'modified', 'ComplianceAuditFeedback')
		),
		'Utils.SoftDelete',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'third_party_id', 'auditor_id', 'start_date', 'end_date', 'email_subject', 'email_body', 'auditee_title', 'auditee_instructions'
			)
		),
		'Visualisation.Visualisation',
		'CustomRoles.CustomRoles' => [
			'roles' => ['auditor_id', 'third_party_contact_id']
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
	);

	public $belongsTo = array(
		'ThirdParty',
		'Auditor' => array(
			'className' => 'User',
			'foreignKey' => 'auditor_id'
		),
		'ThirdPartyContact' => array(
			'className' => 'User',
			'foreignKey' => 'third_party_contact_id'
		)
	);

	public $hasMany = array(
		'ComplianceFinding',
		/*'ComplianceFindingFinding' => array(
			'className' => 'ComplianceFinding',
			'conditions' => array(
				'ComplianceFindingFinding.type !=' => COMPLIANCE_FINDING_AUDIT
			)
		),
		'ComplianceFindingAssessed' => array(
			'className' => 'ComplianceFinding',
			'conditions' => array(
				'ComplianceFindingAssessed.type !=' => COMPLIANCE_FINDING_ASSESED
			)
		),*/
		'ComplianceAuditSetting',
		'ComplianceFindingDistinct' => array(
			'className' => 'ComplianceFinding',
			'fields' => array('COUNT(DISTINCT ComplianceFindingDistinct.compliance_package_item_id) as count')
		),
		'ComplianceFindingDistinctAssessed' => array(
			'className' => 'ComplianceFinding',
			'conditions' => array(
				'ComplianceFindingDistinctAssessed.type' => COMPLIANCE_FINDING_ASSESED
			),
			'fields' => array('COUNT(DISTINCT ComplianceFindingDistinctAssessed.compliance_package_item_id) as count')
		),
	);

	public $hasAndBelongsToMany = array(
		'ComplianceAuditFeedback' => array(
			'joinTable' => 'compliance_audit_feedbacks_compliance_audits',
			'with' => 'ComplianceAuditFeedbacksComplianceAudits'
		),
	);

	public $validate = array(
		'name' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'third_party_id' => array(
			'rule' => 'notBlank',
			'required' => true
		),
		'auditor_id' => array(
			'rule' => 'notBlank',
			'allowEmpty' => false,
			'required' => true,
			'message' => 'Auditor must be selected.'
		),
		'start_date' => array(
			'date' => array(
				'rule' => array('date', 'ymd'),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Enter a valid date.'
			),
			// 'future' => array(
			// 	'rule' => array('checkFutureDate'),
			// 	'message' => 'Start date must not be in the past.'
			// )
		),
		'end_date' => array(
			'date' => array(
				'rule' => array('date', 'ymd'),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Enter a valid date.'
			),
			// 'afterStartDate' => array(
			// 	'rule' => array('checkEndDate', 'start_date'),
			// 	'message' => 'End date must happen after the start date.'
			// )
		),
		'show_analyze_title' => array(
	        'analyzePageChecked' => array (
	            'rule' => 'analyzePageChecked',
	            'message' => 'At least one Analyze Page checkbox have to be checked.'
	        ),
        )
	);

	function analyzePageChecked($field) {
        if (empty($this->data[$this->alias]['show_analyze_title'])
        	&& empty($this->data[$this->alias]['show_analyze_description'])
        	&& empty($this->data[$this->alias]['show_analyze_audit_criteria'])
		) {
			return false;
		}
		else {
			return true;
		}
	}

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Third Party Audits');
        $this->_group = parent::SECTION_GROUP_COMPLIANCE_MGT;

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			),
			'notification-settings' => array(
				'label' => __('Notification Settings')
			),
			'portal-settings' => array(
				'label' => __('Portal Settings')
			),
			'audit-settings' => array(
				'label' => __('Audit Settings')
			)
		);

		$this->fieldData = array(
			'id' => array(
				'label' => __('ID'),
				'type' => 'primary'
			),
			'name' => array(
				'label' => __('Name'),
				'editable' => true
			),
			'third_party_id' => array(
				'label' => __('Compliance Package'),
				'editable' => true,
				'quickAdd' => true,
			),
			'auditor_id' => array(
				'label' => __('Auditor'),
				'editable' => true,
				'quickAdd' => true,
			),
			'third_party_contact_id' => array(
				'label' => __('Third Party Contact'),
				'editable' => true,
				'quickAdd' => true,
			),
			'start_date' => array(
				'label' => __('Audit Start Date'),
				'editable' => true
			),
			'end_date' => array(
				'label' => __('Audit End Date'),
				'editable' => true
			),
			'auditee_title' => array(
				'label' => __('Page Title'),
				'group' => 'portal-settings',
				'editable' => true
			),
			'auditee_instructions' => array(
				'label' => __('Auditee Instructions'),
				'group' => 'portal-settings',
				'editable' => true
			),
			'use_default_template' => array(
				'label' => __('Use Default Template'),
				'group' => 'notification-settings',
				'type' => 'toggle',
				'editable' => false
			),
			'email_subject' => array(
				'label' => __('Email Subject'),
				'group' => 'notification-settings',
				'editable' => false
			),
			'email_body' => array(
				'label' => __('Email Body'),
				'group' => 'notification-settings',
				'editable' => false
			),
			'auditee_notifications' => array(
				'label' => __('Auditee Recieves Notifications'),
				'group' => 'portal-settings',
				'type' => 'toggle',
				'editable' => false
			),
			'auditee_emails' => array(
				'label' => __('Auditee Recieves Emails'),
				'group' => 'portal-settings',
				'type' => 'toggle',
				'editable' => false
			),
			'auditor_notifications' => array(
				'label' => __('Auditor Recieves Notifications'),
				'group' => 'portal-settings',
				'type' => 'toggle',
				'editable' => false
			),
			'auditor_emails' => array(
				'label' => __('Auditor Recieves Emails'),
				'group' => 'portal-settings',
				'type' => 'toggle',
				'editable' => false
			),
			'show_analyze_title' => array(
				'label' => __('Show Title Field'),
				'group' => 'portal-settings',
				'type' => 'toggle',
				'editable' => false
			),
			'show_analyze_description' => array(
				'label' => __('Show Description Field'),
				'group' => 'portal-settings',
				'type' => 'toggle',
				'editable' => false
			),
			'show_analyze_audit_criteria' => array(
				'label' => __('Show Audit Criteria Field'),
				'group' => 'portal-settings',
				'type' => 'toggle',
				'editable' => false
			),
			'show_findings' => array(
				'label' => __('Display Findings to Auditee in PDF format'),
				'group' => 'portal-settings',
				'type' => 'toggle',
				'editable' => false
			),
			'status' => array(
				'label' => __('Status'),
				'hidden' => true
			)
		);

		$this->notificationSystem = array(
			'macros' => array(
				'AUDITNAME' => array(
					'field' => 'ComplianceAudit.name',
					'name' => __('Third Party Audit Name')
				),
				'AUDITORNAME' => array(
					'field' => 'Auditor.full_name',
					'name' => __('Third Party Audit Auditor')
				),
				'AUDITSTART' => array(
					'field' => 'ComplianceAudit.start_date',
					'name' => __('Third Party Audit Start')
				),
				'AUDITEND' => array(
					'field' => 'ComplianceAudit.end_date',
					'name' => __('Third Party Audit End')
				),
				/**
				 * reserved for third party audits notifications
				 * 'AUDITCOMPLIANCELIST'
				 *
				 * reserved for third party audits notifications
				 * 'LOGINERAMBAURL'
				 */
			),
			'customEmail' =>  true
		);

		$this->advancedFilter = array(
			__('General') => array(
				'id' => array(
					'type' => 'text',
					'name' => __('ID'),
					'show_default' => true,
					'filter' => false
				),
				'name' => array(
					'type' => 'multiple_select',
					'name' => __('Name'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'ComplianceAudit.id',
						'field' => 'ComplianceAudit.id',
					),
					'data' => array(
						'method' => 'getComplianceAudits'
					),
					'field' => 'ComplianceAudit.name',
					'editable' => 'name'
				),
				'third_party_id' => array(
					'type' => 'multiple_select',
					'name' => __('Compliance Package'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'ComplianceAudit.third_party_id',
						'field' => 'ComplianceAudit.id',
					),
					'data' => array(
						'method' => 'getThirdParties',
					),
					'contain' => array(
						'ThirdParty' => array(
							'name'
						)
					),
					'editable' => 'third_party_id'
				),
				'auditor_id' => array(
					'type' => 'multiple_select',
					'name' => __('Auditor'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'ComplianceAudit.auditor_id',
						'field' => 'ComplianceAudit.id',
					),
					'data' => array(
						'method' => 'getUsers',
						'result_key' => true
					),
					'editable' => 'auditor_id'
				),
				'third_party_contact_id' => array(
					'type' => 'multiple_select',
					'name' => __('Third Party Contact'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'ComplianceAudit.third_party_contact_id',
						'field' => 'ComplianceAudit.id',
					),
					'data' => array(
						'method' => 'getUsers',
						'result_key' => true
					),
					'editable' => 'third_party_contact_id'
				),
			),
			__('Findings') => array(
				'finding' => array(
					'type' => 'text',
					'name' => __('Findings'),
					'filter' => false,
					'field' => 'ComplianceAudit.id',
					'outputFilter' => array('ComplianceAudits', 'outputFindingsLink')
				),
			),
			__('Audit Items') => array(
				'item_id' => array(
					'type' => 'text',
					'name' => __('Item ID'),
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findByPackageItemId',
						'field' => 'ComplianceAudit.id',
					),
					// 'many' => true,
					'field' => 'all',
					'containable' => array(
						'ComplianceAuditSetting' => array(
							'fields' => array('id'),
							'CompliancePackageItem' => array(
								'fields' => array('id')
							)
						)
					),
					'outputFilter' => array('ComplianceAudits', 'outputItemIds')
				),
			),
		);

		$aditionalFilters = array(
			self::FILTER_AUDIT => array(
				__('General') => array(
					'start_date' => array(
						'type' => 'date',
						'comparison' => true,
						'name' => __('Start Date'),
						'show_default' => true,
						'filter' => array(
							'type' => 'subquery',
							'method' => 'findComplexType',
							'findField' => 'ComplianceAudit.start_date',
							'field' => 'ComplianceAudit.id',
						),
						'editable' => 'start_date'
					),
					'end_date' => array(
						'type' => 'date',
						'comparison' => true,
						'name' => __('End Date'),
						'show_default' => true,
						'filter' => array(
							'type' => 'subquery',
							'method' => 'findComplexType',
							'findField' => 'ComplianceAudit.end_date',
							'field' => 'ComplianceAudit.id',
						),
						'editable' => 'end_date'
					),
					'status' => array(
						'type' => 'select',
						'name' => __('Status'),
						'show_default' => true,
						'filter' => array(
							'type' => 'subquery',
							'method' => 'findComplexType',
							'findField' => 'ComplianceAudit.status',
							'field' => 'ComplianceAudit.id',
						),
						'data' => array(
							'method' => 'getStatuses',
							'empty' => __('All'),
							'result_key' => true,
						),
					),
				),

			),
			self::FILTER_FINDINGS => array(
				__('Findings') => array(
					'finding' => array(
						'type' => 'text',
						'name' => __('Findings'),
						'show_default' => true,
						'filter' => false,
						'field' => 'ComplianceAudit.id',
						'outputFilter' => array('ComplianceAudits', 'outputFindingsLink')
					),
					'finding_type' => array(
						'type' => 'select',
						'name' => __('Type'),
						'show_default' => false,
						'filter' => array(
							'type' => 'subquery',
							'method' => 'findByFindingType',
							'field' => 'ComplianceAudit.id',
						),
						'data' => array(
							'method' => 'getFindingTypes',
							'empty' => __('All'),
							'result_key' => true
						),
						'many' => true,
						'field' => 'ComplianceFinding.{n}.type',
						'containable' => array(
							'ComplianceFinding' => array(
								'fields' => array('type')
							)
						),
					),
					'finding_title' => array(
						'type' => 'text',
						'name' => __('Title'),
						'show_default' => false,
						'filter' => array(
							'type' => 'subquery',
							'method' => 'findByFindingTitle',
							'field' => 'ComplianceAudit.id',
						),
						'many' => true,
						'field' => 'ComplianceFinding.{n}.title',
						'containable' => array(
							'ComplianceFinding' => array(
								'fields' => array('title')
							)
						),
					),
					'finding_classification_id' => array(
						'type' => 'multiple_select',
						'name' => __('Classification'),
						'show_default' => false,
						'filter' => array(
							'type' => 'subquery',
							'method' => 'findByFindingClassification',
							'field' => 'ComplianceAudit.id',
						),
						'data' => array(
							'method' => 'getFindingClassification',
						),
						'many' => true,
						'field' => 'ComplianceFinding.{n}.Classification.{n}.name',
						'containable' => array(
							'ComplianceFinding' => array(
								'fields' => array('id'),
								'Classification' => array(
									'fields' => array('name')
								)
							)
						),
					),
					'finding_deadline' => array(
						'type' => 'date',
						'comparison' => false,
						'name' => __('Deadline'),
						'filter' => array(
							'type' => 'subquery',
							'method' => 'findByFindingDeadline',
							'field' => 'ComplianceAudit.id',
						),
						'field' => 'ComplianceFinding.{n}.deadline',
						'containable' => array(
							'ComplianceFinding' => array(
								'fields' => array('deadline')
							)
						),
					),
					'finding_description' => array(
						'type' => 'text',
						'name' => __('Description'),
						'filter' => array(
							'type' => 'subquery',
							'method' => 'findByFindingDesc',
							'field' => 'ComplianceAudit.id',
						),
						'field' => 'ComplianceFinding.{n}.description',
						'containable' => array(
							'ComplianceFinding' => array(
								'fields' => array('description')
							)
						),
					),
					'finding_status' => array(
						'type' => 'select',
						'name' => __('Status'),
						'show_default' => false,
						'filter' => array(
							'type' => 'subquery',
							'method' => 'findByFindingStatus',
							'field' => 'ComplianceAudit.id',
						),
						'data' => array(
							'method' => 'getFindingStatuses',
							'empty' => __('All'),
						),
						'field' => 'ComplianceFinding.{n}.ComplianceFindingStatus.name',
						'containable' => array(
							'ComplianceFinding' => array(
								'ComplianceFindingStatus' => array(
									'fields' => array('name')
								)
							)
						),
					),
				),
				__('Audit') => array(
					'item_name' => array(
						'type' => 'text',
						'name' => __('Item Name'),
						'show_default' => false,
						'filter' => array(
							'type' => 'subquery',
							'method' => 'findByPackageItemName',
							'field' => 'ComplianceAudit.id',
						),
						'field' => 'all',
						'containable' => array(
							'ComplianceAuditSetting' => array(
								'fields' => array('id'),
								'CompliancePackageItem' => array(
									'fields' => array('name')
								)
							)
						),
						'outputFilter' => array('ComplianceAudits', 'outputItemNames')
					),
					'description' => array(
						'type' => 'text',
						'name' => __('Compliance Requirement Description'),
						'show_default' => false,
						'filter' => array(
							'type' => 'subquery',
							'method' => 'findByPackageItemDesc',
							'field' => 'ComplianceAudit.id',
						),
						'many' => true,
						'field' => 'ComplianceAuditSetting.{n}.CompliancePackageItem.description',
						'containable' => array(
							'ComplianceAuditSetting' => array(
								'fields' => array('id'),
								'CompliancePackageItem' => array(
									'fields' => array('description')
								)
							)
						),
					)
				),

			),
			self::FILTER_ITEMS => array(
				__('Audit') => array(
					'item_name' => array(
						'type' => 'text',
						'name' => __('Item Name'),
						'show_default' => false,
						'filter' => array(
							'type' => 'subquery',
							'method' => 'findByPackageItemName',
							'field' => 'ComplianceAudit.id',
						),
						'field' => 'all',
						'containable' => array(
							'ComplianceAuditSetting' => array(
								'fields' => array('id'),
								'CompliancePackageItem' => array(
									'fields' => array('name')
								)
							)
						),
						'outputFilter' => array('ComplianceAudits', 'outputItemNames')
					),
					'description' => array(
						'type' => 'text',
						'name' => __('Compliance Requirement Description'),
						'show_default' => false,
						'filter' => array(
							'type' => 'subquery',
							'method' => 'findByPackageItemDesc',
							'field' => 'ComplianceAudit.id',
						),
						'many' => true,
						'field' => 'ComplianceAuditSetting.{n}.CompliancePackageItem.description',
						'containable' => array(
							'ComplianceAuditSetting' => array(
								'fields' => array('id'),
								'CompliancePackageItem' => array(
									'fields' => array('description')
								)
							)
						),
					),
					'feedback_profile_id' => array(
						'type' => 'multiple_select',
						'name' => __('Feedback Profile'),
						'show_default' => true,
						'filter' => array(
							'type' => 'subquery',
							'method' => 'findByFeedbackProfile',
							'field' => 'ComplianceAudit.id',
						),
						'data' => array(
							'method' => 'getFeedbackProfiles',
							'empty' => __('All'),
							'result_key' => true,
						),
						'many' => true,
						'field' => 'ComplianceAuditFeedback.{n}.ComplianceAuditFeedbackProfile.name',
						'containable' => array(
							'ComplianceAuditFeedback' => array(
								'fields' => array('id'),
								'ComplianceAuditFeedbackProfile' => array(
									'fields' => array('name')
								)
							)
						),
					),
					'setting_status' => array(
						'type' => 'select',
						'name' => __('Status'),
						'show_default' => false,
						'filter' => array(
							'type' => 'subquery',
							'method' => 'findBySettingStatus',
							'field' => 'ComplianceAudit.id',
						),
						'data' => array(
							'method' => 'getSettingStatuses',
							'empty' => __('All'),
							'result_key' => true,
						),
						// 'many' => true,
						'field' => 'all',
						'contain' => array(
							'ComplianceAuditSetting' => array(
								'status'
							)
						),
						'outputFilter' => array('ComplianceAudits', 'outputStatuses')
					),
					'auditee_id' => array(
						'type' => 'multiple_select',
						'name' => __('Auditee'),
						'show_default' => true,
						'filter' => array(
							'type' => 'subquery',
							'method' => 'findBySettingAuditee',
							'field' => 'ComplianceAudit.id',
						),
						'data' => array(
							'method' => 'getUsers',
						),
						'many' => true,
						'field' => 'ComplianceAuditSetting.{n}.Auditee.{n}.full_name',
						'containable' => array(
							'ComplianceAuditSetting' => array(
								'fields' => array('id'),
								'Auditee' => array(
									'fields' => array('full_name')
								)
							)
						),
					),
				),
			),
		);

		$activeFilter = self::FILTER_AUDIT;
		$request = Router::getRequest();
		if (!empty($request->query[ADVANCED_FILTER_PARAM]) && in_array($request->query[ADVANCED_FILTER_PARAM], self::getFilters())) {
			$activeFilter = $request->query[ADVANCED_FILTER_PARAM];
		}

		$this->mergeAdvancedFilterFields($aditionalFilters[$activeFilter]);

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Compliance Audit'),
			'pdf_file_name' => __('compliance_audit'),
			'csv_file_name' => __('compliance_audit'),
			'active_filter' => $activeFilter,
			'bulk_actions' => true,
			'history' => true,
            'trash' => true,
			'additional_actions' => array(
				'ComplianceAuditSetting' => __('Audit Items'),
                'ComplianceFinding' => __('Findings'),
			),
			'use_new_filters' => true,
			'add' => true,
		);

		parent::__construct($id, $table, $ds);
	}

	public static function getFilters() {
		return array(
			self::FILTER_AUDIT, self::FILTER_FINDINGS, self::FILTER_ITEMS
		);
	}

	/**
	 * Check if and Audit has any Auditees associated or not.
	 */
	public function hasAuditees($id) {
		$count = $this->ComplianceAuditSetting->ComplianceAuditSettingsAuditee->find('count', array(
			'conditions' => array(
				'ComplianceAuditSetting.compliance_audit_id' => $id
			)
		));

		return (bool) $count;
	}

	public function getFindingStatuses() {
		$data = $this->ComplianceFinding->ComplianceFindingStatus->find('list', array(
			'fields' => array('ComplianceFindingStatus.id', 'ComplianceFindingStatus.name'),
			'recursive' => 0
		));
		return $data;
	}

	public function getFindingTypes() {
		return getFindingTypes();
	}

	public function getComplianceAudits() {
		$data = $this->find('list', array(
			'fields' => array('ComplianceAudit.id', 'ComplianceAudit.name'),
			'recursive' => 0
		));
		return $data;
	}

	public function getThirdParties() {
		$data = $this->ComplianceAuditSetting->CompliancePackageItem->CompliancePackage->ThirdParty->find('all', array(
			'conditions' => array(
			),
			'fields' => array(
				'ThirdParty.id',
				'ThirdParty.name',
				'ThirdParty.description'
			),
			'contain' => array(
				'CompliancePackage' => array(
					'CompliancePackageItem'
				)
			),
			'order' => array( 'ThirdParty.id' => 'ASC' ),

		));
		$data = $this->filterComplianceData($data);

		$list = array();
		foreach ($data as $item) {
			$list[$item['ThirdParty']['id']] = $item['ThirdParty']['name'];
		}

		return $list;
	}

	public function filterComplianceData($data) {
		foreach ($data as $key => $entry) {
			$hasItems = false;
			foreach ( $entry['CompliancePackage'] as $compliance_package ) {
				if ( ! $hasItems && ! empty( $compliance_package['CompliancePackageItem'] ) ) {
					$hasItems = true;
				}
			}

			if ( ! $hasItems ) {
				unset($data[$key]);
			}
		}

		return $data;
	}

	public function getUsers() {
		$this->Auditor->virtualFields['full_name'] = 'CONCAT(Auditor.name, " ", Auditor.surname)';
		$data = $this->Auditor->find('list', array(
			'fields' => array('Auditor.id', 'Auditor.full_name'),
		));
		return $data;
	}

	public function getAuditeeIds($id) {
		$data = $this->ComplianceAuditSetting->ComplianceAuditSettingsAuditee->find('list', array(
			'conditions' => array(
				'ComplianceAuditSetting.compliance_audit_id' => $id
			),
			'fields' => array('ComplianceAuditSettingsAuditee.id', 'ComplianceAuditSettingsAuditee.auditee_id'),
			'group' => 'ComplianceAuditSettingsAuditee.auditee_id',
			'recursive' => 0
		));

		return $data;
	}

	public function getStatuses() {
		return static::statuses();
	}

	public static function statuses($status = null) {
		$statuses = array(
			COMPLIANCE_AUDIT_STARTED => __('Accepting Answers'),
			COMPLIANCE_AUDIT_STOPPED => __('Finished')
		);

		return ($status !== null) ? $statuses[$status] : $statuses;
	}

	public function getSettingStatuses() {
		return getComplianceAuditSettingStatuses(null, null, true);
	}

	public function getFeedbackProfiles()
	{
		$data = $this->ComplianceAuditFeedback->ComplianceAuditFeedbackProfile->find('list', array(
			'order' => array('ComplianceAuditFeedbackProfile.name' => 'ASC'),
			'fields' => array('ComplianceAuditFeedbackProfile.id', 'ComplianceAuditFeedbackProfile.name'),
			'recursive' => -1
		));
		return $data;
	}

	public function getFindingClassification() {
		$data = $this->ComplianceFinding->Classification->find('list', array(
			'order' => array('Classification.name' => 'ASC'),
			'fields' => array('Classification.id', 'Classification.name'),
			'recursive' => -1
		));
		return $data;
	}

	public function findByFindingDeadline($data = array(), $filter) {
		$this->ComplianceFinding->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceFinding->Behaviors->attach('Search.Searchable');

		$query = $this->ComplianceFinding->getQuery('all', array(
			'conditions' => array(
				'ComplianceFinding.deadline ' . getComparisonTypes()[$filter['comp_type']] => $data['finding_deadline']
			),
			'contain' => array(),
			'fields' => array(
				'ComplianceFinding.compliance_audit_id'
			),
		));

		return $query;
	}

	public function findByFindingStatus($data) {
		$this->ComplianceFinding->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceFinding->Behaviors->attach('Search.Searchable');

		$query = $this->ComplianceFinding->getQuery('all', array(
			'conditions' => array(
				'ComplianceFinding.compliance_finding_status_id' => $data['finding_status']
			),
			'contain' => array(),
			'fields' => array(
				'ComplianceFinding.compliance_audit_id'
			),
		));

		return $query;
	}

	public function findByFindingDesc($data = array(), $filter) {
		$this->ComplianceFinding->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceFinding->Behaviors->attach('Search.Searchable');

		$query = $this->ComplianceFinding->getQuery('all', array(
			'conditions' => array(
				'ComplianceFinding.description LIKE' => '%' . $data[$filter['name']] . '%'
			),
			'contain' => array(),
			'fields' => array(
				'ComplianceFinding.compliance_audit_id'
			),
		));

		return $query;
	}

	public function findByFindingTitle($data) {
		$this->ComplianceFinding->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceFinding->Behaviors->attach('Search.Searchable');

		$query = $this->ComplianceFinding->getQuery('all', array(
			'conditions' => array(
				'ComplianceFinding.title LIKE' => '%' . $data['finding_title'] . '%'
			),
			'contain' => array(),
			'fields' => array(
				'ComplianceFinding.compliance_audit_id'
			),
		));

		return $query;
	}

	public function findByFindingClassification($data) {
		$this->ComplianceFinding->Classification->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceFinding->Classification->Behaviors->attach('Search.Searchable');

		$queryChild = $this->ComplianceFinding->Classification->getQuery('all', array(
			'conditions' => array(
				'Classification.id' => $data['finding_classification_id'],
			),
			'contain' => array(),
			'fields' => array(
				'Classification.compliance_finding_id',
			)
		));

		$this->ComplianceFinding->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceFinding->Behaviors->attach('Search.Searchable');

		$query = $this->ComplianceFinding->getQuery('all', array(
			'conditions' => array(
				'ComplianceFinding.id IN (' . $queryChild . ')',
			),
			'contain' => array(),
			'fields' => array(
				'ComplianceFinding.compliance_audit_id'
			),
		));

		return $query;
	}

	public function findByFindingType($data) {
		$this->ComplianceFinding->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceFinding->Behaviors->attach('Search.Searchable');

		$query = $this->ComplianceFinding->getQuery('all', array(
			'conditions' => array(
				'ComplianceFinding.type' => $data['finding_type']
			),
			'contain' => array(),
			'fields' => array(
				'ComplianceFinding.compliance_audit_id'
			),
		));

		return $query;
	}

	public function findBySettingStatus($data) {
		$this->ComplianceAuditSetting->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceAuditSetting->Behaviors->attach('Search.Searchable');

		$query = $this->ComplianceAuditSetting->getQuery('all', array(
			'conditions' => array(
				'ComplianceAuditSetting.status' => $data['setting_status']
			),
			'contain' => array(),
			'fields' => array(
				'ComplianceAuditSetting.compliance_audit_id'
			),
		));

		return $query;
	}

	public function findBySettingAuditee($data) {
		$this->ComplianceAuditSetting->ComplianceAuditSettingsAuditee->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceAuditSetting->ComplianceAuditSettingsAuditee->Behaviors->attach('Search.Searchable');

		$queryChild = $this->ComplianceAuditSetting->ComplianceAuditSettingsAuditee->getQuery('all', array(
			'conditions' => array(
				'ComplianceAuditSettingsAuditee.auditee_id' => $data['auditee_id'],
			),
			'contain' => array(),
			'fields' => array(
				'ComplianceAuditSettingsAuditee.compliance_audit_setting_id',
			)
		));

		$this->ComplianceAuditSetting->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceAuditSetting->Behaviors->attach('Search.Searchable');

		$query = $this->ComplianceAuditSetting->getQuery('all', array(
			'conditions' => array(
				'ComplianceAuditSetting.id IN (' . $queryChild . ')',
			),
			'contain' => array(),
			'fields' => array(
				'ComplianceAuditSetting.compliance_audit_id'
			),
		));

		return $query;
	}

	public function findByPackageItemDesc($data) {
		$this->ComplianceAuditSetting->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceAuditSetting->Behaviors->attach('Search.Searchable');

		$query = $this->ComplianceAuditSetting->getQuery('all', array(
			'conditions' => array(
				'CompliancePackageItem.description LIKE' => '%' . $data['description'] . '%',
			),
			'contain' => array(
				'CompliancePackageItem'
			),
			'fields' => array(
				'ComplianceAuditSetting.compliance_audit_id',
			)
		));

		return $query;
	}

	public function findByFeedbackProfile($data) {
		$this->ComplianceAuditFeedback->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceAuditFeedback->Behaviors->attach('Search.Searchable');

		$queryChild = $this->ComplianceAuditFeedback->getQuery('all', array(
			'conditions' => array(
				'ComplianceAuditFeedback.compliance_audit_feedback_profile_id' => $data['feedback_profile_id'],
			),
			'fields' => array(
				'ComplianceAuditFeedback.id',
			)
		));

		$this->ComplianceAuditFeedbacksComplianceAudits->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceAuditFeedbacksComplianceAudits->Behaviors->attach('Search.Searchable');

		$query = $this->ComplianceAuditFeedbacksComplianceAudits->getQuery('all', array(
			'conditions' => array(
				'ComplianceAuditFeedbacksComplianceAudits.compliance_audit_feedback_id IN (' . $queryChild . ')'
			),
			'fields' => array(
				'ComplianceAuditFeedbacksComplianceAudits.compliance_audit_id'
			),
		));

		return $query;
	}

	public function findByPackageItemId($data) {
		$this->ComplianceAuditSetting->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceAuditSetting->Behaviors->attach('Search.Searchable');

		$query = $this->ComplianceAuditSetting->getQuery('all', array(
			'conditions' => array(
				'CompliancePackageItem.item_id LIKE' => '%' . $data['item_id'] . '%',
			),
			'contain' => array(
				'CompliancePackageItem'
			),
			'fields' => array(
				'ComplianceAuditSetting.compliance_audit_id',
			)
		));

		return $query;
	}

	public function findByPackageItemName($data = array(), $filter) {
		$this->ComplianceAuditSetting->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceAuditSetting->Behaviors->attach('Search.Searchable');

		$query = $this->ComplianceAuditSetting->getQuery('all', array(
			'conditions' => array(
				'CompliancePackageItem.name LIKE' => '%' . $data[$filter['name']] . '%',
			),
			'contain' => array(
				'CompliancePackageItem'
			),
			'fields' => array(
				'ComplianceAuditSetting.compliance_audit_id',
			)
		));

		return $query;
	}

	/**
	 * checkFutureDate
	 * Custom Validation Rule: Ensures a selected date is either the
	 * present day or in the future.
	 *
	 * @param array $check Contains the value passed from the view to be validated
	 * @return bool False if in the past, True otherwise
	 */
	public function checkFutureDate($check) {
		$value = array_values($check);
		return CakeTime::fromString($value['0']) >= CakeTime::fromString(date('Y-m-d'));
	}

	public function checkEndDate($endDate, $startDate) {
		if (!isset($this->data[$this->name][$startDate])) {
			return true;
		}

		return $this->data[$this->name][$startDate] <= $endDate['end_date'];
	}

	public function afterSave($created, $options = array()) {
		$ret = $this->ComplianceAuditSetting->syncSettings($this->id);
		$ret &= $this->resaveNotifications($this->id);

		return $ret;
	}

	public function resaveNotifications($id) {
		$ret = true;

		$this->bindNotifications();
		$ret &= $this->NotificationObject->NotificationSystem->saveCustomUsersByModel($this->alias, $id);

		$findingIds = $this->ComplianceFinding->find('list', array(
			'conditions' => array(
				'ComplianceFinding.compliance_audit_id' => $id
			),
			'fields' => array('id', 'id'),
			'recursive' => -1
		));

		$this->ComplianceFinding->bindNotifications();
		$ret &= $this->ComplianceFinding->NotificationObject->NotificationSystem->saveCustomUsersByModel('ComplianceFinding', $findingIds);

		return $ret;
	}

	public function startAudit($id) {
		$this->id = $id;
		$this->set(array('status' => COMPLIANCE_AUDIT_STARTED));
		$ret = $this->save(null, array('callbacks' => false, 'validate' => false));

		if ($ret) {
			$ret &= $this->quickLogSave($id, 2, __('This audit has been started'));
		}

		return $ret;
	}

	public function stopAudit($id) {
		$this->id = $id;
		$this->set(array('status' => COMPLIANCE_AUDIT_STOPPED));
		$ret = $this->save(null, array('callbacks' => false, 'validate' => false));

		if ($ret) {
			$ret &= $this->quickLogSave($id, 2, __('This audit has been finished'));
		}

		return $ret;
	}
}
