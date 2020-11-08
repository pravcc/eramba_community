<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('AdvancedFiltersObject', 'AdvancedFilters.Lib');
App::uses('FieldDataCollection', 'FieldData.Model/FieldData');
App::uses('FormReloadListener', 'Crud.Controller/Crud/Listener');

/**
 * SubSection listener that handles indexes for a certain parent object (foreign_key).
 */
class SubSectionListener extends CrudListener
{
	protected static $_isSubSectionRequest = false;

	protected $_settings = [
		'addButton' => true,
		'parentField' => null
	];

	public function implementedEvents() {
		return array(
			'Crud.beforeHandle' => array('callable' => 'beforeHandle', 'priority' => 9),
			'Crud.beforeFilterItems' => 'beforeFilterItems',
			'Crud.beforePaginate' => 'beforePaginate',
			'Crud.beforeFilter' => 'beforeFilter',
			'Crud.beforeSave' => array('callable' => 'beforeSave', 'priority' => 50),
			'Crud.beforeRender' => array('callable' => 'beforeRender', 'priority' => 50),
			'AdvancedFilter.afterFind' => array('callable' => 'afterFilterFind', 'priority' => 51)
		);
	}

	public function beforeHandle(CakeEvent $e)
	{
		$request = $e->subject->request;
		$model = $e->subject->model;
		$action = $e->subject->crud->action();

		// conditions from old code to determine subsection request
		if (($model instanceof InheritanceInterface && $this->hasForeignKey()) || !$model instanceof InheritanceInterface) {
			static::$_isSubSectionRequest = true;
		}

		if (!$this->_parentFieldEnabled($e)) {
			return true;
		}

		// handle passed param for add action
		if ($action instanceof AddCrudAction) {
			if ($request->is('get')) {
				$this->_controller()->Crud->on('beforeRender', array($this, '_configureAddRequestData'), [
					'priority' => 9
				]);
			} else {
				if (!FormReloadListener::isFormReloadRequest($e->subject->request)) {
					$this->_controller()->Crud->on('beforeSave', array($this, '_configureAddRequestData'), [
						'priority' => 9
					]);
				}
			}
		}

		$this->_configureValidation($e);
	}

	public static function isSubSectionRequest()
	{
		return static::$_isSubSectionRequest;
	}

	public function beforePaginate(CakeEvent $e)
	{
		$subject = $e->subject;	
		$model = $subject->model;
		$request = $this->_request();

		if ($this->hasForeignKey()) {
			$foreignKey = $this->getForeignKey();
			$column = $this->getParentColumn($model);

			$subject->paginator->settings['conditions'][$model->alias . '.' . $column] = $foreignKey;
			if ($model->schema('model') !== null) {
				$subject->paginator->settings['conditions'][$model->alias . '.model'] = $model->parentModel();
			}
		}
	}

	public function beforeFilterItems(CakeEvent $e)
	{
		$subject = $e->subject;
		$request = $subject->request;
		$controller = $subject->controller;
		$model = $subject->model;
		$crud = $subject->crud;

		if (static::isSubSectionRequest()) {
			if ($this->config('addButton')) {
				// generate add button
				$this->_initAddButton();
			}

			$_filter = new AdvancedFiltersObject();
			$_filter->setModel($subject->model);
			$_filter->setName(null);
			$_filter->setDescription(null);
			$_filter->setFilterValues([]);

			// custom sorting if planned_date exists
			if ($model->schema('planned_date') !== null) {
				$_filter->setFilterValues([
					'_order_column' => 'planned_date',
					'_order_direction' => 'DESC'
				]);
			}

			// handle pages
			if (isset($request->query['_page'])) {
				$_filter->setFilterValues([
					'_page' => $request->query['_page']
				]);
			}

			// hanadle page limit
			if (isset($request->query['_pageLimit'])) {
				$_filter->setFilterValues([
					'_pageLimit' => $request->query['_pageLimit']
				]);
			}

			$items = new ArrayIterator();
			$subject->items->append($_filter);

			// Set Crud action to use modal
			if ($crud->action()->config('action') === 'index' &&
				empty($request->query['reload_advanced_filter_only'])) {
				$crud->action()->config('useModal', true);
			}
		}
	}

