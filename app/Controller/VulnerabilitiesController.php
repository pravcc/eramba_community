<?php
App::uses('AppController', 'Controller');

class VulnerabilitiesController extends AppController
{
	public $name = 'Vulnerabilities';

	public $helpers = [];
	public $components = [
		'Paginator',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				],
			],
			'listeners' => ['Api', 'ApiPagination', '.SubSection']
		],
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
		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'history', 'restore']);

		parent::beforeFilter();

		$this->Security->unlockedActions = array('liveEdit');

		$this->title = __('Vulnerabilities');
		$this->subTitle = __('');
	}

	public function index() {
		$this->subTitle = __('Vulnerabilities lists');

		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Create a Vulnerability');

		return $this->Crud->execute();
	}

	public function edit() {
		$this->title = __('Edit a Vulnerability');

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->title = __('Delete a Vulnerability');

		return $this->Crud->execute();
	}

	public function history($id)
	{
		return $this->Crud->execute();
	}

	public function restore($autidId)
	{
		return $this->Crud->execute();
	}
}