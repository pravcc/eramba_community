<?php
App::uses('SearchListener', 'Crud.Controller/Crud/Listener');
App::uses('AdvancedFiltersModule', 'AdvancedFilters.Lib');
App::uses('AdvancedFiltersObject', 'AdvancedFilters.Lib');
App::uses('AdvancedFiltersView', 'AdvancedFilters.Controller/Crud/View');
App::uses('SubSectionListener', 'Controller/Crud/Listener');

/**
 * Advanced Filters Listener
 */
class AdvancedFiltersListener extends SearchListener
{
	// protected $_isFilter = false;

/**
 * Constructor
 *
 * @param CrudSubject $subject
 * @param array $defaults Default settings
 * @return void
 */
	public function __construct(CrudSubject $subject, $defaults = array()) {
		$defaults = am([
			'component' => [
				'commonProcess' => [
					'filterEmpty' => true
				]
			]
		], $defaults);

		parent::__construct($subject, $defaults);
	}

/**
 * Returns a list of all events that will fire in the controller during its lifecycle.
 * You can override this function to add you own listener callbacks
 *
 * We attach at priority 50 so normal bound events can run before us
 *
 * @return array
 */
	public function implementedEvents()
	{
		return parent::implementedEvents() + [
			'Crud.afterPaginate' => ['callable' => 'afterPaginate', 'priority' => 50],
			'Crud.beforeSave' => ['callable' => 'beforeSave', 'priority' => 50],
			'Crud.beforeDelete' => ['callable' => 'beforeDelete', 'priority' => 50],
			'Crud.beforeFind' => ['callable' => 'beforeFind', 'priority' => 50],
			'Crud.beforeRender' => ['callable' => 'beforeRender', 'priority' => 50],

			// 'Crud.beforeIndexList' => ['callable' => 'beforeIndexList', 'priority' => 50],
			'Crud.beforeFilterItems' => ['callable' => 'beforeFilterItems', 'priority' => 50],
		];
	}

	/**
	 * Before Handle callback that executes just before the action is called.
	 * 
	 * @param  CakeEvent $event
	 * @return void
	 */
	public function beforeHandle(CakeEvent $event)
	{
		$request = $this->_request();
		$model = $this->_model();
		
		if (!array_key_exists($model->alias, $request->data)) {
			return;
		}

		if (!array_key_exists('advanced_filter', $request->data($model->alias))) {
			return;
		}

		$controller = $this->_controller();

		if (isset($request->data['AdvancedFilterValue'])) {
			$request->data[$model->alias] = array_merge($request->data[$model->alias], $request->data['AdvancedFilterValue']);
		}

		$this->_ensureComponent($controller);
		$this->_ensureBehavior($model);
		$this->_commonProcess($controller, $model->name);
	}

	/**
	 * Load required components if missing.
	 * 
	 * @param  Controller $controller Controller
	 * @return void
	 */
	protected function _ensureComponent(Controller $controller)
	{
		parent::_ensureComponent($controller);
	}

	/**
	 * Load required behaviors if missing.
	 * 
	 * @param  Model  $model Model
	 * @return void
	 */
	protected function _ensureBehavior(Model $model)
	{
		parent::_ensureBehavior($model);

		if ($model->Behaviors->enabled('AdvancedFilters.AdvancedFilters')) {
			return;
		}

		$model->Behaviors->load('AdvancedFilters.AdvancedFilters');
		$model->Behaviors->AdvancedFilters->setup($model);
	}

	/**
	 * Execute commonProcess on Prg component
	 *
	 * @param Controller $controller
	 * @param string $modelClass
	 * @return void
	 */
	protected function _commonProcess(Controller $controller, $modelClass)
	{
		$controller->Prg->commonProcess($modelClass, [
			'excludedParams' => $this->_getExcludedParams($controller, $modelClass)
		]);
	}

