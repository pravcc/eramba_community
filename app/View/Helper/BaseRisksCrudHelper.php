<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('Router', 'Routing');
App::uses('CakeEvent', 'Event');

class BaseRisksCrudHelper extends CrudHelper
{
	public $helpers = ['Html', 'LimitlessTheme.LayoutToolbar'];

	public function implementedEvents()
    {
        return [
            'LayoutToolbar.beforeRender' => ['callable' => 'beforeLayoutToolbarRender', 'priority' => 50],
        ];
    }

	public function beforeLayoutToolbarRender(CakeEvent $event)
	{
		$this->_setToolbar($event);
	}

	protected function _setToolbar(CakeEvent $event)
	{
		$currentModel = $event->subject->model->alias;

		if (empty($this->LayoutToolbar->config('settings'))) {
			$this->LayoutToolbar->addItem(__('Settings'), '#', [
				'slug' => 'settings',
			]);
		}

		$this->LayoutToolbar->addItem(__('Risk Appetite'), '#', [
			// 'icon' => 'plus2',
			'parent' => 'settings',
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-target' => 'modal',
			'data-yjs-event-on' => 'click',
			'data-yjs-modal-size-width' => 80,
			'data-yjs-datasource-url' =>  Router::url([
				'controller' => 'riskAppetites',
				'action' => 'edit',
				1
			]),
		]);

		$calcId = 1;
		if ($currentModel == 'Risk') {
			$calcId = 1;
		}

		if ($currentModel == 'ThirdPartyRisk') {
			$calcId = 2;
		}

		if ($currentModel == 'BusinessContinuity') {
			$calcId = 3;
		}

		$this->LayoutToolbar->addItem(__('Calculation Method'), '#', [
			// 'icon' => 'plus2',
			'parent' => 'settings',
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-target' => 'modal',
			'data-yjs-event-on' => 'click',
			'data-yjs-modal-size-width' => 80,
			'data-yjs-datasource-url' =>  Router::url([
				'controller' => 'riskCalculations',
				'action' => 'edit',
				$calcId
			]),
		]);

		$this->LayoutToolbar->addItem(__('Classifications'), '#', [
			'parent' => 'settings',
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-target' => 'modal',
			'data-yjs-event-on' => 'click',
			'data-yjs-modal-size-width' => 80,
			'data-yjs-datasource-url' =>  Router::url([
				'controller' => 'riskClassifications',
				'action' => 'index'
			])
		]);

		$this->LayoutToolbar->addItem(__('Threats'), '#', [
			'parent' => 'settings',
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-target' => 'modal',
			'data-yjs-event-on' => 'click',
			'data-yjs-modal-size-width' => 80,
			'data-yjs-datasource-url' =>  Router::url([
				'controller' => 'threats',
				'action' => 'index'
			])
		]);

		$this->LayoutToolbar->addItem(__('Vulnerabilities'), '#', [
			'parent' => 'settings',
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-target' => 'modal',
			'data-yjs-event-on' => 'click',
			'data-yjs-modal-size-width' => 80,
			'data-yjs-datasource-url' =>  Router::url([
				'controller' => 'vulnerabilities',
				'action' => 'index'
			])
		]);

		$this->LayoutToolbar->addItem(__('Residual Risk'), '#', [
			'parent' => 'settings',
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-target' => 'modal',
			'data-yjs-event-on' => 'click',
			// 'data-yjs-datasource-url' =>  Router::url([
			// 	'controller' => 'settings',
			// 	'action' => 'residualRisk'
			// ]),
			'data-yjs-datasource-url' =>  Router::url([
				'controller' => 'settings',
				'action' => 'edit',
				'RISK_GRANULARITY'
			]),
		]);
	}
}
