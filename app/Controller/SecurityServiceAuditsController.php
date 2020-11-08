<?php
App::uses('AppController', 'Controller');

/**
 * @section
 */
class SecurityServiceAuditsController extends AppController
{
	public $helpers = ['UserFields.UserField'];
	public $components = [
		'Paginator', 'ObjectStatus.ObjectStatus',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				]
			],
			'listeners' => [
				'Api', 'ApiPagination', '.SubSection', 'BulkActions.BulkActions', 'Widget.Widget', 'Attachments.AttachmentField',
				'Visualisation.Visualisation',
				'.ModuleDispatcher' => [
					'listeners' => [
						'NotificationSystem.NotificationSystem',
						'CustomFields.CustomFields',
						'Reports.Reports',
					]
				]
			]
		],
		//'Visualisation.Visualisation',
		'UserFields.UserFields' => [
			'fields' => ['AuditOwner', 'AuditEvidenceOwner']
		]
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
		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'trash', 'history', 'restore']);

		parent::beforeFilter();

		$this->title = __('Security Service Audits');
		$this->subTitle = __('');
	}

	// public function _beforeFilterItems(CakeEvent $e)
	// {
	// 	// for a temporal filter - lightweight index modal
	// 	if (isset($e->subject->request->params['pass'][0])) {
	// 		$_filter = new AdvancedFiltersObject();
	// 		$_filter->setModel($e->subject->model);

	// 		$securityServiceId = $e->subject->request->params['pass'][0];
	// 		$_filter->setName(__('Audits'));
	// 		$_filter->setDescription(__('List of Audits for a certain Security Service'));

	// 		$_filter->setFilterValues([
	// 			'security_service_id' => $securityServiceId,
	// 			'_order_column' => 'planned_date',
	// 			'_order_direction' => 'DESC'
	// 		]);

	// 		$items = new ArrayIterator();
	// 		$e->subject->items->append($_filter);

	// 		// Set Crud action to use modal
	// 		if ($this->Crud->action()->config('action') === 'index') {
	// 			$this->Crud->action()->config('useModal', true);
	// 		}

 //        	$e->subject->controller->set('add_new_button', [
 //        		'controller' => 'securityServiceAudits',
 //        		'action' => 'add',
 //        		$e->subject->request->params['pass'][0]
 //        	]);
	// 	}
	// }

	public function index($id = null) {
		$this->title = __('Internal Controls Audit Records');
		$this->subTitle = __('Describes all audit records for all internal controls.');

        $this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		// $this->Crud->on('beforeFilterItems', array($this, '_beforeFilterItems'));

		// $this->Crud->on('afterPaginate', array($this, '_afterPaginate'));

		return $this->Crud->execute();

		/*$this->SecurityServiceAudit->virtualFields['year'] = 'YEAR(SecurityServiceAudit.planned_date)';

		if (empty($year)) {
			$year = date('Y');
		}

		$availableYears = $this->getAvailableYears($id);
		if (!in_array(date('Y'), $availableYears)) {
			$availableYears[] = date('Y');
		}
		
		$availableYears = array_unique($availableYears);
		sort($availableYears);

		$this->paginate = [
			'conditions' => [
				'SecurityServiceAudit.security_service_id' => $id,
				'YEAR(SecurityServiceAudit.planned_date)' => $year
			],
			'order' => ['SecurityServiceAudit.planned_date' => 'ASC'],
		];

		$this->Prg->commonProcess('SecurityServiceAudit');
		unset($this->request->data['SecurityServiceAudit']);

		$filterConditions = $this->SecurityServiceAudit->parseCriteria($this->Prg->parsedParams());
		if (!empty($filterConditions) && empty($this->request->query['advanced_filter'])) {
			$this->Paginator->settings['conditions'] = $filterConditions;
			$this->Crud->action()->config('filter.enabled', false);
			$this->set('filterConditions', true);
		}

		$this->set('security_service_id', $id);
		$this->set('page', $this->getItemPage($id));
		$this->set('modalPadding', true);

		$this->set('availableYears', $availableYears);
		$this->set('currentYear', $year);

		$this->Crud->execute();*/
	}

	// public function _afterPaginate(CakeEvent $event) {
	// 	if (!empty($this->viewVars['filterConditions']) && !empty($event->subject->items)) {
	// 		$data = $event->subject->items;
	// 		$id = $data[0]['SecurityServiceAudit']['security_service_id'];
	// 		$availableYears = $this->getAvailableYearsFromAudits($data);
	// 		$year = $availableYears[0];

	// 		$this->set('security_service_id', $id);
	// 		$this->set('availableYears', $availableYears);
	// 		$this->set('currentYear', $year);
	// 	}
	// }

	/**
	 * Get an array of Year values from a data of audits.
	 */
	// private function getAvailableYearsFromAudits($data) {
	// 	$years = array();
	// 	if (!empty($data)) {
	// 		foreach ($data as $item) {
	// 			$years[] = $item['SecurityServiceAudit']['year'];
	// 		}
	// 	}

	// 	return $years;
	// }

	/**
	 * Group audits into years.
	 */
	// private function getAvailableYears($securityServiceId) {
	// 	$data = $this->SecurityServiceAudit->find('list', array(
	// 		'conditions' => array(
	// 			'SecurityServiceAudit.security_service_id' => $securityServiceId
	// 		),
	// 		'fields' => array(
	// 			'SecurityServiceAudit.id',
	// 			'SecurityServiceAudit.year'
	// 		),
	// 		'group' => 'SecurityServiceAudit.year',
	// 		'order' => array('SecurityServiceAudit.year' => 'ASC'),
	// 		'recursive' => -1
	// 	));

	// 	return $data;
	// }

	// private function getItemPage($id) {
		/*$this->loadModel('SecurityService');
		$order = $this->SecurityService->find('count', array(
			'conditions' => array(
				'SecurityService.id <=' => $id
			),
			'recursive' => -1
		));

		$page = floor($order/10);
		return $page;*/
		// return 1;
	// }

	public function delete($id = null) {
		$this->title = __('Delete a Security Service Audit.');

		return $this->Crud->execute();
	}

	public function trash() {
		$this->set('title_for_layout', __('Security Service Audits (Trash)'));
		$this->set('subtitle_for_layout', __('This is the list of audits.'));

		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function add($securityServiceId = null) {
		$this->title = __('Create a Security Service Audit');
		$this->subTitle = __('The objective is to audit the security control for efficiency utilizing the metrics reviews and success criteria defined on the control. You should be able to add evidence that suppors the audit.');

		$this->SecurityServiceAudit->setCreateValidation();

		$this->Crud->on('afterSave', array($this, '_afterSave'));
		$this->Crud->on('beforeRender', array($this, '_beforeAddRender'));
		$this->Crud->on('beforeRender', array($this, '_beforeAddEditRender'));

		return $this->Crud->execute();
	}

	public function _beforeAddRender(CakeEvent $event) {
		$model = $event->subject->model;
		$request = $event->subject->request;

		if (isset($request->data['SecurityServiceAudit']['security_service_id'])) {
			$securityServiceId = $request->data['SecurityServiceAudit']['security_service_id'];

			// $request->data['SecurityServiceAudit']['security_service_id'] = $securityServiceId;
			$data = $model->SecurityService->find('first', [
				'conditions' => [
					'SecurityService.id' => $securityServiceId
				]
			]);
			// ddd($data);
			$setDefaults = [
				'audit_metric_description',
				'audit_success_criteria',
				// 'AuditOwner'
			];

			foreach ($setDefaults as $field) {
				$request->data['SecurityServiceAudit'][$field] = $data['SecurityService'][$field];
			}
		}
	}

	public function edit($id = null) {
		$this->title = __('Edit a Security Service Audit');
		$this->subTitle = __('The objective is to audit the security control for efficiency utilizing the metrics reviews and success criteria defined on the control. You should be able to add evidence that suppors the audit.');

		$this->Crud->on('afterSave', array($this, '_afterSave'));
		$this->Crud->on('beforeRender', array($this, '_beforeAddEditRender'));

		return $this->Crud->execute();
	}

	public function _beforeAddEditRender(CakeEvent $e)
	{
		if ($this->_FieldDataCollection->has('result')) {
			$this->_FieldDataCollection->get('result')->toggleEditable(true);
		}
	}

	public function _afterSave(CakeEvent $event) {
		if (empty($event->subject->success)) {
			return false;
		}

		$auditId = $event->subject->id;

		$ret = true;

		if (isset($this->request->data['SecurityServiceAudit']['result'])) {
			if ($this->request->data['SecurityServiceAudit']['result'] == AUDIT_FAILED) {
				$data = $this->SecurityServiceAudit->find( 'first', array(
					'conditions' => array(
						'SecurityServiceAudit.id' => $auditId
					),
					'recursive' => 0
				));

				$this->SecurityServiceAudit->triggerNotification('security_service_audit_failed', $auditId, [
					'force' => true
				]);
			}

			if ($this->request->data['SecurityServiceAudit']['result'] == AUDIT_PASSED) {
				$this->SecurityServiceAudit->triggerNotification('security_service_audit_passed', $auditId, [
					'force' => true
				]);
			}
		}

		return $ret;
	}

	public function getIndexUrlFromComponent($model, $foreign_key) {
		return parent::getIndexUrl($model, $foreign_key);
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
