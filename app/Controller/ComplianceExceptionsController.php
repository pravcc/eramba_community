<?php
App::uses('AppController', 'Controller');

/**
 * @section
 */
class ComplianceExceptionsController extends AppController
{
	public $helpers = ['ImportTool.ImportTool', 'UserFields.UserField'];
	public $components = ['Search.Prg', 'Pdf', 'Paginator', 'AdvancedFilters', 'ObjectStatus.ObjectStatus',
		'Ajax' => [
			'actions' => ['add', 'edit', 'delete'],
			'modules' => ['comments', 'records', 'attachments', 'notifications']
		],
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				]
			],
			'listeners' => [
				'Api', 'ApiPagination', 'BulkActions.BulkActions', 'Widget.Widget',
				'Visualisation.Visualisation',
				'Taggable.Taggable' => [
					'fields' => ['Tag']
				],
				'.ModuleDispatcher' => [
					'listeners' => [
						'NotificationSystem.NotificationSystem',
						'CustomFields.CustomFields',
						'Reports.Reports',
					]
				]
			]
		],
		// 'Visualisation.Visualisation',
		'UserFields.UserFields' => [
			'fields' => ['Requestor']
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

		$this->title = __('Compliance Exceptions');
		$this->subTitle = __('Manage all compliance exceptions in the scope of this GRP program');
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		$this->title = __('Compliance Exception Management');

		$this->set('complianceManagementViewItemUrl', $this->ComplianceException->ComplianceManagement->advancedFilterSettings['view_item']['ajax_action']);
		$this->initOptions();

		$this->Prg->commonProcess('ComplianceException');
		unset($this->request->data['ComplianceException']);

		$filterConditions = $this->ComplianceException->parseCriteria($this->Prg->parsedParams());
		if (!empty($filterConditions) && empty($this->request->query['advanced_filter'])) {
			$this->Paginator->settings['conditions'] = $filterConditions;
			$this->set('filterConditions', true);
		}

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Compliance Exception.');

		return $this->Crud->execute();
	}

	public function trash() {
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		$this->set('title_for_layout', __('Compliance Exceptions (Trash)'));

		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Create a Compliance Exception');
		$this->initAddEditSubtitle();

		$this->initOptions();

		return $this->Crud->execute();
	}

	public function edit($id = null) {
		$this->title = __('Edit a Compliance Exception');
		$this->initAddEditSubtitle();

		$this->initOptions();

		return $this->Crud->execute();
	}

	private function initOptions() {
		$statuses = array(
			0 => __( 'Closed' ),
			1 => __( 'Open' )
		);

		$users = $this->getUsersList();

		$this->set( 'statuses', $statuses );
		$this->set( 'users', $users );
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('Compliance Exceptions are used to record the temporary acceptance of some compliance issue. They are used while running a compliance management program by mapping them to those compliance requirements which the organization has decided not to be compliant');
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
