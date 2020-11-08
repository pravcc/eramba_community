<?php
App::uses('BaseFilterDashboardAttribute', 'AdvancedFilters.Lib/Dashboard/Attribute');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');
App::uses('Project', 'Model');

class AdminDashboardAttribute extends BaseFilterDashboardAttribute {

	public $mapFieldsForUrl = null;
	public $totalTitle = null;

	/**
	 * Prepend to array like array_unshift but this prepends it in an associative form to work here.
	 * 
	 * @param  array &$arr   Array to modify.
	 * @param  array $pepend Array to prepend.
	 * @return void
	 */
	public static function prependConfig(&$arr, $pepend) {
		$arr = $pepend + $arr;
	}

	public function __construct(Dashboard $Dashboard, DashboardKpiObject $DashboardKpiObject = null) {
		if ($this->totalTitle === null) {
			$this->totalTitle = __('Total number of %s');
		}

		$totalTitle = $this->totalTitle;

		$totalConfig = [
			'title' => $totalTitle,
			'params' => []
		];
		$totalConfigAssoc = [
			'total' => $totalConfig
		];

		$missedReviewParam = [
			'value' => AbstractQuery::PLUS_14_DAYS_VALUE,
			'comparisonType' => AbstractQuery::COMPARISON_UNDER
		];

		$reviewsConfig = [
			'next_reviews' => [
				'title' => __('Expired'),	
				'params' => [
					'ObjectStatus_expired_reviews' => [
						'value' => true,
						'comparisonType' => AbstractQuery::COMPARISON_EQUAL
					]
				]
			],
			'missed_reviews' => [
				'title' => __('Coming Reviews (14 Days)')
			]
		];

		$riskConfig = $reviewsConfig;
		$riskConfig['missed_reviews']['params']['review'] = $missedReviewParam;
		$riskConfig['total']['title'] = __('Total Number of Risks');
		self::prependConfig($riskConfig, $totalConfigAssoc);

		$policyConfig = $reviewsConfig;
		$policyConfig['missed_reviews']['params']['next_review_date'] = $missedReviewParam;
		self::prependConfig($policyConfig, $totalConfigAssoc);
		$policyConfig['total']['params'] = [
			'status' => [
				'value' => SECURITY_POLICY_RELEASED,
				'comparisonType' => AbstractQuery::COMPARISON_EQUAL
			]
		];

		$this->templates = [
			'Risk' => $riskConfig,
			'ThirdPartyRisk' => $riskConfig,
			'BusinessContinuity' => $riskConfig,
			// 
			'SecurityService' => [
				'total' => $totalConfig,
				'missing_audits' => [
					'title' => __('Missing Audits'),
					'params' => [
						'ObjectStatus_audits_last_missing' => [
							'value' => true,
							'comparisonType' => AbstractQuery::COMPARISON_EQUAL
						]
					]
				],
				'failed_audits' => [
					'title' => __('Failed Audits'),
					'params' => [
						'ObjectStatus_audits_last_not_passed' => [
							'value' => true,
							'comparisonType' => AbstractQuery::COMPARISON_EQUAL
						]
					]
				],
				'issue' => [
					'title' => __('Issues'),
					'params' => [
						'ObjectStatus_control_with_issues' => [
							'value' => true,
							'comparisonType' => AbstractQuery::COMPARISON_EQUAL
						]
					]	
				]
			],
			'SecurityPolicy' => $policyConfig,
			'SecurityIncident' => [
				'total' => [
					'title' => __('Total'),
					'params' => [
						'security_incident_status_id' => [
							'value' => SECURITY_INCIDENT_ONGOING,
							'comparisonType' => AbstractQuery::COMPARISON_EQUAL
						],
					]
				],
				'open' => [
					'title' => __('Open'),
					'params' => [
						'security_incident_status_id' => [
							'value' => SECURITY_INCIDENT_ONGOING,
							'comparisonType' => AbstractQuery::COMPARISON_EQUAL
						]
					]
				], 
				'closed' => [
					'title' => __('Closed'),
					'params' => [
						'security_incident_status_id' => [
							'value' => SECURITY_INCIDENT_CLOSED,
							'comparisonType' => AbstractQuery::COMPARISON_EQUAL
						]
					]
				], 
				'incomplete_stage' => [
					'title' => __('Incomplete Lifecycle'),
					'params' => [
						'security_incident_status_id' => [
							'value' => SECURITY_INCIDENT_ONGOING,
							'comparisonType' => AbstractQuery::COMPARISON_EQUAL
						],
						'ObjectStatus_lifecycle_incomplete' => [
							'value' => true,
							'comparisonType' => AbstractQuery::COMPARISON_EQUAL
						]
					]
				], 
			],
			
			'Project' => [
				'total' => [
					'title' => $totalTitle,
					'params' => [
						'project_status_id' => [
							'value' => Project::STATUS_ONGOING,
							'comparisonType' => AbstractQuery::COMPARISON_EQUAL
						],
					]
				],
				'expired' => [
					'title' => __('Expired'),
					'params' => [
						'ObjectStatus_expired' => [
							'value' => true,
							'comparisonType' => AbstractQuery::COMPARISON_EQUAL
						],
					]
				],
				'comming_dates' => [
					'title' => __('Coming Deadline (14 Days)'),
					'params' => [
						'project_status_id' => [
							'value' => Project::STATUS_ONGOING,
							'comparisonType' => AbstractQuery::COMPARISON_EQUAL
						],
						'deadline' => [
							'value' => AbstractQuery::PLUS_14_DAYS_VALUE,
							'comparisonType' => AbstractQuery::COMPARISON_UNDER
						],
					]
				],
				'expired_tasks' => [
					'title' => __('Project with Expired Tasks'),
					'params' => [
						'ObjectStatus_expired_tasks' => [
							'value' => true,
							'comparisonType' => AbstractQuery::COMPARISON_EQUAL
						],
					]
				]
			]
		];
		
		parent::__construct($Dashboard, $DashboardKpiObject);
	}

	public function listAttributes(Model $Model) {
		if (isset($this->templates[$Model->alias])) {
			return array_keys($this->templates[$Model->alias]);
		}

		return [];
	}

	public function templateInstance(Model $Model, $path) {
		$path = $Model->alias . '.' . $path;
		return parent::templateInstance($Model, $path);
	}

}