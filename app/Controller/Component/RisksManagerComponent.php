<?php

App::uses('Component', 'Controller');
App::uses('Group', 'Model');
App::uses('RiskClassification', 'Model');

/**
 * Component to manage Risk sections.
 */
class RisksManagerComponent extends Component {
	public $components = ['Session', 'Crud'];
	public $settings = [
	];

	/**
	 * Runtime configuration values.
	 * 
	 * @var array
	 */
	protected $_runtime = [
	];

	/**
	 * Reference to the current event manager.
	 *
	 * @var CakeEventManager
	 */
	// protected $_eventManager;

	public function __construct(ComponentCollection $collection, $settings = array()) {
		if (empty($this->settings)) {
			$this->settings = array(
			);
		}

		$settings = array_merge($this->settings, (array)$settings);
		parent::__construct($collection, $settings);

		$this->_runtime = $this->settings;
	}

	public function initialize(Controller $controller) {
		$this->controller = $controller;
		$this->request = $this->controller->request;
	}

	/**
	 * Risk model class name for the current controller.
	 * 
	 * @return string
	 */
	public function modelClass() {
		return $this->Crud->getSubject()->modelClass;
	}

	/**
	 * Risk model object for the current controller.
	 * 
	 * @return object Instance of BaseRisk class.
	 */
	public function model() {
		return $this->Crud->getSubject()->model;
	}

	/**
	 * Process classification fields via ajax request.
	 * 
	 * @return void
	 */
	public function processClassifications()
	{
		// $this->controller->allowOnlyAjax();
		$this->controller->autoRender = false;

		$setData = $this->getDataToSet();
		$this->controller->set($setData);
		$this->controller->initOptions();

		$this->validateClassification($setData);

		return $this->renderElement();
	}

	/**
	 * Validate classifications.
	 * 
	 * @param array $setData Data from getDataToSet().
	 * @return void
	 */
	public function validateClassification($setData)
	{
		$type = $setData['type'] == RiskClassification::TYPE_ANALYSIS ? 'RiskClassification' : 'RiskClassificationTreatment';

		$this->model()->set([
			$this->model()->alias => [
				$type => $setData['classificationIds']
			]
		]);

		$this->model()->validates([
			'fieldList' => [$type]
		]);
	}

	/**
	 * Generic method to render classifications element to show/manage/calculate risk classifications.
	 * 
	 * @return View
	 */
	public function renderElement()
	{
		return $this->controller->render('../Elements/risks/risk_classifications/classifications_ajax');
	}

	/**
	 * Get the array of data that need to be set to manage classifications and risk calculation.
	 *
	 * @param  string $classificationModel Type of classifications, either RiskClassification or
	 *                                     RiskClassificationTreatment
	 * @return array Array of data
	 */
	public function getDataToSet($classificationModel)
	{
		// Risk or ThirdPartyRisk or BusinessContinuity
		$modelAlias = $this->model()->alias;
		$scoreAssocModel = $this->model()->scoreAssocModel;

		$relatedItemIds = [];
		if (isset($this->request->data[$modelAlias][$scoreAssocModel])) {
			$relatedItemIds = $this->request->data[$modelAlias][$scoreAssocModel];
		}
		elseif (isset($this->request->data[$scoreAssocModel])) {
			$relatedItemIds = Hash::extract($this->request->data[$scoreAssocModel], '{n}.id');
		}
		// $relatedItemIds = json_decode($this->request->query['relatedItemIds']); 
		// $this->request->query['relatedItemIds'] = $relatedItemIds;

		if (empty($relatedItemIds)) {
			$this->controller->set('classificationsNotPossible', true);
		}

		$classificationIds = [];
		if (isset($this->request->data[$modelAlias][$classificationModel])) {
			$classificationIds = $this->request->data[$modelAlias][$classificationModel];
		}
		elseif (isset($this->request->data[$classificationModel])) {
			$classificationIds = Hash::extract($this->request->data[$classificationModel], '{n}.id');
		}

		// $classificationIds = json_decode($this->request->query['classificationIds']);
		if (empty($classificationIds)) {
			for ($i = 0; $i <= count($this->model()->getFormClassifications()); $i++) {
				$classificationIds[$i] = "";
			}
		}

		// $this->request->query['classificationIds'] = $classificationIds;

		if (method_exists($this->model(), 'isMageritPossible')) {
			$this->controller->set('isMageritPossible', $this->model()->isMageritPossible($relatedItemIds));
		}

		$setData = $this->model()->getRiskCalculationData($classificationIds, $relatedItemIds);
		// $data = array_merge($setData, $this->request->query);

		return $setData;
	}
}
