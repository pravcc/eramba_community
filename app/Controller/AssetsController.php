<?php
App::uses('AppController', 'Controller');

/**
 * @section
 */
class AssetsController extends AppController {

	public $helpers = array('ImportTool.ImportTool', 'Assets');
	public $components = array(
		'Paginator', 'Pdf', 'Search.Prg', 'AdvancedFilters', 'ObjectStatus.ObjectStatus',
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
		'ReviewsPlanner.Reviews',
		'UserFields.UserFields' => [
			'fields' => ['AssetOwner', 'AssetGuardian', 'AssetUser']
		]
	);

	public function beforeFilter() {
		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'trash', 'history', 'restore']);

		parent::beforeFilter();

		$this->title = __('Asset Identification');
		$this->subTitle = __('Describes all assets in the scope of this GRC program');

		$this->Crud->on('beforeRender', array($this, '_beforeRender'));
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function add()
	{
		$this->Crud->on('beforeRender', [$this, 'beforeAddEditRender']);

		return $this->Crud->execute();
	}

	public function edit( $id = null ) {
		$this->title = __('Edit an Asset');

		$this->Crud->on('beforeRender', [$this, 'beforeAddEditRender']);

		return $this->Crud->execute();
	}

	public function delete($id = null)
	{
		return $this->Crud->execute();
	}

	public function trash() {
		$this->set('title_for_layout', __('Asset Identification (Trash)'));

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

	public function beforeAddEditRender(CakeEvent $e)
	{
		// $e->subject->controller->_FieldDataCollection->get('review')->toggleEditable(true);
		// $e->subject->controller->_FieldDataCollection->get('AssetClassification')->toggleEditable(true);
		// $e->subject->controller->_FieldDataCollection->get('RiskClassificationTreatment')->toggleEditable(true);
	}

	/**
	 * Section callback to set additional variables and options for an action.
	 */
	public function _beforeRender(CakeEvent $event) {
		//add/edit
		$this->loadModel('AssetClassificationType');
		$classifications = $this->AssetClassificationType->find('all', array(
			'order' => array('AssetClassificationType.name' => 'ASC'),
			'recursive' => 1
		));
		$this->set('classifications', $classifications);

		//index
		// $this->set('assetClassificationData', $this->getAssetClassificationsData());
		// $this->setRelatedAssets();
	}

	public function getLegals() {
		$this->YoonityJSConnector->deny();

		$this->allowOnlyAjax();
		$this->autoRender = false;

		$buIds = json_decode($this->request->query['buIds']);
		$data = $this->Asset->BusinessUnit->getLegalIds($buIds);

		echo json_encode($data);
	}
}