	protected function _initAddButton()
	{
		$controller = $this->_controller();
		$request = $this->_request();
		$model = $this->_model();

		$addButtonOptions = [
			'action' => 'add'
		];

		if ($this->hasForeignKey()) {
			$addButtonOptions[] = $this->getForeignKey();
		}

		$addRoute = $model->getMappedRoute($addButtonOptions);
		$controller->Modals->addFooterButton(__('Add New'), [
            'class' => 'btn btn-primary',
            'data-yjs-request' => "crud/showForm",
            'data-yjs-target' => "modal", 
            'data-yjs-datasource-url' => Router::url($addRoute), 
            'data-yjs-event-on' => "click",
        ], 'subsection-add-button');

		// alternate way to show add new button
		if (!$request->is('ajax')) {
        	$controller->set('add_new_button', $addRoute);
        }
	}

	/**
	 * Before filter action that changes things to apply trash.
	 */
	public function beforeFilter(CakeEvent $e)
	{
		// and attach an event to the advanced filter object to additionally make changes to the final query
		$AdvancedFiltersObject = $e->subject->AdvancedFiltersObject;
		$AdvancedFiltersObject->getEventManager()->attach(
			[$this, '_beforeFilterFind'],
			'AdvancedFilter.beforeFind',
			[
				// 'passParams' => true
			]
		);
	}

	/**
	 * This callback makes changes to the query.
	 * 
	 * @param  array $query  Query array.
	 * @return array         Updated query.
	 */
	public function _beforeFilterFind(CakeEvent $e)
	{
		$subject = $e->subject;	
		$model = $subject->getModel();
		$request = $this->_request();

		if ($this->hasForeignKey()) {
			$foreignKey = $this->getForeignKey();
			$column = $this->getParentColumn($model);

			$e->data[0]['conditions'][$model->alias . '.' . $column] = $foreignKey;
			if ($model->schema('model') !== null) {
				$e->data[0]['conditions'][$model->alias . '.model'] = $model->parentModel();
			}
		}
	}

	public function beforeSave(CakeEvent $e)
	{
		$subject = $e->subject;
		$model = $subject->model;
		$request = $subject->request;
		$action = $e->subject->crud->action();

		if ($action instanceof AddCrudAction && $model instanceof InheritanceInterface) {
			// we hard-code the parent only if 'parentField' feature is disabled
			if (!$this->_parentFieldEnabled($e)) {
				$request->data[$model->alias][$this->getParentColumn($model)] = $this->getForeignKey();
			}

			if ($model->schema('model') !== null) {
				$request->data[$model->alias]['model'] = $model->parentModel();
			}
		}
	}

	public function hasForeignKey()
	{
		$request = $this->_request();
		return isset($request->params['pass'][0]);
	}

	public function getForeignKey()
	{
		$request = $this->_request();
		if ($this->hasForeignKey()) {
			return $request->params['pass'][0];
		}

		return false;
	}

	/**
	 * Get parent model association column.
	 * 
	 * @param  Model  $Model
	 * @return string        Column name.
	 */
	public function getParentColumn(Model $Model)
	{
		$parentModel = $Model->parentModel();
		$assoc = $Model->getAssociated($parentModel);

		return $assoc['foreignKey'];
	}
	
	/**
	 * Before render callback that sets all required data into the view.
	 * 
	 * @param  CakeEvent $e
	 * @return void
	 */
	public function beforeRender(CakeEvent $e)
	{
		$controller = $e->subject->controller;

		// reloads all indexes after add/edit submission
		$controller->Modals->changeConfig('footer.buttons.saveBtn.options.data-yjs-on-modal-close', '@reload-parent');

		// only for situations where delete button actually exists (delete crud action)
		// otherwise throws a php notice because its not a button defined by default in ModalsComponent
		// @see ModalsComponent::__construct();
		if (Hash::get($controller->Modals->settings, 'footer.buttons.deleteBtn') !== null) {
			$controller->Modals->changeConfig('footer.buttons.deleteBtn.options.data-yjs-on-modal-close', '@reload-parent');
		}

		$action = $e->subject->crud->action();

		// for add action we configure the custom parent field within a special tab
		$this->_configureAddFieldData($e);

		// for edit action we configure the custom parent field as hidden
		$this->_configureEditFieldData($e);

		$controller->set('isSubsection', true);
	}

