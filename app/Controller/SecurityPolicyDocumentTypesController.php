<?php
App::uses('AppController', 'Controller');

class SecurityPolicyDocumentTypesController extends AppController
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
				'delete' => [
					'enabled' => true,
					'view' => 'delete'
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

		$this->title = __('Security Policy Document Types');
		$this->subTitle = __('');
	}

	public function index() {
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Create a Security Policy Document Type');

		$this->request->data['AssetMediaType']['editable'] = 1;
		$this->Crud->on('beforeRender', [$this, '_beforeAddEditRender']);

		return $this->Crud->execute();
	}

	public function edit($id) {
		$this->title = __('Edit a Security Policy Document Type');

		if (!$this->SecurityPolicyDocumentType->isEditable($id)) {
			throw new NotFoundException();
		}
		$this->Crud->on('afterFind', [$this, '_afterEditFind']);
		$this->Crud->on('beforeRender', [$this, '_beforeAddEditRender']);

		return $this->Crud->execute();
	}

	public function _beforeAddEditRender(CakeEvent $e)
	{
		$this->_FieldDataCollection->get('name')->toggleEditable(true);
	}

	public function _afterEditFind(CakeEvent $e)
	{
		$id = $e->subject->id;
		if (!$this->SecurityPolicyDocumentType->isEditable($id)) {
			throw new ForbiddenException("This is a system type that cannot be edited.", 1);
		}
	}

	public function delete() {
		$this->title = __('Delete a Security Policy Document Type');

		$this->Crud->on('beforeDelete', array($this, '_beforeDelete'));
		$this->Crud->on('beforeRender', [$this, '_deleteBeforeRender']);

		return $this->Crud->execute();
	}

	public function _deleteBeforeRender(CakeEvent $event) {
		if (!empty($event->subject->request->params['pass'][0])) {
			$id = $event->subject->request->params['pass'][0];
			$isDeletable = $this->SecurityPolicyDocumentType->isDeletable($id);
			$this->set('isDeletable', $isDeletable);

			if (!$isDeletable) {
				$this->Modals->changeConfig('footer.buttons.deleteBtn.visible', false);
			}
		}
	}

	public function _beforeDelete(CakeEvent $event)
	{
		$id = $event->subject->id;
		if (!$this->SecurityPolicyDocumentType->isDeletable($id)) {
			throw new ForbiddenException(__('There are policies using this tag and therefore we cant delete this item. Please use filters to select and update all policies using this tag to another tag.'), 1);
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
}