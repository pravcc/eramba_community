<?php
App::uses('AppController', 'Controller');

class SystemLogsController extends AppController
{
	public $components = [
		'Paginator', 'Search.Prg', 'AdvancedFilters',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
				],
			],
		],
	];
	public $helpers = [];

	public $uses = ['SystemLogs.SystemLog'];

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
		$this->Crud->enable(['index']);

		parent::beforeFilter();

		$this->title = __('System Logs');
		$this->subTitle = __('');
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');

		$this->Crud->on('beforeFilter', function(CakeEvent $event) {
			$AdvancedFiltersObject = $event->subject->AdvancedFiltersObject;
			$AdvancedFiltersObject->getEventManager()->attach([$this, '_beforeFilterFind'], 'AdvancedFilter.beforeFind');
		});

		return $this->Crud->execute();
	}

	public function _beforeFilterFind(CakeEvent $event)
	{
		$event->data[0]['conditions']["{$this->modelClass}.model"] = $this->{$this->modelClass}->relatedModel;
	}
}