	/**
	 * Has parent field enabled in add form.
	 * 
	 * @return bool In case of add action and configured 'parentField' setting return true otherwise false.
	 */
	protected function _parentFieldEnabled(CakeEvent $e)
	{
		$action = $e->subject->crud->action();
		$request = $e->subject->request;

		if (!empty($request->query['BulkActions'])) {
			return false;
		}

		if ($this->_getParentField($e) !== false) {
			return true;
		}

		return false;
	}

	protected function _configureValidation(CakeEvent $e)
	{
		$subject = $e->subject;
		$controller = $subject->controller;
		$model = $subject->model;
		$action = $subject->crud->action();

		// configure the field data parent tab in case its part of the listener's settings
		if (!$this->_parentFieldEnabled($e)) {
			return false;
		}

		if (!$action instanceof AddCrudAction) {
			return false;
		}

		$model->validator()->add($this->_getParentField($e), 'notBlank', array(
			'rule' => 'notBlank',
			'required' => true,
			'message' => __('This field is required')
		));
	}

	/**
	 * For Add action we configure the parent field in a separate dynamic tab.
	 * 
	 * @param  CakeEvent $e
	 */
	protected function _configureAddFieldData(CakeEvent $e)
	{
		$subject = $e->subject;
		$controller = $subject->controller;
		$action = $subject->crud->action();

		// configure the field data parent tab in case its part of the listener's settings
		if (!$this->_parentFieldEnabled($e)) {
			return false;
		}

		if (!$action instanceof AddCrudAction) {
			return false;
		}

		$subject = $e->subject;
		$request = $subject->request;
		$model = $subject->model;
		$controller = $subject->controller;
		$FieldCollection = $controller->_FieldDataCollection;
		$ParentField = $FieldCollection->get($this->_getParentField($e));

		$model->fieldGroupData['parent'] = new FieldGroupEntity([
   			'__key' => 'parent',
   			'label' => $ParentField->label(),
   			// 'navItemOptions' => []
   		]);

		$ParentField->moveToGroup($model->fieldGroupData['parent']);

		// we create a new collection having only single parent field
		// in case there is no value selected yet
		if (empty($request->data[$model->alias][$this->_getParentField($e)])) {
	   		$_Collection = new FieldDataCollection([], $model);
	   		$_Collection->add($ParentField);

			$this->_controller()->_FieldDataCollection = $_Collection;
		}
	}

	/**
	 * For Add action we configure the parent field in a separate dynamic tab.
	 * 
	 * @param  CakeEvent $e
	 */
	public function _configureAddRequestData(CakeEvent $e)
	{
		$subject = $e->subject;
		$controller = $subject->controller;
		$model = $subject->model;
		$action = $subject->crud->action();
		$request = $subject->request;

		// configure the field data parent tab in case its part of the listener's settings
		if (!$this->_parentFieldEnabled($e)) {
			return false;
		}

		if (!$action instanceof AddCrudAction) {
			return false;
		}

		// if request parameter for AddCrudAction is defined,
		// it means we auto-select the parent value
		if (isset($request->params['pass'][0]) && empty($request->data[$model->alias][$this->_getParentField($e)])) {
			$request->data[$model->alias][$this->_getParentField($e)] = $request->params['pass'][0];
		}
	}

	/**
	 * For Edit action we make the parent field hidden as per default.
	 * 
	 * @param  CakeEvent $e
	 */
	protected function _configureEditFieldData(CakeEvent $e)
	{
		$subject = $e->subject;
		$controller = $subject->controller;
		$action = $subject->crud->action();

		if (!$this->_parentFieldEnabled($e)) {
			return false;
		}

		if (!$action instanceof EditCrudAction) {
			return false;
		}

		$controller->_FieldDataCollection->get($this->_getParentField($e))->config('type', 'hidden');
	}

	/**
	 * Get the parent field based on SubSectionBehavior settings.
	 * 
	 * @param  CakeEvent $e
	 * @return mixed        Parent field as string if applicable, false otherwise.
	 */
	protected function _getParentField(CakeEvent $e)
	{
		$model = $e->subject->model;

		if (isset($model->Behaviors->SubSection->settings[$model->alias]['parentField'])) {
			return $model->Behaviors->SubSection->settings[$model->alias]['parentField'];
		}

		return false;
	}

}
