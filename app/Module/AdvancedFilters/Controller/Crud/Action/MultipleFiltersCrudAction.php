<?php
App::uses('CrudAction', 'Crud.Controller/Crud');
App::uses('CrudActionTrait', 'Controller/Crud/Trait');
App::uses('AdvancedFilterUserSetting', 'AdvancedFilters.Model');

class MultipleFiltersCrudAction extends CrudAction {
	const ACTION_SCOPE = CrudAction::SCOPE_MODEL;

	use CrudActionTrait;

	public function __construct(CrudSubject $subject, array $defaults = array()) {
		$defaults = am([
			// contain for the find (moved from model here)
            'contain' => [],
            'filter' => [
				'enabled' => true
			],
			'view' => 'AdvancedFilters./Elements/index'
        ], $defaults);

        parent::__construct($subject, $defaults);
	}

	protected function _get() {
		$controller = $this->_controller();
		$request = $this->_request();

		$dataTables = [];
		$controller->loadModel('AdvancedFilters.AdvancedFilter');
		if (isset($request->query['single_filter_id'])) {
			$AdvancedFiltersObject = $controller->AdvancedFilter->filter($request->query['single_filter_id']);

			$controller->set('AdvancedFiltersObject', $AdvancedFiltersObject);
			$controller->set('id', $request->query['single_filter_id']);
			$this->view('AdvancedFilters./Elements/filter_object');

		}
		elseif (isset($request->query['advanced_filter_id'])) {
			$controller->loadModel('AdvancedFilters.AdvancedFilter');
			$AdvancedFiltersObject = $controller->AdvancedFilter->initSkeleton($request->query['advanced_filter_id']);
			// $request->query = $this->_processQuery($request->query);
			$AdvancedFiltersObject->setFilterValues($request->query);
			$AdvancedFiltersObject->filter();

			$dataTables = [$request->query['advanced_filter_id'] => $AdvancedFiltersObject];
		}
		else {
			$list = $controller->AdvancedFilter->find('list', [
				'conditions' => [
					'AdvancedFilter.model' => $this->_model()->alias,
					'AdvancedFilterUserSetting.default_index' => AdvancedFilterUserSetting::DEFAULT_INDEX
				],
				'fields' => ['id'],
				'recursive' => 0
			]);

			$dataTables = [];
			foreach ($list as $id) {
				$dataTables[$id] = $controller->AdvancedFilter->filter($id);
			}
		}

		$controller->set('dataTables', $dataTables);

		// $controller->set(array('success' => $subject->success, $subject->viewVar => $items));
		$this->_trigger('beforeRender');
	}

	protected function _post() {
		return $this->_get();
	}


}
