<?php
App::uses('CrudAction', 'Crud.Controller/Crud');
App::uses('CrudActionTrait', 'Controller/Crud/Trait');
App::uses('AdvancedFilterUserSetting', 'AdvancedFilters.Model');
App::uses('AdvancedFiltersObject', 'AdvancedFilters.Lib');
App::uses('DebugTimer', 'DebugKit.Lib');

class AdvancedFiltersCrudAction extends CrudAction
{
	use CrudActionTrait;

	/**
	 * Default settings for advanced filter's 'index' actions.
	 * 
	 * @var array
	 */
	protected $_settings = [
		'enabled' => true,
		'view' => 'AdvancedFilters./Elements/index',
		'viewVar' => 'data',
		'useModal' => false,
		'findMethod' => 'all',
		'serialize' => array(),
		'api' => array(
			'success' => array(
				'code' => 200
			),
			'error' => array(
				'code' => 400
			)
		)
	];

	const ACTION_SCOPE = CrudAction::SCOPE_MODEL;

	// use CrudActionTrait;


/**
 * HTTP GET handler
 *
 * @return void
 */
	protected function _get()
	{
		if ($this->_controller()->request->is('api')) {
			$this->_indexGet();
		} else {
			$this->_filterGet();
		}
	}

/**
 * Compute pagination settings
 *
 * Initializes PaginatorComponent if it isn't loaded already
 * Modified the findType based on the CrudAction configuration
 *
 * @return array The Paginator settings
 */
	public function paginationConfig() {
		$controller = $this->_controller();

		if (!isset($controller->Paginator)) {
			$pagination = isset($controller->paginate) ? $controller->paginate : array();
			$controller->Paginator = $controller->Components->load('Paginator', $pagination);
		}

		$Paginator = $controller->Paginator;
		$settings = &$Paginator->settings;

		if (isset($settings[$controller->modelClass])) {
			if (empty($settings[$controller->modelClass]['findType'])) {
				$settings[$controller->modelClass]['findType'] = $this->_getFindMethod('all');
			}
		} elseif (empty($settings['findType'])) {
			$settings['findType'] = $this->_getFindMethod('all');
		}

		$settings['paramType'] = 'querystring';

		// Set controller's paginator maxLimit to 1000 (cake's default is 100)
		$settings['maxLimit'] = 1000;

		return $settings;
	}

	// protected function _dataTableGet()
	// {
	// 	$controller = $this->_controller();
	// 	$model = $this->_model();

	// 	$query = $controller->request->query;

	// 	$controller->viewClass = 'Json';
	// 	$this->view('AdvancedFilters./Elements/data_table_json');
	// 	$viewVar = $this->viewVar();

	// 	$id = $query['advanced_filter_id'];
	// 	$_filter = $this->_initFilter($model);
	// 	$_filter->setId($id);
	// 	$data = $_filter->filter();

	// 	$controller->set([
	// 		'AdvancedFiltersObject' => $_filter,
	// 		$viewVar => $data
	// 	]);
	// }

	protected function _indexGet() {
		$this->paginationConfig();

		$controller = $this->_controller();

		$success = true;
		$viewVar = $this->viewVar();

		$subject = $this->_trigger('beforePaginate', array('paginator' => $controller->Paginator, 'success' => $success, 'viewVar' => $viewVar));
		$items = $controller->paginate($this->_model());
		$subject = $this->_trigger('afterPaginate', array('success' => $subject->success, 'viewVar' => $subject->viewVar, 'items' => $items));

		$items = $subject->items;

		if ($items instanceof Iterator) {
			$items = iterator_to_array($items);
		}

		$controller->set(array('success' => $subject->success, $subject->viewVar => $items));
		$this->_trigger('beforeRender', $subject);
	}

	
/**
 * Change the name of the view variable name
 * of the data when its sent to the view
 *
 * @param mixed $name
 * @return mixed
 */
	public function viewVar($name = null)
	{
		if (empty($name)) {
			return $this->config('viewVar');
		}

		return $this->config('viewVar', $name);
	}

