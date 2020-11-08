<?php
App::uses('DashboardKpi', 'Dashboard.Model');
App::uses('FilterAdapter', 'AdvancedFilters.Lib/QueryAdapter');
App::uses('AdvancedFiltersObject', 'AdvancedFilters.Lib');
App::uses('Project', 'Model');

class DashboardUserSync
{

	public function __construct()
	{
		$this->DashboardKpi = ClassRegistry::init('Dashboard.DashboardKpi');
		$this->CustomRolesUser = ClassRegistry::init('CustomRoles.CustomRolesUser');
		$this->VisualisationSettingsUser = ClassRegistry::init('Visualisation.VisualisationSettingsUser');
		$this->CustomRolesUsers = ClassRegistry::init('CustomRoles.CustomRolesUsers');
		$this->VisualisationShareUser = ClassRegistry::init('Visualisation.VisualisationShareUser');

		$riskConfig = [
			'total' => [
				'title' => __('Total'),
				'params' => []
			],
			'next_reviews' => [
				'title' => __('Expired'),	
				'params' => [
					'ObjectStatus_expired_reviews' => [
						'value' => true,
						'comparisonType' => FilterAdapter::COMPARISON_EQUAL
					]
				]
			],
			'missed_reviews' => [
				'title' => __('Coming Reviews (14 Days)'),
				'params' => [
					'review' => [
						'value' => FilterAdapter::PLUS_14_DAYS_VALUE,
						'comparisonType' => FilterAdapter::COMPARISON_UNDER
					]
				]
			]
		];

		$this->sync = [
			'Risk' => $riskConfig,
			'ThirdPartyRisk' => $riskConfig,
			'BusinessContinuity' => $riskConfig,

			'SecurityService' => [
				'total' => [
					'title' => __('Total'),
					'params' => []
				],
				'missing_audits' => [
					'title' => __('Missing Audits'),
					'params' => [
						'ObjectStatus_audits_last_missing' => [
							'value' => true,
							'comparisonType' => FilterAdapter::COMPARISON_EQUAL
						]
					]
				],
				'failed_audits' => [
					'title' => __('Failed Audits'),
					'params' => [
						'ObjectStatus_audits_last_not_passed' => [
							'value' => true,
							'comparisonType' => FilterAdapter::COMPARISON_EQUAL
						]
					]
				],
				'issue' => [
					'title' => __('Issues'),
					'params' => [
						'ObjectStatus_control_with_issues' => [
							'value' => true,
							'comparisonType' => FilterAdapter::COMPARISON_EQUAL
						]
					]	
				]
			],

			'SecurityPolicy' => [
				'total' => [
					'title' => __('Total'),
					'params' => [
						'status' => [
							'value' => SECURITY_POLICY_RELEASED,
							'comparisonType' => FilterAdapter::COMPARISON_EQUAL
						]
					]
				],
				'next_reviews' => [
					'title' => __('Expired'),	
					'params' => [
						'ObjectStatus_expired_reviews' => [
							'value' => true,
							'comparisonType' => FilterAdapter::COMPARISON_EQUAL
						]
					]
				],
				'missed_reviews' => [
					'title' => __('Coming Reviews (14 Days)'),
					'params' => [
						'next_review_date' => [
							'value' => FilterAdapter::PLUS_14_DAYS_VALUE,
							'comparisonType' => FilterAdapter::COMPARISON_UNDER
						]
					]
				]
			],

			'SecurityIncident' => [
				'total' => [
					'title' => __('Total'),
					'params' => [
						'security_incident_status_id' => [
							'value' => SECURITY_INCIDENT_ONGOING,
							'comparisonType' => FilterAdapter::COMPARISON_EQUAL
						],
					]
				],
				'open' => [
					'title' => __('Open'),
					'params' => [
						'security_incident_status_id' => [
							'value' => SECURITY_INCIDENT_ONGOING,
							'comparisonType' => FilterAdapter::COMPARISON_EQUAL
						]
					]
				], 
				'closed' => [
					'title' => __('Closed'),
					'params' => [
						'security_incident_status_id' => [
							'value' => SECURITY_INCIDENT_CLOSED,
							'comparisonType' => FilterAdapter::COMPARISON_EQUAL
						]
					]
				], 
				'incomplete_stage' => [
					'title' => __('Incomplete Lifecycle'),
					'params' => [
						'security_incident_status_id' => [
							'value' => SECURITY_INCIDENT_ONGOING,
							'comparisonType' => FilterAdapter::COMPARISON_EQUAL
						],
						'ObjectStatus_lifecycle_incomplete' => [
							'value' => true,
							'comparisonType' => FilterAdapter::COMPARISON_EQUAL
						]
					]
				], 
			],

			'Project' => [
				'total' => [
					'title' => __('Total'),
					'params' => [
						'project_status_id' => [
							'value' => Project::STATUS_ONGOING,
							'comparisonType' => FilterAdapter::COMPARISON_EQUAL
						],
					]
				],
				'expired' => [
					'title' => __('Expired'),
					'params' => [
						'ObjectStatus_expired' => [
							'value' => true,
							'comparisonType' => FilterAdapter::COMPARISON_EQUAL
						],
					]
				],
				'comming_dates' => [
					'title' => __('Coming Deadline (14 Days)'),
					'params' => [
						'project_status_id' => [
							'value' => Project::STATUS_ONGOING,
							'comparisonType' => FilterAdapter::COMPARISON_EQUAL
						],
						'deadline' => [
							'value' => FilterAdapter::PLUS_14_DAYS_VALUE,
							'comparisonType' => FilterAdapter::COMPARISON_UNDER
						],
					]
				],
				'expired_tasks' => [
					'title' => __('Project with Expired Tasks'),
					'params' => [
						'ObjectStatus_expired_tasks' => [
							'value' => true,
							'comparisonType' => FilterAdapter::COMPARISON_EQUAL
						],
					]
				]
			],

			'ComplianceAnalysisFinding' => [
				'expired' => [
					'title' => __('Expired'),
					'params' => [
						'status' => [
							'value' => POLICY_EXCEPTION_CLOSED,
							'comparisonType' => FilterAdapter::COMPARISON_NOT_EQUAL
						],
						'due_date' => [
							'value' => FilterAdapter::TODAY_VALUE,
							'comparisonType' => FilterAdapter::COMPARISON_UNDER
						]
					]
				],
				'open' => [
					'title' => __('Open'),
					'params' => [
						'status' => [
							'value' => POLICY_EXCEPTION_OPEN,
							'comparisonType' => FilterAdapter::COMPARISON_EQUAL
						]
					]
				], 
				'closed' => [
					'title' => __('Closed'),
					'params' => [
						'status' => [
							'value' => POLICY_EXCEPTION_CLOSED,
							'comparisonType' => FilterAdapter::COMPARISON_EQUAL
						]
					]
				]
			]
		];

	}

