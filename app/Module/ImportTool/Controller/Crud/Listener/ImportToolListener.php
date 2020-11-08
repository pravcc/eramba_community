<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('ImportToolView', 'ImportTool.Controller/Crud/View');

/**
 * ImportTool Listener
 */
class ImportToolListener extends CrudListener
{
	const REQUEST_PARAM = 'ImportTool';

	public function implementedEvents() {
		return array(
			'Crud.initialize' => 'initialize',
			'Crud.beforeRender' => 'beforeRender'
		);
	}

	public function initialize(CakeEvent $e)
	{
		if ($this->_isImportRequest($e->subject->request)) {
			$this->_crud()->config('actions.add.className', 'ImportTool.ImportTool');
			// ddd($this->_crud()->config('actions.add.className'));
		}
		// $this->_crud()->mapAction('import', ['className' => 'ImportTool.ImportTool'], true);
	}

	/**
	 * Check if the current request is actually request for Inline Edit action.
	 * 
	 * @param  CakeRequest $request
	 * @return boolean              True if it is a request for Inline Edit, False otherwise.
	 */
	protected function _isImportRequest(CakeRequest $request)
	{
		$model = $this->_model();
		$alias = $model->alias;

		$queryConds = isset($request->query[self::REQUEST_PARAM]) && $request->query[self::REQUEST_PARAM];
		// $dataConds = isset($request->data[$alias][self::REQUEST_PARAM]) && $request->data[$alias][self::REQUEST_PARAM];

		$conds = $queryConds;
		// $conds = $queryConds || $dataConds;
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
		$ImportToolView = new ImportToolView($e->subject);

		$this->_controller()->set('ImportTool', $ImportToolView);
	}

}
