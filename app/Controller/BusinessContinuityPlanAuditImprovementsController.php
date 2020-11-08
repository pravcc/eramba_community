<?php
App::uses('AppController', 'Controller');

class BusinessContinuityPlanAuditImprovementsController extends AppController
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

		$audit = ClassRegistry::init('BusinessContinuityPlanAudit')->find('first', [
			'conditions' => [
				'BusinessContinuityPlanAudit.id' => $auditId
			],
			'recursive' => -1
		]);

		if (empty($audit)) {
			throw new NotFoundException();
		}

		$this->request->data['BusinessContinuityPlanAuditImprovement']['user_id'] = $this->logged['id'];
		$this->request->data['BusinessContinuityPlanAuditImprovement']['business_continuity_plan_audit_id'] = $audit['BusinessContinuityPlanAudit']['id'];

		$this->Crud->on('beforeRender', [$this, '_beforeRender']);

		return $this->Crud->execute();
	}

	public function edit($auditId = null) {
		$this->title = __('Edit an Audit Improvement');

		$this->request->data['BusinessContinuityPlanAuditImprovement']['user_id'] = $this->logged['id'];
		unset($this->request->data['BusinessContinuityPlanAuditImprovement']['goal_audit_id']);

		$this->Crud->on('beforeRender', [$this, '_beforeRender']);

		return $this->Crud->execute();
	}

	public function _beforeRender(CakeEvent $event)
	{
		$this->Modals->changeConfig('footer.buttons.saveBtn.options.data-yjs-on-modal-close', '@reload-parent');
	}
}
