<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Router', 'Routing');
App::uses('FormReloadListener', 'Controller/Crud/Listener');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');

class FormReloadHelper extends AppHelper
{
	public $helpers = ['Html'];

	public function getReloadUrl($options = [])
	{
		$url = Router::reverseToArray($this->_View->request);

		$url['?'][FormReloadListener::REQUEST_PARAM] = true;

		if (!empty($options['field'])) {
			$url['?'][FormReloadListener::REQUEST_PARAM_FIELD] = ($options['field'] instanceof FieldDataEntity) ? $options['field']->getFieldName() : $options['field'];
		}

		return Router::url($url);
	}

	/**
	 * Generates form reload trigger elem options.
	 * 
	 * @param array $options
	 * @return array elem options.
	 */
	public function triggerOptions($options = [])
	{
		$options = array_merge([
			'on' => 'change',
			'form' => $this->_View->get('formName'),
			'modalId' => (!empty($this->_View->get('modal'))) ? $this->_View->get('modal')->getModalId() : '',
		], $options);

		if (empty($options['url'])) {
			$options['url'] = $this->getReloadUrl($options);
		}

		return [
			'data-yjs-request' => 'crud/submitForm',
			'data-yjs-target' => 'modal',
			'data-yjs-datasource-url' => $options['url'],
			'data-yjs-forms' => $options['form'],
			'data-yjs-modal-id' => $options['modalId'],
			'data-yjs-event-on' => $options['on']
		];
	}
}