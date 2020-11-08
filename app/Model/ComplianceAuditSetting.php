<?php
App::uses('AppModel', 'Model');
App::uses('BulkAction', 'BulkActions.Model');
App::uses('InheritanceInterface', 'Model/Interface');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');

class ComplianceAuditSetting extends AppModel implements InheritanceInterface
{
	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];
	
	public $mapping = array(
		'logRecords' => true,
		'workflow' => false,
		'indexController' => 'complianceAuditSettings',
		/*'indexController' => array(
			'basic' => 'complianceAudits',
			'advanced' => 'complianceAudits',
			'action' => 'analyze',
			'params' => array('compliance_audit_id')
		)*/
	);

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'AuditLog.Auditable',
		'Utils.SoftDelete',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict'
		),
		'Visualisation.Visualisation',
		'CustomRoles.CustomRoles' => [
			'roles' => ['Auditee']
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
	);

	public $validate = array(
		'status' => array(
			'notEmpty' => array(
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'You must choose a status.'
			),
			'allowedStatus' => array(
				'rule' => array('inList', array('1', '2', '3')),
				'message' => 'Status must be selected from the available list of options.'
			)
		),
		'compliance_audit_id' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Compliance audit must be selected.'
		),
		'compliance_package_item_id' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Compliance package item must be selected.'
		)
	);

	public $belongsTo = array(
		'ComplianceAudit',
		'CompliancePackageItem',
		'ComplianceAuditFeedbackProfile'
	);

	public $hasMany = array(
		'ComplianceAuditSettingNotification',
		'ComplianceAuditAuditeeFeedback',
	);

	public $hasAndBelongsToMany = array(
		'Auditee' => array(
			'className' => 'User',
			'with' => 'ComplianceAuditSettingsAuditee',
			'joinTable' => 'compliance_audit_settings_auditees',
			'associationForeignKey' => 'auditee_id',
			'foreignKey' => 'compliance_audit_setting_id'
		),
		// 'ComplianceAuditFeedback' => array(
		// 	'className' => 'ComplianceAuditFeedback',
		// 	'with' => 'ComplianceAuditAuditeeFeedback',
		// 	'joinTable' => 'compliance_audit_auditee_feedbacks',
		// 	'associationForeignKey' => 'compliance_audit_feedback_id',
		// 	'foreignKey' => 'compliance_audit_setting_id'
		// )
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Audit Settings');
        $this->_group = parent::SECTION_GROUP_COMPLIANCE_MGT;

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			)
		);

		$this->fieldData = array(
			'status' => array(
				'label' => __('Status'),
				'options' => array($this, 'getSettingStatuses'),
				'editable' => true
			),
			'Auditee' => array(
				'label' => __('Auditee'),
				'editable' => true
			),
			'compliance_audit_feedback_profile_id' => array(
				'label' => __('Feedback Profile'),
				'editable' => true,
				'empty' => __('None')
			),
			'compliance_package_item_id' => array(
				'label' => __('Complinace Package Item')
			),
			'compliance_audit_id' => array(
				'label' => __('Complinace Audit')
			)
		);

		$this->advancedFilter = array(
			__('General') => array(
				'compliance_audit_id' => array(
					'type' => 'multiple_select',
					'name' => __('Complinace Audit'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'ComplianceAuditSetting.compliance_audit_id',
						'field' => 'ComplianceAuditSetting.id',
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
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'ThirdParty.id',
						'field' => 'ComplianceAuditSetting.compliance_audit_id',
						'path' => [
							'ComplianceAudit' => [
								'findField' => 'ComplianceAudit.third_party_id',
								'field' => 'CompliancePackageItem.id',
							],
						],
						'comparisonTypes' => [
                            AbstractQuery::COMPARISON_IN,
                            AbstractQuery::COMPARISON_NOT_IN,
                            AbstractQuery::COMPARISON_IS_NULL,
                        ]
					),
					'data' => array(
						'method' => 'getThirdParties',
					),
					'field' => 'ComplianceAudit.ComplianceAudit.ThirdParty.name',
					'containable' => array(
						'ComplianceAudit' => array(
							'fields' => array('id', 'third_party_id'),
							'ThirdParty' => array(
								'fields' => array('id', 'name'),
							)
						)
					),
				),
				'auditor' => array(
					'type' => 'multiple_select',
					'name' => __('Auditor'),
					'show_default' => false,
					'field' => 'ComplianceAudit.Auditor.full_name',
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'ComplianceAudit.auditor_id',
						'field' => 'ComplianceAuditSetting.compliance_audit_id',
					),
					'field' => 'ComplianceAudit.auditor_id',
					'data' => array(
						'method' => 'getUsers',
						'result_key' => true,
					),
				),
				'third_party_contact' => array(
					'type' => 'multiple_select',
					'name' => __('Third Party Contact'),
					'show_default' => false,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'ComplianceAudit.third_party_contact_id',
						'field' => 'ComplianceAuditSetting.compliance_audit_id',
					),
					'field' => 'ComplianceAudit.third_party_contact_id',
					'data' => array(
						'method' => 'getUsers',
						'result_key' => true,
					),
				),


			),

			__('Audit') => array(
				'id' => array(
					'type' => 'text',
					'name' => __('ID'),
					'filter' => false
				),
				'compliance_package_item_item_id' => array(
					'type' => 'text',
					'name' => __('Item ID'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'CompliancePackageItem.item_id',
						'field' => 'ComplianceAuditSetting.compliance_package_item_id',
					),
					'field' => 'CompliancePackageItem.item_id',
					'containable' => array('CompliancePackageItem')
				),
				'compliance_package_item_name' => array(
					'type' => 'text',
					'name' => __('Item Name'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'CompliancePackageItem.name',
						'field' => 'ComplianceAuditSetting.compliance_package_item_id',
					),
					'field' => 'CompliancePackageItem.name',
					'containable' => array('CompliancePackageItem')
				),
				'finding' => array(
					'type' => 'text',
					'name' => __('Findings'),
					'show_default' => true,
					'filter' => false,
					'field' => 'all',
					'outputFilter' => array('ComplianceAuditSettings', 'outputFindingsLink')
				),
				'compliance_package_item_description' => array(
					'type' => 'text',
					'name' => __('Compliance Requirement Description'),
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'CompliancePackageItem.description',
						'field' => 'ComplianceAuditSetting.compliance_package_item_id',
					),
					'field' => 'CompliancePackageItem.description',
					'containable' => array('CompliancePackageItem')
				),
				'compliance_audit_feedback_profile_id' => array(
					'type' => 'multiple_select',
					'name' => __('Feedback Profile'),
					'show_default' => false,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'ComplianceAuditSetting.compliance_audit_feedback_profile_id',
						'field' => 'ComplianceAuditSetting.id',
					),
					'data' => array(
						'method' => 'getFeedbackProfiles',
						'empty' => __('All'),
						'result_key' => true,
					),
					'editable' => 'compliance_audit_feedback_profile_id'
				),
				'feedback_answers' => array(
					'type' => 'multiple_select',
					'name' => __('Feedback Answers'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findByFeedbackAnswers',
						'field' => 'ComplianceAuditSetting.id',
					),
					'data' => array(
						'method' => 'getFeedbackAnswers',
					),
					'many' => true,
					'field' => 'ComplianceAuditAuditeeFeedback.{n}.ComplianceAuditFeedback.name',
					'containable' => array(
						'ComplianceAuditAuditeeFeedback' => array(
							'fields' => array('id'),
							'ComplianceAuditFeedback' => array(
								'fields' => array('id', 'name')
							)
						)
					),
					// 'field' => 'all',
					// 'contain' => array(
					// 	'compliance_audit_feedback_profile_id'
					// ),
					// 'outputFilter' => array('ComplianceAuditSettings', 'outputAnswers'),
					// 'containable' => array('CompliancePackageItem')
				),
				'status' => array(
					'type' => 'select',
					'name' => __('Status'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'ComplianceAuditSetting.status',
						'field' => 'ComplianceAuditSetting.id',
					),
					'data' => array(
						'method' => 'getSettingStatuses',
						'empty' => __('All'),
						'result_key' => true,
					),
					// 'many' => true,
					'field' => 'ComplianceAuditSetting.status'
				),
				'auditee_id' => array(
					'type' => 'multiple_select',
					'name' => __('Auditee'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'Auditee.id',
						'field' => 'ComplianceAuditSetting.id',
					),
					'data' => array(
						'method' => 'getUsers',
					),
					'many' => true,
					'field' => 'Auditee.{n}.full_name',
					'containable' => array(
						'Auditee' => array(
							'fields' => array('full_name')
						)
					),
					'editable' => 'Auditee'
				),
			)
		);

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Compliance Audit Analysis'),
			'pdf_file_name' => __('compliance_audit_analysis'),
			'csv_file_name' => __('compliance_audit_analysis'),
			// 'actions' => false,
			'reset' => array(
				'controller' => 'complianceAudits',
				'action' => 'index'
			),
			'history' => true,
			'bulk_actions' => array(
				BulkAction::TYPE_EDIT
			),
			'trash' => false,
			'view_item' => false,
			'use_new_filters' => true
		);

		parent::__construct($id, $table, $ds);
	}

	public function beforeFind($query){
		if (isset($query['contain']['ComplianceAudit'])) {
			$query['conditions']['ComplianceAudit.deleted'] = 0;
		}

		return $query;
	}

	public function beforeSave($options = array()){
		// transforms the data array to save the HABTM relation
		// $this->transformDataToHabtm(array('Auditee'));

		return true;
	}

	public function parentModel() {
		return 'ComplianceAudit';
	}

	public function parentNode($type) {
        return $this->visualisationParentNode('compliance_audit_id');
    }

	/**
	 * Customized title for the record.
	 */
	public function getRecordTitle($id) {
		$data = $this->find('first', array(
			'conditions' => array(
				'ComplianceAuditSetting.id' => $id
			),
			'fields' => array(
				'CompliancePackageItem.item_id',
				'CompliancePackageItem.name'
			),
			'recursive' => 0
		));

		return sprintf('%s (%s)', $data['CompliancePackageItem']['name'], $data['CompliancePackageItem']['item_id']);
	}

	/**
	 * Find a setting item in database.
	 * @param  int     $auditId                 Audit ID.
	 * @param  int     $compliancePackageItemId Compliance package item ID.
	 * @param  mixed   $settingId               Searches only for this ID when not null.
	 * @param  boolean $extensive               Return more deep information.
	 */
	public function readSettings($auditId, $compliancePackageItemId, $settingId = null, $extensive = false) {
		if (empty($settingId)) {
			$conds = array(
				'ComplianceAuditSetting.compliance_audit_id' => $auditId,
				'ComplianceAuditSetting.compliance_package_item_id' => $compliancePackageItemId
			);
		}
		else {
			$conds = array(
				'ComplianceAuditSetting.id' => $settingId
			);
		}

		$contain = array(
			'Auditee'
		);

		if ($extensive) {
			$contain = am($contain, array(
				'ComplianceAudit' => array(
					'Auditor'
				),
				'CompliancePackageItem'
			));
		}

		$data = $this->find('first', array(
			'conditions' => $conds,
			'contain' => $contain
		));

		if (empty($data)) {
			return array();
		}

		if (!empty($data['Auditee'])) {
			$auditeeIds = array();
			foreach ($data['Auditee'] as $auditee) {
				$auditeeIds[] = $auditee['id'];
			}

			$data['ComplianceAuditSetting']['auditee_id'] = $auditeeIds;
		}

		return $data;
	}


	public function afterSave($created, $options = array()) {
		$this->resaveNotifications($this->id);

		return true;
	}

	/**
	 * Saves auditees to audit settings.
	 */
	private function saveAuditees($list, $id) {
		$ret = true;

		if (!empty($list)) {
			$saveData = array();
			foreach ($list as $auditee_id) {
				$saveData[] = array(
					'compliance_audit_setting_id' => $id,
					'auditee_id' => $auditee_id
				);
			}

			$ret &= $this->ComplianceAuditSettingsAuditee->saveMany($saveData, array('validate' => false));
		}

		return $ret;
	}

	/**
	 * When someone clicks the send notification button, this saves when and where it went.
	 *
	 * @param  array  $settingIds Compliance Audit Setting IDs.
	 */
	public function saveNotification($settingIds = array()) {
		if (empty($settingIds)) {
			return true;
		}

		$saveData = array();
		foreach ($settingIds as $id) {
			$saveData[] = array(
				'compliance_audit_setting_id' => $id
			);
		}

		$ret = $this->ComplianceAuditSettingNotification->saveMany($saveData, array('validate' => false));

		return $ret;
	}

	/**
	 * Create system log for each item when notifications are sent.
	 */
	public function saveRecords($settingIds = array()) {
		if (empty($settingIds)) {
			return true;
		}

		$ret = true;
		foreach ($settingIds as $id) {
			$this->addNoteToLog(__('<b>Audit notifications sent</b>'));
			$ret &= $this->setSystemRecord($id, 2);
		}

		return $ret;
	}

	public function syncSettings($auditId) {
		// data from form submission
		$auditData = $this->ComplianceAudit->data;

		$data = $this->ComplianceAudit->find('first', array(
			'conditions' => array(
				'ComplianceAudit.id' => $auditId
			),
			'fields' => array('id', 'third_party_id'),
			'contain' => array(
				'ThirdParty' => array(
					'fields' => array('id'),
					'CompliancePackage' => array(
						'fields' => array('id'),
						'CompliancePackageItem' => array(
							'fields' => array('id')
						)
					)
				)
			)
		));

		if (empty($data)) {
			return false;
		}

		$exists = $this->find('list', array(
			'conditions' => array(
				'ComplianceAuditSetting.compliance_audit_id' => $auditId
			),
			'fields' => array('ComplianceAuditSetting.compliance_package_item_id', 'ComplianceAuditSetting.compliance_package_item_id'),
			'recursive' => -1
		));

		$saveData = array();
		foreach ($data['ThirdParty']['CompliancePackage'] as $package) {
			foreach ($package['CompliancePackageItem'] as $item) {
				if (!in_array($item['id'], $exists)) {
					$tmpSaveData = array(
						'compliance_audit_id' => $auditId,
						'compliance_package_item_id' => $item['id'],
						'auditee_notifications' => 1,
						'auditee_emails' => 1,
						'auditor_notifications' => 1,
						'auditor_emails' => 1
					);

					if (!empty($auditData['Default_ComplianceAuditSetting'])) {
						$tmpSaveData = am($tmpSaveData, $auditData['Default_ComplianceAuditSetting']);
					}

					$saveData[] = $tmpSaveData;
				}
			}
		}

		if (!empty($saveData)) {
			return $this->saveAll($saveData, array('validate' => false, 'deep' => false));
		}

		return true;
	}

	public function resaveNotifications($id) {
		//resave notifications because of auditees
		$data = $this->find('first', array(
			'conditions' => array(
				'ComplianceAuditSetting.id' => $id
			),
			'contain' => array(
				'ComplianceAudit' => array(
					'fields' => array('id'),
					'ComplianceFinding' => array(
						'fields' => array('id')
					)
				)
			)
		));

		$this->ComplianceAudit->bindNotifications();
		$ret = $this->ComplianceAudit->NotificationObject->NotificationSystem->saveCustomUsersByModel('ComplianceAudit', $data['ComplianceAudit']['id']);

		$findingIds = array();
		if (!empty($data['ComplianceAudit']['ComplianceFinding'])) {
			$findingIds = Hash::extract($data, 'ComplianceAudit.ComplianceFinding.{n}.id');
			$this->ComplianceAudit->ComplianceFinding->bindNotifications();
			$ret &= $this->ComplianceAudit->ComplianceFinding->NotificationObject->NotificationSystem->saveCustomUsersByModel('ComplianceFinding', $findingIds);
		}

		return $ret;
	}

	/**
	 * Reads feedback information for an audit setting.
	 *
	 * @param  array  $setting ComplianceAuditSetting data array.
	 * @return array           Feedback.
	 */
	public function getAuditeeFeedbacks($arr = array()) {
		if (!empty($arr['ComplianceAuditAuditeeFeedback'])) {
			$feedbacks = array();
			if (!empty($arr['ComplianceAuditFeedbackProfile']['ComplianceAuditFeedback'])) {
				foreach ($arr['ComplianceAuditFeedbackProfile']['ComplianceAuditFeedback'] as $feedback) {
					$feedbacks[$feedback['id']] = $feedback['name'];
				}
			}
			
			$auditeeFeedbacks = array();
			foreach ($arr['ComplianceAuditAuditeeFeedback'] as $auditeeFeedback) {
				if ($auditeeFeedback['compliance_audit_feedback_profile_id'] == $arr['compliance_audit_feedback_profile_id']) {
					$auditeeName = trim($auditeeFeedback['User']['full_name']);

					$auditeeFeedbacks[$auditeeName][] = $feedbacks[$auditeeFeedback['compliance_audit_feedback_id']];
				}
			}

			return $auditeeFeedbacks;
		}

		return false;
	}

	public function getFeedbackProfiles()
	{
		$data = $this->ComplianceAuditFeedbackProfile->find('list', array(
			'order' => array('ComplianceAuditFeedbackProfile.name' => 'ASC'),
			'fields' => array('ComplianceAuditFeedbackProfile.id', 'ComplianceAuditFeedbackProfile.name'),
			'recursive' => -1
		));
		return $data;
	}

	public function getFeedbackAnswers() {
		$data = $this->ComplianceAuditFeedbackProfile->ComplianceAuditFeedback->getList();

		return $data;
	}

	public function getSettingStatuses() {
		return getComplianceAuditSettingStatuses(null, null, true);
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

	public function findBySettingAuditee($data) {
		$this->ComplianceAuditSettingsAuditee->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceAuditSettingsAuditee->Behaviors->attach('Search.Searchable');

		$queryChild = $this->ComplianceAuditSettingsAuditee->getQuery('all', array(
			'conditions' => array(
				'ComplianceAuditSettingsAuditee.auditee_id' => $data['auditee_id'],
			),
			'contain' => array(),
			'fields' => array(
				'ComplianceAuditSettingsAuditee.compliance_audit_setting_id',
			),
			'group' => 'ComplianceAuditSettingsAuditee.compliance_audit_setting_id'
		));

		return $queryChild;
	}

	public function findByFeedbackAnswers($data) {
		$this->ComplianceAuditAuditeeFeedback->Behaviors->attach('Containable', array('autoFields' => false));
		$this->ComplianceAuditAuditeeFeedback->Behaviors->attach('Search.Searchable');

		$queryChild = $this->ComplianceAuditAuditeeFeedback->getQuery('all', array(
			'conditions' => array(
				'ComplianceAuditAuditeeFeedback.compliance_audit_feedback_id' => $data['feedback_answers'],
			),
			'contain' => array(),
			'fields' => array(
				'ComplianceAuditAuditeeFeedback.compliance_audit_setting_id',
			),
			'group' => 'ComplianceAuditAuditeeFeedback.compliance_audit_setting_id'
		));

		return $queryChild;
	}
}
