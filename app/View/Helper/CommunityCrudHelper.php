<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('Router', 'Routing');
App::uses('AppModule', 'Lib');

class CommunityCrudHelper extends CrudHelper
{
	public $helpers = ['Html', 'Community', 'LimitlessTheme.LayoutToolbar'];

	public function implementedEvents()
    {
        return [
            'LayoutToolbar.beforeRender' => [
            	['callable' => 'beforeLayoutToolbarRenderNotificationSystem', 'priority' => 55],
            	['callable' => 'beforeLayoutToolbarRenderCustomFields', 'priority' => 80],
            	['callable' => 'beforeLayoutToolbarRenderReports', 'priority' => 53],
            ],
        ];
    }

    /**
     * Check if $listener is part of default controller listener config.
     * 
     * @param  string  $listener Listener name.
     * @return boolean           True if is part of default config, False otherwise.
     */
    protected function _isListenerDispatched($listener)
    {
    	$Community = $this->_View->get('Community');
    	$moduleDispatcherConfig = $Community->getModuleDispatcherConfig();

    	if (is_array($moduleDispatcherConfig) && in_array($listener, $Community->getModuleDispatcherConfig())) {
    		return true;
    	}

    	return false;
    }

	public function beforeLayoutToolbarRenderNotificationSystem($event)
	{
		if ($this->_isListenerDispatched('NotificationSystem.NotificationSystem') && !AppModule::loaded('NotificationSystem')) {
			$this->LayoutToolbar->addItem(
				__('Notifications') . ' ' . $this->Community->enterpriseLabel(),
				$this->Community->enterpriseUrl(),
				[
					'target' => '_blank',
					'class' => 'enterprise-toolbar-button'
				]
			);
		}
	}

	public function beforeLayoutToolbarRenderCustomFields($event)
	{
		if ($this->_isListenerDispatched('CustomFields.CustomFields') && !AppModule::loaded('CustomFields')) {
			$this->LayoutToolbar->addItem(
				__('Customization') . ' ' . $this->Community->enterpriseLabel(),
				$this->Community->enterpriseUrl(),
				[
					'target' => '_blank',
					'class' => 'enterprise-toolbar-button'
				]
			);
		}
	}

	public function beforeLayoutToolbarRenderReports($event)
	{
		if ($this->_isListenerDispatched('Reports.Reports') && !AppModule::loaded('Reports')) {
			$this->LayoutToolbar->addItem(
				__('Reports') . $this->Community->enterpriseLabel(),
				$this->Community->enterpriseUrl(),
				[
					'target' => '_blank',
					'class' => 'enterprise-toolbar-button'
				]
			);
		}
	}
}
