<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('AdvancedFiltersObject', 'AdvancedFilters.Lib');
App::uses('Review', 'Model');

/**
 * ReviewsPlannerListener
 */
class RiskReviewsPlannerListener extends CrudListener
{

	public function implementedEvents() {
		return array(
			// 'Crud.startup' => array('callable' => 'startup', 'priority' => 50),
			'Crud.beforeRender' => array('callable' => 'beforeRender', 'priority' => 49)
		);
	}

	/**
	 * Before render callback that sets all required data into the view.
	 * 
	 * @param  CakeEvent $e
	 * @return void
	 */
	public function beforeRender(CakeEvent $e)
	{
		$args = $e->subject->controller->listArgs();

		$action = $e->subject->crud->action();
		if ($action instanceof AddCrudAction) {
			$this->_configureFieldData($e);
		}

		if ($action instanceof EditCrudAction) {
			$this->_configureFieldData($e);
		}
	}

	protected function _configureFieldData(CakeEvent $e)
	{
		$Risk = ClassRegistry::init('Risk');

		$nextReviewDate = $Risk->getFieldDataEntity('review');
		$config = $nextReviewDate->config();
		$config['description'] = __('Enter the date for the next Risk review, remember that review dates can only be updated with "reviews", this means that if you edit the Risk the review date wont be a field you can edit.');
		$config['editable'] = true;
		$config['group'] = 'risk';
		$config['label'] = __('Next Review Date');
		$config['renderHelper'] = ['Reviews', 'nextReviewDateField'];
		$nextReviewDate = new FieldDataEntity($config, $e->subject->model);

		$ReviewsCollection = &$this->_controller()->_FieldDataCollection;

		$action = $e->subject->crud->action();
		if ($action instanceof AddCrudAction) {
			$ReviewsCollection->get('planned_date')->toggleEditable(true);
		}

		if ($action instanceof AddCrudAction || $action instanceof EditCrudAction) {
			$ReviewsCollection->add($nextReviewDate);
		}
	}

}