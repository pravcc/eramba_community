<?php
App::uses('AppController', 'Controller');
App::uses('AdvancedFiltersComponent', 'Controller/Component');
App::uses('RiskClassification', 'Model');
App::uses('RiskCalculation', 'Model');

/**
 * @section
 */
class RisksController extends AppController
{
	public $helpers = ['UserFields.UserField'];
	public $components = array(
		'Search.Prg', 'AdvancedFilters', 'Paginator', 'Pdf', 'ObjectStatus.ObjectStatus',
		'CustomValidator.CustomValidator',
		'Ajax' => array(
			'actions' => array('add', 'edit', 'delete'),
			'modules' => array('comments', 'records', 'attachments', 'notifications')
		),
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				],
				'residualRisk' => [
					'className' => 'AppEdit',
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
		//'Visualisation.Visualisation',
		'ReviewsPlanner.Reviews',
		'UserFields.UserFields' => [
			'fields' => ['Owner', 'Stakeholder']
		],
		'RisksManager'
	);

	protected $_appControllerConfig = [
		'components' => [
		],
		'helpers' => [
		],
		'elements' => [
		]
	];

	public function beforeFilter() {
		$this->Auth->allow('processClassifications');
		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'trash', 'history', 'restore']);

		parent::beforeFilter();

		$this->title = __('Asset based Risk Management');
		$this->subTitle = __('Manage all asset risks in the scope of your GRC program');
		
		$this->set('appetiteMethod', ClassRegistry::init('RiskAppetite')->getCurrentType());

		$this->Crud->on('beforeRender', array($this, '_beforeRender'));
	}

	/**
	 * Section callback to set additional variables and options for an action.
	 */
	public function _beforeRender(CakeEvent $event) {
		//index
		// $this->setIndexData();
		
		//add/edit
		$this->initOptions();
	}

	public function index( $view = 'listRisks' ) {
		// $this->Crud->on('afterPaginate', array($this, '_afterPaginate'));
		$this->set('view', $view);

		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	private function setIndexData() {
		$this->set('riskClassificationData', $this->Risk->RiskClassification->getAllData('Risk'));
		$this->set('assetClassificationData', $this->Risk->Asset->getAssetClassificationsData());

		$assetData = $this->Risk->getAllHabtmData('Asset', array(
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

		$securityServicesData = $this->Risk->getAllHabtmData('SecurityService', array(
			'contain' => $this->UserFields->attachFieldsToArray(['ServiceOwner'], [
				'SecurityServiceType'
			], 'SecurityService')
		));

		$riskExceptionData = $this->Risk->getAllHabtmData('RiskException', array(
			'contain' => $this->UserFields->attachFieldsToArray(['Requester'], [], 'RiskException')
		));

		$this->set('assetData', $assetData);
		$this->set('riskExceptionData', $riskExceptionData);
		$this->set('securityServicesData', $securityServicesData);
	}

	public function delete($id = null) {
		$this->title =  __('Risk');
		$this->subTitle = __('Delete a Risk.');
		return $this->Crud->execute();
	}

	public function add() {
		$this->title =  __('Create a Risk');
		$this->subTitle = __('Manage your Asset Based risk management framework including the analysis, treatment and communication of risks.');

		return $this->Crud->execute();
	}

	public function edit( $id = null ) {
		$this->title =  __('Edit a Risk');
		$this->subTitle = __('Manage your Asset Based risk management framework including the analysis, treatment and communication of risks.');

		return $this->Crud->execute();
	}

	public function trash() {
	    $this->set('title_for_layout', __('Risks (Trash)'));

	    $this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

	    return $this->Crud->execute();
	}

	private function setValidationErrorFlash() {
		$errorMsg = __('One or more inputs you entered are invalid. Please try again.');
		if (!empty($this->Risk->validationErrors['risk_score'])) {
			$errorMsg = implode('<br />', $this->Risk->validationErrors['risk_score']);
		}

		$this->Session->setFlash($errorMsg, FLASH_ERROR);
	}

	private function invalidateDependencies() {
		if ($this->request->data['Risk']['risk_mitigation_strategy_id'] == RISK_MITIGATION_ACCEPT) {
			if (empty($this->request->data['Risk']['RiskException'])) {
				$this->Risk->invalidate('RiskException', __('This field cannot be left blank'));
			}
			unset($this->Risk->validate['SecurityService']);
		}

		if ($this->request->data['Risk']['risk_mitigation_strategy_id'] == RISK_MITIGATION_AVOID) {
			if (empty($this->request->data['Risk']['RiskException'])) {
				$this->Risk->invalidate('RiskException', __('This field cannot be left blank'));
			}
			unset($this->Risk->validate['SecurityService']);
		}

		if ($this->request->data['Risk']['risk_mitigation_strategy_id'] == RISK_MITIGATION_MITIGATE) {
			if (empty($this->request->data['Risk']['SecurityService'])) {
				$this->Risk->invalidate('SecurityService', __('This field cannot be left blank'));
			}
			unset($this->Risk->validate['RiskException']);
		}

		if ($this->request->data['Risk']['risk_mitigation_strategy_id'] == RISK_MITIGATION_TRANSFER) {
			if (empty($this->request->data['Risk']['RiskException'])) {
				$this->Risk->invalidate('RiskException', __('This field cannot be left blank'));
			}
			unset($this->Risk->validate['SecurityService']);
		}
	}

	private function fixClassificationIds() {
		$tmp = array();
		if (!empty($this->request->data['Risk']['RiskClassification'])) {
			foreach ( $this->request->data['Risk']['RiskClassification'] as $classification_id ) {
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
			'model' => 'Risk'
		]);

		$this->render('../Elements/risks/risk_classifications/classifications_ajax');
	}

	public function calculateRiskScoreAjax() {
		$this->YoonityJSConnector->deny();

		// $this->allowOnlyAjax();
		$this->autoRender = false;

		$assetIds = json_decode($this->request->data['relatedItemIds']);
		$classificationIds = json_decode($this->request->data['classificationIds']);

		$riskScore = $this->Risk->calculateRiskScore($classificationIds, $assetIds);
		$appetiteThreshold = $this->Risk->RiskClassification->getRiskAppetiteThreshold($classificationIds);

		echo json_encode(array(
			'riskScore' => $riskScore,
			'riskAppetite' => RISK_APPETITE,
			'riskCalculationMath' => $this->Risk->getCalculationMath(),
			'otherData' => $this->Risk->getOtherData(),
			'classificationCriteria' => $this->Risk->RiskClassification->getRiskCriteria($classificationIds),
			'riskAppetiteThreshold' => [
				'data' => $appetiteThreshold,
				'class' => RiskAppetitesHelper::colorClasses($appetiteThreshold['color'])
			]
		));
	}

	public function getThreatsVulnerabilities() {
		$this->YoonityJSConnector->deny();

		$this->allowOnlyAjax();
		$this->autoRender = false;

		$assetIds = json_decode($this->request->data['assocIds']);
		$data = $this->Risk->Asset->getThreatsVulnerabilities($assetIds);

		echo json_encode($data);
	}

	public function getPolicies() {
		$this->YoonityJSConnector->deny();

		$this->allowOnlyAjax();
		$this->autoRender = false;

		$controlIds = json_decode($this->request->query['controlIds']);
		$data = $this->Risk->SecurityService->getSecurityPolicyIds($controlIds);

		echo json_encode($data);
	}

	/**
	 * Initialize options for join elements.
	 */
	public function initOptions() {
		$mitigate_id = RISK_MITIGATION_MITIGATE;
		$accept_id = RISK_MITIGATION_ACCEPT;
		$transfer_id = RISK_MITIGATION_TRANSFER;
		
		$this->set( 'mitigate_id', $mitigate_id );
		$this->set( 'accept_id', $accept_id );
		$this->set( 'transfer_id', $transfer_id );
		$this->set('calculationMethod', $this->Risk->getMethod());
		$this->set( 'classifications', $this->Risk->getFormClassifications() );

		// if (isset($this->request->data['Risk']['RiskClassification'])) {
		// 	$classificationIds = json_decode($this->request->data['Risk']['RiskClassification']);
		// 	$this->set('classificationCriteria',$this->Risk->RiskClassification->getRiskCriteria($classificationIds));
		// }

		$this->_initAdditionalOptions();
	}

	/**
	 * Additional Risk score data thats used for magerit calculation
	 * 
	 * @return void
	 */
	protected function _initAdditionalOptions()
	{
		$sectionValues = $this->Risk->getSectionValues();
		$calculationValues = $this->Risk->getClassificationTypeValues($sectionValues);
		$calculationMethod = $this->Risk->getMethod();

		if ($calculationMethod == RiskCalculation::METHOD_MAGERIT) {
			$specialClassificationTypeUsed = $calculationValues[1];
			$specialClassificationTypeData = $this->Risk->RiskClassification->RiskClassificationType->find('first', array(
				'conditions' => array(
					'RiskClassificationType.id' => $specialClassificationTypeUsed
				),
				'order' => array('RiskClassificationType.name' => 'ASC'),
				'recursive' => 1
			));
			$this->set('specialClassificationTypeData', $specialClassificationTypeData);
		}
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
