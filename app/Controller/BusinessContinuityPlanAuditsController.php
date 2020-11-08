<?php
App::uses('BusinessContinuityPlanAudit', 'Model');
App::uses('AppController', 'Controller');

class BusinessContinuityPlanAuditsController extends AppController {

	public $helpers = [];
	public $components = [
		'Paginator', 'ObjectStatus.ObjectStatus',
		// 'Visualisation.Visualisation',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				]
			],
			'listeners' => [
				'Api', 'ApiPagination', '.SubSection', 'BulkActions.BulkActions', 'Widget.Widget',
				'.ModuleDispatcher' => [
					'listeners' => [
						'NotificationSystem.NotificationSystem',
						'Reports.Reports',
					]
				]
			]
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
		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'history', 'restore', 'trash']);

		parent::beforeFilter();

		$this->title = __('Business Continuity Plans Audit');
		$this->subTitle = __('This is a report of all the audits registed for this service.');
	}

	public function index($id = null)
	{
		$this->title = __('Business Continuity Plans Audit Report');

		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Business Continuity Plans Audit');

		return $this->Crud->execute();
	}

	public function add($planId = null) {
		$this->title = __('Add a Business Continuity Plans Audit');

		$this->Crud->on('beforeRender', array($this, '_beforeAddRender'));

		$this->BusinessContinuityPlanAudit->setCreateValidation();

		return $this->Crud->execute();
	}

	public function _beforeAddRender(CakeEvent $event) {
		$request = $event->subject->request;

		if (isset($request->data['BusinessContinuityPlanAudit']['business_continuity_plan_id'])) {
			$plan = ClassRegistry::init('BusinessContinuityPlan')->find('first', [
				'conditions' => [
					'BusinessContinuityPlan.id' => $request->data['BusinessContinuityPlanAudit']['business_continuity_plan_id']
				]
			]);

			if (!empty($plan)) {
				$request->data['BusinessContinuityPlanAudit']['audit_metric_description'] = $plan['BusinessContinuityPlan']['audit_metric'];
				$request->data['BusinessContinuityPlanAudit']['audit_success_criteria'] = $plan['BusinessContinuityPlan']['audit_success_criteria'];
			}
		}
	}

	public function edit($id = null) {
		$this->title = __('Edit a Business Continuity Plans Audit');
		$this->initAddEditSubtitle();

		unset($this->request->data['BusinessContinuityPlanAudit']['business_continuity_plan_id']);

		return $this->Crud->execute();
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('The objective is to audit the security control for efficiency utilizing the metrics reviews and success criteria defined on the continuity plan. You should be able to add evidence that suppors the audit.' );
	}

	public function history($id)
	{
		return $this->Crud->execute();
	}

	public function restore($autidId)
	{
		return $this->Crud->execute();
	}

	public function trash() {
		$this->title = __('Business Continuity Plans Audit (Trash)');

		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
    }
}