	/**
	 * Skip parameters having default value for shorter querystring.
	 *
	 * @see http://httpd.apache.org/docs/2.2/mod/core.html#limitrequestline
	 */
	protected function _getExcludedParams(Controller $controller, $modelClass)
	{
		$request = &$controller->request;
		$data = &$request->data[$modelClass];

		$this->_model()->buildAdvancedFilterArgs();
		$filterArgs = $this->_model()->advancedFilter;

		$exclude = [];
		foreach ($filterArgs as $fieldSet) {
			foreach ($fieldSet as $field => $fieldData) {
				$showKey = $field . '__show';

				// handle 'show' key for optimizing final query
				if (isset($data[$showKey]) && isset($fieldData['show_default'])) {
					// in case the show key within request data is the same as default value
					// we wont push it into the query
					if ($data[$showKey] == $fieldData['show_default']) {
						unset($data[$showKey]);
						$exclude[] = $showKey;
					}
				}

				$compKey = $field . '__comp_type';

				if ($fieldData['filter']['method'] = 'findComplexType' && isset($data[$compKey])) {
					$queryClass = Inflector::classify($fieldData['type']) . 'Query';
					App::uses($queryClass, 'Lib/AdvancedFilters/Query');

					$defaultComp = $queryClass::$defaultComparison;

					if ($data[$compKey] == $defaultComp) {
						unset($data[$compKey]);
						$exclude[] = $compKey;
					}
				}
			}
		}

		return $exclude;
	}

	public function beforeIndexList(CakeEvent $event)
	{	
		// $request = $event->subject->request;
		// if (isset($request->query['advanced_filter_id'])) {
		// 	$event->subject->list = $request->query['advanced_filter_id'];
		// 	$this->_isFilter = true;
		// }
	}

	public function beforeFilterItems(CakeEvent $event)
	{	
		// on subsection request we dont want to run this filter logic
		if (SubSectionListener::isSubSectionRequest()) {
			return;
		}

		$controller = $this->_controller();
		$model = $this->_model();

		$request = $event->subject->request;
		$query = $request->query;

		unset($query['_']);

		// leave current request as it is if we are not executing a filter query action
		if (!isset($query['advanced_filter'])) {
			return true;
		}

		unset($query['advanced_filter']);

		// configure an index with a single specific filter
		if (isset($query['advanced_filter_id'])) {
			// ...
			$submittedQuery = AdvancedFiltersObject::trimFilterQuery($query);
			unset($submittedQuery['advanced_filter_id']);
			unset($submittedQuery['_page']);
			unset($submittedQuery['_pageLimit']);
			unset($submittedQuery['_limit']);
			unset($submittedQuery['_order_column']);
			unset($submittedQuery['_order_direction']);

			$AdvancedFilter = ClassRegistry::init('AdvancedFilters.AdvancedFilter');
			if (empty($query['advanced_filter_id'])) {
				$data = $AdvancedFilter->find('first', [
					'conditions' => [
						'AdvancedFilter.user_id' => $controller->logged['id']
					],
					'fields' => [
						'AdvancedFilter.id'
					],
					'order' => [
						'AdvancedFilter.created' => 'DESC'
					],
					'recursive' => -1
				]);

				$request->query['advanced_filter_id'] = $data['AdvancedFilter']['id'];
			}

			$exists = $AdvancedFilter->find('count', [
				'conditions' => [
					'AdvancedFilter.id' => $request->query['advanced_filter_id']
				],
				'recursive' => -1
			]);

			if ($exists) {
				if (empty($submittedQuery)) {
					$setFilterValues = true;
				} else {
					$setFilterValues = false;
				}

				$_filter = new AdvancedFiltersObject($request->query['advanced_filter_id'], $controller->logged['id'], $setFilterValues);

				$event->subject->items->append($_filter);
			} else {
				return false;
			}
		}

		// situation where user filters but doesnt have any saved filter selected within that request
		elseif (!isset($query['advanced_filter_id'])) {
			$_filter = new AdvancedFiltersObject();

			if (isset($query['__custom_id'])) {
				$_filter->setId($query['__custom_id']);
			}
			unset($query['__custom_id']);

			$_filter->setModel($model);
			$_filter->setFilterValues($query);
			$_filter->manageFilter(true);

			$event->subject->items->append($_filter);
		}

		$query = AdvancedFiltersObject::trimFilterQuery($query);

		$AdvancedFilterUserSetting = ClassRegistry::init('AdvancedFilters.AdvancedFilterUserSetting');

		if (isset($request->query['advanced_filter_id'])) {
			// save the _pageLimit param on each iteration
			if (isset($query['_pageLimit'])) {
				$AdvancedFilterUserSetting->saveLimit(
					$request->query['advanced_filter_id'],
					$controller->logged['id'],
					$query['_pageLimit']
				);
			}

			// otherwise get the _pageLimit parameter from the database where its stored
			if (!isset($query['_pageLimit'])) {
				$previousLimit = $AdvancedFilterUserSetting->getLimit(
					$request->query['advanced_filter_id'],
					$controller->logged['id']
				);

				$query['_pageLimit'] = $previousLimit;
			}
		}

		// we unset here _page and _pageLimit parameters from the query to check if is customized
		// but dont affect the real query
		$isCustomizedQuery = $query;
		unset($isCustomizedQuery['_page']);
		unset($isCustomizedQuery['_pageLimit']);

		$isCustomized = isset($request->query['advanced_filter_id']);
		$isCustomized &= !empty($isCustomizedQuery);
		$isCustomized &= !AdvancedFiltersObject::isJustSaved($event->subject->items[0]->getId(), $query);

		// configure a single specific filter but with customized filter parameters
		if ($isCustomized) {
			$controller->set('customizedFilter', true);
		}

		if (!empty($query)) {
			$event->subject->items[0]->setFilterValues($query);
		}

		// configure correct layout and template for ajax requests
		if ($request->is('ajax') && !empty($request->query['reload_advanced_filter_only'])) {
			$event->subject->controller->layout = 'clean';
			$controller->set('AdvancedFiltersObject', $event->subject->items[0]);
			$this->_action()->view('AdvancedFilters./Elements/filter_object');
		}
	}

