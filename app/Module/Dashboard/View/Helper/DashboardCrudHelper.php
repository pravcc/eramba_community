<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('Router', 'Routing');

class DashboardCrudHelper extends CrudHelper
{
	public $helpers = ['Html', 'SectionCrud', 'LimitlessTheme.LayoutToolbar'];

	public function implementedEvents()
    {
        return [
            'LayoutToolbar.beforeRender' => ['callable' => 'beforeLayoutToolbarRender', 'priority' => 50],
        ];
    }

	public function beforeLayoutToolbarRender($event)
	{
		$Dashboard = $this->_View->get('Dashboard');

		$this->_setToolbar($Dashboard);
	}

	protected function _setToolbar($Dashboard)
	{
		$this->SectionCrud->setAddAction();

		$this->LayoutToolbar->addItem(__('Store Current KPI Values'), [
			'plugin' => 'dashboard',
			'controller' => 'dashboardKpis',
			'action' => 'store_logs'
		], [
			'parent' => 'actions',
			// 'icon' => 'add-to-list'
		]);

		$this->LayoutToolbar->addItem(__('Recalculate Current KPI Values'), [
			'plugin' => 'dashboard',
			'controller' => 'dashboardKpis',
			'action' => 'recalculate_values'
		], [
			'parent' => 'actions',
			// 'icon' => 'add-to-list'
		]);

		$currentKpiExportAction = [__('Current KPI Values'), [
			'plugin' => 'dashboard',
			'controller' => 'dashboardKpis',
			'action' => 'export_values'
		], [
		]];

		$historicalKpiExportAction = [__('Historical KPI Values'), [
			'plugin' => 'dashboard',
			'controller' => 'dashboardKpis',
			'action' => 'export_logs'
		], [
		]];

		// we add new export button group dropdown
		$this->LayoutToolbar->addItem(__('Export'), '#', [
			'slug' => 'export',
		], [
			$currentKpiExportAction,
			$historicalKpiExportAction
		]);
	}
}
