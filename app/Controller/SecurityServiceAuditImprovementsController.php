<?php
App::uses('AppController', 'Controller');

class SecurityServiceAuditImprovementsController extends AppController
{
	public $helpers = [];
	public $components = [
		'ObjectStatus.ObjectStatus',
		'Crud.Crud' => [
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
		$this->Crud->enable(['add', 'edit', 'delete']);

		parent::beforeFilter();

		$this->title = __('Audit Improvement');
		$this->subTitle = __('');
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete an Audit Improvement.');

		return $this->Crud->execute();
	}

	public function add($auditId = null) {
		$this->title = __('Create an Audit Improvement');

		$this->Crud->on('beforeRender', [$this, '_beforeRender']);

		$this->addEditCommonProcess($auditId);

		return $this->Crud->execute();
	}

	public function edit($auditId = null) {
		$this->title = __('Edit an Audit Improvement');

		$this->Crud->on('beforeRender', [$this, '_beforeRender']);

		$this->addEditCommonProcess($auditId);

		return $this->Crud->execute();
	}

	public function _beforeRender(CakeEvent $event)
	{
		$this->Modals->changeConfig('footer.buttons.saveBtn.options.data-yjs-on-modal-close', '@reload-parent');
	}

	private function addEditCommonProcess($auditId) {
		$audit = $this->getAudit($auditId);

		if (empty($audit)) {
			throw new NotFoundException();
		}

		$this->set('security_service_name', $audit['SecurityService']['name']);
		$this->set('audit_id', $auditId);

		if ($this->request->is('post')) {
			$this->request->data['SecurityServiceAuditImprovement']['security_service_audit_id'] = $auditId;
			$this->request->data['SecurityServiceAuditImprovement']['user_id'] = $this->logged['id'];
		}
	}

	private function getAudit($id) {
		$this->loadModel('SecurityServiceAudit');
		$audit = $this->SecurityServiceAudit->find('first', array(
			'conditions' => array(
				'SecurityServiceAudit.id' => $id
			),
			'fields' => array('SecurityService.name', 'SecurityService.id'),
			'recursive' => 0
		));

		return $audit;
	}
}
