<?php
App::uses('AppController', 'Controller');

class SamlConnectorsController extends AppController
{
	public $helpers = [];
	public $components = [
		'SamlAuth',
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

		$this->Auth->allow(['getMetadata', 'singleSingOn', 'singleLogout']);

		$this->title = __('SAML Connectors (BETA Feature)');
		$this->subTitle = __('This section allows you to manage SAML connectors which you can use in this eramba application.');
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
		$this->title = __('Create an SAML Connector');
		
		$this->setAddEditCommonData();

		return $this->Crud->execute();
	}

	public function edit( $id = null )
	{
		$this->title = __('Edit an SAML Connector');

		$this->setAddEditCommonData();

		return $this->Crud->execute();
	}

	private function setAddEditCommonData()
	{
		$this->set('loginRedirectUrls', $this->SamlAuth->getLoginRedirectUrls());
	}

	public function delete($id = null)
	{
		$this->Crud->on('beforeDelete', [$this, '_beforeDelete']);

		return $this->Crud->execute();
	}

	public function getMetadata()
	{
		$this->layout = false;
		$this->autoRender = false;

		$metadata = $this->SamlAuth->getMetadata();

		echo $metadata;
	}

	public function singleSingOn()
	{
		$this->layout = false;
		$this->autoRender = false;

		$this->SamlAuth->login();
	}

	public function singleLogout()
	{
		$this->layout = false;
		$this->autoRender = false;

		$this->SamlAuth->logout();
	}
}
