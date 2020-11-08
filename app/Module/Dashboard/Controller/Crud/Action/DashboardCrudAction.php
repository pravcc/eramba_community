<?php

App::uses('CrudAction', 'Crud.Controller/Crud/Action');
App::uses('DashboardKpi', 'Dashboard.Model');

class DashboardCrudAction extends CrudAction {
	protected $_settings = array(
		'enabled' => true,
		'type' => DashboardKpi::TYPE_USER,
		'models' => [
			'SecurityService',
			'Risk'
		]
	);

	public function __construct(CrudSubject $subject, array $defaults = array()) {
        parent::__construct($subject, $defaults);
	}

	protected function _get() {
		$controller = $this->_controller();
		$model = $this->_model();	

		$controller->set('title_for_layout', __('Dashboard'));
		$controller->set('subtitle_for_layout', __('Hello!'));

		$data = $model->find('all', [
			'conditions' => [
				'DashboardKpi.type' => $this->config('type'),
				'DashboardKpi.model' => $this->config('models')
			],
			'recursive' => -1
		]);

		$items = [];
		foreach ($data as &$item) {
			$item['_params'] = ($model->getFilterParams($item['DashboardKpi']['id']));
			$items[$item['DashboardKpi']['model']][] = $item;
			// $item['_url'] = [
			// 	'plugin' => null,
			// 	'controller' => 'aaa',
			// 	'action' => 'index',
			// 	'?' => am(array('advanced_filter' => 1), $item['_params'])
			// ];
		}

		// debug($items);

		$controller->set(array('data' => $items));
		$this->_trigger('beforeRender');
	}

}
