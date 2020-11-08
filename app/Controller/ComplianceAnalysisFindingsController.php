<?php
App::uses('Hash', 'Utility');
App::uses('AppController', 'Controller');

/**
 * @section
 */
class ComplianceAnalysisFindingsController extends AppController
{
	public $helpers = ['UserFields.UserField', 'ImportTool.ImportTool'];
	public $components = ['Paginator', 'Pdf', 'Search.Prg', 'AdvancedFilters', 'ObjectStatus.ObjectStatus',
		'Ajax' => [
			'actions' => ['add', 'edit'],
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
		//'Visualisation.Visualisation',
		'UserFields.UserFields' => [
			'fields' => ['Owner', 'Collaborator']
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

		$this->title = __('Compliance Analysis Findings');
		$this->subTitle = __('Manage all compliance findings in the scope of your GRC program');

		$this->Auth->allow('loadPackageItems');
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		$this->Crud->on('afterPaginate', array($this, '_afterPaginate'));

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->subTitle = __('Delete a Finding.');

		return $this->Crud->execute();
	}

	public function trash()
	{
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');
		
		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Create an Compliance Analysis Finding');

		// $this->Crud->on('beforeRender', array($this, '_handleRequestData'));
		$this->Crud->on('beforeRender', array($this, '_setPackageItemOptions'));

		// $this->initOptions();

		return $this->Crud->execute();
	}

	public function edit( $id = null ) {
		$this->title = __('Edit a Compliance Analysis Finding');

		// $this->Crud->on('beforeRender', array($this, '_handleRequestData'));
		$this->Crud->on('beforeRender', array($this, '_setPackageItemOptions'));

		// $this->initOptions();

		return $this->Crud->execute();
	}

	public function _setPackageItemOptions(CakeEvent $event)
	{
		$requestData = $event->subject->request->data;

		$regulators = [];

		if (!empty($requestData['CompliancePackageRegulator'])) {
			$regulators = Hash::extract($requestData['CompliancePackageRegulator'], '{n}.id');
		}
		elseif (!empty($requestData['ComplianceAnalysisFinding']['CompliancePackageRegulator'])) {
			$regulators = $requestData['ComplianceAnalysisFinding']['CompliancePackageRegulator'];
		}

		$this->set('compliancePackageItemsCustom', $this->ComplianceAnalysisFinding->getPackageItemOptions($regulators));
	}

	public function _handleRequestData(CakeEvent $event) {
		// $data = $event->subject->request->data;

		//handle duplicit values
		// $this->_removeDuplicitValues($data['ComplianceAnalysisFinding']['Owner']);
		// $this->_removeDuplicitValues($data['ComplianceAnalysisFinding']['Collaborator']);
		// $this->_removeDuplicitValues($data['ComplianceAnalysisFinding']['ThirdParty']);

		// $event->subject->request->data = $data;
		// ddd($event->subject->request->data);
	}

	private function _removeDuplicitValues(&$data) {
		if (!empty($data)) {
			$data = array_unique($data);
		}
		return $data;
	}

	public function _afterFind(CakeEvent $event) {
		// $data = $event->subject->item;
		// $data['ComplianceAnalysisFinding']['ThirdParty'] = Hash::extract($data, 'ThirdParty.{n}.id');
		// $this->request->data = $data;
	}

	/**
	 * @deprecated
	 */
	private function initOptions() {
		$packages = $this->ComplianceAnalysisFinding->ComplianceManagement->getThirdParties();

		// $CompliancePackage = $this->ComplianceAnalysisFinding->CompliancePackage;
		// $CompliancePackage->virtualFields['tp_list_name'] = 'CONCAT(CompliancePackage.name, " (", ThirdParty.name, ")")';
		// $packages = $CompliancePackage->find('list', [
		// 	'order' => ['CompliancePackage.name' => 'ASC', 'ThirdParty.name' => 'ASC'],
		// 	'fields' => [
		// 		'CompliancePackage.id',
		// 		'ThirdParty.name'
		// 	],
		// 	'recursive' => 0
		// ]);
		// unset($CompliancePackage->virtualFields['tp_list_name']);

		$this->set(compact('packages'));
		$this->initPackageItems();
	}

	public function loadPackageItems($id = null) {
		// debug($this->request);
		$this->request->data = $this->request->query['data'];
		// $this->set('packageIds', $data['ComplianceAnalysisFinding']['ThirdParty']);
		if (isset($data['ComplianceAnalysisFinding']['CompliancePackageItem'])) {
			// $this->request->data['ComplianceAnalysisFinding']['CompliancePackageItem'] = $data['ComplianceAnalysisFinding']['CompliancePackageItem'];
		}
		$this->initOptions();
	}

	public function initPackageItems($packageIds = null) {
		if (isset($this->request->data['ComplianceAnalysisFinding']['ThirdParty'])) {
			$packageIds = $this->request->data['ComplianceAnalysisFinding']['ThirdParty'];
		}
		$this->set('packageIds', $packageIds);
		// $this->request->data['ComplianceAnalysisFinding']['CompliancePackageItem']

		$conds = [];
		// debug($packageIds);
		if ($packageIds !== null) {
			$conds = [
				'ThirdParty.id' => $packageIds
			];
		}

		$data = $this->ComplianceAnalysisFinding->ThirdParty->find('all', array(
			'conditions' => $conds,
			'fields' => array(
				'ThirdParty.id',
				'ThirdParty.name',
				'ThirdParty.description'
			),
			'contain' => array(
				'CompliancePackage' => array(
					'CompliancePackageItem'
				)
			),
			'order' => array( 'ThirdParty.id' => 'ASC' ),

		));
		$data = filterComplianceData($data);
		// debug($data);
		$packageItemsList = [];
		foreach ($data as $key => $item) {
			$packageItemsList[$item['ThirdParty']['id']] = array_combine(Hash::extract($item, 'CompliancePackage.{n}.CompliancePackageItem.{n}.id'),
				Hash::format($item, array('CompliancePackage.{n}.CompliancePackageItem.{n}.item_id', 'CompliancePackage.{n}.CompliancePackageItem.{n}.name'), '(%s) %s'));
				//Hash::extract($item, 'CompliancePackage.{n}.CompliancePackageItem.{n}.item_id'));
		}

		$this->set('packageItemsList', $packageItemsList);

		$CompliancePackageItem = $this->ComplianceAnalysisFinding->CompliancePackageItem;
		$CompliancePackageItem->virtualFields['list_name'] = 'CONCAT("(", CompliancePackageItem.item_id, ") ", CompliancePackageItem.name, "")';

		$conds = [];
		// debug($packageIds);
		if ($packageIds !== null) {
			$conds = [
				'CompliancePackageItem.compliance_package_id' => $packageIds
			];
		}

		$packageItems = $CompliancePackageItem->find('all', [
			'conditions' => $conds,
			'order' => ['CompliancePackageItem.item_id' => 'ASC'],
			'fields' => [
				'CompliancePackageItem.compliance_package_id',
				'CompliancePackageItem.id',
				'CompliancePackage.name',
				'list_name'
			],
			'recursive' => 0
		]);
		// debug($data);
		$packageItemsGroup = Hash::combine($data, '{n}.CompliancePackage.{n}.CompliancePackageItem.{n}.id', '{n}.CompliancePackage.{n}.CompliancePackageItem.{n}.name', '{n}.ThirdParty.id');
		// debug($packageItemsGroup);
		unset($CompliancePackageItem->virtualFields['tp_list_name']);

		$this->set(compact('packageItems', 'packageItemsGroup'));
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
