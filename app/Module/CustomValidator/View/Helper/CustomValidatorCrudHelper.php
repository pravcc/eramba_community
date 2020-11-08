<?php
App::uses('CrudHelper', 'View/Helper');
App::uses('Router', 'Routing');

class CustomValidatorCrudHelper extends CrudHelper
{
	public $helpers = ['Html', 'LimitlessTheme.LayoutToolbar'];

	public function implementedEvents()
    {
        return [
            'LayoutToolbar.beforeRender' => ['callable' => 'beforeLayoutToolbarRender', 'priority' => 50],
        ];
    }

	public function beforeLayoutToolbarRender($event)
	{
		$CustomValidator = $this->_View->get('CustomValidator');

		if ($CustomValidator->enabled()) {
			$this->_setToolbar($CustomValidator);
		}
	}

	protected function _setToolbar($CustomValidator)
	{
		$slug = 'custom-validator';

		$this->LayoutToolbar->addItem(__('Treatment Options'), '#', [
			'parent' => 'settings',
			'slug' => $slug,
		]);

		$valiators = $CustomValidator->getValidators();

		foreach ($valiators as $validatorName => $validator) {
			$validatorSlug = "{$slug}-{$validatorName}";

			$this->LayoutToolbar->addItem($validator['title'], '#', [
				'parent' => $slug,
				'slug' => $validatorSlug,
			]);

			$this->LayoutToolbar->addItem(__('Edit'), '#', [
				'parent' => $validatorSlug,
				'slug' => "{$validatorSlug}-edit",
				'icon' => 'pencil',
				'data-yjs-request' => 'crud/showForm',
				'data-yjs-target' => 'modal',
				'data-yjs-event-on' => 'click',
				'data-yjs-datasource-url' =>  Router::url([
					'plugin' => 'custom_validator',
					'controller' => 'customValidator',
					'action' => 'setup',
					$CustomValidator->getSubject()->model->modelFullAlias(),
					$validatorName
				]),
			]);
		}
	}
}
