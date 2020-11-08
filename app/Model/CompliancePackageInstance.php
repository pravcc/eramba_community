<?php
App::uses('CompliancePackageRegulator', 'Model');

class CompliancePackageInstance extends CompliancePackageRegulator
{
	public $useTable = 'compliance_package_regulators';

	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);

		$this->label = __('Compliance Analysis');
	}

	public function getNotificationSystemConfig()
	{
		return parent::getNotificationSystemConfig();
	}

	public function getMacrosConfig()
	{
		return [
			'assoc' => [
				'CompliancePackage.CompliancePackageItem.ComplianceManagement'
			],
		];
	}

	public function getReportsConfig()
	{
		$complianceByTreatmentStrategy = [
			'title' => __('Compliance by Treatment Status'),
			'type' => ReportBlockChartSetting::TYPE_BAR,
			'dataFn' => 'packageByTreatmentStrategyChart'
		];

		$complianceByStatus = [
			'title' => __('Compliance by Status'),
			'type' => ReportBlockChartSetting::TYPE_BAR,
			'dataFn' => 'packageByStatusChart'
		];

		return [
			'finder' => [
				'options' => [
					'contain' => [
						'Owner',
						'OwnerGroup',
						'Legal',
						'CompliancePackage' => [
							'CompliancePackageRegulator',
							'CompliancePackageItem' => [
								'ComplianceManagement' => [
									'SecurityService' => [
										'SecurityServiceAudit'
									],
									'SecurityPolicy',
									'Risk',
									'ThirdPartyRisk',
									'BusinessContinuity',
									'Project',
									'ComplianceAnalysisFinding',
									'ComplianceException',
									'Asset',
									'ComplianceTreatmentStrategy',
									'ComplianceException',
									'CompliancePackageItem' => [
										'CompliancePackage' => [
											'CompliancePackageRegulator'
										]
									],
									'Legal',
									'Owner'
								]
							]
						],
					]
				]
			],
			'table' => [
				'model' => [
					'CompliancePackage.CompliancePackageItem.ComplianceManagement' => __('Compliance Analysis Item')
				]
			],
			'chart' => [
				1 => array_merge($complianceByTreatmentStrategy, [
					'templateType' => ReportTemplate::TYPE_ITEM,
					'description' => __('This chart shows the treatment status for each chapter on the compliance package.'),
				]),
				2 => array_merge($complianceByTreatmentStrategy, [
					'templateType' => ReportTemplate::TYPE_SECTION,
					'description' => __('This chart shows the treatment status for each compliance package.'),
				]),
				3 => array_merge($complianceByStatus, [
					'templateType' => ReportTemplate::TYPE_ITEM,
					'description' => __('This chart shows for each chapter what is the intended compliance treatment.'),
				]),
				4 => array_merge($complianceByStatus, [
					'templateType' => ReportTemplate::TYPE_SECTION,
					'description' => __('This chart shows for each compliance package what is the intended compliance treatment.'),
				]),
				5 => [
					'title' => __('Top 10 Controls that failed the most Audits (by proportion)'),
                    'description' => __('This charts looks at all controls used in a given compliance analysis package and sorts them by those that proportionally failed the most audits.'),
                    'type' => ReportBlockChartSetting::TYPE_BAR,
                    'templateType' => ReportTemplate::TYPE_SECTION,
                    'className' => 'ComplianceFailedAuditsChart',
                    'params' => [
                        'percentage' => true
                    ]
				],
				6 => [
					'title' => __('Top 10 Controls that failed the most Audits (by number)'),
                    'description' => __('This charts looks at all controls used in a given compliance analysis package and sorts them by those that failed the most audits. A second bar shows the total number of audits.'),
                    'type' => ReportBlockChartSetting::TYPE_BAR,
                    'templateType' => ReportTemplate::TYPE_SECTION,
                    'className' => 'ComplianceFailedAuditsChart',
                    'params' => []
				],
			]
		];
	}
}
