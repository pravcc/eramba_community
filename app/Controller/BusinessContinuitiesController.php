<?php
App::uses('AppController', 'Controller');
App::uses('RiskAppetitesHelper', 'View/Helper');
App::uses('RiskClassification', 'Model');

/**
 * @section
 */
class BusinessContinuitiesController extends AppController {

	public $helpers = ['UserFields.UserField'];
	public $components = [
		'Search.Prg', 'AdvancedFilters', 'Paginator', 'Pdf', 'Paginator', 'ObjectStatus.ObjectStatus',
		'CustomValidator.CustomValidator',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				]
			],
			'listeners' => [
				'Api', 'ApiPagination', '.Risks', 'BulkActions.BulkActions', 'Widget.Widget',
				'Visualisation.Visualisation',
				'Taggable.Taggable' => [
					'fields' => ['Tag']
				],
				'.ModuleDispatcher' => [
					'listeners' => [
						'NotificationSystem.NotificationSystem',
						'CustomFields.CustomFields',
						'Reports.Reports',
					]
				]
			]
		],
		// 'Visualisation.Visualisation',
		'ReviewsPlanner.Reviews',
		'UserFields.UserFields' => [
			'fields' => ['Owner', 'Stakeholder']
		],
		'RisksManager'
	];

	public function beforeFilter() {
		$this->Auth->allow('processClassifications');
		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'trash', 'history', 'restore']);

		parent::beforeFilter();

		$this->title = __('Business Continuities');
		$this->subTitle = __('Manage all business risks in the scope of your GRC program');

		$this->set('appetiteMethod', ClassRegistry::init('RiskAppetite')->getCurrentType());
	}

	public function index($view = 'listBusinessContinuities') {
		$this->Crud->on('afterPaginate', array($this, '_afterPaginate'));
		$this->title = __('Business Based Risk Management');

		$this->set('view', $view);

		$this->setIndexData();

		$this->Crud->on('beforePaginate', function(CakeEvent $event)
		{
			$event->subject->paginator->settings['contain']['BusinessContinuityPlan'] = $this->UserFields->attachFieldsToArray(['LaunchInitiator', 'Sponsor', 'Owner'], [
				'fields' => [
					'id', 'title', 'security_service_type_id', 'audits_all_done', 'audits_last_passed', 'audits_last_missing', 'ongoing_corrective_actions', 'audits_improvements'
				],
				'SecurityServiceType',
				'BusinessContinuityTask' => [
					'BusinessContinuityTaskReminder'
				]
			], 'BusinessContinuityPlan');
		});

		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function _afterPaginate(CakeEvent $event) {
		$model = $event->subject->model;
		foreach ($event->subject->items as $key => $item) {
			$event->subject->items[$key]['risk_appetite_threshold'] = [
				RiskClassification::TYPE_ANALYSIS => $model->riskThreshold(
					$item[$model->alias]['id'],
					RiskClassification::TYPE_ANALYSIS
				),
				RiskClassification::TYPE_TREATMENT => $model->riskThreshold(
					$item[$model->alias]['id'],
					RiskClassification::TYPE_TREATMENT
				),
			];
		}
	}

	private function setIndexData() {
		$this->set('riskClassificationData', $this->BusinessContinuity->RiskClassification->getAllData('BusinessContinuity'));

		$securityServicesData = $this->BusinessContinuity->getAllHabtmData('SecurityService', array(
			'contain' => $this->UserFields->attachFieldsToArray(['ServiceOwner'], [
				'SecurityServiceType'
			], 'SecurityService')
		));

		$riskExceptionData = $this->BusinessContinuity->getAllHabtmData('RiskException', array(
			'contain' => $this->UserFields->attachFieldsToArray(['Requester'], [], 'RiskException')
		));

		$this->set('riskExceptionData', $riskExceptionData);
		$this->set('securityServicesData', $securityServicesData);
	}

	public function _afterDelete(CakeEvent $event) {
		if ($event->subject->success) {
			$this->deleteJoins($event->subject->id);
		}
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Business Continuity.');

		$this->Crud->on('afterDelete', array($this, '_afterDelete'));

		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Create a Business Continuity');
		$this->initAddEditSubtitle();

		$this->Crud->on('beforeSave', array($this, '_beforeSave'));
		$this->Crud->on('beforeRender', array($this, '_beforeAddEditRender'));

		$this->initOptions();

		return $this->Crud->execute();
	}

	public function _beforeAddEditRender(CakeEvent $event) {
		$processIds = [];
		if (isset($this->request->data['BusinessContinuity']['Process'])) {
			$processIds = $this->request->data['BusinessContinuity']['Process'];
		}
		elseif (isset($this->request->data['Process'])) {
			$processIds = Hash::extract($this->request->data['Process'], '{n}.id');
		}

		$processes = [];
		if (is_array($processIds)) {
			$processes = $this->BusinessContinuity->Process->find('all', array(
				'conditions' => array(
					'Process.id' => $processIds
				),
				'fields' => array('Process.id', 'Process.name', 'Process.rto', 'Process.rpo', 'Process.rpd'),
				'recursive' => -1
			));
		}

		$this->_additionalClassificationOptions($processes);
	}

	public function _beforeSave(CakeEvent $event) {
		$this->request->data['BusinessContinuity']['RiskClassification'] = $this->fixClassificationIds();
	}

	public function edit($id = null) {
		$id = (int) $id;

		$this->title = __('Edit a Business Continuity');
		$this->initAddEditSubtitle();

		$this->Crud->on('beforeSave', array($this, '_beforeSave'));
		$this->Crud->on('beforeRender', array($this, '_beforeAddEditRender'));

		$this->initOptions();

		return $this->Crud->execute();
	}

	public function trash() {
	    $this->set( 'title_for_layout', __( 'Business Continuity (Trash)' ) );
	    $this->set( 'subtitle_for_layout', __( 'This is the list of business continuities.' ) );

	    $this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

	    return $this->Crud->execute();
	}

	private function invalidateDependencies() {
		if ($this->request->data['BusinessContinuity']['risk_mitigation_strategy_id'] == RISK_MITIGATION_ACCEPT) {
			if (empty($this->request->data['BusinessContinuity']['RiskException'])) {
				$this->BusinessContinuity->invalidate('RiskException', __('This field cannot be left blank'));
			}
			unset($this->BusinessContinuity->validate['SecurityService']);
		}

		if ($this->request->data['BusinessContinuity']['risk_mitigation_strategy_id'] == RISK_MITIGATION_AVOID) {
			if (empty($this->request->data['BusinessContinuity']['RiskException'])) {
				$this->BusinessContinuity->invalidate('RiskException', __('This field cannot be left blank'));
			}
			unset($this->BusinessContinuity->validate['SecurityService']);
		}

		if ($this->request->data['BusinessContinuity']['risk_mitigation_strategy_id'] == RISK_MITIGATION_MITIGATE) {
			if (empty($this->request->data['BusinessContinuity']['SecurityService'])) {
				$this->BusinessContinuity->invalidate('SecurityService', __('This field cannot be left blank'));
			}
			unset($this->BusinessContinuity->validate['RiskException']);
		}

		if ($this->request->data['BusinessContinuity']['risk_mitigation_strategy_id'] == RISK_MITIGATION_TRANSFER) {
			if (empty($this->request->data['BusinessContinuity']['RiskException'])) {
				$this->BusinessContinuity->invalidate('RiskException', __('This field cannot be left blank'));
			}
			unset($this->BusinessContinuity->validate['SecurityService']);
		}
	}

	private function fixClassificationIds() {
		$tmp = array();
		if (!empty($this->request->data['BusinessContinuity']['RiskClassification'])) {
			foreach ( $this->request->data['BusinessContinuity']['RiskClassification'] as $classification_id ) {
				if ( $classification_id ) {
					$tmp[] = $classification_id;
				}
			}
		}

		return $tmp;
	}

	public function processClassifications()
	{
		$classData = $this->RisksManager->getDataToSet($this->request->query['classModel']);
		$this->set($classData);
		$this->set($this->request->query);
		$this->initOptions();
		$this->set([
			'model' => 'BusinessContinuity'
		]);

		$processes = [];
		if (is_array($this->request->data['BusinessContinuity']['Process'])) {
			$processes = $this->BusinessContinuity->Process->find('all', array(
				'conditions' => array(
					'Process.id' => $this->request->data['BusinessContinuity']['Process']
				),
				'fields' => array('Process.id', 'Process.name', 'Process.rto', 'Process.rpo', 'Process.rpd'),
				'recursive' => -1
			));
		}

		$this->_additionalClassificationOptions($processes);
		

		$this->render('../Elements/risks/risk_classifications/classifications_ajax');
	}

	protected function _additionalClassificationOptions($processes)
	{
		$process = array();
		if(!empty($processes)){
			$rpd = 0;

			$varsSet = false;
			foreach ($processes as $p) {
				$proc = $p['Process'];

				if (!$varsSet) {
					$mto = $proc['rpo'];
					$rto = $proc['rto'];
					$varsSet = true;
				}

				$process[] = array(
					'id' => $proc['id'],
					'text' => $proc['name']
				);

				$rpd+= $proc['rpd'];
				$mto = ($mto>$proc['rpo'])?$proc['rpo']:$mto;
				$rto = ($rto>$proc['rto'])?$proc['rto']:$rto;
			}
		}

		$additionalDataToSet = [
			'process' => $process,
			'rpd' => (isset($rpd)?$rpd:0),
			'mto' => (isset($mto)?$mto:0),
			'rto' => (isset($rto)?$rto:0)
		];

		$this->set($additionalDataToSet);
	}

	public function getProcesses() {
		$this->YoonityJSConnector->deny();

		$this->allowOnlyAjax();
		$this->autoRender = false;

		$buIds = json_decode($this->request->query['buIds']);
		$data = array_values(ClassRegistry::init('BusinessUnit')->getProcesses($buIds));

		echo json_encode($data);
	}

	public function getThreatsVulnerabilities() {
		$this->allowOnlyAjax();
		$this->autoRender = false;

		$businessUnitIds = json_decode($this->request->data['assocIds']);
		$data = $this->BusinessContinuity->BusinessUnit->getThreatsVulnerabilities($businessUnitIds);

		echo json_encode($data);
	}

	public function getPolicies() {
		$this->allowOnlyAjax();
		$this->autoRender = false;

		$controlIds = json_decode($this->request->query['controlIds']);
		$data = $this->BusinessContinuity->SecurityService->getSecurityPolicyIds($controlIds);

		echo json_encode($data);
	}

	/**
	 * Delete all many to many joins in related tables.
	 * @param  integer $id Risk ID
	 */
	private function deleteJoins( $id ) {
		$ret = $this->BusinessContinuity->BusinessContinuitiesBusinessUnit->deleteAll( array(
			'BusinessContinuitiesBusinessUnit.business_continuity_id' => $id
		) );

		$ret &= $this->BusinessContinuity->BusinessContinuitiesThreat->deleteAll( array(
			'BusinessContinuitiesThreat.business_continuity_id' => $id
		) );

		$ret &= $this->BusinessContinuity->BusinessContinuitiesVulnerability->deleteAll( array(
			'BusinessContinuitiesVulnerability.business_continuity_id' => $id
		) );

		$ret &= $this->BusinessContinuity->BusinessContinuitiesSecurityService->deleteAll( array(
			'BusinessContinuitiesSecurityService.business_continuity_id' => $id
		) );

		$ret &= $this->BusinessContinuity->BusinessContinuitiesRiskException->deleteAll( array(
			'BusinessContinuitiesRiskException.business_continuity_id' => $id
		) );

		$ret &= $this->BusinessContinuity->BusinessContinuitiesRiskClassification->deleteAll( array(
			'BusinessContinuitiesRiskClassification.business_continuity_id' => $id
		) );

		$ret &= $this->BusinessContinuity->BusinessContinuitiesProcess->deleteAll( array(
			'BusinessContinuitiesProcess.business_continuity_id' => $id
		) );

		$ret &= $this->BusinessContinuity->BusinessContinuitiesBusinessContinuityPlan->deleteAll( array(
			'BusinessContinuitiesBusinessContinuityPlan.business_continuity_id' => $id
		) );

		$ret &= $this->BusinessContinuity->BusinessContinuitiesProjects->deleteAll( array(
			'BusinessContinuitiesProjects.business_continuity_id' => $id
		) );

		$ret &= $this->BusinessContinuity->RisksSecurityPolicy->deleteAll(array(
			'RisksSecurityPolicy.risk_id' => $id,
			'risk_type' => 'business-risk'
		));

		if ($ret) {
			return true;
		}

		return false;
	}

	/**
	 * Initialize options for join elements.
	 */
	public function initOptions() {
		$this->loadModel( 'RiskClassificationType' );

		$bcps = $this->BusinessContinuity->BusinessContinuityPlan->find('list', array(
			'conditions' => array(
				'BusinessContinuityPlan.security_service_type_id !=' => SECURITY_SERVICE_DESIGN
			),
			'order' => array('BusinessContinuityPlan.title' => 'ASC'),
			'recursive' => -1
		));

		$mitigate_id = RISK_MITIGATION_MITIGATE;

		$accept_id = RISK_MITIGATION_ACCEPT;

		$transfer_id = RISK_MITIGATION_TRANSFER;

		$this->set('classifications', $this->BusinessContinuity->getFormClassifications());
		$this->set('bcps', $bcps);
		$this->set('mitigate_id', $mitigate_id);
		$this->set('accept_id', $accept_id);
		$this->set('transfer_id', $transfer_id);
		$this->set('calculationMethod', $this->BusinessContinuity->getMethod());
	}

	private function initAddEditSubtitle() {
		$this->set( 'subtitle_for_layout', __( 'Manage your Business Impact Analysis framework including the analysis, treatment and communication of risks.' ) );
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