	// synchronize user dashboard
	public function sync()
	{
		$ret = true;

		$users = $this->CustomRolesUser->find('list', [
			'fields' => [
				'CustomRolesUser.user_id'
			],
			'recursive' => -1
		]);

		$iterations = 0;
		foreach ($this->sync as $model => $kpiList) {

			$Model = ClassRegistry::init($model);

			$customRoles = [];
			if ($Model->Behaviors->loaded('CustomRoles.CustomRoles')) {
				$customRoles = $Model->Behaviors->CustomRoles->getModelSettings($Model);
			}

			foreach ($kpiList as $kpi => $kpiParams) {

				foreach ($customRoles as $role) {

					foreach ($users as $userId) {
						$iterations++;

						$DashboardKpiAttribute = $this->_buildAttributes($kpi, $role, $userId);

						// first lets synchronize the structure, meaning
						// make sure there is a KPI record in the database
						$ret &= $structure = $this->_syncStructure($model, $DashboardKpiAttribute, $kpiParams);

						// if everything checks out then recalculate the value for this KPI
						if ($structure) {
							$ret &= $this->_syncValue($model, $DashboardKpiAttribute, $kpiParams);
						}

					}

				}

			}

		}

		return $ret;
	}

	// build attributes for a single iteration
	protected function _buildAttributes($kpi, $role, $userId)
	{
		// temporarily we use a format compatible with the current dashboard logic
		// {"User":"expired","CustomRoles.CustomRole":"Owner","CustomRoles.CustomUser":"1"}
		
		return [
			0 => [
				'model' => 'User',
				'foreign_key' => $kpi
			],
			1 => [
				'model' => 'CustomRoles.CustomRole',
				'foreign_key' => $role
			],
			2 => [
				'model' => 'CustomRoles.CustomUser',
				'foreign_key' => $userId
			]
		];
	}

	protected function _buildAttributesFromKpi($data)
	{
		$json = json_decode($data['DashboardKpi']['json'], true);

		$kpi = $json['User'];
		$role = $json['CustomRoles.CustomRole'];
		$userId = $json['CustomRoles.CustomUser'];

		return $this->_buildAttributes($kpi, $role, $userId);
	}

