<?php
App::uses('AppController', 'Controller');

class GroupsController extends AppController
{
	public $name = 'Groups';
	public $uses = array('Group');
	public $components = [
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				]
			],
			'listeners' => [
				'Widget.Widget'
			]
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
		$this->Crud->enable(['index', 'add', 'edit', 'delete']);

		parent::beforeFilter();

		$this->title = __('User Account Group Management');
		$this->subTitle = __( 'Manage user groups and where they can access in the system' );

		// $this->Crud->addListener('NotificationSystem', 'NotificationSystem.NotificationSystem');
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		// $this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');
		$this->Crud->addListener('ImportTool', 'ImportTool.ImportTool');
		$this->Crud->addListener('SettingsSubSection', 'SettingsSubSection');

		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Create a Role');
		$this->initAddEditSubtitle();

		return $this->Crud->execute();
	}

	public function edit( $id = null ) {
		$this->title = __('Edit a Role');
		$this->initAddEditSubtitle();

		return $this->Crud->execute();
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('Groups are used to control what access to the system is granted to system users. Once a group is created make sure you define the level of access trough the use of "Group Access" settings. Once the access definition is done, you might grant a system user a group.');
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Role');

		return $this->Crud->execute();
	}
}
