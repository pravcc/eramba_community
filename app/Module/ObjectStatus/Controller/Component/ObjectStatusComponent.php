<?php
App::uses('Component', 'Controller');
App::uses('Hash', 'Utility');

class ObjectStatusComponent extends Component {
    public $components = ['Crud'];
    public $settings = [
        'listenerClass' => 'ObjectStatus.ObjectStatus'
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

        $this->Crud->addListener('ObjectStatus', $this->settings['listenerClass']);

        //load helper
        $this->controller->helpers[] = 'ObjectStatus.ObjectStatus';

        $this->setConfigData();
    }

    public function setConfigData($model = null) {
        if ($model === null) {
            $model = $this->controller->modelClass;
        }

        $data = $this->controller->{$model}->getObjectStatusConfig();

        $data = Hash::remove($data, '{s}.callback');

        $this->controller->set('objectStatus', $data);
    }
}
