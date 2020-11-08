<?php
App::uses('Hash', 'Utility');
App::uses('ThirdParty', 'Model');
App::uses('ComplianceTreatmentStrategy', 'Model');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');
App::uses('AppController', 'Controller');

/**
 * @section
 */
class ComplianceManagementsController extends AppController
{
	public $helpers = [];
	public $components = [
		'Pdf', 'Paginator', 'ObjectStatus.ObjectStatus',
		'Ajax' => [
			'actions' => ['add', 'edit', 'delete', 'analyze']
		],
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				]
			],
			'listeners' => [
				'Api', 'ApiPagination', 'BulkActions.BulkActions', 'Widget.Widget', 'Visualisation.Visualisation',
				'.ModuleDispatcher' => [
					'listeners' => [
						'NotificationSystem.NotificationSystem',
						'CustomFields.CustomFields',
						'Mapping.Mapping',
						'Reports.Reports',
					]
				]
			]
		],
		'UserFields.UserFields' => [
			'fields' => ['Owner']
		]
	];

	protected $_appControllerConfig = [
		'components' => [
		],
		'helpers' => [
		],
		'elements' => [
		]
	];

	public function beforeFilter() {
		$this->Ajax->settings['modules'] = ['comments', 'records', 'attachments'];

		$this->Crud->enable(['add', 'edit', 'history', 'restore', 'delete', 'trash']);

		parent::beforeFilter();

		$this->title = __('Compliance Analysis');
		$this->subTitle = __('Manage your compliance requirement by mapping controls, risks, exceptions, projects for each Compliance Package Item you have previously defined.');
	}

	// public function _afterPaginate(CakeEvent $event) {
	// 	$this->attachStats($event->subject->items);
	// }

	public function index() {
		// if (empty($this->request->query['advanced_filter'])
		// 	&& empty($this->request->data['ComplianceManagement']['advanced_filter'])
		// ) {
		// 	$this->Crud->useModel('ThirdParty');
		// 	$this->Crud->on('afterPaginate', array($this, '_afterPaginate'));
		// }

		// $this->Crud->enable(['index']);

		// $this->Paginator->settings['conditions'] = [
		// 	'ThirdParty.id' => $this->getFilteredThirdPartyIds()
		// ];

		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		// $this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');
		$this->Crud->addListener('ObjectStatus', 'ObjectStatus.ObjectStatus');

		return $this->Crud->execute();
	}

	private function getFilteredThirdPartyIds() {
		$this->loadModel('ThirdParty');

		// we get the list of Third Party IDs to fetch paginated results from
		$thirdParties = $this->ThirdParty->find('all', array(
			'conditions' => [
				'ThirdParty.third_party_type_id' => ThirdParty::TYPE_REGULATORS
			],
			'fields' => array(
				'ThirdParty.id'
			),
			'contain' => array(
				'CompliancePackage' => array(
					'fields' => array('id'),
					'CompliancePackageItem' => array(
						'fields' => array('id')
					)
				)
			),
			'order' => array( 'ThirdParty.id' => 'ASC' )
		));

		$thirdParties = $this->ComplianceManagement->filterComplianceData($thirdParties);
		
		$thirdPartyIds = Hash::extract($thirdParties, '{n}.ThirdParty.id');

		return $thirdPartyIds;
	}

	private function attachStats(&$data) {
		foreach ($data as $key => $item) {
			$stats = [];

			$conditions = $this->AdvancedFilters->buildConditions('ComplianceManagement', [
				'third_party' => [$item['ThirdParty']['id']],
				'third_party__comp_type' => AbstractQuery::COMPARISON_IN,
			]);
			$stats['compliance_management_count'] = $this->ComplianceManagement->find('count', ['conditions' => $conditions]);

			$conditions = $this->AdvancedFilters->buildConditions('ComplianceManagement', [
				'third_party' => [$item['ThirdParty']['id']],
				'third_party__comp_type' => AbstractQuery::COMPARISON_IN,
				'compliance_treatment_strategy_id' => [ComplianceTreatmentStrategy::STRATEGY_COMPLIANT],
				'compliance_treatment_strategy_id__comp_type' => AbstractQuery::COMPARISON_IN
			]);
			$stats['compliance_mitigate'] = $this->ComplianceManagement->find('count', ['conditions' => $conditions]);

			$conditions = $this->AdvancedFilters->buildConditions('ComplianceManagement', [
				'third_party' => [$item['ThirdParty']['id']],
				'third_party__comp_type' => AbstractQuery::COMPARISON_IN,
				'compliance_treatment_strategy_id' => [0],
				'compliance_treatment_strategy_id__comp_type' => AbstractQuery::COMPARISON_IS_NULL,
			]);
			$stats['compliance_overlooked'] = $this->ComplianceManagement->find('count', ['conditions' => $conditions]);

			$conditions = $this->AdvancedFilters->buildConditions('ComplianceManagement', [
				'third_party' => [$item['ThirdParty']['id']],
				'third_party__comp_type' => AbstractQuery::COMPARISON_IN,
				'compliance_treatment_strategy_id' => [ComplianceTreatmentStrategy::STRATEGY_NOT_APPLICABLE],
				'compliance_treatment_strategy_id__comp_type' => AbstractQuery::COMPARISON_IN
			]);
			$stats['compliance_not_applicable'] = $this->ComplianceManagement->find('count', ['conditions' => $conditions]);

			$conditions = $this->AdvancedFilters->buildConditions('ComplianceManagement', [
				'third_party' => [$item['ThirdParty']['id']],
				'third_party__comp_type' => AbstractQuery::COMPARISON_IN,
				'compliance_treatment_strategy_id' => [ComplianceTreatmentStrategy::STRATEGY_NOT_COMPLIANT],
				'compliance_treatment_strategy_id__comp_type' => AbstractQuery::COMPARISON_IN
			]);
			$stats['compliance_not_compliant'] = $this->ComplianceManagement->find('count', ['conditions' => $conditions]);

			$conditions = $this->AdvancedFilters->buildConditions('ComplianceManagement', [
				'third_party' => [$item['ThirdParty']['id']],
				'third_party__comp_type' => AbstractQuery::COMPARISON_IN,
				'security_service_audits_last_missing' => 1,
			]);
			$stats['compliance_without_controls'] = $this->ComplianceManagement->find('count', ['conditions' => $conditions]);

			$conditions = $this->AdvancedFilters->buildConditions('ComplianceManagement', [
				'third_party' => [$item['ThirdParty']['id']],
				'third_party__comp_type' => AbstractQuery::COMPARISON_IN,
				'security_service_audits_last_not_passed' => 1,
			]);
			$stats['failed_controls'] = $this->ComplianceManagement->find('count', ['conditions' => $conditions]);

			$conditions = $this->AdvancedFilters->buildConditions('ComplianceManagement', [
				'third_party' => [$item['ThirdParty']['id']],
				'third_party__comp_type' => AbstractQuery::COMPARISON_IN,
				'security_policy_expired_reviews' => 1,
			]);
			$stats['missing_review'] = $this->ComplianceManagement->find('count', ['conditions' => $conditions]);

			$conditions = $this->AdvancedFilters->buildConditions('ComplianceManagement', [
				'third_party' => [$item['ThirdParty']['id']],
				'third_party__comp_type' => AbstractQuery::COMPARISON_IN,
				'asset_id' => [0],
				'asset_id__comp_type' => AbstractQuery::COMPARISON_IS_NOT_NULL,
			]);
			$stats['assets'] = $this->ComplianceManagement->find('count', ['conditions' => $conditions]);

			$data[$key]['stats'] = $stats;
		}
	}

	public function _afterPaginateAnalyze(CakeEvent $event) {
		if (!empty($event->subject->items)) {
			$event->subject->items = $event->subject->items[0];
			$this->title = __('Compliance Analysis:') . ' ' . $event->subject->items['ThirdParty']['name'];
		}
	}

	public function _beforeAnalyzeRender(CakeEvent $e) {
		$this->set('appetiteMethod', ClassRegistry::init('RiskAppetite')->getCurrentType());
	}

	private function getAnalyzeData($tp_id) {
		$this->loadModel( 'ThirdParty' );

		return $this->ThirdParty->find( 'first', array(
			'conditions' => array(
				'ThirdParty.id' => $tp_id
			),
			'fields' => array( 'ThirdParty.id', 'ThirdParty.name' ),
			'contain' => array(
				'CompliancePackage' => array(
					'CompliancePackageItem' => array(
						'order' => array('CompliancePackageItem.item_id' => 'ASC'),
						'ComplianceManagement' => array(
							'fields' => array('*'),
							'SecurityService'/* => array(
								'fields' => array( 'id', 'name' )
							)*/,
							'SecurityPolicy'/* => array(
								'fields' => array( 'id', 'index', 'status' )
							)*/,
							'ComplianceException'/* => array(
								'fields' => '*'
							)*/,
							'Risk'/* => array(
								'fields' => array( 'id', 'title' )
							)*/,
							'ThirdPartyRisk'/* => array(
								'fields' => array( 'id', 'title' )
							)*/,
							'BusinessContinuity'/* => array(
								'fields' => array( 'id', 'title' )
							)*/,
							'Legal'/* => array(
								'fields' => array( 'name' )
							)*/,
							'Project',
							'Owner',
							'Asset',
						),
						'Attachment' => array(
							'fields' => array( 'id' )
						),
						'Comment'  => array(
							'fields' => array( 'id' )
						)
					)
				)
			),
			'recursive' => 2
		) );
	}

	private function initAnalyzeOptions() {
		$strategies = $this->ComplianceManagement->getStrategies();

		$exceptions = $this->ComplianceManagement->ComplianceException->find('list', array(
			'order' => array('ComplianceException.title' => 'ASC'),
			'recursive' => -1
		));

		$this->set( 'strategies', $strategies );
		$this->set( 'exceptions', $exceptions );

		return array(
			'strategies' => $strategies,
			'exceptions' => $exceptions
		);
	}

	public function add($compliance_package_item_id = null) {
		$this->title = __('Create a Compliance Package');
		$this->initAddEditSubtitle();

		// $tmp = $this->ComplianceManagement->CompliancePackageItem->find('first', array(
		// 	'conditions' => array(
		// 		'CompliancePackageItem.id' => $compliance_package_item_id
		// 	),
		// 	'recursive' => 0
		// ));

		// $this->set('data', $tmp);

		// $this->set('compliance_package_item_id', $compliance_package_item_id);

		// $this->initOptions();

		return $this->Crud->execute();
	}

	public function edit($id = null) {
		$this->title = __('Edit a Compliance Package');
		$this->initAddEditSubtitle();

		$this->Crud->on('beforeRender', array($this, '_beforeEditRender'));

		return $this->Crud->execute();
	}

	public function _beforeEditRender(CakeEvent $e) {
		$subject = $e->subject;
		$controller = $subject->controller;
		$model = $subject->model;
		$request = $subject->request;

		if (isset($request->params['pass'][0])) {
			$id = $request->params['pass'][0];

			$complianceManagement = $model->find('first', [
				'conditions' => [
					'ComplianceManagement.id' => $id
				],
				'fields' => [
					'compliance_package_item_id'
				],
				'recursive' => -1
			]);

			$compliancePackageItem = $model->CompliancePackageItem->find('first', [
				'conditions' => [
					'CompliancePackageItem.id' => $complianceManagement['ComplianceManagement']['compliance_package_item_id']
				],
				'recursive' => -1
			]);

			$controller->set(compact('compliancePackageItem'));
		}
	}

	/**
	 * Initialize options for join elements.
	 */
	private function initOptions() {
		$security_services = $this->ComplianceManagement->SecurityService->find('list', array(
			'conditions' => array(
				'SecurityService.security_service_type_id' => SECURITY_SERVICE_PRODUCTION
			),
			'order' => array('SecurityService.name' => 'ASC'),
			'recursive' => -1
		));

		$security_policies = $this->getSecurityPoliciesList();

		$this->set( 'security_services', $security_services );
		$this->set( 'security_policies', $security_policies );
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('You can manage your compliance requirement by mapping controls, risks, exceptions, projects for each Compliance Package Item you have previously defined');
	}

	public function getPolicies() {
		$this->allowOnlyAjax();
		$this->autoRender = false;

		$securityServiceIds = json_decode($this->request->query['securityServiceIds']);
		$data = $this->ComplianceManagement->SecurityService->getSecurityPolicyIds($securityServiceIds);

		echo json_encode($data);
	}

	public function delete($id = null)
	{
		return $this->Crud->execute();
	}

	public function history($id)
	{
		return $this->Crud->execute();
	}

	public function restore($autidId)
	{
		return $this->Crud->execute();
	}

}
