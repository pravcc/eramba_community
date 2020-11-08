<?php
App::uses('AppController', 'Controller');

class AssetClassificationsController extends AppController
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
				'add' => [
					'enabled' => true,
					'saveMethod' => 'saveAssociated'
				],
				'edit' => [
					'enabled' => true,
					'saveMethod' => 'saveAssociated'
				],
				'delete' => [
					'enabled' => true,
					'view' => 'delete'
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

		$this->title = __('Asset Classifications');
		$this->subTitle = __('Asset classification is a common good practice and a requirement in certain standards such as ISO 27001 The classification must be defined according to business needs. Once the classification has been defined, it can be applied on every identified asset.');
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete an Asset Classification.');

		$this->set('isUsed', $this->AssetClassification->isUsed($id));
		$this->Crud->on('beforeRender', [$this, '_deleteBeforeRender']);

		return $this->Crud->execute();
	}

	public function _deleteBeforeRender(CakeEvent $event) {
		if (!empty($event->subject->request->params['pass'][0])) {
			$id = $event->subject->request->params['pass'][0];
			$isUsed = $this->AssetClassification->isUsed($id);
			$this->set('isUsed', $isUsed);

			if ($isUsed) {
				$this->Modals->changeConfig('footer.buttons.deleteBtn.visible', false);
			}
		}
	}

	public function add() {
		$this->title = __('Create an Asset Classification');
		$this->initAddEditSubtitle();

		$this->Crud->on('beforeSave', [$this, '_beforeSave']);

		return $this->Crud->execute();
	}

	public function edit($id = null) {
		$this->title = __('Edit an Asset Classification');
		$this->initAddEditSubtitle();

		$this->set('isUsed', $this->AssetClassification->isUsed($id));

		$this->Crud->on('beforeSave', [$this, '_beforeSave']);

		return $this->Crud->execute();
	}

	public function _beforeSave(CakeEvent $event)
	{
		if (!empty($this->request->data['AssetClassification']['asset_classification_type_id'])) {
			unset($this->request->data['AssetClassificationType']);
		}
	}

	public function history($id)
	{
		return $this->Crud->execute();
	}

	public function restore($autidId)
	{
		return $this->Crud->execute();
	}

	public function getCriteria()
	{
		$data = $this->request->data['Asset']['AssetClassification'];
		$value = current($data);

		$data = $this->AssetClassification->find('first', [
			'conditions' => [
				'AssetClassification.id' => $value
			],
			'recursive' => -1
		]);

		$this->set(compact('data'));
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('Asset classification is a common good practice and a requirement in certain standards such as ISO 27001:2005. The classification must be defined according to business needs. Once the classification has been defined, it can be applied on every identified asset.');
	}
}
