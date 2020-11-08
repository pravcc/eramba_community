<?php
App::uses('RiskAppetite', 'Model');

class RiskAppetitesController extends AppController {
	public $helpers = array( 'Html', 'Form' );
	public $components = array( 'Session', 'Paginator', 'Ajax' => array(
			'actions' => array('edit')
		),
		// 'redirects' => array(
		// 	'index' => array(
		// 		'url' => array('controller' => 'risks', 'action' => 'index')
		// 	)
		// ),
		'Crud.Crud' => [
			'listeners' => [
				'Crud.DebugKit'
			],
			// actions disabled by default
			'actions' => [
				'edit' => [
					'enabled' => true,
					'className' => 'AppEdit',
					'saveOptions' => [
						'deep' => true
					],
					'saveMethod' => 'saveAssociated',
					'saveAssociatedHandler' => false,
					'view' => 'edit'
				]
			]
		]
	);

	protected $_appControllerConfig = [
		'components' => [
			'Ajax' => false
		],
		'helpers' => [
		],
		'elements' => [
		]
	];

	public function beforeFilter() {
		parent::beforeFilter();

		$this->Security->unlockedFields[] = 'RiskAppetiteThreshold';
	}

	public function index() {
		return $this->redirect([
			'plugin' => false,
			'controller' => 'riskAppetites',
			'action' => 'edit',
			1
		]);
	}

	public function edit() {
		$this->set('title_for_layout', __('Risk Appetites'));

		$this->Crud->on('beforeFind', array($this, '_beforeFind'));
		$this->Crud->on('beforeSave', array($this, '_beforeSave'));
		$this->Crud->on('afterSave', array($this, '_afterSave'));
		$this->Crud->on('beforeRender', array($this, '_initOptions'));
		$this->Crud->on('afterFind', array($this, '_afterFind'));

		return $this->Crud->execute();
	}

	public function _beforeFind(CakeEvent $event) {
		$event->subject->query['contain'] = [
			'RiskAppetiteThreshold' => [
				'RiskAppetiteThresholdClassification'
			],
			'RiskAppetiteThresholdDefault',
			'RiskClassificationType'
		];
	}

	public function _afterFind(CakeEvent $event) {
		$classificationTypes = Hash::extract($event->subject->item, 'RiskClassificationType.{n}.id');

		$this->_setDynamicClassificationFields($classificationTypes);
	}

	public function _beforeSave(CakeEvent $event) {
		// ddd($event->subject->controller->request->data);
	}

	public function _afterSave(CakeEvent $event) {
		// ddd($event->subject->model);
		// no need to recalculate stuff if successful save didnt happen
		if ($event->subject->success === false) {
			return false;
		}

		$model = $event->subject->model;
		$data = $this->request->data;

		if ($model->getCurrentType() == RiskAppetite::TYPE_INTEGER) {
			ClassRegistry::init('Setting')->updateVariable('RISK_APPETITE', $data['RiskAppetite']['risk_appetite']);
		}

		$recalculateModel = [
			'Risk',
			'ThirdPartyRisk',
			'BusinessContinuity'
		];

		foreach ($recalculateModel as $model) {
			$Model = ClassRegistry::init($model);
			$ids = $Model->find('list', array(
				'fields' => array('id', 'id'),
				'recursive' => -1
			));
			
			foreach ($ids as $risk_id) {
				$Model->saveRiskScoreWrapper($risk_id);

				$Model->id = $risk_id;
				$Model->Behaviors->ObjectStatus->triggerObjectStatus($Model);
			}
		}

		$event->subject->controller->request->data['redirect_url'] = ['action' => 'edit', 1, '?' => [
			'success' => $event->subject->success
		]];
	}

	public function _initOptions(CakeEvent $event) {
		// ddd($event->subject->model);
		$this->set($event->subject->model->getFieldCollection()->getViewOptions());

		$FieldDataThresholdCollection = $this->RiskAppetite->RiskAppetiteThreshold->getFieldCollection();
		$thresholdViewOptions = $FieldDataThresholdCollection->getViewOptions('FieldDataThresholdCollection');
		// unset($thresholdViewOptions['FieldDataCollection']);
		$this->set($thresholdViewOptions);
		// $this->set('FieldDataThresholdCollection', $FieldDataThresholdCollection);

		$appetite = $event->subject->model->find('first', [
			'recursive' => -1
		]);

		$this->loadModel('RiskClassificationType');
		$types = $this->RiskClassificationType->find('list', array(
			'fields' => array('id', 'name'),
			'recursive' => -1
		));

		$this->set('classificationTypes', $types);
		$this->set('classificationTypeCount', count($types));
		$this->set('methods', $this->RiskAppetite->methods());
		$this->set('activeSlug', $appetite['RiskAppetite']['method']);

		if (!empty($this->request->data['RiskAppetite']['RiskClassificationType'])) {
			$this->_setDynamicClassificationFields($this->request->data['RiskAppetite']['RiskClassificationType']);
		}

		if (isset($this->request->query['success']) && $this->request->query['success']) {
			$this->set('ajaxSuccess', true);
		}
	}

	protected function _setDynamicClassificationFields($classificationTypes) {
		$RiskAppetiteThreshold = $this->RiskAppetite->RiskAppetiteThreshold;

		$classificationOptions = [];
		foreach ($classificationTypes as $type) {
			$classificationOptions[$type] = $RiskAppetiteThreshold->RiskAppetiteThresholdClassification->getClassifications($type);
		}

		$RiskAppetiteThresholdClassificationCollection = $RiskAppetiteThreshold->RiskAppetiteThresholdClassification->getFieldCollection();
		$this->set('RiskAppetiteThresholdClassificationCollection', $RiskAppetiteThresholdClassificationCollection);
		$this->set('classificationOptions', $classificationOptions);
	}

	/**
	 * Handler for adding a new threshold item.
	 * 
	 * @param  integer $index Index for hasMany relation.
	 * @return void
	 */
	public function thresholdItem($index = 0) {
		$this->Components->YoonityJSConnector->deny();
		// $this->modelClass = 'RiskAppetiteThreshold';
		// $this->Crud->useModel('RiskAppetiteThreshold');

		$RiskAppetiteThreshold = $this->RiskAppetite->RiskAppetiteThreshold;
		$RiskAppetiteThresholdCollection = $RiskAppetiteThreshold->getFieldCollection();
		$this->_FieldDataCollection = $RiskAppetiteThresholdCollection;

		// ddd($RiskAppetiteThresholdCollection->getViewOptions());
		$this->_setDynamicClassificationFields($this->request->query['types']);
		$this->set($RiskAppetiteThresholdCollection->getViewOptions('RiskAppetiteThresholdCollection'));
		$this->set('index', $index);
		$this->set('isDefault', false);
	}

}
