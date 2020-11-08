<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('Router', 'Routing');

class AdvancedFiltersCrudHelper extends CrudHelper
{
	public $helpers = ['Html', 'LimitlessTheme.LayoutToolbar'];

	public function implementedEvents()
    {
        return [
            'LayoutToolbar.beforeRender' => ['callable' => 'beforeLayoutToolbarRender', 'priority' => 56],
        ];
    }

	public function beforeLayoutToolbarRender($event)
	{
		$AdvancedFilters = $this->_View->get('AdvancedFilters');

		$Subject = $AdvancedFilters->getSubject();

		$this->_handleRoot(__('Filters'), $Subject->model->alias);
		$this->_handleChildren($Subject->model->alias);
	}

	protected function _handleRoot($label, $slug, $parent = null)
	{
		$AdvancedFilters = $this->_View->get('AdvancedFilters');

		$this->LayoutToolbar->addItem(
			$label,
			'#',
			[
				// 'icon' => 'stack3',
				'notification' => $AdvancedFilters->getCount($slug) ? $AdvancedFilters->getCount($slug) : false,
				'slug' => $slug . '-advanced-filters-filters',
				'parent' => $parent . '-advanced-filters-filters',
			]
		);
	}

	protected function _handleChildren($root)
	{
		$AdvancedFilters = $this->_View->get('AdvancedFilters');
		$Subject = $AdvancedFilters->getSubject();

		$this->LayoutToolbar->addItem(
			__('New'),
			'#',
			[
				'icon' => 'plus2',
				'parent' => $root . '-advanced-filters-filters',
				'escape' => false,
				'data-yjs-request' => 'crud/showForm',
				'data-yjs-target' => 'modal',
				'data-yjs-event-on' => 'click',
				'data-yjs-datasource-url' =>  Router::url([
					'plugin' => 'advanced_filters',
					'controller' => 'advancedFilters',
					'action' => 'add',
					ClassRegistry::init($root)->alias
				])
			]
		);

		if ($AdvancedFilters->hasItems($root)) {
			$this->LayoutToolbar->addItem(
				__('Saved'),
				'#',
				[
					'icon' => 'three-bars',
					'parent' => $root . '-advanced-filters-filters',
					'slug' => $root . '-advanced-filters-saved'
				]
			);

			foreach ($AdvancedFilters->getSavedFilters($root) as $id => $filter) {
				$this->LayoutToolbar->addItem(
					$filter,
					[
						'plugin' => 'advanced_filters',
						'controller' => 'advancedFilters',
						'action' => 'redirectAdvancedFilter',
						$id
					],
					[
						// 'icon' => 'gear',
						'parent' => $root . '-advanced-filters-saved',
						'slug' => 'advanced-filter-' . $id
					]
				);
			}
		}
	}
}
