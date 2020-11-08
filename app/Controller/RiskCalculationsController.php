<?php
App::uses('Router', 'Routing');

class RiskCalculationsController extends AppController {
	public $helpers = array( 'Html', 'Form' );
	// public $components = array( 'Session', 'Paginator', 'Ajax' => array(
	// 	'actions' => array('edit'),
	// 	'redirects' => array(
	// 		'index' => array(
	// 			'url' => array('controller' => 'risks', 'action' => 'index')
	// 		)
	// 	)
	// ));
	public $components = [
		'Search.Prg', 'Paginator',
		'Ajax' => [
			'actions' => ['add', 'edit', 'delete'],
			'modules' => ['comments', 'records', 'attachments', 'notifications']
		],
		'Crud.Crud' => [
			'actions' => [
				'edit' => [
					'view' => 'edit',
					'saveMethod' => 'saveAssociated'
				]
			]
		]
	];

	public function beforeFilter() {
		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'trash']);

		parent::beforeFilter();
	}

	public function index()
	{
		return $this->Crud->execute();
	}

	public function add()
	{
		return $this->Crud->execute();
	}

	public function delete($id = null)
	{
		return $this->Crud->execute();
	}

	public function trash()
	{
		return $this->Crud->execute();
	}

	public function warning() {
		$this->set('title_for_layout', __('Calculation Warning'));
		$this->set('showHeader', true);

		$this->set('changes', $this->Session->read('RiskCalculation.changes'));
		$this->set('warning', __("If you continue, we'll update risk scores for every existing risk but it might happen than some risks re-calculation wont work until you manually edit, re-classify the risk and save. Simply filter all your risks and check the score has been updated, if not please edit and correct the issue."));

		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data = $this->Session->read('RiskCalculation.formData');
			$id = $this->Session->read('RiskCalculation.id');

			$this->Session->write('RiskCalculation.warning', true);
			$this->Session->delete('RiskCalculation.formData');

			return $this->edit($id);
		}

		// $this->Modals->settings['layout'] = 'LimitlessTheme.modals/modal';
		$this->YoonityJSConnector->setState(null);
		$this->Ajax->initModal('warning');

		$this->Modals->addFooterButton(__('Continue'), [
            'class' => 'btn btn-danger',
            'data-yjs-request' => "app/submitForm",
            'data-yjs-target' => "modal", 
            'data-yjs-modal-id' => $this->Modals->getModalId(),
            'data-yjs-on-modal-success' => "close", 
            'data-yjs-datasource-url' => Router::url(['controller' => 'riskCalculations', 'action' => 'warning']), 
            'data-yjs-forms' => 'risk-calculation-warning-form', 
            'data-yjs-event-on' => "click",
            'data-yjs-on-success-reload' => "#main-toolbar|#main-content"
        ], 'risk-calculation-warning');
	}

	private function handleWarnings($id, $originalFormData) {
		$changes = $this->RiskCalculation->checkChanges();

		// check session if warning wasnt proceeded before already
		if (!empty($changes) && !$this->Session->check('RiskCalculation.warning')) {
			$this->Session->write('RiskCalculation.formData', $originalFormData);
			$this->Session->write('RiskCalculation.id', $id);
			$this->Session->write('RiskCalculation.changes', $changes);
			$this->Session->write('RiskCalculation.warningType', 'zero-risk-score');

			return $this->redirect(array('action' => 'warning'));
		}
		else {
			$changes = $this->Session->read('RiskCalculation.changes');
			// remove session
			$this->Session->delete('RiskCalculation');
			return $changes;
		}

		return true;
	}

	public function edit($id)
	{
		$this->title = __('Edit a Risk Calculation');
		
		$this->Crud->on('beforeSave', array($this, '_beforeSave'));
		$this->Crud->on('afterSave', array($this, '_afterSave'));
		$this->Crud->on('beforeRender', array($this, '_beforeRender'));
		
		return $this->Crud->execute('edit', [$id]);
	}

	public function _beforeSave(CakeEvent $e)
	{
		$request = $e->subject->request;
		$requestData = &$request->data;
		$originalFormData = $request->data;
		$this->storedItem = $request->data;

		$id = $request->data['RiskCalculation']['id'];
		$model = $request->data['RiskCalculation']['model'];
		$methods = ['eramba', 'erambaMultiply'];

		foreach ($methods as $method) {
			if (!empty($requestData['RiskCalculationValue'][$method])) {
				$requestData['RiskCalculationValue'][$method];
				foreach ($requestData['RiskCalculationValue'][$method] as $key => $value) {
					if (empty($value)) {
						unset($requestData['RiskCalculationValue'][$method][$key]);
						continue;
					}

					$requestData['RiskCalculationValue'][$method][$key] = array(
						'field' => 'default',
						'value' => $value
					);
				}
			}
		}

		if (!empty($requestData['RiskCalculation']['method'])) {
			$calcValue = $requestData['RiskCalculationValue'][$requestData['RiskCalculation']['method']];
			$requestData['RiskCalculationValue'] = $calcValue;

			if (in_array($requestData['RiskCalculation']['method'], $methods)) {
				if (empty($requestData['RiskCalculationValue'])) {
					$requestData['RiskCalculationValue'] = array();
				}
			}
		}

		$validates = $this->RiskCalculation->saveAll($requestData, ['validate' => 'only']);
		if ($validates) {
			$warnings = $this->handleWarnings($id, $originalFormData);
			$this->warnings = $warnings;
		}
		else {
			$this->warning = false;
		}

		$this->RiskCalculation->RiskCalculationValue->deleteAll(array(
			'RiskCalculationValue.risk_calculation_id' => $requestData['RiskCalculation']['id']
		));

		// ddd($requestData);
	}

	public function _afterSave(CakeEvent $e)
	{
		$ret = true;

		$request = $e->subject->request;
		$item = $request->data;
		$model = $item['RiskCalculation']['model'];

		$warnings = $this->warnings;
		if ($e->subject->success) {
			$this->loadModel($model);
			$risks = $this->{$model}->find('list', array(
				'recursive' => -1
			));

			if (!empty($warnings)) {
				foreach ($risks as $riskId => $name) {
					$riskScore = 0;
					$residualRisk = 0;

					$saveData = array(
						'risk_score' => $riskScore,
						'residual_risk' => $residualRisk,
						'risk_score_formula' => '',
						'residual_risk_formula' => ''
					);

					$this->{$model}->id = $riskId;
					$ret &= (bool) $this->{$model}->save($saveData, false);

					if (in_array('method', $warnings)) {
						$ret &= $this->{$model}->quickLogSave($riskId, 2, __('Risk Calculation has been updated from "%s" to "%s" method the Risk Score attribute for all Risks have been updated to zero until they get classified again',
							$this->RiskCalculation->methods[$this->storedItem['RiskCalculation']['method']]['name'],
							$this->RiskCalculation->methods[$item['RiskCalculation']['method']]['name']));
					}
					else {
						$this->loadModel('RiskClassificationType');
						$previous = $this->RiskClassificationType->find('list', array(
							'conditions' => array(
								'RiskClassificationType.id' => $this->RiskCalculation->changesData['settings']['original']
							),
							'fields' => array('id', 'name'),
							'recursive' => -1
						));

						$new = $this->RiskClassificationType->find('list', array(
							'conditions' => array(
								'RiskClassificationType.id' => $this->RiskCalculation->changesData['settings']['request']
							),
							'fields' => array('id', 'name'),
							'recursive' => -1
						));

						$ret &= $this->{$model}->quickLogSave($riskId, 2, __('Risk settings for the methodology "%1$s" have changed from %2$s to %3$s and therefore the Risk score for all Risk have been set to zero until they get classified again',
							$this->RiskCalculation->methods[$item['RiskCalculation']['method']]['name'],
							implode(', ', $previous),
							implode(', ', $new)
						));
					}
				}
			}
			else {
				$ret &= $this->{$model}->calculateAndSaveRiskScoreById($risks);
				foreach ($risks as $risk_id) {
					$ret &= $this->{$model}->quickLogSave($risk_id, 2, __('Calculation method changed to %s - Risk Scores re-calculated', $this->RiskCalculation->methods[$item['RiskCalculation']['method']]['name']));
				}
			}
		}
	}

	public function _beforeRender(CakeEvent $e)
	{
		$item = &$e->subject->request->data;
		$model = $item['RiskCalculation']['model'];

		$this->set('model', $model);
		$this->set('methods', $this->RiskCalculation->methods);
		$this->set('availableMethods', $this->RiskCalculation->calcRules[$model]);
		$this->set('edit', true);

		$calcValue = $item['RiskCalculationValue'];
		unset($item['RiskCalculationValue']);
		$item['RiskCalculationValue'][$item['RiskCalculation']['method']] = $calcValue;

		$this->initOptions();
	}

	private function initOptions()
	{
		$this->loadModel('RiskClassificationType');
		$types = $this->RiskClassificationType->find('list', array(
			'fields' => array('id', 'name'),
			'recursive' => -1
		));

		$typesNotEmpty = $this->RiskClassificationType->find('list', array(
			'conditions' => array(
				'risk_classification_count >=' => 1
			),
			'fields' => array('id', 'name'),
			'recursive' => 0
		));

		$this->set('riskClassificationTypes', $types);
		$this->set('riskClassificationTypesNotEmpty', $typesNotEmpty);
	}

}
