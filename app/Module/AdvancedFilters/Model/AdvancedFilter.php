<?php
App::uses('AdvancedFiltersAppModel', 'AdvancedFilters.Model');
App::uses('AdvancedFilterValue', 'AdvancedFilters.Model');
App::uses('AdvancedFiltersModule', 'AdvancedFilters.Lib');
App::uses('AdvancedFiltersObject', 'AdvancedFilters.Lib');
App::uses('ClassRegistry', 'Utility');
App::uses('FilterAdapter', 'AdvancedFilters.Lib/QueryAdapter');

class AdvancedFilter extends AdvancedFiltersAppModel
{
	const MAX_SELECTION_SIZE = 100;
	const NOT_DELETED = 0;
	const DELETED = 1;

	public $recursive = 0;

	public $actsAs = array(
		'FieldData.FieldData',
		'Containable'
	);

	public $hasOne = [
		'AdvancedFilterUserSetting' => array(
			'className' => 'AdvancedFilters.AdvancedFilterUserSetting',
			'foreignKey' => 'advanced_filter_id'
		)
	];

	public $hasMany = [
		'AdvancedFilterValue' => [
			'className' => 'AdvancedFilters.AdvancedFilterValue'
		],
		'AdvancedFilterCron',
		'AdvancedFilterUserParam' => [
			'className' => 'AdvancedFilters.AdvancedFilterUserParam'
		],
	];

