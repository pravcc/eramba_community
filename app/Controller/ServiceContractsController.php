<?php
App::uses('AppController', 'Controller');

/**
 * @section
 */
class ServiceContractsController extends AppController
{
	public $helpers = ['UserFields.UserField'];
	public $components = [
		'ObjectStatus.ObjectStatus',
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
				'.ModuleDispatcher' => [
					'listeners' => [
						'NotificationSystem.NotificationSystem',
						'CustomFields.CustomFields',
						'Reports.Reports',
					]
				]
			]
		],
		'UserFields.UserFields'
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

		$this->title = __('Support Contracts');
		$this->subTitle = __('Manage all support contracts attached to Internal Controls.');
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		// $this->Paginator->settings['conditions'] = [
		// 	'ThirdParty.service_contract_count !=' => 0
		// ];

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Service Contract.');

		return $this->Crud->execute();
	}

	public function add($tp_id = null) {
		$this->title = __('Create a Service Contract');
		$this->initAddEditSubtitle();

		if (!empty($tp_id)) {
			$data = $this->ServiceContract->ThirdParty->find('first', array(
				'conditions' => array(
					'ThirdParty.id' => $tp_id
				),
				'recursive' => -1
			));

			if (empty($data)) {
				throw new NotFoundException();
			}
		}

		$this->set('tp_id', $tp_id);

		return $this->Crud->execute();
	}

	public function edit($id = null) {
		$this->title = __('Edit a Service Contract');
		$this->initAddEditSubtitle();

		return $this->Crud->execute();
	}

	public function trash()
	{
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('You can Here you can define any support contracts you have with suppliers. Map them to Security Services in order to keep budgets clear, and also get warnings when they are set to expire. Also for example, if you manage multiple SSL certificates you can define them here to ensure they don’t expire.');
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