	// sync the kpi row to the dashboard_kpis table
	protected function _syncStructure($model, $DashboardKpiAttribute, $kpiParams)
	{
		$exist = $this->DashboardKpi->kpiExist($model, $DashboardKpiAttribute);

		$title = $kpiParams['title'];
		$ret = true;
		if (!$exist) {
			$saveData = [
				'class_name' => 'Visualisation.VisualizedKpi',
				'title' => $title,
				'type' => DashboardKpi::TYPE_USER,
				'category' => DashboardKpi::CATEGORY_GENERAL,
				'model' => $model,
				'DashboardKpiAttribute' => $DashboardKpiAttribute
			];

			$this->DashboardKpi->create($saveData);
			$ret &= $this->DashboardKpi->save();
		}

		// sync the title
		else {
			$data = $this->DashboardKpi->getByAttributes($model, $DashboardKpiAttribute);

			$id = $data['DashboardKpi']['id'];
			$savedTitle = $data['DashboardKpi']['title'];

			if ($savedTitle !== $title) {
				$this->DashboardKpi->id = $id;
				$ret &= (bool) $this->DashboardKpi->saveField('title', $title, false);
			}
		}

		return $ret;
	}

	protected function _syncValue($model, $DashboardKpiAttribute, $kpiParams)
	{
		// $value = $this->_getValue($model, $DashboardKpiAttribute, $kpiParams);
		$kpi = $this->DashboardKpi->getByAttributes($model, $DashboardKpiAttribute);

		// ! OPTIMIZE
		$ret = $this->DashboardKpi->recalculate($kpi['DashboardKpi']['id']);

		return $ret;

	}

	// public method to get the value of a certain existing KPI ID
	public function getValue($kpiId)
	{
		$data = $this->DashboardKpi->find('first', [
			'conditions' => [
				'DashboardKpi.id' => $kpiId
			],
			'recursive' => -1
		]);

		$DashboardKpiAttribute = $this->_buildAttributesFromKpi($data);

		$model = $data['DashboardKpi']['model'];
		$kpiParams = Hash::get($this->sync, $model . '.' . $DashboardKpiAttribute[0]['foreign_key']);

		$value = $this->_getValue($model, $DashboardKpiAttribute, $kpiParams);

		return $value;

	}

	// internal method to get the value of a KPI	
	protected function _getValue($model, $DashboardKpiAttribute, $kpiParams)
	{
		// ddd($kpiParams);
		$Model = ClassRegistry::init($model);
		$userId = $DashboardKpiAttribute[2]['foreign_key'];

		$resultQuery = [
			'conditions' => [],
			'softDelete' => true
		];

		// 1. part is visualisation conditions
		$visualisationConditions = $this->_getVisualisationConditions($model, $userId);

		if ($visualisationConditions === false) {
			return 0;
		} else {
			// conditions string
			if (is_string($visualisationConditions)) {
				$resultQuery['conditions'][] = $visualisationConditions;
			}

			// exempted user / allowed all objects
			if ($visualisationConditions === true) {
			}
		}

		// 2. part is filters conditions
		$filterConditions = $this->_getFilterConditions($model, $kpiParams['params']);

		// if there is some condition returned
		if ($filterConditions !== true) {
			$resultQuery['conditions'] = array_merge($resultQuery['conditions'], $filterConditions);
		}

		// 3. part is custom roles conditions
		$customRolesConditions = $this->_getCustomRolesConditions($model, $DashboardKpiAttribute);
		
		$resultQuery['joins'] = $customRolesConditions['joins'];
		// if (!isset($resultQuery['conditions'])) {
		// 	ddd($visualisationConditions);
		// }
		$resultQuery['conditions'] = array_merge($resultQuery['conditions'], $customRolesConditions['conditions']);

		$value = (int) $Model->find('count', $resultQuery);

		return $value;
	}

	/**
	 * Custom roles query, Query array is returned.
	 */
	protected function _getCustomRolesConditions($model, $DashboardKpiAttribute)
	{
		$Model = ClassRegistry::init($model);

		$joins = [
			[
				// @todo wrong aliasing
				'table' => 'custom_roles_role_users',
				'alias' => 'CustomRolesUsers',
				'type' => 'INNER',
				'conditions' => [
					'CustomRolesUsers.model' => $Model->alias,
					'CustomRolesUsers.foreign_key = ' . $Model->escapeField($Model->primaryKey)
				]	
			],
			[
				'table' => 'custom_roles_users',
				'alias' => 'CustomRolesUser',
				'type' => 'INNER',
				'conditions' => [
					'CustomRolesUser.user_id = CustomRolesUsers.user_id'
				]	
			],
			[
				'table' => 'custom_roles_roles',
				'alias' => 'CustomRolesRole',
				'type' => 'INNER',
				'conditions' => [
					'CustomRolesRole.id = CustomRolesUsers.custom_roles_role_id'
				]	
			],
		];

		$conds = [
			'CustomRolesUser.user_id' => $DashboardKpiAttribute[2]['foreign_key'],
			'CustomRolesRole.field' => $DashboardKpiAttribute[1]['foreign_key']
		];

		$query = [
			'joins' => $joins,
			'conditions' => $conds
		];

		return $query;
	}