	public $validate = array(
		'name' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'model' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		)
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Advanced Filters');

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			]
		];

		$this->fieldData = [
			'name' => [
				'label' => __('Name'),
				'editable' => true,
				// 'description' => __('tbd'),
				'renderHelper' => ['AdvancedFilters', 'nameField']
			],
			'description' => [
				'label' => __('Description'),
				'editable' => true,
				// 'description' => __('tbd'),
				'renderHelper' => ['AdvancedFilters', 'descriptionField']
			],
			'private' => [
				'label' => __('Private'),
				'editable' => true,
				'type' => 'toggle',
				'description' => __('If enabled this filter will be shown only to your user, by default filters are shown to all users on the system'),
				'renderHelper' => ['AdvancedFilters', 'privateField']
			],
			'log_result_count' => [
				'label' => __('Store the number of results in a daily log'),
				'editable' => true,
				'type' => 'toggle',
				'description' => __('If enabled a CSV file with two columns will be created and updated every night, the first column will have a date and the second column will include the number of results this filter produced')
			],
			'log_result_data' => [
				'label' => __('Store full filter results in a daily log'),
				'editable' => true,
				'type' => 'toggle',
				'description' => __('If enabled a CSV file with two columns will be created and updated every night, the first column will have a date and the second the full output of the filter')
			],
			'model' => [
				'editable' => false,
				'hidden' => true
			]
		];

		parent::__construct($id, $table, $ds);
	}

	public function afterSave($created, $options = array())
	{
		parent::afterSave($created, $options);

		Cache::clear(false, 'advanced_filters_settings');
		Cache::clear(false, 'layout_toolbar');
	}

	public function afterDelete()
	{
		parent::afterDelete();

		Cache::clear(false, 'advanced_filters_settings');
		Cache::clear(false, 'layout_toolbar');
	}

	public function getUsersToSync()
	{
		$User = ClassRegistry::init('User');
		$listUsers = $User->find('list', [
			'fields' => ['id'],
			'recursive' => -1
		]);

		return $listUsers;
	}

	public function syncDefaultIndex($specificUserId = null, $specificModels = null, $updatedCreated = null)
	{
		if ($specificModels === null) {
			$syncModels = [
				'Legal',
				'ThirdParty',
				'SecurityService',
				'SecurityServiceAudit',
				'SecurityServiceMaintenance',
				'SecurityPolicy',
				'SecurityPolicyReview',
				'Risk',
				'Asset',
				'AssetReview',
				'RiskReview',
				'ThirdPartyRiskReview',
				'BusinessContinuityReview',
				'ThirdPartyRisk',
				'BusinessContinuity',
				'BusinessUnit',
				'Process',
				'ServiceContract',
				'RiskException',
				'PolicyException',
				'Project',
				'ProjectAchievement',
				'ProjectExpense',
				'SecurityServiceIssue',
				'ProgramScope',
				'ProgramIssue',
				'TeamRole',
				'ComplianceException',
				'CompliancePackageRegulator',
				'CompliancePackage',
				'ComplianceManagement',
				'ComplianceAnalysisFinding',
				'Goal',
				'GoalAudit',
				'SecurityIncident',
				'SecurityIncidentStagesSecurityIncident',
				'BusinessContinuityPlan',
				'BusinessContinuityPlanAudit',
				'BusinessContinuityTask',
				'User',
				'UserSystemLog',
				'Group',
				'Queue',
				'Cron',
				'OauthConnector',
				'LdapConnector',
				'DataAsset',
				'DataAssetInstance',
				'AwarenessProgram',
				'AwarenessProgramActiveUser',
				'AwarenessProgramIgnoredUser',
				'AwarenessProgramCompliantUser',
				'AwarenessProgramNotCompliantUser',
				'AwarenessReminder',
				'LdapSynchronizationSystemLog',
				'Translation',
				'SamlConnector'
			];

			if (AppModule::loaded('Mapping')) {
				$syncModels = array_merge($syncModels, [
					'ComplianceManagementMappingRelation'
				]);
			}

			if (AppModule::loaded('AccountReviews')) {
				$syncModels = array_merge($syncModels, [
					'AccountReview',
					'AccountReviewFeedback',
					'AccountReviewFinding',
					'AccountReviewPull',
					'AccountReviewPullSystemLog'
				]);
			}

			if (AppModule::loaded('VendorAssessments')) {
				$syncModels = array_merge($syncModels, [
					'VendorAssessment',
					'VendorAssessmentFeedback',
					'VendorAssessmentFinding',
					'VendorAssessmentSystemLog'
				]);
			}
		} else {
			if (!is_array($specificModels)) {
				$specificModels = [$specificModels];
			}

			$syncModels = $specificModels;
		}

		if ($specificUserId === null) {
			$User = ClassRegistry::init('User');
			$listUsers = $this->getUsersToSync();
		} else {
			$listUsers = [$specificUserId];
		}

		$ret = true;
		foreach ($syncModels as $model) {

			foreach ($listUsers as $userId) {
				if (in_array($model, ['Queue']) || ClassRegistry::init($model) instanceof SystemLog) {
					continue;
				}

				if (ClassRegistry::init($model)->Behaviors->enabled('Comments.Comments')) {
					$AdvancedFilterValue = $this->_buildShowFields($model, ['comment_message']);

					$AdvancedFilterValue[] = [
						'field' => 'last_comment',
						'value' => FilterAdapter::MINUS_1_DAYS_VALUE
					];
					$AdvancedFilterValue[] = [
						'field' => 'last_comment__comp_type',
						'value' => FilterAdapter::COMPARISON_ABOVE
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Items with new comments (Since Yesterday)'),
							'slug' => 'new-comment',
							'description' => __('This is the list of items that have received comments since yesterday'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => 0,
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if (ClassRegistry::init($model)->Behaviors->enabled('Attachments.Attachments')) {
					$AdvancedFilterValue = $this->_buildShowFields($model, ['attachment_filename']);

					$AdvancedFilterValue[] = [
						'field' => 'last_attachment',
						'value' => FilterAdapter::MINUS_1_DAYS_VALUE
					];
					$AdvancedFilterValue[] = [
						'field' => 'last_attachment__comp_type',
						'value' => FilterAdapter::COMPARISON_ABOVE
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Items with new attachments (Since Yesterday)'),
							'slug' => 'new-attachment',
							'description' => __('This is the list of items that have received attachments since yesterday'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => 0,
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if (ClassRegistry::init($model)->hasField('modified')) {
					$AdvancedFilterValue = $this->_buildShowFields($model);

					$AdvancedFilterValue[] = [
						'field' => 'modified',
						'value' => FilterAdapter::MINUS_1_DAYS_VALUE
					];
					$AdvancedFilterValue[] = [
						'field' => 'modified__comp_type',
						'value' => FilterAdapter::COMPARISON_ABOVE
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Updated items (Since Yesterday)'),
							'slug' => 'modified-items',
							'description' => __('This is the list of items that received updates yesterday'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => 0,
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if (ClassRegistry::init($model)->hasField('created')) {
					$AdvancedFilterValue = $this->_buildShowFields($model);

					$AdvancedFilterValue[] = [
						'field' => 'created',
						'value' => FilterAdapter::MINUS_1_DAYS_VALUE
					];
					$AdvancedFilterValue[] = [
						'field' => 'created__comp_type',
						'value' => FilterAdapter::COMPARISON_ABOVE
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('New items (Since Yesterday)'),
							'slug' => 'created-items',
							'description' => __('This is the list of new items since yesterday'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => 0,
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}
			}

			if ($updatedCreated === true) {
				continue;
			}

			if ($model == 'ComplianceManagement') {
				$regulators = ClassRegistry::init('CompliancePackageRegulator')->getList();
				
				foreach ($regulators as $cprId => $name) {
					$ret &= ClassRegistry::init('CompliancePackageRegulator')->syncComplianceManagementIndex($cprId, $listUsers);
				}

				continue;
			}

			if ($model == 'CompliancePackage') {
				$regulators = ClassRegistry::init('CompliancePackageRegulator')->getList();
				
				foreach ($regulators as $cprId => $name) {
					$ret &= ClassRegistry::init('CompliancePackageRegulator')->syncCompliancePackagesIndex($cprId, $listUsers);
				}

				continue;
			}

			if ($model == 'Queue') {
				$ret &= ClassRegistry::init('Queue')->syncIndex($listUsers);

				continue;
			}
			
			foreach ($listUsers as $userId) {
				$orderCol = null;

				if (ClassRegistry::init($model)->hasField('modified')) {
					$orderCol = 'modified';
				}

				if (ClassRegistry::init($model)->hasField('created')) {
					$orderCol = 'created';
				}

				if ($model == 'LdapSynchronizationSystemLog') {
					$orderCol = 'id';
				}

				$orderColParams = [];
				if ($orderCol !== null) {
					$orderColParams = [
						'_order_column' => $orderCol,
						'_order_direction' => 'DESC'
					];
				}

				$AdvancedFilterValue = $this->_buildShowDefaultFields($model, $orderColParams);
				$awarenessStatusForAllItems = [
					'AwarenessProgramActiveUser',
					'AwarenessProgramIgnoredUser',
					'AwarenessProgramCompliantUser',
					'AwarenessProgramNotCompliantUser',
					'AwarenessReminder',
					// 'AwarenessTraining'
				];

				if (in_array($model, $awarenessStatusForAllItems)) {
					$AdvancedFilterValue[] = [
						'field' => 'AwarenessProgram-status',
						'value' => 'started'
					];
					$AdvancedFilterValue[] = [
						'field' => 'AwarenessProgram-status__comp_type',
						'value' => 5
					];
				}

				$allItemsDescription = __('Filter that shows everything');
				if ($model == 'AwarenessProgramActiveUser') {
					$allItemsDescription = __('Lists all participants for awareness trainings which are currently started');
				}
				if ($model == 'AwarenessProgramIgnoredUser') {
					$allItemsDescription = __('Lists all ignored participants for awareness trainings which are currently started');
				}
				if ($model == 'AwarenessProgramCompliantUser') {
					$allItemsDescription = __('Lists all compliant participants for awareness trainings which are currently started');
				}
				if ($model == 'AwarenessProgramNotCompliantUser') {
					$allItemsDescription = __('Lists all non-compliant participants for awareness trainings which are currently started');
				}

				// dont set all items filter default for some sections
				if (in_array($model, ['Project', 'ProjectAchievement'])) {
					$allItemsDefault = '0';
				} else {
					$allItemsDefault = '1';
				}

				$this->create();
				$ret &= $this->saveAssociated([
					'AdvancedFilter' => [
						'user_id' => $userId,
						'name' => __('All Items'),
						'slug' => 'all-items',
						'description' => $allItemsDescription,
						'model' => $model,
						'private' => 1,
						'log_result_data' => 0,
						'log_result_count' => 0,
						'system_filter' => 1
					],
					'AdvancedFilterUserSetting' => [
						'model' => $model,
						'default_index' => $allItemsDefault,
						'user_id' => $userId
					],
					'AdvancedFilterValue' => $AdvancedFilterValue
				]);

				$skipModifiedCreated = [
					'AwarenessProgramActiveUser',
					'AwarenessProgramIgnoredUser',
					'AwarenessProgramCompliantUser',
					'AwarenessProgramNotCompliantUser',
					'AwarenessReminder',
					// 'AwarenessTraining'
				];

				if (in_array($model, $skipModifiedCreated)) {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'modified',
						'value' => '_minus_7_days_'
					];
					$AdvancedFilterValue[] = [
						'field' => 'modified__comp_type',
						'value' => 1
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Last Modified'),
							'slug' => 'last-modified',
							'description' => __('Filter that shows last 10 modified items'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1,
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '0',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'created',
						'value' => '_minus_7_days_'
					];
					$AdvancedFilterValue[] = [
						'field' => 'created__comp_type',
						'value' => 1
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Last Created'),
							'slug' => 'last-created',
							'description' => __('Filter that shows last 10 created items'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '0',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if ($model == 'SecurityServiceAudit') {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'planned_date',
						'value' => '_plus_14_days_'
					];
					$AdvancedFilterValue[] = [
						'field' => 'planned_date__comp_type',
						'value' => 2
					];
					$AdvancedFilterValue[] = [
						'field' => 'planned_date__use_calendar',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'result__comp_type',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'result',
						'value' => '_null_'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Audits due in 14 Days'),
							'slug' => 'due-in-14-days',
							'description' => __('This is the list of audits due in the coming 14 Days'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					// expired audits
					App::uses('SecurityServiceAudit', 'Model');
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_audit_missing',
						'value' => 1
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_audit_missing__show',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Expired Audits'),
							'slug' => 'expired',
							'description' => __('This filter shows all expired audits'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					// failed audits
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'result__comp_type',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'result',
						'value' => SecurityServiceAudit::RESULT_FAILED
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Failed Audits'),
							'slug' => 'failed',
							'description' => __('This filter shows all audits which have failed'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					// passed audits
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'result__comp_type',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'result',
						'value' => SecurityServiceAudit::RESULT_PASSED
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Pass Audits'),
							'slug' => 'pass',
							'description' => __('This filter shows all audits which have passed'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					// completed audits
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'result__comp_type',
						'value' => 6
					];
					$AdvancedFilterValue[] = [
						'field' => 'result',
						'value' => '_null_'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Completed Audits'),
							'slug' => 'completed',
							'description' => __('This filter shows all completed (Fail and Pass) audits'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if ($model == 'SecurityServiceMaintenance') {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'planned_date',
						'value' => '_plus_14_days_'
					];
					$AdvancedFilterValue[] = [
						'field' => 'planned_date__comp_type',
						'value' => 2
					];
					$AdvancedFilterValue[] = [
						'field' => 'planned_date__use_calendar',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'result__comp_type',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'result',
						'value' => '_null_'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Maintenances due in 14 Days'),
							'slug' => 'due-in-14-audits',
							'description' => __('This is the list of maintenances due in the coming 14 Days'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					// expired maintenances
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_maintenance_missing',
						'value' => 1
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_maintenance_missing__show',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Expired Maintenances'),
							'slug' => 'expired',
							'description' => __('This filter shows all expired mantainances'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					// failed maintenances
					App::uses('SecurityServiceAudit', 'Model');
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'result__comp_type',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'result',
						'value' => SecurityServiceAudit::RESULT_FAILED
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Failed Maintenances'),
							'slug' => 'failed',
							'description' => __('This filter shows all mantainances which have failed'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					// passed maintenances
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'result__comp_type',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'result',
						'value' => SecurityServiceAudit::RESULT_PASSED
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Pass Maintenances'),
							'slug' => 'pass',
							'description' => __('This filter shows all mantainances which have passed'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if ($model == 'SecurityPolicyReview') {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'planned_date',
						'value' => '_plus_14_days_'
					];
					$AdvancedFilterValue[] = [
						'field' => 'planned_date__comp_type',
						'value' => 2
					];
					$AdvancedFilterValue[] = [
						'field' => 'planned_date__use_calendar',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'result__comp_type',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'result',
						'value' => '_null_'
					];
					$AdvancedFilterValue[] = [
						'field' => 'completed',
						'value' => '0'
					];
					$AdvancedFilterValue[] = [
						'field' => 'completed__comp_type',
						'value' => 5
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Security Policy Reviews due in 14 Days'),
							'slug' => 'due-in-14-days',
							'description' => __('This is the list of reviews due in the coming 14 Days'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if ($model == 'AssetReview') {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'planned_date',
						'value' => '_plus_14_days_'
					];
					$AdvancedFilterValue[] = [
						'field' => 'planned_date__comp_type',
						'value' => 2
					];
					$AdvancedFilterValue[] = [
						'field' => 'planned_date__use_calendar',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'result__comp_type',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'result',
						'value' => '_null_'
					];
					$AdvancedFilterValue[] = [
						'field' => 'completed',
						'value' => '0'
					];
					$AdvancedFilterValue[] = [
						'field' => 'completed__comp_type',
						'value' => 5
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Asset Reviews due in 14 Days'),
							'slug' => 'due-in-14-days',
							'description' => __('This is the list of reviews due in the coming 14 Days'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if (in_array($model, ['RiskReview', 'ThirdPartyRiskReview', 'BusinessContinuityReview'])) {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'planned_date',
						'value' => '_plus_14_days_'
					];
					$AdvancedFilterValue[] = [
						'field' => 'planned_date__comp_type',
						'value' => 2
					];
					$AdvancedFilterValue[] = [
						'field' => 'planned_date__use_calendar',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'result__comp_type',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'result',
						'value' => '_null_'
					];
					$AdvancedFilterValue[] = [
						'field' => 'completed',
						'value' => '0'
					];
					$AdvancedFilterValue[] = [
						'field' => 'completed__comp_type',
						'value' => 5
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Risk Reviews due in 14 Days'),
							'slug' => 'due-in-14-days',
							'description' => __('This is the list of reviews due in the coming 14 Days'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if (in_array($model, ['Risk', 'ThirdPartyRisk', 'BusinessContinuity'])) {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_expired',
						'value' => 1
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_expired__show',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Risk with Expired Reviews'),
							'slug' => 'expired-reviews',
							'description' => __('Lists of risks with expired reviews'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '0',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if (in_array($model, ['SecurityIncident'])) {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'security_incident_status_id',
						'value' => 2
					];
					$AdvancedFilterValue[] = [
						'field' => 'security_incident_status_id__show',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Ongoing Incidents'),
							'slug' => 'ongoing',
							'description' => __('This is the list of all ongoing incidents'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '0',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					//
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'security_incident_status_id',
						'value' => 3
					];
					$AdvancedFilterValue[] = [
						'field' => 'security_incident_status_id__show',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Closed Incidents'),
							'slug' => 'closed',
							'description' => __('This is the list of all closed incidents'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '0',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					//
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'security_incident_status_id',
						'value' => 2
					];
					$AdvancedFilterValue[] = [
						'field' => 'security_incident_status_id__show',
						'value' => '1'
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_lifecycle_incomplete',
						'value' => 1
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_lifecycle_incomplete__show',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Incidents with missing analysis'),
							'slug' => 'missing-analysis',
							'description' => __('This is the list of all ongoing incidents that have one or more incomplete analysis'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '0',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if (in_array($model, ['Project'])) {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'project_status_id',
						'value' => 2
					];
					$AdvancedFilterValue[] = [
						'field' => 'project_status_id__show',
						'value' => '1'
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_expired_tasks',
						'value' => 1
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_expired_tasks__show',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Projects with Expired Tasks'),
							'slug' => 'expired-task',
							'description' => __('This is the list of projects that are ongoing and contain one or more expired tasks'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					//
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'project_status_id',
						'value' => 2
					];
					$AdvancedFilterValue[] = [
						'field' => 'project_status_id__show',
						'value' => '1'
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_expired',
						'value' => 1
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_expired__show',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Expired Projects'),
							'slug' => 'expired',
							'description' => __('This is the list of projects that are ongoing and contain a due date in the past'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '0',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					//
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'project_status_id',
						'value' => 2
					];
					$AdvancedFilterValue[] = [
						'field' => 'project_status_id__show',
						'value' => '1'
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_no_updates',
						'value' => 1
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_no_updates__show',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Projects without updates in the last two weeks'),
							'slug' => 'no-updates',
							'description' => __('This is the list of ongoing projects that have not been updated in the last couple of weeks'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					//
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'project_status_id',
						'value' => 2
					];
					$AdvancedFilterValue[] = [
						'field' => 'project_status_id__show',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Ongoing Projects'),
							'slug' => 'ongoing-projects',
							'description' => __('This is the list of ongoing projects'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					//
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'project_status_id',
						'value' => 3
					];
					$AdvancedFilterValue[] = [
						'field' => 'project_status_id__show',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Closed Projects'),
							'slug' => 'completed',
							'description' => __('This is the list of closed projects'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '0',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					//
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'project_status_id',
						'value' => 1
					];
					$AdvancedFilterValue[] = [
						'field' => 'project_status_id__show',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Planned Projects'),
							'slug' => 'planned',
							'description' => __('This is the list of planned projects'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '0',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if (in_array($model, ['ProjectAchievement'])) {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'completion__comp_type',
						'value' => 2
					];
					$AdvancedFilterValue[] = [
						'field' => 'completion',
						'value' => '100'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Pending Tasks'),
							'slug' => 'pending',
							'description' => __('Tasks which are not 100% completed'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'date__comp_type',
						'value' => 2
					];
					$AdvancedFilterValue[] = [
						'field' => 'date__use_calendar',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'date',
						'value' => '_today_'
					];
					$AdvancedFilterValue[] = [
						'field' => 'completion__comp_type',
						'value' => 2
					];
					$AdvancedFilterValue[] = [
						'field' => 'completion',
						'value' => '100'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Expired Tasks'),
							'slug' => 'expired',
							'description' => __('Tasks which have a deadline set in the past'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if (in_array($model, ['ComplianceAnalysisFinding'])) {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'status',
						'value' => 1
					];
					$AdvancedFilterValue[] = [
						'field' => 'status__show',
						'value' => '1'
					];
					$AdvancedFilterValue[] = [
						'field' => 'status__comp_type',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'due_date',
						'value' => '_plus_14_days_'
					];
					$AdvancedFilterValue[] = [
						'field' => 'due_date__comp_type',
						'value' => 2
					];
					$AdvancedFilterValue[] = [
						'field' => 'due_date__use_calendar',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'due_date__show',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Compliance Findings due in 14 Days'),
							'slug' => 'due-in-14-days',
							'description' => __('This is the list of compliance findings due in the next 14 days'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '0',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					// ...
					// 
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_expired',
						'value' => 1
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_expired__show',
						'value' => '1'
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_expired__comp_type',
						'value' => 0
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Expired Compliance Findings'),
							'slug' => 'expired',
							'description' => __('This is the list of compliance analysis findings that have a due date in the past and are still open'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '0',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if (in_array($model, ['SecurityService'])) {
					// controls with issues
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_control_with_issues',
						'value' => 1
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_control_with_issues__show',
						'value' => 1
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Controls with Issues'),
							'slug' => 'with-issues',
							'description' => __('This filter shows controls which currently have one or more ongoing issue'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '0',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					// control with missing audits
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_audits_last_missing',
						'value' => 1
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_audits_last_missing__show',
						'value' => 1
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Controls with Missing Audits'),
							'slug' => 'missing-audits',
							'description' => __('This filter shows controls which currently miss one or more audits'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '0',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					// control with missing maintenances
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_maintenances_last_missing',
						'value' => 1
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_maintenances_last_missing__show',
						'value' => 1
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Controls with Missing Maintenances'),
							'slug' => 'missing-maintenances',
							'description' => __('This filter shows controls which currently miss one or more Maintenances'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '0',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					// control with failed maintenances
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_maintenances_last_not_passed',
						'value' => 1
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_maintenances_last_not_passed__show',
						'value' => 1
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Controls with Failed Maintenances'),
							'slug' => 'failed-maintenances',
							'description' => __('This filter shows controls which currently have failed Maintenances'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '0',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					// control with failed audits
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_audits_last_not_passed',
						'value' => 1
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_audits_last_not_passed__show',
						'value' => 1
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Controls with Failed Audits'),
							'slug' => 'failed-audits',
							'description' => __('This filter shows controls which currently have failed Audits'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '0',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if (in_array($model, ['SecurityPolicy'])) {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'status',
						'value' => '1'
					];
					$AdvancedFilterValue[] = [
						'field' => 'status__comp_type',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_expired_reviews',
						'value' => '1'
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_expired_reviews__show',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Policies with Missing Reviews'),
							'slug' => 'missing-reviews',
							'description' => __('This filter shows all policies that are published and currently miss one or more reviews'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if (in_array($model, ['Asset'])) {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_expired_reviews',
						'value' => '1'
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_expired_reviews__show',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Assets with Missing Reviews'),
							'slug' => 'missing-reviews',
							'description' => __('This filter shows all assets that are published and currently miss one or more reviews'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if ($model == 'ServiceContract') {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'end',
						'value' => '_plus_14_days_'
					];
					$AdvancedFilterValue[] = [
						'field' => 'end__comp_type',
						'value' => 2
					];
					$AdvancedFilterValue[] = [
						'field' => 'end__use_calendar',
						'value' => 0
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Contracts due in the next 14 Days'),
							'slug' => 'due-in-14-days',
							'description' => __('This is the list of contracts that expire in the next couple of weeks'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_expired',
						'value' => 1
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_expired__show',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Expired Contracts'),
							'slug' => 'expired',
							'description' => __('This is the list of contracts that have an end date in the past'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if (in_array($model, ['RiskException', 'PolicyException', 'ComplianceException'])) {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'expiration',
						'value' => '_plus_14_days_'
					];
					$AdvancedFilterValue[] = [
						'field' => 'expiration__comp_type',
						'value' => 2
					];
					$AdvancedFilterValue[] = [
						'field' => 'expiration__use_calendar',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'status__comp_type',
						'value' => 0
					];
					$AdvancedFilterValue[] = [
						'field' => 'status',
						'value' => 1
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Exceptions due in the next 14 days'),
							'slug' => 'due-in-14-days',
							'description' => __('This is the list of exceptions expiring in the next 14 days'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_expired',
						'value' => 1
					];
					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_expired__show',
						'value' => 1
					];


					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Expired Exceptions'),
							'slug' => 'expired',
							'description' => __('This is the list of exceptions which have already expired'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if (in_array($model, ['VendorAssessment'])) {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'submited',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Submitted Assessments'),
							'slug' => 'submitted',
							'description' => __('This filter shows all submitted assessments'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'submited',
						'value' => '0'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Not Submitted Assessments'),
							'slug' => 'not-submitted',
							'description' => __('This filter shows all not submitted assessments'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if (in_array($model, ['VendorAssessmentFeedback'])) {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'completed',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Answered Items'),
							'slug' => 'answered',
							'description' => __('This filter shows all answered items'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'completed',
						'value' => '0'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Unanswered Items'),
							'slug' => 'unanswered',
							'description' => __('This filter shows all not answered items'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if (in_array($model, ['VendorAssessmentFinding'])) {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'status',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Open Findings'),
							'slug' => 'open',
							'description' => __('This filter shows all open findings'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'status',
						'value' => '0'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Closed Findings'),
							'slug' => 'closed',
							'description' => __('This filter shows all closed findings'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);

					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'status',
						'value' => '1'
					];

					$AdvancedFilterValue[] = [
						'field' => 'ObjectStatus_expired',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Expired Findings'),
							'slug' => 'expired',
							'description' => __('This filter shows all expired findings'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if (in_array($model, ['DataAssetInstance'])) {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'DataAssetSetting-gdpr_enabled',
						'value' => '1'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('GDPR Scope Assets'),
							'slug' => 'gdpr-enabled',
							'description' => __('This is the list of all assets in the scope of GDPR'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}

				if (in_array($model, ['DataAsset'])) {
					App::uses('DataAsset', 'Model');

					foreach (DataAsset::statuses() as $statusKey => $statusName) {
						$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
						$AdvancedFilterValue[] = [
							'field' => 'data_asset_status_id',
							'value' => $statusKey
						];

						$this->create();
						$ret &= $this->saveAssociated([
							'AdvancedFilter' => [
								'user_id' => $userId,
								'name' => __('%s Stage Flows', $statusName),
								'slug' => 'status-' . $statusKey,
								'description' => __('This filter shows all flows that are assigned to the "%s" stage', $statusName),
								'model' => $model,
								'private' => 1,
								'log_result_data' => 0,
								'log_result_count' => 0,
								'system_filter' => 1
							],
							'AdvancedFilterUserSetting' => [
								'model' => $model,
								'default_index' => '1',
								'user_id' => $userId
							],
							'AdvancedFilterValue' => $AdvancedFilterValue
						]);
					}
				}

				if (in_array($model, ['Cron'])) {
					$AdvancedFilterValue = $this->_buildShowDefaultFields($model);
					$AdvancedFilterValue[] = [
						'field' => 'status',
						'value' => 'error'
					];

					$this->create();
					$ret &= $this->saveAssociated([
						'AdvancedFilter' => [
							'user_id' => $userId,
							'name' => __('Failed CRONs'),
							'slug' => 'open',
							'description' => __('This filter shows CRONs with error'),
							'model' => $model,
							'private' => 1,
							'log_result_data' => 0,
							'log_result_count' => 0,
							'system_filter' => 1
						],
						'AdvancedFilterUserSetting' => [
							'model' => $model,
							'default_index' => '1',
							'user_id' => $userId
						],
						'AdvancedFilterValue' => $AdvancedFilterValue
					]);
				}
			}
		}

		return $ret;
	}

	public function buildShowDefaultFields($model, $options = [])
	{
		return $this->_buildShowDefaultFields($model, $options);
	}

	/**
	 * @param  Model  $model
	 * @param  array  $options Array key => value pairs (field => value) which will overwrite predefined values
	 * @return array
	 */
	protected function _buildShowDefaultFields($model, $options = [])
	{
		$displayField = ClassRegistry::init($model)->displayField;

		// for Compliance sections we use a specific sort field
		$customComplianceSortConds = $model == 'ComplianceManagement';
		$customComplianceSortConds = $customComplianceSortConds || $model == 'CompliancePackage';
		$customComplianceSortConds = $customComplianceSortConds || $model == 'CompliancePackageItem';
		if ($customComplianceSortConds) {
			$displayField = 'id';
		}

		$AdvancedFilterValue = [
			[
				'field' => 'advanced_filter',
				'value' => '1'
			]
		];

		$showDefaultFields = $this->_getShowDefaultFields($model);
		foreach ($showDefaultFields as $field) {
			$AdvancedFilterValue[] = [
				'field' => $field . '__show',
				'value' => '1'
			];
		}

		$AdvancedFilterValue[] = [
			'field' => '_limit',
			'value' => AdvancedFilterValue::LIMIT_UNLIMITED
		];
		$AdvancedFilterValue[] = [
			'field' => '_order_column',
			'value' => $displayField
		];
		$AdvancedFilterValue[] = [
			'field' => '_order_direction',
			'value' => 'ASC'
		];

		foreach ($AdvancedFilterValue as $afvKey => $afvVal) {
			if (array_key_exists($afvVal['field'], $options)) {
				$AdvancedFilterValue[$afvKey] = [
					'field' => $afvVal['field'],
					'value' => $options[$afvVal['field']]
				];
			}
		}

		return $AdvancedFilterValue;
	}

	/**
	 * @param  Model  $model
	 * @param  array  $fields additional fields
	 * @return array
	 */
	protected function _buildShowFields($model, $fields = [], $order = null)
	{
		$Model = ClassRegistry::init($model);

		if ($Model->Behaviors->enabled('AdvancedFilters.AdvancedFilters')) {
			$Model->buildAdvancedFilterArgs();
		}

		if (!empty($order)) {
			reset($order);
			$orderCol = key($order);
			$orderDir = $order[$orderCol];
		}
		else {
			$orderCol = $Model->displayField;
			$orderDir = 'ASC';
		}

		$AdvancedFilterValue = [
			[
				'field' => 'advanced_filter',
				'value' => '1'
			]
		];

		if (!$Model->Behaviors->enabled('DisplayFilterFields')) {
			$Model->Behaviors->load('DisplayFilterFields');
			$Model->Behaviors->DisplayFilterFields->setup($Model);
		}

		$fields = array_merge(((array) $Model->getDisplayFilterFields()), $fields);

		foreach ($Model->advancedFilter as $fieldSet) {
            foreach ($fieldSet as $field => $fieldData) {
                $AdvancedFilterValue[] = [
					'field' => $field . '__show',
					'value' => in_array($field, $fields) ? 1 : 0
				];
            }
        }

		$AdvancedFilterValue[] = [
			'field' => '_limit',
			'value' => AdvancedFilterValue::LIMIT_UNLIMITED
		];
		$AdvancedFilterValue[] = [
			'field' => '_order_column',
			'value' => $orderCol
		];
		$AdvancedFilterValue[] = [
			'field' => '_order_direction',
			'value' => $orderDir
		];

		return $AdvancedFilterValue;
	}

	protected function _getShowDefaultFields($model)
	{
		$Model = ClassRegistry::init($model);

		if ($Model->Behaviors->enabled('AdvancedFilters.AdvancedFilters')) {
			$Model->buildAdvancedFilterArgs();
		}

		$showDefaultFields = [];
		foreach ($Model->advancedFilter as $fieldSet) {
            foreach ($fieldSet as $field => $fieldData) {
                if (!empty($fieldData['show_default'])) {
                    $showDefaultFields[] = $field;
                }
            }
        }

        return $showDefaultFields;
	}

	public function get($id)
	{
		if (!is_numeric($id)) {
			return false;
		}
		
		$data = $this->find('first', [
			'conditions' => [
				'AdvancedFilter.id' => $id
			],
			'fields' => [
				'AdvancedFilter.name',
				'AdvancedFilter.description',
				'AdvancedFilter.model',
				'AdvancedFilter.system_filter',
				'AdvancedFilter.log_result_count',
				'AdvancedFilter.log_result_data',
				'AdvancedFilterUserSetting.id',
				'AdvancedFilterUserSetting.default_index'
			],
			'recursive' => 0
		]);

		return $data;
	}

	/**
	 * Get field values for a filter in a $key => $value format.
	 * 
	 * @param  int   $id Advanced filter ID
	 * @return array     Formatted values as result
	 */
	public function getFormattedValues($id)
	{
		$values = $this->_getFilterValues($id);
		$formattedValues = self::_buildValues($values);

		return $formattedValues;
	}

	/**
	 * returns filter values data 
	 * 
	 * @param  int $id advanced_fiter_id
	 * @return array
	 */
	protected function _getFilterValues($id) {
		$values = $this->AdvancedFilterValue->find('all', array(
			'conditions' => array(
				'AdvancedFilterValue.advanced_filter_id' => $id
			),
			'recursive' => -1
		));

		return $values;
	}

	/**
	 * returns well formated AdvancedFilterValue data for request
	 * 
	 * @param  array $data
	 * @return array $formatedData
	 */
	protected static function _buildValues($data) {
		$formatedData = array();

		foreach ($data as $item) {
			$value = $item['AdvancedFilterValue']['value'];
			// if ($item['AdvancedFilterValue']['many']) {
			// 	$value = explode(',', $value);
			// }
			$formatedData[$item['AdvancedFilterValue']['field']] = $value;
		}

		return $formatedData;
	}

	/**
	 * Check if filter exists.
	 */
	public function filterExists($model, $slug, $userId)
	{
		return (boolean) $this->find('count', [
			'conditions' => [
				'AdvancedFilter.model' => $model,
				'AdvancedFilter.slug' => $slug,
				'AdvancedFilter.user_id' => $userId
			],
			'recursive' => -1
		]);
	}

	/**
	 * Rename filter.
	 */
	public function renameFilter($model, $slug, $name)
	{
		$ds = $this->getDataSource();

		return (boolean) $this->updateAll(['name' => $ds->value($name)], [
			'AdvancedFilter.model' => $model,
			'AdvancedFilter.slug' => $slug
		]);
	}
}