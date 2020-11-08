<?php
App::uses('AppController', 'Controller');

/**
 * @section
 */
class SecurityServiceMaintenancesController extends AppController
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
				'Api','ApiPagination', '.SubSection', 'BulkActions.BulkActions', 'Widget.Widget', 'Attachments.AttachmentField',
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
			'fields' => ['MaintenanceOwner']
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

		$this->title = __('Security Service Maintenances');
		$this->subTitle = __('');
	}

	public function index( $id = null ) {
		$this->title = __('Internal Control Maintenances Records');
		$this->subTitle = __('Describes all mantainance records for all controls.');

		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();

		// $this->paginate = array(
		// 	'conditions' => array(
		// 		'SecurityServiceMaintenance.security_service_id' => $id
		// 	),
		// 	'order' => array('SecurityServiceMaintenance.id' => 'ASC'),
		// );

		// $this->Prg->commonProcess('SecurityServiceMaintenance');
		// unset($this->request->data['SecurityServiceMaintenance']);

		// $filterConditions = $this->SecurityServiceMaintenance->parseCriteria($this->Prg->parsedParams());
		// if (!empty($filterConditions) && empty($this->request->query['advanced_filter'])) {
		// 	$this->Paginator->settings['conditions'] = $filterConditions;
		// 	$this->Crud->action()->config('filter.enabled', false);
		// }

		// $this->set('security_service_id', $id);
		// $this->set('page', $this->getItemPage($id));
		// $this->set('modalPadding', true);

		// return $this->Crud->execute();
	}

	private function getItemPage($id) {
		$this->loadModel('SecurityService');
		$order = $this->SecurityService->find('count', array(
			'conditions' => array(
				'SecurityService.id <=' => $id
			),
			'recursive' => -1
		));

		$page = ceil($order/10);
		return $page;
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Security Service Maintenance.');

		return $this->Crud->execute();
	}

	public function trash() {
		$this->set('title_for_layout', __( 'Security Service Maintenances (Trash)') );
		$this->set('subtitle_for_layout', __( 'This is the list of maintenances.') );

		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function add($securityServiceId = null) {
		$this->title = __('Create a Security Service Maintenance');
		$this->subTitle = __('The objective is to keep track of the regular tasks Service Controls require in order to function properly.');


		$this->SecurityServiceMaintenance->setCreateValidation();
		$this->Crud->on('beforeRender', array($this, '_beforeAddEditRender'));

		return $this->Crud->execute();
	}

	public function edit( $id = null ) {
		$this->title = __('Edit a Security Service Maintenance');
		$this->subTitle = __( 'The objective is to keep track of the regular tasks Service Controls require in order to function properly.' );
		$this->Crud->on('beforeRender', array($this, '_beforeAddEditRender'));

		return $this->Crud->execute();
	}

	public function _beforeAddEditRender(CakeEvent $e)
	{
		if ($this->_FieldDataCollection->has('result')) {
			$this->_FieldDataCollection->get('result')->toggleEditable(true);
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