	protected function _getFilterConditions($model, $params)
	{
		// if there are no params, we return true as in no conditions
		if (empty($params)) {
			return true;
		}

		$Model = ClassRegistry::init($model);

		$_filter = new AdvancedFiltersObject();
		$_filter->setModel($Model);
		$_filter->setFilterValues([]);

		$_filter->setConvertedValues($params);
		$conditions = $_filter->getConditions();

		return $conditions;
		/*
		$query = [
			'conditions' => $conditions,
			'fields' => [$findOn->escapeField($findField)]
		];

		if ($Model->alias !== $findOn->alias) {
			$query['recursive'] = -1;
			$primaryField = $Model->escapeField($Model->primaryKey);

			$query = $findOn->getQuery('all', $query);
			$query = [
				'conditions' => [
					"{$primaryField} IN ({$query})"
				],
				'fields' => [$primaryField]
			];
		}

		return $query;*/


	}

	protected function _getVisualisationConditions($model, $userId)
	{
		$Model = ClassRegistry::init($model);

		$primaryField = $Model->escapeField($Model->primaryKey);

		$modelJoins = $this->VisualisationSettingsUser->getJoins();

		$this->CustomRolesUsers->initAcl();

		$objectJoins = $this->VisualisationShareUser->getJoins();

		$modelJoins[] = $objectJoins[] = [
				'table' => 'aros',
				'alias' => 'Aro',
				'type' => 'INNER',
				'conditions' => [
					'Permission.aro_id = Aro.id'
				]
		];

		$query = [
			'conditions' => [
				'Aco.model' => $model,
				'Aro.model' => 'User',
				'Aro.foreign_key' => $userId,
				'Permission._read' => 1
			],
			'fields' => ['Aco.foreign_key'],
			'recursive' => -1
		];

		$modelQuery = $query + ['joins' => $modelJoins];
		$modelQuery['conditions']['Aco.foreign_key'] = null;

		// if user is exempted for a section we return true
		$modelNodes = $this->VisualisationSettingsUser->find('list', $modelQuery);
		if ($modelNodes) {
			return true;
		}

		$query['joins'] = $objectJoins;
		unset($query['conditions']['Aco.foreign_key']);
		$query['conditions']['Aco.foreign_key !='] = null;

		$this->VisualisationShareUser->Behaviors->load('Search.Searchable');
		$this->VisualisationShareUser->initAcl();

		$list = $this->VisualisationShareUser->find('list', $query);
		$controlled = $Model->getControlled();

		if (is_array($controlled) && count($controlled)) {
			foreach ($controlled as $id) {
				if (in_array($id, $list)) {
					continue;
				}

				// visualisation check if the user has access via Share button
				$check = $this->VisualisationShareUser->Acl->check([
					'Visualisation.VisualisationUser' => [
						'id' => $userId
					]
				], [
					$Model->alias => [
						$Model->primaryKey => $id
					]
				], 'read');

				$customRoleData = ClassRegistry::init('CustomRoles.CustomRolesUser')->find('first', [
					'conditions' => [
						'user_id' => $userId
					],
					'fields' => [
						'id'
					],
					'recursive' => -1
				]);

				$customRoleId = $customRoleData['CustomRolesUser']['id'];

				// custom roles check if the user has access via his Custom Roles within an object
				$check = $check || $this->CustomRolesUsers->Acl->check([
					'CustomRoles.CustomRolesUser' => [
						'id' => $customRoleId
					]
				], [
					$Model->alias => [
						$Model->primaryKey => $id
					]
				], 'read');

				if ($check === true) {
					$list[] = $id;
				}
			}
		}

		// if list of IDs are not empty, we return string as a part of $conditions
		if (!empty($list)) {
			$conditions = "{$primaryField} IN (" . (implode(',', $list)) . ")";
			return $conditions;
		}

		// otherwise return false as in no objects are allowed for the given user
		return false;
	}

	protected function _findKpi()
	{

	}

}