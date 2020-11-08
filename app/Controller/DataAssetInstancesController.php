<?php
App::uses('AppController', 'Controller');

/**
 * @section
 */
class DataAssetInstancesController extends AppController
{
	public $helpers = ['UserFields.UserField'];
	public $components = [
		'Search.Prg', 'Paginator', 'Pdf', 'ObjectStatus.ObjectStatus',
		//'Visualisation.Visualisation',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true,
				],
			],
			'listeners' => [
				'Api', 'ApiPagination', 'Widget.Widget', 'Visualisation.Visualisation',
				'.ModuleDispatcher' => [
					'listeners' => [
						'Reports.Reports',
					]
				]
			]
		],
		'UserFields.UserFields'
	];

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

		$this->title = __('Data Flow Analysis');
		$this->subTitle = __('Describes flows for each Data asset in the scope of this GRC program');
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');
		$this->Crud->addListener('ObjectStatus', 'ObjectStatus.ObjectStatus');

		$this->Crud->on('beforeFilter', array($this, '_beforeFilter'));

		return $this->Crud->execute();
	}

	public function _beforeFilter(CakeEvent $event)
	{
		// and attach an event to the advanced filter object to additionally make changes to the final query
		$AdvancedFiltersObject = $event->subject->AdvancedFiltersObject;
		$AdvancedFiltersObject->getEventManager()->attach(
			[$this, '_beforeFilterFind'],
			'AdvancedFilter.beforeFind'
		);
	}

	/**
	 * Force contain of DataAssetSetting.
	 */
	public function _beforeFilterFind(CakeEvent $event)
	{
		$event->data[0]['contain'][] = 'DataAssetSetting';
	}
}
