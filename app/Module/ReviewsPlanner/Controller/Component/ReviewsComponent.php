<?php

App::uses('Component', 'Controller');

class ReviewsComponent extends Component {
	public $components = ['Session', 'Crud.Crud'];
	public $settings = [
		'enabled' => true
	];

	/**
	 * Runtime configuration values.
	 * 
	 * @var array
	 */
	protected $_runtime = [
	];

	public function __construct(ComponentCollection $collection, $settings = array()) {
		if (empty($this->settings)) {
			$this->settings = array(
			);
		}

		$settings = array_merge($this->settings, (array)$settings);
		parent::__construct($collection, $settings);
	}

	public function initialize(Controller $controller) {
	}

	public function startup(Controller $controller) {
		$this->model = $this->Crud->getSubject()->model;
		$this->controller = $controller;

		$this->Crud->on('beforeSave', [$this, '_beforeSaveCrud']);
	}

	/**
	 * Handles non-changable date field while editing an item by user.
	 */
	public function _beforeSaveCrud(CakeEvent $event) {
		$model = $this->model;
		$dateColumn = $model->getReviewColumn();

        if (isset($event->subject->id)) {
            $model->id = $event->subject->id;
            $event->subject->request->data[$model->alias][$dateColumn] = $model->field($dateColumn);

            // questionable for bulk edit mutliple save
            // the thing is the while bulk edit, the second item that is being edited has 'dateColumn' missing
            if ($model->validator()->getField($dateColumn) !== null) {
	            $model->validator()->getField($dateColumn)->removeRule('future');
	            unset($model->validate[$dateColumn]['future']);
	        }
        }
    }
	
}
