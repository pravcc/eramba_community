<?php
App::uses('Component', 'Controller');
App::uses('Hash', 'Utility');

class CustomValidatorComponent extends Component {
    public $components = ['Crud'];
    public $settings = [
        'listenerClass' => 'CustomValidator.CustomValidator'
    ];

    public function __construct(ComponentCollection $collection, $settings = []) {
        if (empty($this->settings)) {
            $this->settings = [];
        }

        $settings = array_merge($this->settings, (array)$settings);
        parent::__construct($collection, $settings);
    }

    public function initialize(Controller $controller) {
        $this->controller = $controller;

        $this->Crud->addListener('CustomValidator', $this->settings['listenerClass']);

        $this->controller->helpers[] = 'CustomValidator.CustomValidatorFields';

        // $this->setConfigData();
    }

    public function setConfigData($model = null) {
        if ($model === null) {
            $model = $this->controller->modelClass;
        }

        $data = $this->controller->{$model}->getCustomValidatorConfigData();

        $this->controller->set('customValidator', $data);
    }
}
