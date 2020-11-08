<?php
App::uses('AppController', 'Controller');

class AssetLabelsController extends AppController
{
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

		$this->title = __('Asset Label Classifications');
		$this->subTitle = __('Asset Labels are used in order to profile data assets according to the security treatment they require. Typical labels are: Confidential, Secret, Public, Etc. Labels defined in this section are applied to the Asset Identified.');
	}

	public function index() {
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->title = __('Delete an Asset Label Classification');
		$this->subTitle = false;

		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Create an Asset Label Classification');
		$this->initAddEditSubtitle();

		return $this->Crud->execute();
	}

	public function edit($id = null) {
		$this->title = __('Edit an Asset Label Classification');
		$this->initAddEditSubtitle();

		return $this->Crud->execute();
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('Asset Labels are used in order to profile data assets according to the security treatment they require. Typical labels are: Confidential, Secret, Public, Etc. Labels defined in this section are applied to the Asset Identified.');
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
