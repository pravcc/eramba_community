<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('Router', 'Routing');
App::uses('CakeEvent', 'Event');

class SecurityPoliciesCrudHelper extends CrudHelper
{
	public $helpers = ['Html', 'LimitlessTheme.LayoutToolbar', 'LimitlessTheme.ItemDropdown', 'Policy'];

	public function implementedEvents()
    {
        return [
            'LayoutToolbar.beforeRender' => ['callable' => 'beforeLayoutToolbarRender', 'priority' => 50],
            'ItemDropdown.beforeRender' => ['callable' => 'beforeItemDropdownRender', 'priority' => 60],
        ];
    }

    public function beforeItemDropdownRender(CakeEvent $event)
    {
    	$Item = $event->data;
    	$Model = $Item->getModel();

        $this->ItemDropdown->addItem(__('Direct Link'), '#', [
            'data-yjs-request' => 'crud/load',
            'data-yjs-target' => 'modal',
            'data-yjs-datasource-url' => Router::url([
				'controller' => 'securityPolicies',
				'action' => 'getDirectLink',
				$Item->getPrimary()
			]),
            'data-yjs-event-on' => 'click',
            'data-yjs-modal-breadcrumbs' => $Model->label(),
        ]);

        $this->ItemDropdown->addItem(__('Clone'), '#', [
            'data-yjs-request' => 'crud/load',
            'data-yjs-target' => 'modal',
            'data-yjs-datasource-url' => Router::url([
				'controller' => 'securityPolicies',
				'action' => 'duplicate',
				$Item->getPrimary()
			]),
            'data-yjs-event-on' => 'click',
            'data-yjs-modal-size-width' => 80,
            'data-yjs-on-complete' => '#main-toolbar|#main-content',
            'data-yjs-modal-breadcrumbs' => $Model->label(),
        ]);

        $viewUrl = $this->Policy->getDocumentUrl($Item->getPrimary(), true);
		$viewUrl['?']['from_app'] = true;

        $this->ItemDropdown->addItem(__('View'), '#', [
            'data-yjs-request' => 'crud/load',
            'data-yjs-target' => 'modal',
            'data-yjs-datasource-url' => Router::url($viewUrl),
            'data-yjs-event-on' => 'click',
            'data-yjs-modal-size-width' => 80,
            'data-yjs-modal-breadcrumbs' => $Model->label(),
        ]);
    }

	public function beforeLayoutToolbarRender(CakeEvent $event)
	{
		$this->_setToolbar();
	}

	protected function _setToolbar()
	{
		if (empty($this->LayoutToolbar->config('settings'))) {
			$this->LayoutToolbar->addItem(__('Settings'), '#', [
				'slug' => 'settings',
			]);
		}

		$this->LayoutToolbar->addItem(__('Document Types'), '#', [
			'parent' => 'settings',
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-target' => 'modal',
			'data-yjs-event-on' => 'click',
			'data-yjs-modal-size-width' => 80,
			'data-yjs-datasource-url' =>  Router::url([
				'controller' => 'securityPolicyDocumentTypes',
				'action' => 'index'
			])
		]);

		$this->LayoutToolbar->addItem(__('Policy Portal Authentication'), '#', [
			'parent' => 'settings',
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-target' => 'modal',
			'data-yjs-event-on' => 'click',
			'data-yjs-modal-size-width' => 80,
			'data-yjs-datasource-url' =>  Router::url([
				'controller' => 'ldapConnectorAuthentications',
				'action' => 'edit',
				'?' => [
					'tabActive' => 'policy'
				]
			])
		]);
	}
}
