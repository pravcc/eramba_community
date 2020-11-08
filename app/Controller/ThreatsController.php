<?php
App::uses('AppController', 'Controller');

class ThreatsController extends AppController
{
	public $name = 'Threats';

	public $helpers = [];
	public $components = [
		'Paginator',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				]
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

		$this->title = __('Threats');
		$this->subTitle = __('');
	}

	public function index() {
		$this->subTitle = __('Threats lists');

		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Create a Threat');

		return $this->Crud->execute();
	}

	public function edit() {
		$this->title = __('Edit a Threat');

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->title = __('Delete a Threat');

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