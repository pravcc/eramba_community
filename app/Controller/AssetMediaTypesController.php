<?php
App::uses('AppController', 'Controller');

class AssetMediaTypesController extends AppController
{
	public $name = 'AssetMediaTypes';

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

	public function beforeFilter() {
		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'history', 'restore']);

		parent::beforeFilter();

		$this->title = __('Asset Types');
		$this->subTitle = __('');
	}

	public function index() {
		$this->subTitle = __('Asset types lists');

		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Create a Asset Type');

		//set asset type as editable
		$this->request->data['AssetMediaType']['editable'] = 1;
		$this->Crud->on('beforeRender', [$this, '_beforeAddEditRender']);

		return $this->Crud->execute();
	}

	public function edit($id = null) {
		$this->title = __('Edit an Asset Type');
		$this->Crud->on('beforeRender', [$this, '_beforeAddEditRender']);
		$this->Crud->on('afterFind', [$this, '_afterEditFind']);

		return $this->Crud->execute();
	}

	public function _beforeAddEditRender(CakeEvent $e)
	{
		$this->_FieldDataCollection->get('name')->toggleEditable(true);
	}

	public function _afterEditFind(CakeEvent $e)
	{
		if (!$e->subject->item['AssetMediaType']['editable']) {
			throw new ForbiddenException("This is a system type that cannot be edited.", 1);
			
		}
	}

	public function delete($id = null) {
		$this->title = __('Delete a Asset Type');

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