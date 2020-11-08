<?php
App::uses('AppController', 'Controller');
App::uses('ThirdParty', 'Model');

/**
 * @section
 */
class ThirdPartiesController extends AppController
{
	public $helpers = ['UserFields.UserField'];
	public $components = [
		'Paginator',
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
		'UserFields.UserFields' => [
			'fields' => ['Sponsor']
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

		$this->title = __('Third Parties');
		$this->subTitle = __('Describes all third parties asociated with this GRC program');
	}


	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function delete($id = null)
	{
		$this->subTitle = __('Delete a Third Party.');

		return $this->Crud->execute();
	}

	public function add()
	{
		$this->title = __('Create a Third Party');

		return $this->Crud->execute();
	}

	public function edit($id = null)
	{
		$this->title = __('Edit a Third Party');

		return $this->Crud->execute();
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('Most organizations execute businesses with the help of other partners (customers, suppliers, Etc). Understanding the exchange of information in between your organization and third parties is essential to the security program. Those Third Parties defined in this section will be used for Third Party Risk and Compliance Management purposes.');
	}

	public function trash()
	{
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
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
