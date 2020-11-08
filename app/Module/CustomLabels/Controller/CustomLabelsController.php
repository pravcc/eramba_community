<?php
App::uses('CustomLabelsAppController', 'CustomLabels.Controller');
App::uses('FieldGroupEntity', 'FieldData.Model/FieldData');
App::uses('FieldDataCollection', 'FieldData.Model/FieldData');

class CustomLabelsController extends CustomLabelsAppController
{
	public $helpers = [];

	public $components = [];

	public $uses = ['CustomLabels.CustomLabel'];

	protected $_appControllerConfig = [
		'components' => [
		],
		'helpers' => [
		],
		'elements' => [
		]
	];

	public function beforeFilter()
	{
		parent::beforeFilter();

		$this->title = __('Custom Labels');
		$this->subTitle = __('');
	}

	/**
	 * Edit section custom labels.
	 * 
	 * @param string $model
	 * @return void
	 */
	public function edit($model)
	{
		$Model = ClassRegistry::init($model);

		$formData = $Model->getCustomLabelsFormData();

		if ($this->request->is('post')) {
			if ($this->CustomLabel->saveMany($this->_getRequestSaveData())) {
				$this->Flash->set(__('Custom label successfully updated.'), ['element' => 'success']);
			}
			else {
				$this->Flash->set(__('Something went wrong, please try it again.'), ['element' => ['error']]);
			}
		}
		else {
			$this->request->data['CustomLabel'] = $formData;
		}

		$this->set('formName', 'CustomLabelsEdit');
		$this->set('FieldDataCollection', $this->_getEditFieldDataCollection($formData));

        $formUrl = Router::url(Router::reverseToArray($this->request));
        $this->set('formUrl', $formUrl);

        $this->Modals->init();
        $this->Modals->setHeaderHeading(__('Custom Labels (%s)', $Model->label(['singular' => true])));
        $this->Modals->showFooterSaveButton();
	}

	/**
	 * Get data to save from request.
	 * 
	 * @return array Save data.
	 */
	protected function _getRequestSaveData()
	{
		$data = [];

		foreach ($this->request->data['CustomLabel'] as $key => $item) {
			if (trim($item['label']) == '') {
				$item['label'] = null;
			}
			if (trim($item['description']) == '') {
				$item['description'] = null;
			}

			$data[$key] = $item;
		}

		return $data;
	}

	/**
	 * Build FieldDataCollection for edit form.
	 * 
	 * @param array $formData 
	 * @return FieldDataCollection
	 */
	protected function _getEditFieldDataCollection($formData)
	{
		$this->CustomLabel->fieldGroupData = [
			'default' => new FieldGroupEntity(['__key' => 'default', 'label' => __('Roles')])
		];

		$Collection = new FieldDataCollection([], $this->CustomLabel);

		foreach ($formData as $key => $item) {
			$Collection->add("{$key}.id", [
				'type' => 'hidden',
				'editable' => true,
			]);

			$Collection->add("{$key}.type", [
				'type' => 'hidden',
				'editable' => true,
			]);

			$Collection->add("{$key}.model", [
				'type' => 'hidden',
				'editable' => true,
			]);

			$Collection->add("{$key}.subject", [
				'type' => 'hidden',
				'editable' => true,
			]);

			$Collection->add("{$key}.label", array_merge([
				'type' => 'text',
				'label' => 'CustomRole',
				'editable' => true,
				'renderHelper' => ['CustomLabels.CustomLabels', 'labelField'],
				'description' => __('Provide a name for this custom role.')
			], $item['fieldDataConfig']));

			$Collection->add("{$key}.description", [
				'type' => 'textarea',
				'label' => false,
				'editable' => true,
				'renderHelper' => ['CustomLabels.CustomLabels', 'descriptionField'],
				'description' => __('Provide a description for this custom role.')
			]);
		}

		return $Collection;
	}

}
