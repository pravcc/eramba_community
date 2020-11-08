<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('BulkActionsView', 'BulkActions.Controller/Crud/View');

/**
 * BulkActions Listener
 */
class BulkActionsListener extends CrudListener
{
	const REQUEST_PARAM = 'BulkActions';

	public function implementedEvents() {
		return array(
			'Crud.initialize' => 'initialize',
			'Crud.beforeHandle' => 'beforeHandle',
			'Crud.beforeRender' => 'beforeRender'
		);
	}

	public function initialize(CakeEvent $e)
	{
		if ($this->isBulkRequest()) {
			$this->_crud()->config('actions.edit.className', 'BulkActions.BulkActionsEdit');
			$this->_crud()->config('actions.delete.className', 'BulkActions.BulkActionsDelete');
		}
	}

	public function beforeHandle(CakeEvent $e)
	{
		
	}

	/**
	 * Check if the current request is actually request for Inline Edit action.
	 * 
	 * @param  CakeRequest $request
	 * @return boolean              True if it is a request for Inline Edit, False otherwise.
	 */
	public function isBulkRequest(CakeRequest $request = null)
	{
		$model = $this->_model();
		$alias = $model->alias;

		if ($request === null) {
			$request = $this->_request();
		}

		$queryConds = isset($request->query[self::REQUEST_PARAM]) && $request->query[self::REQUEST_PARAM];

		$conds = $queryConds;
		// $conds &= $request->is('ajax');

		return $conds;
	}

	/**
	 * Before render callback that sets all required data into the view.
	 * 
	 * @param  CakeEvent $e
	 * @return void
	 */
	public function beforeRender(CakeEvent $e)
	{
		$BulkActionsView = new BulkActionsView($e->subject);

		$this->_controller()->set('BulkActions', $BulkActionsView);
	}

}
