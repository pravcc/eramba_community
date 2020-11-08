<?php
App::uses('AppController', 'Controller');

/**
 * @section
 */
class PolicyExceptionsController extends AppController
{
	public $helpers = ['ImportTool.ImportTool', 'UserFields.UserField'];
	public $components = [
		'Search.Prg', 'AdvancedFilters', 'Paginator', 'ObjectStatus.ObjectStatus',
		//'Visualisation.Visualisation',
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
				'add' => [
					'saveMethod' => 'saveAssociated'
				],
				'edit' => [
					'saveMethod' => 'saveAssociated'
				]
			],
			'listeners' => [
				'Api', 'ApiPagination', 'BulkActions.BulkActions', 'Widget.Widget',
				'Visualisation.Visualisation',
				'Taggable.Taggable' => [
					'fields' => [
						'Classification'
					]
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

		$this->title = __('Policy Exceptions');
		$this->subTitle = __('Manage all policy exceptions in the scope of this GRP program');
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		$filterConditions = $this->PolicyException->parseCriteria($this->Prg->parsedParams());
		if (!empty($filterConditions) && empty($this->request->query['advanced_filter'])) {
			$this->Paginator->settings['conditions'] = $filterConditions;
		}

		$this->setIndexData();

		$this->Crud->on('beforePaginate', function(CakeEvent $event)
		{
			$event->subject->paginator->settings['contain']['SecurityPolicy'] = $this->UserFields->attachFieldsToArray(['Owner'], [], 'SecurityPolicy');
		});

		return $this->Crud->execute();
	}

	private function setIndexData() {
		$assetData = $this->PolicyException->getAllHabtmData('Asset', array(
			'contain' => array(
				'RelatedAssets' => array(
					'fields' => array('id', 'name')
				),
				'Legal' => array(
					'fields' => array( 'id', 'name' )
				),
				'AssetLabel' => array(
					'fields' => array( 'name' )
				)
			)
		));

		$this->set('assetData', $assetData);
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Policy Exception.');

		return $this->Crud->execute();
	}

	public function trash()
	{
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Create a Policy Exception');
		$this->initAddEditSubtitle();

		$this->initOptions();

		return $this->Crud->execute();
	}

	public function edit($id = null) {
		$this->title = __('Edit a Policy Exception');
		$this->initAddEditSubtitle();

		$this->initOptions();

		return $this->Crud->execute();
	}

	/**
	 * Initialize options for join elements.
	 */
	private function initOptions() {
		$security_policies = $this->getSecurityPoliciesList();

		$classificationsTmp = $this->PolicyException->Classification->find('list', array(
			'order' => array('Classification.name' => 'ASC'),
			'fields' => array('Classification.id', 'Classification.name'),
			'group' => array('Classification.name'),
			'recursive' => -1
		));
		$classifications = array();
		foreach ($classificationsTmp as $c) {
			$classifications[] = $c;
		}

		$this->set( 'security_policies', $security_policies );
		$this->set( 'classifications', $classifications);
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('Policy exceptions are highly applicable on most programs as they are used every time a Policy exception is required. For example, a user requesting access to a system which would normally would be above its access level would require a Policy Exception for a determined period of time.');
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