	protected function _filterGet()
	{
		$controller = $this->_controller();
		$request = $this->_request();
		$model = $this->_model();

		$items = new ArrayIterator();

		DebugTimer::start('Event: Crud.beforeHandle');
		$subject = $this->_trigger('beforeFilterItems', compact('items'));
		if (!$subject->items->count() && !isset($controller->request->query['advanced_filter_id'])) {
			$subject->items = $this->_getIndexItems();
		}
		DebugTimer::stop('Event: Crud.beforeHandle');

		DebugTimer::start('Event: AdvancedFiltersCrudAction - Filtering results');
		foreach ($subject->items as &$AdvancedFiltersObject) {
			DebugTimer::start('Event: AdvancedFiltersCrudAction - Filtering for ' . $AdvancedFiltersObject->getId());
			DebugTimer::start('Event: Crud.beforeFilter for ' . $AdvancedFiltersObject->getId());
			$this->_trigger('beforeFilter', compact('AdvancedFiltersObject'));
			DebugTimer::stop('Event: Crud.beforeFilter for ' . $AdvancedFiltersObject->getId());
			
			if ($request->is('ajax') && $subject->items->count() == 1) {
				$AdvancedFiltersObject->filter();
			}

			DebugTimer::start('Event: Crud.afterFilter for ' . $AdvancedFiltersObject->getId());
			$this->_trigger('afterFilter', compact('AdvancedFiltersObject'));
			DebugTimer::stop('Event: Crud.afterFilter for ' . $AdvancedFiltersObject->getId());
			DebugTimer::stop('Event: AdvancedFiltersCrudAction - Filtering for ' . $AdvancedFiltersObject->getId());
		}
		DebugTimer::stop('Event: AdvancedFiltersCrudAction - Filtering results');

		$controller->set($this->viewVar(), $subject->items);
		$this->_setData();
		$this->_trigger('beforeRender', $subject);
	}

	protected function _getIndexItems()
	{
		$model = $this->_model();
		$controller = $this->_controller();

		$AdvancedFilter = ClassRegistry::init('AdvancedFilters.AdvancedFilter');
		$AdvancedFilterUserSetting = $AdvancedFilter->AdvancedFilterUserSetting;

		$query = [
			'conditions' => [
				$AdvancedFilter->escapeField('model') => $model->alias,
				'OR' => [
					[
						$AdvancedFilter->escapeField('user_id') => $controller->logged['id'],
						$AdvancedFilterUserSetting->escapeField('default_index') => $AdvancedFilterUserSetting::DEFAULT_INDEX,
					],
					[
						// $AdvancedFilter->escapeField('user_id') => $controller->logged['id'],
						$AdvancedFilter->escapeField('private') => 0,
						$AdvancedFilterUserSetting->escapeField('default_index') => $AdvancedFilterUserSetting::DEFAULT_INDEX,
					]
				]
				
			],
			'fields' => [
				$AdvancedFilter->alias . '.' . $AdvancedFilter->primaryKey
			],
			'recursive' => 0
		];

		$list = $AdvancedFilter->find('list', $query);

		$items = new ArrayIterator();
		foreach ((array) $list as $id) {
			$_filter = $this->_initFilter($model);
			$_filter->setId($id);

			$items->append($_filter);
		}
		
		return $items;
	}

	/**
	 * Initialize AdvancedFilterObject having all required functionality (Visualisation, etc..).
	 * 
	 * @return AdvancedFilterObject
	 */
	protected function _initFilter(Model $Model)
	{
		$_filter = new AdvancedFiltersObject(null, $this->_controller()->logged['id']);
		$_filter->setModel($Model);

		return $_filter;
	}

	protected function _setData()
	{
		$controller = $this->_controller();
		$model = $this->_model();

		$settings = array(
			'model' => $model->alias,
			'advancedFilter' => $controller->{$model->alias}->advancedFilter,
			'advancedFilterSettings' => $controller->{$model->alias}->advancedFilterSettings
		);

		$filterSetting = array(
			'model' => $model->alias,
			'fields' => $settings['advancedFilter'],
			'settings' => $settings['advancedFilterSettings']
		);

		$controller->set('filter', $filterSetting);

		//
        // Init modal
        if ($this->_settings['useModal']) {
            $this->_controller()->Modals->init();
        }
        //
	}

	protected function _post()
	{
		return $this->_get();
	}


}
