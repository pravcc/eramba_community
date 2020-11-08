<?php
App::uses('AppController', 'Controller');

class GoalAuditImprovementsController extends AppController
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

	public function beforeFilter() {
		$this->Ajax->settings['modules'] = [];

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

		$audit = ClassRegistry::init('GoalAudit')->find('first', [
			'conditions' => [
				'GoalAudit.id' => $auditId
			],
			'recursive' => -1
		]);

		if (empty($audit)) {
			throw new NotFoundException();
		}

		$this->request->data['GoalAuditImprovement']['user_id'] = $this->logged['id'];
		$this->request->data['GoalAuditImprovement']['goal_audit_id'] = $audit['GoalAudit']['id'];

		$this->Crud->on('beforeRender', [$this, '_beforeRender']);

		return $this->Crud->execute();
	}

	public function edit($auditId = null) {
		$this->title = __('Edit an Audit Improvement');

		$this->request->data['GoalAuditImprovement']['user_id'] = $this->logged['id'];
		unset($this->request->data['GoalAuditImprovement']['goal_audit_id']);

		$this->Crud->on('beforeRender', [$this, '_beforeRender']);

		return $this->Crud->execute();
	}

	public function _beforeRender(CakeEvent $event)
	{
		$this->Modals->changeConfig('footer.buttons.saveBtn.options.data-yjs-on-modal-close', '@reload-parent');
	}
}
