<?php
App::uses('CustomValidatorAppController', 'CustomValidator.Controller');
App::uses('CustomValidatorField', 'CustomValidator.Model');

class CustomValidatorController extends CustomValidatorAppController {

	public $uses = ['CustomValidator.CustomValidatorField'];
	public $components = [
	];

	protected $_appControllerConfig = [
		'components' => [
		],
		'helpers' => [
		],
		'elements' => [
		]
	];

	public function beforeFilter() {
		parent::beforeFilter();

		if (in_array($this->request->params['action'], ['getValidation'])) {
			$this->Security->csrfCheck = false;
		}

		$this->title = __('Custom Validator');
		$this->subTitle = __('');
	}

	public function setup($model, $validator)
	{
		$Model = ClassRegistry::init($model);

		if (!$Model->Behaviors->enabled('CustomValidator.CustomValidator')) {
			throw new NotFoundException();
		}

		$validatorConfig = $Model->getCustomValidator($validator);

		if (empty($validator) || empty($validatorConfig)) {
			throw new NotFoundException();
		}

		$this->title = $validatorConfig['title'];

		if ($this->request->is('post')) {
			$ret = $Model->saveCustomValidator($validator, $this->request->data['CustomValidatorField']);
			if ($ret) {
				$this->Session->setFlash(__('Custom Validator was succesfully saved.'), FLASH_OK);
			}
			else {
				$this->Session->setFlash(__('Error while saving the data. Please try it again.'), FLASH_ERROR);
			}
		}
		else {
			$this->request->data['CustomValidatorField'] = $Model->getCustomValidatorData($validator);
		}

		$Collection = $Model->getValidatorFieldDataCollection($validator);

		$this->set('ValidatorCollection', $Collection);
		$this->set($Collection->getViewOptions());
        $this->set('formName', 'CustomValidatorEdit');
        $this->set('formUrl', Router::url(Router::reverseToArray($this->request)));

		$this->Modals->init(true);
		$this->Modals->setHeaderHeading($this->title);
        $this->Modals->showFooterSaveButton();
	}

}