	// public function beforeFilterRender(CakeEvent $event)
	// {
	// 	if ($event->subject->request->is('ajax')) {
	// 		$this->_controller()->set('AdvancedFiltersObject', $event->subject->items[0]);
	// 	}
	// }

	protected function _filter()
	{
		$controller = $this->_controller();

		
	}

	/**
	 * Configure view to render based on $request->query.
	 * 
	 * @param  Controller $controller
	 * @return void
	 */
	protected function _configureView(Controller $controller)
	{
		$request = $this->_request();

		// lets first check if we want to render advanced filters from $request->query params
		if (array_key_exists(AdvancedFiltersModule::QUERY_PARAM, $request->query)) {
			$elem = 'index';
			// $elem = 'trash'; @todo
			// $view = ''
			// $view = DS . 'Elements' . DS . AdvancedFiltersModule::ELEMENT_PATH . $elem;
			$view = 'AdvancedFilters./Elements/index';
		}
		// otherwise set controller's configured view
		else {
			$view = $controller->view;
		}

		$this->_action()->view($view);
	}

/**
 * beforePaginate callback
 *
 * @param CakeEvent $e
 * @return void
 */
	public function beforePaginate(CakeEvent $e)
	{
	}

	public function afterPaginate(CakeEvent $e)
	{
	}

	protected function _processQuery($query)
	{
	}

	/**
	 * BeforePaginate callback to modify pagination settings based on AdvancedFilters configuration.
	 * 
	 * @param  CakeEvent $e
	 * @return void
	 */
	/*public function beforePaginate(CakeEvent $e)
	{
		// temporary solution
		$params = [
			'varchar' => [
				'value' => 'kjhg',
				'comparisonType' => AbstractQuery::COMPARISON_EQUAL
			]
		];

		$model = $this->_model();
		$controller = $this->_controller();
		$Paginator = $controller->Paginator;
		$settings = &$Paginator->settings;

		$conditions = $model->filterCriteria($params);

		$settings['conditions'] = $conditions;
		$settings['limit'] = 15;
		$settings['group'] = [
			$model->escapeField('id')
		];
	}*/

	/**
	 * Before render callback that sets all required data into the view.
	 * 
	 * @param  CakeEvent $e
	 * @return void
	 */
	public function beforeRender(CakeEvent $e)
	{
		$this->_controller()->set('AdvancedFilters', new AdvancedFiltersView($e->subject));

		$this->_controller()->Components->load('AdvancedFilters');
		$this->_controller()->Components->AdvancedFilters->initialize($this->_controller());
		$this->_controller()->Components->AdvancedFilters->setSettings($this->_model()->alias);
		$this->_controller()->Components->AdvancedFilters->setFilterData($this->_model()->alias);
	}
}
