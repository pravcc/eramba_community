<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('AdvancedFiltersObject', 'AdvancedFilters.Lib');
App::uses('Review', 'Model');

/**
 * ReviewsPlannerListener
 */
class ReviewsPlannerListener extends CrudListener
{

	public function implementedEvents() {
		return array(
			'Crud.initialize' => array('callable' => 'initialize', 'priority' => 50),
			'Crud.startup' => array('callable' => 'startup', 'priority' => 50),
			'Crud.beforeFilterItems' => array('callable' => 'beforeFilterItems', 'priority' => 50),
			'Crud.beforeFilter' => array('callable' => 'beforeFilter', 'priority' => 50),
			'Crud.beforeSave' => array('callable' => 'beforeSave', 'priority' => 50),
			'Crud.beforeRender' => array('callable' => 'beforeRender', 'priority' => 50),
			'AdvancedFilter.beforeFind' => array('callable' => 'beforeFilterFind', 'priority' => 50)
		);
	}

	public function initialize(CakeEvent $e)
	{
		$controller = $this->_controller();

		if (!isset($controller->request->params['pass'][0])) {
			trigger_error('ReviewsController $request->params is missing a model name for the parent model which is required for CRUD, Visualisation and entire Reviews functionality to work properly.');

			return true;
		}

		$args = $this->listArgs();

		$this->_currentModel = $args->model;
		$this->_reviewModel = Review::buildModelName($this->_currentModel);

		$this->_crud()->useModel($this->_reviewModel);
		$controller->modelClass = $this->_reviewModel;
	}

	public function startup(CakeEvent $e)
	{
	}

	public function beforeFilterItems(CakeEvent $e)
	{	
		$action = $e->subject->crud->action;
		$model = $e->subject->model;
		$controller = $e->subject->controller;
		$request = $e->subject->request;

		$args = $this->listArgs();

		if ($args->foreignKey !== null) {
			$_filter = new AdvancedFiltersObject();
			$_filter->setModel($model);
			$_filter->setName(__('Reviews'));
			$_filter->setDescription(__('This is a list of reviews'));
			$_filter->setFilterValues([
				'_order_column' => 'planned_date',
				'_order_direction' => 'DESC'
			]);
			$_filter->manageFilter(false);

			$items = new ArrayIterator();
			$e->subject->items->append($_filter);
		}

		// Set Crud action to use modal
		if ($this->_crud()->action()->config('action') === 'index') {
			$this->_crud()->action()->config('useModal', true);
		}

		if ($args->foreignKey !== null) {
			$controller->set('add_new_button', [
				'plugin' => 'reviews_planner',
	    		'controller' => 'reviewsPlanner',
	    		'action' => 'add',
	    		$this->_currentModel,
	    		$args->foreignKey
	    	]);
		}
	}

	/**
	 * Before filter action that changes things to apply trash.
	 */
	public function beforeFilter(CakeEvent $e)
	{
		// and attach an event to the advanced filter object to additionally make changes to the final query
		$AdvancedFiltersObject = $e->subject->AdvancedFiltersObject;
		$this->attachListener($AdvancedFiltersObject);
	}

	public function attachListener(AdvancedFiltersObject $Filter)
	{
		$Filter->getEventManager()->attach($this);
	}

	/**
	 * This callback makes changes to the query which disables SoftDelete functionality
	 * 
	 * @param  array $query  Query array.
	 * @return array         Updated query.
	 */
	public function beforeFilterFind(CakeEvent $e)
	{
		$model = $e->subject->getModel();
		$args = $this->listArgs();

		if ($args->foreignKey !== null) {
			$e->data[0]['conditions'][$model->alias . '.foreign_key'] = $args->foreignKey;
		}
		// ddd($e->data);
		// $e->data[0]['softDelete'] = false;
	}

	public function listArgs()
	{
		$controller = $this->_controller();
		$pass = $this->_controller()->request->params['pass'];
		$model = $pass[0];
		$foreignKey = null;
		$id = null;

		if (is_numeric($model)) {
			$id = $model;

			$controller->loadModel('Review');
			$review = $controller->Review->find('first', [
				'conditions' => [
					'Review.id' => $model
				],
				'fields' => [
					'Review.model',
					'Review.foreign_key',
				],
				'recursive' => -1
			]);

			$model = $review['Review']['model'];
			$foreignKey = $review['Review']['foreign_key'];
		}
		else {
			if (isset($pass[1])) {
				$foreignKey = $pass[1];
			}
		}

		$args = new stdClass();
		$args->model = $model;
		$args->foreignKey = $foreignKey;
		$args->id = $id;

		return $args;
	}

	public function getMainItem()
	{
		$args = $this->listArgs();

		$ReviewModel = ClassRegistry::init($this->_reviewModel);
		return $ReviewModel->{$ReviewModel->getRelatedModel()}->find('first', array(
			'conditions' => array(
				'id' => $args->foreignKey
			),
			'recursive' => -1
		));
	}

	public function beforeSave(CakeEvent $e)
	{
		$args = $this->listArgs();

		$this->_controller()->set('relatedModel', $args->model);
		$this->_controller()->set('foreign_key', $args->foreignKey);

		// $this->initData($model, $foreign_key);

		$this->_controller()->set('reviewModel', $this->_reviewModel);

		$this->_request()->data[$this->_reviewModel]['user_id'] = $this->_controller()->logged['id'];
		$this->_request()->data[$this->_reviewModel]['model'] = $args->model;
		$this->_request()->data[$this->_reviewModel]['foreign_key'] = $args->foreignKey;
	}

	/**
	 * Before render callback that sets all required data into the view.
	 * 
	 * @param  CakeEvent $e
	 * @return void
	 */
	public function beforeRender(CakeEvent $e)
	{
		$args = $this->listArgs();

		$action = $e->subject->crud->action();
		if ($action instanceof AddCrudAction) {
			$this->_initData($args->model, $args->foreignKey);
		}

		if ($action instanceof EditCrudAction) {
			$this->_initData($args->model, $args->foreignKey, $args->id);
		}
	}

	protected function _initData($model, $foreign_key, $reviewId = null) {
		$reviewModel = $this->_reviewModel;
		$this->_controller()->set('reviewModel', $this->_reviewModel);

		$ReviewModel = ClassRegistry::init($this->_reviewModel);

		$ReviewModelAlias = $ReviewModel->alias;

		$reviewCompleted = false;
		if ($reviewId) {
			$reviewCompleted = $ReviewModel->find('count', array(
				'conditions' => [
					$ReviewModelAlias . '.id' => $reviewId,
					$ReviewModelAlias . '.completed' => REVIEW_COMPLETE
				],
				'recursive' => -1
			));
		}
		$this->_controller()->set('reviewCompleted', $reviewCompleted);

		$mainItem = $this->getMainItem();

		$this->_controller()->set('mainItem', $mainItem);

		$prevReview = $ReviewModel->getLastCompletedReview($foreign_key);

		$futureConds = array(
			$ReviewModelAlias . '.foreign_key' => $foreign_key,
			$ReviewModelAlias . '.planned_date >' => date('Y-m-d'),
			$ReviewModelAlias . '.completed' => REVIEW_NOT_COMPLETE,
		);

		if (!empty($reviewId)) {
			$futureConds[$ReviewModelAlias . '.id !='] = $reviewId;
		}

		$futureReview = $ReviewModel->find('first', array(
			'conditions' => $futureConds,
			'recursive' => -1
		));
		
		$this->_controller()->set('prevReview', $prevReview);
		$this->_controller()->set('futureReview', $futureReview);
	}

}
