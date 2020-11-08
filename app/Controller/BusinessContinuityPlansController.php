<?php
App::uses('AppController', 'Controller');

/**
 * @section
 */
class BusinessContinuityPlansController extends AppController {

	public $helpers = ['UserFields.UserField'];
	public $components = [
		'Search.Prg', 'Pdf', 'Paginator', 'ObjectStatus.ObjectStatus',// 'Visualisation.Visualisation',
		'Ajax' => [
			'actions' => ['add', 'edit', 'delete'],
			'modules' => ['comments', 'records', 'attachments', 'notifications']
		],
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				],
				'auditCalendarFormEntry' => [
					'className' => 'AuditCalendarFormEntry',
					'enabled' => true
				]
			],
			'listeners' => [
				'BulkActions.BulkActions', 'Widget.Widget',
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
		'UserFields.UserFields' => [
			'fields' => ['LaunchInitiator', 'Sponsor', 'Owner']
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

	public function beforeFilter()
	{
		$this->Auth->allow('runCron');

		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'trash', 'history', 'restore']);

		parent::beforeFilter();

		$this->title = __('Business Continuity Plans');
		$this->subTitle = __('Define your continuity plans in detail and ensure the plan participants are aware of their responsibilities.');
	}

	public function index($id = null)
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		$this->set('openId', $id);
		$this->set('userList', $this->getUsersList());

		$this->Crud->on('beforePaginate', function(CakeEvent $event)
		{
			$event->subject->paginator->settings['contain']['BusinessContinuityTask'] = $this->UserFields->attachFieldsToArray(['AwarenessRole'], [
				'fields' => ['id', 'step', 'when', 'who', 'does', 'where', 'how'],
				'order' => 'BusinessContinuityTask.step ASC',
				'BusinessContinuityTaskReminder' => [
					'order' => 'BusinessContinuityTaskReminder.created DESC',
					'limit' => 1
				],
				'Comment',
				'Attachment',
				'NotificationObject'
			], 'BusinessContinuityTask');
		});

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Business Continuity Plan.');

		return $this->Crud->execute();
	}

	public function trash()
	{
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function acknowledge( $bct_id = null, $bctr_id = null ) {
		$this->set( 'title_for_layout', __( 'Business Continuity Plans Acknowledge' ) );
		$this->set( 'subtitle_for_layout', __( 'Manage your Continuity Plans.' ) );
		$this->set( 'userList', $this->getUsersList() );

		if ( $bct_id ) {
			$this->loadModel( 'BusinessContinuityTaskReminder' );
			$this->BusinessContinuityTaskReminder->updateAll(
				array(
					'BusinessContinuityTaskReminder.seen' => true,
					'BusinessContinuityTaskReminder.modified' => "'" . CakeTime::format( 'Y-m-d H:i:s', CakeTime::fromString( 'now' ) ) . "'"
				),
				array( 'BusinessContinuityTaskReminder.id' => $bctr_id )
			);

			$data = $this->BusinessContinuityPlan->find('all', array(
				'conditions' => array(
				),
				'fields' => array(
					'BusinessContinuityPlan.id',
					'BusinessContinuityPlan.title'
				),
				'contain' => array(
					'BusinessContinuityTask' => array(
						'fields' => array( 'id', 'step', 'when', 'who', 'does', 'where', 'how' ),
						'conditions' => array(
							'BusinessContinuityTask.id' => $bct_id,
						),
						'BusinessContinuityTaskReminder' => array(
							'order' => 'BusinessContinuityTaskReminder.created DESC',
							'limit' => 1
						)
					)
				),
				'order' => array( 'BusinessContinuityPlan.id' => 'ASC' )
			));

			$this->set( 'data', $data );
		}
		else {
			$this->set( 'data', array() );
		}
	}

	public function acknowledgeItem( $id = null ) {
		if ( ! $id ) {
			die();
		}

		$this->loadModel( 'BusinessContinuityTaskReminder' );
		$this->BusinessContinuityTaskReminder->updateAll(
			array(
				'BusinessContinuityTaskReminder.acknowledged' => true,
				'BusinessContinuityTaskReminder.modified' => "'" . CakeTime::format( 'Y-m-d H:i:s', CakeTime::fromString( 'now' ) ) . "'"
			),
			array( 'BusinessContinuityTaskReminder.id' => $id )
		);

		$this->Session->setFlash( __( 'Business Continuity Task was successfully acknowledged.' ), FLASH_OK );
		$this->redirect( array( 'controller' => 'businessContinuityPlans', 'action' => 'acknowledge' ) );
	}

	public function add() {
		$this->title = __('Create a Business Continuity Plan');
		$this->initAddEditSubtitle();

		$this->Crud->action()->config('saveAssociatedHandler', false);
		$this->Crud->action()->saveMethod('saveAssociated');
		$this->Crud->action()->saveOptions([
			'deep' => true
		]);
		$this->Crud->on('beforeSave', array($this, '_beforeSave'));

		if ($this->request->is('post')) {
			$this->updateFieldsWhenDesign();
		}

		return $this->Crud->execute();
	}

	public function edit( $id = null ) {
		$this->title = __('Edit a Business Continuity Plan');
		$this->initAddEditSubtitle();

		$this->Crud->action()->config('saveAssociatedHandler', false);
		$this->Crud->action()->saveMethod('saveAssociated');
		$this->Crud->action()->saveOptions([
			'deep' => true
		]);
		$this->Crud->on('beforeSave', array($this, '_beforeSave'));

		if ($this->request->is('post')) {
			$this->updateFieldsWhenDesign();
		}

		return $this->Crud->execute();
	}

	public function _beforeSave(CakeEvent $event) {
		$id = null;
		if (isset($event->subject->id)) {
			$id = $event->subject->id;
		}

		if ($id !== null) {
			$this->BusinessContinuityPlan->BusinessContinuityPlanAuditDate->deleteAll(array(
				'BusinessContinuityPlanAuditDate.business_continuity_plan_id' => $id
			));
		}
	}

	public function auditCalendarFormEntry($model)
	{
		return $this->Crud->execute();
	}

	private function updateFieldsWhenDesign() {
		$auditsText = __('The plan is in design. Audits not possible.');

		if (isset($this->request->data['BusinessContinuityPlan']['security_service_type_id']) &&
			$this->request->data['BusinessContinuityPlan']['security_service_type_id'] == SECURITY_SERVICE_DESIGN) {
			$text = $this->request->data['BusinessContinuityPlan']['audit_metric'];
			$pos = strpos($text, $auditsText);

			if ($pos === false) {
				$this->request->data['BusinessContinuityPlan']['audit_metric'] .= ' ' . $auditsText;
			}

			$text = $this->request->data['BusinessContinuityPlan']['audit_success_criteria'];
			$pos = strpos($text, $auditsText);

			if ($pos === false) {
				$this->request->data['BusinessContinuityPlan']['audit_success_criteria'] .= ' ' . $auditsText;
			}
		}
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('Define your continuity plans in detail and ensure the plan participants are aware of their responsibilities.');
	}

	public function export() {
		$this->loadModel( 'BusinessContinuityTaskReminder' );
		$tmpData = $this->BusinessContinuityTaskReminder->find( 'all', array(
			'recursive' => 1
		) );

		$data = array();
		foreach ( $tmpData as $item ) {

			$tmp = array(
				'BusinessContinuityTaskReminder' => array(
					'business_continuity_task' => $item['BusinessContinuityTask']['step'],
					'user' => $item['User']['full_name'],
					'seen' => $item['BusinessContinuityTaskReminder']['seen'] ? __( 'Yes' ) : __( 'No' ),
					'acknowledged' => $item['BusinessContinuityTaskReminder']['acknowledged'] ? __( 'Yes' ) : __( 'No' ),
					'created' => $item['BusinessContinuityTaskReminder']['created'],
					'modified' => $item['BusinessContinuityTaskReminder']['modified']
				)
			);

			$data[] = $tmp;
		}

		$_serialize = 'data';
		$_header = array(
			'Business Continuity Task Step',
			'User',
			'Seen',
			'Acknowledged',
			'Created',
			'Modified'
		);
		$_extract = array(
			'BusinessContinuityTaskReminder.business_continuity_task',
			'BusinessContinuityTaskReminder.user',
			'BusinessContinuityTaskReminder.seen',
			'BusinessContinuityTaskReminder.acknowledged',
			'BusinessContinuityTaskReminder.created',
			'BusinessContinuityTaskReminder.modified'
		);

		$_bom = true;
		// $_newline = '\r\n';

		$this->response->download( 'bc_task_reminders.csv' );
		$this->viewClass = 'CsvView.Csv';
		$this->set( compact( 'data', '_serialize', '_header', '_extract', '_newline', '_bom' ) );
	}

	public function exportAudits() {
		$this->loadModel('BusinessContinuityPlanAudit');
		$tmpData = $this->BusinessContinuityPlanAudit->find('all', array(
			'contain' => array(
				'BusinessContinuityPlan' => array(
					'fields' => array('id', 'title')
				),
				'User' => array(
					'fields' => array('id', 'name')
				),
				'Attachment' => array(
					'fields' => array('id')
				)
			),
			'order' => array('BusinessContinuityPlanAudit.id' => 'ASC')
		));

		$results = array(
			0 => __( 'Fail' ),
			1 => __( 'Pass' )
		);

		$data = array();
		foreach ($tmpData as $item) {
			$tmp = array(
				'BusinessContinuityPlanAudit' => array(
					'control_name' => $item['BusinessContinuityPlan']['title'],
					'planned_start' => $item['BusinessContinuityPlanAudit']['planned_date'],
					'actual_start' => $item['BusinessContinuityPlanAudit']['start_date'],
					'actual_end' => $item['BusinessContinuityPlanAudit']['end_date'],
					'result' => ($item['BusinessContinuityPlanAudit']['result'] == null) ? '' : $results[$item['BusinessContinuityPlanAudit']['result']],
					'conclusion' => $item['BusinessContinuityPlanAudit']['result_description'],
					'owner' => $item['User']['name'],
					'attachments' => empty($item['Attachment']) ? __('Does not have Attachments') : __('Has Attachments')
				)
			);

			$data[] = $tmp;
		}

		$_serialize = 'data';
		$_header = array(
			'Control Name',
			'Planned Start',
			'Actual Start',
			'Actual End',
			'Result',
			'Conclusion',
			'Owner',
			'Attachments'
		);
		$_extract = array(
			'BusinessContinuityPlanAudit.control_name',
			'BusinessContinuityPlanAudit.planned_start',
			'BusinessContinuityPlanAudit.actual_start',
			'BusinessContinuityPlanAudit.actual_end',
			'BusinessContinuityPlanAudit.result',
			'BusinessContinuityPlanAudit.conclusion',
			'BusinessContinuityPlanAudit.owner',
			'BusinessContinuityPlanAudit.attachments',
		);

		$_bom = true;
		// $_newline = '\r\n';

		$this->response->download('security_services_audits.csv');
		$this->viewClass = 'CsvView.Csv';
		$this->set(compact('data', '_serialize', '_header', '_extract', '_newline', '_bom'));
	}

	public function exportTask( $id = null){
		$data = $this->BusinessContinuityPlan->BusinessContinuityTask->find('all', array(
			'conditions' => array(
				'BusinessContinuityTask.business_continuity_plan_id' => $id
			),
			'contain' => $this->UserFields->attachFieldsToArray(['AwarenessRole'], [
				'BusinessContinuityPlan'
			])
		));

		$_serialize = 'data';
		$_header = array(
			'Business Continuity Plan',
			'Step',
			'When',
			'Who',
			'Awareness Role',
			'Does Something',
			'Where',
			'How'
		);
		$_extract = array(
			'BusinessContinuityPlan.title',
			'BusinessContinuityTask.step',
			'BusinessContinuityTask.when',
			'BusinessContinuityTask.who',
			'AwarenessRole.{n}.full_name_with_type',
			'BusinessContinuityTask.does',
			'BusinessContinuityTask.where',
			'BusinessContinuityTask.how'
		);

		$this->response->download( 'business_continuity_tasks.csv' );
		$this->viewClass = 'CsvView.Csv';
		$this->set( compact( 'data', '_serialize', '_header', '_extract' ) );
	}

	public function exportPdf($id) {
		$this->autoRender = false;
		$this->layout = 'pdf';

		$item = $this->BusinessContinuityPlan->find('first', array(
			'conditions' => array(
				'BusinessContinuityPlan.id' => $id
			),
			'contain' => $this->UserFields->attachFieldsToArray(['LaunchInitiator', 'Sponsor', 'Owner'], array(
				'Attachment',
				'Comment' => array('User'),
				'SystemRecord' => array(
					'limit' => 20,
					'order' => array('created' => 'DESC'),
					'User'
				),
				'BusinessContinuityTask' => array(
					'User'
				),
				'SecurityServiceType' => array(
					'fields' => array( 'id', 'name' )
				),
				'BusinessContinuityPlanAudit' => array(
					'limit' => 20,
					'order' => array('created' => 'DESC'),
					'User'
				),
			)),
			'recursive' => -1
		));

		// debug($item);

		$this->set('item', $item);
		$vars = array(
			'item' => $item
		);

		$name = Inflector::slug($item['BusinessContinuityPlan']['title'], '-');

		$this->Pdf->renderPdf($name, '..'.DS.'BusinessContinuityPlans'.DS.'export_pdf', 'pdf', $vars, true);
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
