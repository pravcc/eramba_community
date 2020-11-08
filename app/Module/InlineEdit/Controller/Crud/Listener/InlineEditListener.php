<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('InlineEditView', 'InlineEdit.Controller/Crud/View');

/**
 * Inline Edit Listener
 */
class InlineEditListener extends CrudListener
{
	const REQUEST_PARAM = 'inlineEdit';

	public function implementedEvents() {
		return array(
			'Crud.beforeHandle' => 'beforeHandle',
			'Crud.beforeSave' => 'beforeSave',
			'Crud.afterSave' => 'afterSave',
			'Crud.beforeRender' => 'beforeRender'
		);
	}

	public function beforeHandle(CakeEvent $e)
	{
		$controller = $e->subject->controller;
		$model = $e->subject->model;

		if ($this->_grantInlineEdit($e)) {
			$args = $this->listArgs();
			$action = $e->subject->crud->action();

			$action->view('InlineEdit./Elements/inline_edit');
			$controller->Modals->changeConfig('layout', 'clean');

			// more strict variation of saveOptions,
			// for the other variation @see beforeSave() method
			// $action->saveOptions([
			// 	'fieldList' => [
			// 		$model->primaryKey,
			// 		$args->field
			// 	]
			// ]);
		}
	}

	public function beforeSave(CakeEvent $e)
	{
		if ($this->_grantInlineEdit($e)) {
			$model = $this->_model();
			$subject = $e->subject;
			$request = $subject->request;
			$id = $subject->id;
			$data = $request->data;
			$receivedFields = $request->data[$model->alias];

			$query = [
				'conditions' => [
					$model->escapeField() => $id
				],
				'recursive' => -1
			];

			$item = $model->find('first', $query);

			// unset($data[$model->alias][self::REQUEST_PARAM]);

			$data = Hash::merge($item, $data);
			$e->subject->request->data = $data;

			// less strict variation of saveOptions than the one used in beforeHandle() method
			$action = $e->subject->crud->action();
			$action->saveOptions([
				'fieldList' => [
					$model->alias => array_keys($receivedFields)
				]
			]);
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
		if ($this->_grantInlineEdit($e)) {
			$this->_controller()->set('InlineEdit', new InlineEditView($e->subject));
		}
	}

	public function afterSave(CakeEvent $e)
	{
		$subject = $e->subject;
		$controller = $subject->controller;

		// conditions needed to pass to show the "click to reload" button
		// in the flash message
		$conds = $this->_grantInlineEdit($e);
		$conds &= $subject->success == true;
		$conds = $conds && $this->getField()->hasDependency();

		if ($conds) {
			$text = null;

			$text .= '<span class="pnotify-block">';
			$text .= __('Some data may have been left outdated');
			$text .= '</span>';

			$text .= '<a data-yjs-request="app/triggerRequest/.advanced-filter-object" data-yjs-event-on="click" class="pnotify-block inline-edit-reload">';
			$text .= __('Click here to reload');
			$text .= '</a>';
			$text .= '<script>new YoonityJS.InitTemplate({template: $(".ui-pnotify")});</script>';

			$controller->Flash->primary($text);
		}
	}

	/**
	 * List provided arguments in the request.
	 * 
	 * @return Object       Parameters.
	 */
	public function listArgs()
	{
		$args = $this->_request()->params['pass'];

		$params = new stdClass();
		$params->id = $args[0];
		$params->field = $args[1];
		$params->uuid = $args[2];

		return $params;
	}

	/**
	 * Get FieldDataEntity for the current field being edited.
	 * 
	 * @return FieldDataEntity
	 */
	public function getField()
	{
		$args = $this->listArgs();
		
		return $this->_model()->getFieldDataEntity($args->field);
	}

	/**
	 * Method checks if current request is really a request for inline edit of some field
	 * and current action can proceed with this request.
	 * 
	 * @param  CakeEvent $e
	 * @return boolean		True if it is, False otherwise.
	 */
	protected function _grantInlineEdit(CakeEvent $e)
	{
		$action = $e->subject->crud->action();

		// if current action is Edit action
		$conds = $action instanceof EditCrudAction;

		// and current request is indeed Inline Edit request
		$conds &= $this->_isInlineEditRequest($e->subject->request);

		return $conds;
	}

	/**
	 * Check if the current request is actually request for Inline Edit action.
	 * 
	 * @param  CakeRequest $request
	 * @return boolean              True if it is a request for Inline Edit, False otherwise.
	 */
	protected function _isInlineEditRequest(CakeRequest $request)
	{
		$model = $this->_model();
		$alias = $model->alias;

		$queryConds = isset($request->query[self::REQUEST_PARAM]) && $request->query[self::REQUEST_PARAM];
		$dataConds = isset($request->data[$alias][self::REQUEST_PARAM]) && $request->data[$alias][self::REQUEST_PARAM];

		$conds = $queryConds || $dataConds;
		// $conds &= $request->is('ajax');

		return $conds;
	}

}
