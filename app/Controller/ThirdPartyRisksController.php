<?php
App::uses('AppController', 'Controller');
App::uses('RiskAppetitesHelper', 'View/Helper');
App::uses('RiskClassification', 'Model');

/**
 * @section
 */
class ThirdPartyRisksController extends AppController
{
	public $helpers = ['UserFields.UserField'];
	public $components = [
		'Search.Prg', 'AdvancedFilters', 'Pdf',  'Paginator', 'ObjectStatus.ObjectStatus',
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
				'Taggable.Taggable' => [
					'fields' => ['Tag']
				],
				'Visualisation.Visualisation',
				'.ModuleDispatcher' => [
					'listeners' => [
						'NotificationSystem.NotificationSystem',
						'CustomFields.CustomFields',
						'Reports.Reports',
					]
				]
			]
		],
		//'Visualisation.Visualisation',
		'ReviewsPlanner.Reviews',
		'UserFields.UserFields' => [
			'fields' => ['Owner', 'Stakeholder']
		],
		'RisksManager'
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
		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'trash', 'history', 'restore']);

		parent::beforeFilter();

		$this->title = __('Third Party Risks');
		$this->subTitle = __('Manage all asset risks in the scope of your GRC program.');

		$this->set('appetiteMethod', ClassRegistry::init('RiskAppetite')->getCurrentType());
	}

	public function index($view = 'listTPRisks') {
		// $this->Crud->on('afterPaginate', array($this, '_afterPaginate'));
		$this->title = __('Third Party based Risk Management');

		$this->set('view', $view);

		$this->setIndexData();

		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	private function setIndexData() {
		$this->set('riskClassificationData', $this->ThirdPartyRisk->RiskClassification->getAllData('ThirdPartyRisk'));
		$this->set('assetClassificationData', $this->ThirdPartyRisk->Asset->getAssetClassificationsData());
		
		$assetData = $this->ThirdPartyRisk->getAllHabtmData('Asset', array(
			'contain' => array(
				'RelatedAssets' => array(
					'fields' => array('id', 'name')
				),
				'Legal' => array(
					'fields' => array( 'id', 'name' )
				),
				'AssetLabel' => array(
					'fields' => array( 'name' )
				)
			)
		));

		$securityServicesData = $this->ThirdPartyRisk->getAllHabtmData('SecurityService', array(
			'contain' => $this->UserFields->attachFieldsToArray(['ServiceOwner'], [
				'SecurityServiceType'
			], 'SecurityService')
		));

		$riskExceptionData = $this->ThirdPartyRisk->getAllHabtmData('RiskException', array(
			'contain' => $this->UserFields->attachFieldsToArray(['Requester'], [], 'RiskException')
		));

		$this->set('assetData', $assetData);
		$this->set('riskExceptionData', $riskExceptionData);
		$this->set('securityServicesData', $securityServicesData);
	}

	public function _afterDelete(CakeEvent $event) {
		if ($event->subject->success) {
			$this->deleteJoins($event->subject->id);
		}
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Third Party Risk.');

		$this->Crud->on('afterDelete', array($this, '_afterDelete'));

		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Create a Third Party Risk');
		$this->initAddEditSubtitle();

		$this->Crud->on('beforeSave', array($this, '_beforeSave'));

		$this->initOptions();

		return $this->Crud->execute();
	}

	public function _beforeSave(CakeEvent $event) {
		$this->request->data['ThirdPartyRisk']['RiskClassification'] = $this->fixClassificationIds();

		$this->invalidateDependencies();
	}

	public function edit( $id = null ) {
		$id = (int) $id;

		$this->title = __('Edit a Third Party Risk');
		$this->initAddEditSubtitle();

		$this->Crud->on('beforeSave', array($this, '_beforeSave'));

		$this->initOptions();

		return $this->Crud->execute();
	}

	public function trash() {
	    $this->set( 'title_for_layout', __( 'Third Party Risks (Trash)' ) );
	    $this->set( 'subtitle_for_layout', __( 'This is the list of third party risks.' ) );

	    $this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');


	    return $this->Crud->execute();
	}

	private function invalidateDependencies() {
		if (isset($this->request->data['ThirdPartyRisk']['risk_mitigation_strategy_id'])) {
			if ($this->request->data['ThirdPartyRisk']['risk_mitigation_strategy_id'] == RISK_MITIGATION_ACCEPT) {
				if (empty($this->request->data['ThirdPartyRisk']['RiskException'])) {
					$this->ThirdPartyRisk->invalidate('RiskException', __('This field cannot be left blank'));
				}
				unset($this->ThirdPartyRisk->validate['SecurityService']);
			}

			if ($this->request->data['ThirdPartyRisk']['risk_mitigation_strategy_id'] == RISK_MITIGATION_AVOID) {
				if (empty($this->request->data['ThirdPartyRisk']['RiskException'])) {
					$this->ThirdPartyRisk->invalidate('RiskException', __('This field cannot be left blank'));
				}
				unset($this->ThirdPartyRisk->validate['SecurityService']);
			}

			if ($this->request->data['ThirdPartyRisk']['risk_mitigation_strategy_id'] == RISK_MITIGATION_MITIGATE) {
				if (empty($this->request->data['ThirdPartyRisk']['SecurityService'])) {
					$this->ThirdPartyRisk->invalidate('SecurityService', __('This field cannot be left blank'));
				}
				unset($this->ThirdPartyRisk->validate['RiskException']);
			}

			if ($this->request->data['ThirdPartyRisk']['risk_mitigation_strategy_id'] == RISK_MITIGATION_TRANSFER) {
				if (empty($this->request->data['ThirdPartyRisk']['RiskException'])) {
					$this->ThirdPartyRisk->invalidate('RiskException', __('This field cannot be left blank'));
				}
				unset($this->ThirdPartyRisk->validate['SecurityService']);
			}
		}
	}

	private function fixClassificationIds() {
		$tmp = array();
		if (isset($this->request->data['ThirdPartyRisk']['RiskClassification'])) {
			foreach ( $this->request->data['ThirdPartyRisk']['RiskClassification'] as $classification_id ) {
				if ( $classification_id ) {
					$tmp[] = $classification_id;
				}
			}
		}

		return $tmp;
	}

	/**
	 * Process classification fields via ajax request.
	 * 
	 * @return void
	 */
	public function processClassifications()
	{
		$classData = $this->RisksManager->getDataToSet($this->request->query['classModel']);
		$this->set($classData);
		$this->set($this->request->query);
		$this->initOptions();
		$this->set([
			'model' => 'ThirdPartyRisk'
		]);

		$this->render('../Elements/risks/risk_classifications/classifications_ajax');
	}

	/**
	 * Delete all many to many joins in related tables.
	 * @param  integer $id Risk ID
	 */
	private function deleteJoins($id) {
		$ret = $this->ThirdPartyRisk->ThirdPartiesThirdPartyRisk->deleteAll( array(
			'ThirdPartiesThirdPartyRisk.third_party_risk_id' => $id
		) );

		$ret &= $this->ThirdPartyRisk->AssetsThirdPartyRisk->deleteAll( array(
			'AssetsThirdPartyRisk.third_party_risk_id' => $id
		) );

		$ret &= $this->ThirdPartyRisk->ThirdPartyRisksThreat->deleteAll( array(
			'ThirdPartyRisksThreat.third_party_risk_id' => $id
		) );

		$ret &= $this->ThirdPartyRisk->ThirdPartyRisksVulnerability->deleteAll( array(
			'ThirdPartyRisksVulnerability.third_party_risk_id' => $id
		) );

		$ret &= $this->ThirdPartyRisk->SecurityServicesThirdPartyRisk->deleteAll( array(
			'SecurityServicesThirdPartyRisk.third_party_risk_id' => $id
		) );

		$ret &= $this->ThirdPartyRisk->RiskExceptionsThirdPartyRisk->deleteAll( array(
			'RiskExceptionsThirdPartyRisk.third_party_risk_id' => $id
		) );

		$ret &= $this->ThirdPartyRisk->RiskClassificationsThirdPartyRisk->deleteAll( array(
			'RiskClassificationsThirdPartyRisk.third_party_risk_id' => $id
		) );

		$ret &= $this->ThirdPartyRisk->ProjectsThirdPartyRisk->deleteAll( array(
			'ProjectsThirdPartyRisk.third_party_risk_id' => $id
		) );

		$ret &= $this->ThirdPartyRisk->RisksSecurityPolicy->deleteAll(array(
			'RisksSecurityPolicy.risk_id' => $id,
			'risk_type' => 'third-party-risk'
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

		$calculationMethod = $this->ThirdPartyRisk->getMethod();

		$mitigate_id = RISK_MITIGATION_MITIGATE;

		$accept_id = RISK_MITIGATION_ACCEPT;

		$transfer_id = RISK_MITIGATION_TRANSFER;
		
		$this->set('calculationMethod', $this->ThirdPartyRisk->getMethod());
		$this->set('classifications', $this->ThirdPartyRisk->getFormClassifications());
		$this->set('mitigate_id', $mitigate_id);
		$this->set('accept_id', $accept_id);
		$this->set('transfer_id', $transfer_id);
	}

	private function initAddEditSubtitle() {
		$this->subTitle  = __('Manage your Third Party based risk management framework including the analysis, treatment and communication of risks.');
	}

	public function getPolicies() {
		$this->allowOnlyAjax();
		$this->autoRender = false;

		$controlIds = json_decode($this->request->query['controlIds']);
		$data = $this->ThirdPartyRisk->SecurityService->getSecurityPolicyIds($controlIds);

		echo json_encode($data);
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
