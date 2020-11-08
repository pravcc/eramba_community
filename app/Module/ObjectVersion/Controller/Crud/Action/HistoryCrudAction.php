<?php
App::uses('CrudAction', 'Crud.Controller/Crud');
App::uses('CrudActionTrait', 'Controller/Crud/Trait');
App::uses('Hash', 'Utility');
App::uses('ObjectVersionHistory', 'ObjectVersion.Lib');

class HistoryCrudAction extends CrudAction
{
	use CrudActionTrait;

	const ACTION_SCOPE = CrudAction::SCOPE_MODEL;
	
	public function __construct(CrudSubject $subject, array $defaults = [])
	{
		$defaults = Hash::merge([
            'view' => 'ObjectVersion./ObjectVersion/history'
        ], $defaults);

        parent::__construct($subject, $defaults);
	}

	/**
	 * HTTP GET handler
	 *
	 * @throws NotFoundException If record not found
	 * @param string $id
	 * @return void
	 */
	protected function _get($id = null)
	{
		$controller = $this->_controller();
		$model = $this->_model();
		$modelAlias = $this->_model()->alias;

		//
        // Init modal
        $controller->Modals->init();
        if (count($controller->Modals->getBreadcrumbs()) == 0) {
        	$TargetModel = ClassRegistry::init($modelAlias);
        	if (!empty($TargetModel)) {
        		$controller->Modals->addBreadcrumb($TargetModel->label(), true);
        	}
        }
        //

		$historyClass = new ObjectVersionHistory($modelAlias, $id);
		$controller->set('historyClass', $historyClass);

		$this->_trigger('beforeRender');
	}
}
