<?php
App::uses('CrudActionTrait', 'Controller/Crud/Trait');
App::uses('CrudAction', 'Crud.Controller/Crud');


/**
 * Handles 'Widget' Crud actions
 */
class WidgetCrudAction extends CrudAction {

	use CrudActionTrait;

/**
 * Default settings for 'edit' actions
 *
 * `enabled` Is this crud action enabled or disabled
 *
 * `findMethod` The default `Model::find()` method for reading data
 *
 * `view` A map of the controller action and the view to render
 * If `NULL` (the default) the controller action name will be used
 *
 * @var array
 */
	protected $_settings = [
		'enabled' => true,
		'findMethod' => 'count',
		'view' => 'Widget./Widget/index',
		'serialize' => []
	];

/**
 * Constant representing the scope of this action
 *
 * @var integer
 */
	const ACTION_SCOPE = CrudAction::SCOPE_RECORD;

/**
 * HTTP GET handler
 *
 * @throws NotFoundException If record not found
 * @param string $id
 * @return void
 */
	protected function _get($modelName = null, $id = null)
	{
		$this->_commonData($id);

		$data = $this->_findRecord($id);
		if (empty($data)) {
			return $this->_notFound($id);
		}

		$this->_trigger('beforeRender');
	}

/**
 * Find a record from the ID
 *
 * @param string $id
 * @param string $findMethod
 * @return array
 */
	protected function _findRecord($id)
	{
		$model = $this->_model();

		$query = [];
		$query['conditions'] = [$model->escapeField() => $id];

		$findMethod = $this->_getFindMethod();

		$subject = $this->_trigger('beforeFind', compact('query', 'findMethod'));
		return $model->find($subject->findMethod, $subject->query);
	}

/**
 * Throw exception if a record is not found
 *
 * @throws Exception
 * @param string $id
 * @return void
 */
	protected function _notFound($id)
	{
		$this->_trigger('recordNotFound', compact('id'));

		$message = $this->message('recordNotFound', compact('id'));
		$exceptionClass = $message['class'];
		throw new $exceptionClass($message['text'], $message['code']);
	}

/**
 * Set common data and execute common processes.
 *
 * @param mixed $id
 * @return void
 */
    protected function _commonData($id = null)
    {
    	// Set form name
        $formName = $this->_controller()->modelClass . 'Widget';
        $this->_controller()->set('formName', $formName);

        // Set URL
        $request = $this->_request();
        $formUrl = Router::url(Router::reverseToArray($request));
        $this->_controller()->set('formUrl', $formUrl);

        // Init modal
        $this->_controller()->Modals->init();
        if (count($this->_controller()->Modals->getBreadcrumbs()) == 0) {
            $this->_controller()->Modals->addBreadcrumb($this->_model()->label(), true);
        }
        $this->_controller()->Modals->setHeaderHeading($this->_controller()->title);
    }

}
