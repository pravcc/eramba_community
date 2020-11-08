<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('AdvancedFiltersObject', 'AdvancedFilters.Lib');
App::uses('ClassRegistry', 'Utility');
App::uses('RiskCalculation', 'Model');

/**
 * Trash Listener
 */
class RisksListener extends CrudListener
{

	public function implementedEvents() {
		return array(
			'Crud.beforeHandle' => array('callable' => 'beforeHandle', 'priority' => 50),
			'Crud.beforeRender' => array('callable' => 'beforeRender', 'priority' => 50),
			'Crud.beforeSave' => array('callable' => 'beforeSave', 'priority' => 50),
			'Crud.beforeFilter' => array('callable' => 'beforeFilter', 'priority' => 50),
		);
	}

	public function beforeHandle(CakeEvent $e)
	{
		$crud = $e->subject->crud;
		$action = $crud->action();
		$request = $this->_request();

		if ($action instanceof AddCrudAction || $action instanceof EditCrudAction) {
			$this->_beforeAddEditRender();
		}

		if ($action instanceof AddCrudAction || $action instanceof EditCrudAction || ($action instanceof AdvancedFiltersCrudAction && !$request->is('ajax'))) {
			// handle error message if there is missing basic risk configuration
			// risk classifications or risk calculation
			$this->_handleRiskConfigurations();
		}
	}

	protected function _handleRiskConfigurations()
	{
		$model = $this->_model();
		$controller = $this->_controller();

		$RiskClassification = ClassRegistry::init('RiskClassification');
		$classifications = $RiskClassification->find('count');

		$calculationMethod = $model->Behaviors->RiskCalculationManager->getMethod($model);
		$calculationValues = $model->Behaviors->RiskCalculationManager->getSectionValues($model);

		$allowCreation = $classifications > 0;
		$allowCreation &= array_key_exists($calculationMethod, RiskCalculation::methods());
		$allowCreation &= count($calculationValues) > 0;

		if ($allowCreation == false) {
			$controller->Flash->error(__('In order to use this module you need to configure the following settings: Risk Classification, Risk Calculation and Risk Appetite'));
		}
	}

	public function beforeSave(CakeEvent $e)
	{
		$model = $this->_model();
		$request = $this->_request();

		// this handles the error while saving empty hasMany association with deep true
		if (isset($request->data[$model->alias]['Tag'])
			&& $request->data[$model->alias]['Tag'] === ''
		) {
			$request->data[$model->alias]['Tag'] = [];
		}
	}

	protected function _beforeAddEditRender()
	{
		$controller = $this->_controller();

		if ($controller->_FieldDataCollection->has('review')) {
			$controller->_FieldDataCollection->get('review')->toggleEditable(true);
		}

		if ($controller->_FieldDataCollection->has('RiskClassification')) {
			$controller->_FieldDataCollection->get('RiskClassification')->toggleEditable(true);
		}

		if ($controller->_FieldDataCollection->has('RiskClassificationTreatment')) {
			$controller->_FieldDataCollection->get('RiskClassificationTreatment')->toggleEditable(true);
		}
	}

	/**
	 * Before filter action that changes things to apply trash.
	 */
	public function beforeFilter(CakeEvent $e)
	{
		$model = $this->_model();
		$action = $e->subject->crud->action;

		$AdvancedFiltersObject = $e->subject->AdvancedFiltersObject;
		$this->attachListener($AdvancedFiltersObject);
	}

	public function attachListener(AdvancedFiltersObject $Filter)
	{
		$Filter->getEventManager()->attach($this);
	}

	/**
	 * Before render callback that sets all required data into the view.
	 * 
	 * @param  CakeEvent $e
	 * @return void
	 */
	public function beforeRender(CakeEvent $e)
	{
		$this->initCalculationOptions();
	}

	public function initCalculationOptions()
	{
		$controller = $this->_controller();

		$riskCalculationData = [
			'RiskClassification' => $controller->RisksManager->getDataToSet('RiskClassification'),
			'RiskClassificationTreatment' => $controller->RisksManager->getDataToSet('RiskClassificationTreatment')
		];
		
		$controller->set(compact('riskCalculationData'));
	}

}
