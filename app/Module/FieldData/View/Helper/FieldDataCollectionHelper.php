<?php
App::uses('AppHelper', 'View/Helper');
App::uses('FormHelper', 'View/Helper');
App::uses('Hash', 'Utility');

class FieldDataCollectionHelper extends AppHelper
{
	public $settings = [];
	public $helpers = ['Form', 'Html', 'FieldData.FieldData', 'LimitlessTheme.TabsComponent', 'LimitlessTheme.ContentPanels', 'CustomFields.CustomFields'];

	protected $_Collection = null;

	public function setCollection(FieldDataCollection $Collection)
	{
		$this->_Collection = $Collection;
	}

	public function getCollection()
	{
		return $this->_Collection;
	}

	public function table(FieldDataCollection $Collection)
	{

	}

	public function form(FieldDataCollection $Collection, $options = [])
	{
		$options = array_merge([
			'raw' => false,
			'tabs' => true,
			'type' => 'post',
			'url' => null,
			'input' => [],
			'class' => [],
			'submit' => []
		], $options);

		$this->setCollection($Collection);

		$out = null;
		$out .= $this->_formCreate($options);
		$out .= $this->_formFields($options);
		$out .= $this->_formSubmit($options);
		$out .= $this->_formEnd();

		return $out;
	}

	protected function _formCreate($options)
	{
		$params = [
			'novalidate' => true,
			'type' => $options['type'],
			'class' => $options['class'],
		];

		if ($options['url']) {
			$params['url'] = $options['url'];
		}

		if (!empty($options['form_name'])) {
			$params['data-yjs-form'] = $options['form_name'];
		}

		if (!empty($options['modal_id'])) {
			$params['data-yjs-modal-id'] = $options['modal_id'];
		}

		return $this->Form->create($this->getCollection()->getModel()->alias, $params);
	}

	protected function _formFields($options)
	{
		$out = null;

		$fields = $this->_getFieldsByGroup($options);
		if ($options['tabs'] == false) {
			return implode('', $fields);
		}

		$groups = $this->_getFormGroups();

		$tabActive = null;
        if (!empty($this->request->query['tabActive'])) {
            $tabActive = $this->request->query['tabActive'];
        }
		
		return $this->TabsComponent->render([
			'nav' => $groups,
			'content' => $fields,
			'active' => $tabActive
		]);

		return $out;
	}

	protected function _getFieldsByGroup($options)
	{
		$ret = [];
		foreach ($this->getCollection() as $field) {
			if ($field->isEditable()) {
				$ret[$field->getGroup()->getKey()][] = $this->FieldData->input($field, $options['input']);
			}
		}

		foreach ($ret as $key => $arr) {
			$ret[$key] = implode('', $arr);
		}

		return $ret;
	}

	protected function _getFormGroups()
	{
		$groups = [];
		foreach ($this->getCollection() as $field) {
			if ($field->isEditable() && !array_key_exists($field->getGroup()->getKey(), $groups)) {
				$groups[$field->getGroup()->getKey()] = [
					'order' => $field->getGroup()->getOrder(),
					'label' => $field->getGroup()->getLabel(),
					'navItemOptions' => $field->getGroup()->getNavItemOptions()
				];
			}
		}

		return Hash::sort($groups, '{s}.order');
	}

	protected function _formEnd()
	{
		return $this->Form->end();
	}

	protected function _formSubmit($options)
	{
		if (is_callable($options['submit'])) {
			return call_user_func_array($options['submit'], []);
		}

		if (is_string($options['submit'])) {
			return $options['submit'];
		}

		// disabled ordinary form submit as modal resolves subission itself
		return null;
		/*$icon = '<i class="icon-arrow-right14 position-right"></i>';

		return $this->Html->div('text-right', $this->Form->button(__('Submit') . ' ' . $icon, array(
			'type' => 'submit',
			'class' => 'btn btn-primary',
			'div' => false,
			'escape' => false
		)));*/
	}
}