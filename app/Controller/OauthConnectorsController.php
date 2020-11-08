<?php
App::uses('AppController', 'Controller');

class OauthConnectorsController extends AppController
{
	public $helpers = [];
	public $components = [
		'Search.Prg',
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

	public function beforeFilter()
	{
		$this->Crud->enable(['index', 'add', 'edit', 'delete']);

		parent::beforeFilter();

		$this->title = __('OAuth Connectors');
		$this->subTitle = __('This section allows you to manage OAuth connectors which you can use in this eramba application.');

		// $this->Crud->addListener('NotificationSystem', 'NotificationSystem.NotificationSystem');
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		// $this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');
		$this->Crud->addListener('SettingsSubSection', 'SettingsSubSection');
		// $this->Crud->addListener('ImportTool', 'ImportTool.ImportTool');
		
		return $this->Crud->execute();
	}

	public function add()
	{
		$this->title = __('Create an OAuth Connector');
		
		$this->setAddEditCommonData();

		return $this->Crud->execute();
	}

	public function edit( $id = null )
	{
		$this->title = __('Edit an OAuth Connector');

		$this->setAddEditCommonData();

		return $this->Crud->execute();
	}

	private function setAddEditCommonData()
	{
		$this->set('redirectUrls', $this->OauthGoogleAuth->getRedirectUrls());
	}

	public function delete($id = null)
	{
		$this->Crud->on('beforeDelete', [$this, '_beforeDelete']);

		return $this->Crud->execute();
	}
}
