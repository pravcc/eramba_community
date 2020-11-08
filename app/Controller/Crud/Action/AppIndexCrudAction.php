<?php

App::uses('IndexCrudAction', 'Crud.Controller/Crud/Action');
App::uses('CrudActionTrait', 'Controller/Crud/Trait');

class AppIndexCrudAction extends IndexCrudAction {
	const VIEW_ITEM_QUERY = 'view_item_id';
	use CrudActionTrait;
	
	public function __construct(CrudSubject $subject, array $defaults = []) {
		$defaults = am([
   //          'filter' => [
			// 	'enabled' => true
			// ],
			// 'view' => '/Elements/section/index'
        ], $defaults);

        parent::__construct($subject, $defaults);
	}

	public function paginationConfig() {
		// shortcuts
		$controller = $this->_controller();
		$model = $this->_model();

		parent::paginationConfig();
		$settings = &$this->_controller()->Paginator->settings;

		$settings['order'] = [
				$model->alias . '.' . $model->displayField => 'ASC',
				$model->alias . '.' . $model->primaryKey => 'ASC'
		];

		return $settings;
	}

	protected function _get() {
		// $response = $this->handleFilterAction($this->config('filter'));
		
		// if the return value from FilterCrudAction is a CakeResponse instance
		// it means it is an active advanced filter
		// if ($response instanceof CakeResponse) {
		// 	return $response;
		// }
		
		return parent::_get();
	}

	protected function _post() {
		return $this->_get();
	}


}
