<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('TrashView', 'Trash.Controller/Crud/View');
App::uses('AdvancedFiltersObject', 'AdvancedFilters.Lib');

/**
 * Trash Listener
 */
class TrashListener extends CrudListener
{

	public function implementedEvents() {
		return array(
			'Crud.beforeRender' => array('callable' => 'beforeRender', 'priority' => 100),
			'Crud.beforeFilterItems' => array('callable' => 'beforeFilterItems', 'priority' => 50),
			'Crud.beforeFilter' => array('callable' => 'beforeFilter', 'priority' => 50),
			'Crud.afterFilter' => array('callable' => 'afterFilter', 'priority' => 50),
			'AdvancedFilter.beforeFind' => array('callable' => 'beforeFilterFind', 'priority' => 50)
		);
	}

	public function isTrash()
	{
		return $this->_crud()->action() instanceof TrashCrudAction;
	}

	public function beforeFilterItems(CakeEvent $e)
	{	
		$action = $e->subject->crud->action;

		// only for trash action we make changes to the execution
		if ($this->isTrash($e)) {
			$_filter = $this->initTrashFilter();

			$items = new ArrayIterator();
			$e->subject->items->append($_filter);
		}
	}

	/**
	 * Initialize a Trash filter with pre-defined parameters to show only deleted items.
	 * 
	 * @return AdvancedFiltersObject
	 */
	public function initTrashFilter()
	{
		$model = $this->_model();
		$request = $this->_request();

		$_filter = new AdvancedFiltersObject();
		$_filter->setModel($model);
		$_filter->setName(__('Trash'));
		$_filter->setDescription(__('All items that have been deleted'));
		$_filter->setFilterValues([
			'deleted' => '1',
			'name__show' => '1'
		]);

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

		$_filter->manageFilter(false);

		return $_filter;
	}

	/**
	 * Before filter action that changes things to apply trash.
	 */
	public function beforeFilter(CakeEvent $e)
	{
		$model = $this->_model();
		$action = $e->subject->crud->action;

		// only for trash action we make changes to the execution
		if ($this->isTrash($e)) {
			// we disable softdelete completely
			$model->Behaviors->disable('Utils.SoftDelete');

			// and attach an event to the advanced filter object to additionally make changes to the final query
			$AdvancedFiltersObject = $e->subject->AdvancedFiltersObject;
			$this->attachListener($AdvancedFiltersObject);
		}
	}

	public function attachListener(AdvancedFiltersObject $Filter)
	{
		$Filter->getEventManager()->attach($this);
		// 	[$this, 'beforeFilterFind'],
		// 	'AdvancedFilter.beforeFind',
		// 	[
		// 		// 'passParams' => true
		// 	]
		// );
	}

	/**
	 * This callback makes changes to the query which disables SoftDelete functionality
	 * 
	 * @param  array $query  Query array.
	 * @return array         Updated query.
	 */
	public function beforeFilterFind(CakeEvent $e)
	{
		$e->data[0]['softDelete'] = false;
	}

	public function afterFilter(CakeEvent $e)
	{
		$model = $this->_model();

		// only for trash action we make changes to the execution
		if ($this->isTrash($e)) {
			// we disable softdelete completely
			$model->Behaviors->enable('Utils.SoftDelete');
		}
	}
	
	/**
	 * Before render callback that sets all required data into the view.
	 * 
	 * @param  CakeEvent $e
	 * @return void
	 */
	public function beforeRender(CakeEvent $e)
	{
		$this->_controller()->set('Trash', new TrashView($e->subject));

		if ($this->isTrash($e)) {
			$actionCrumb = $this->_controller()->Breadcrumbs->getLastCrumb();
			if (!empty($actionCrumb)) {
				$actionCrumb->title(__('Trash'));
			}
		}
	}

}
