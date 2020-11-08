<?php
App::uses('AppController', 'Controller');
App::uses('Hash', 'Utility');
App::uses('ThirdParty', 'Model');
App::uses('CompliancePackage', 'Model');
App::uses('FieldDataCollection', 'FieldData.Model/FieldData');
App::uses('UserFieldsBehavior', 'UserFields.Model/Behavior');

/**
 * @section
 */
class CompliancePackagesController extends AppController
{
	public $helpers = [];
	public $components = ['Search.Prg',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				],
				'duplicate' => [
					'className' => 'AppAdd',
					'enabled' => true
				],
				'import' => [
					'className' => 'AppAdd',
					'enabled' => true,
					'view' => 'import'
				]
			],
			'listeners' => [
				'Widget.Widget', 'Visualisation.Visualisation',
				'.ModuleDispatcher' => [
					'listeners' => [
						'Reports.Reports',
					]
				]
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

	public function __construct($request = null, $response = null) {
		parent::__construct($request, $response);
	}

	public function beforeFilter() {
		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'trash', 'history', 'restore']);

		parent::beforeFilter();

		$this->title = __('Compliance Packages');
		$this->subTitle = __('Packages. Each specific requirement is called Compliance Package Items. In this module you can upload (CSV format) compliance packages or simply create, edit and delete them using the interface.');
	}

	public function index() {
		// hard redirect to compliance package index controller
		return $this->redirect([
			'plugin' => null,
			'controller' => 'compliancePackageItems',
			'action' => 'index'
		]);

		// $this->Crud->on('beforePaginate', array($this, '_beforePaginate'));
		// $this->Crud->on('afterPaginate', array($this, '_afterPaginate'));

		$this->title = __('Compliance Package Database');

		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	// public function _afterSave(CakeEvent $event) {
	// 	if ($event->subject->success) {
	// 		$this->redirect(['action' => 'index']);
	// 	}
	// }

	// public function _beforePaginate(CakeEvent $e) {
	// 	// debug(Debugger::exportVar($e->subject->paginator,4));exit;
	// 	$e->subject->paginator->settings['order'] = [
	// 		'CompliancePackage.third_party_id' => 'ASC'
	// 	];

	// 	$e->subject->paginator->settings['limit'] = 1000;

	// 	// only regulator third parties are shown
	// 	$additionalConds = CompliancePackage::thirdPartyListingConditions();
	// 	$e->subject->paginator->settings['conditions'] = am(
	// 		$e->subject->paginator->settings['conditions'],
	// 		$additionalConds
	// 	);

	// 	$groupList = $e->subject->model->ThirdParty->find('list', [
	// 		'fields' => [
	// 			'ThirdParty.id', 'ThirdParty.name',
	// 		],
	// 		'group' => ['ThirdParty.id'],
	// 		'joins' => [
	// 			[
	// 				'alias' => 'CompliancePackage',
	// 				'table' => 'compliance_packages',
	// 				'type' => 'INNER',
	// 				'conditions' => [
	// 					'ThirdParty.id = CompliancePackage.third_party_id'
	// 				]
	// 			]
	// 		],
	// 		'recursive' => -1
	// 	]);
	// 	// debug($groupList);exit;

	// 	$e->subject->controller->set('groupList', $groupList);

	// }

	public function _afterPaginate(CakeEvent $e) {
		$data = Hash::combine($e->subject->items, '{n}.CompliancePackage.id', '{n}', '{n}.CompliancePackage.third_party_id');
		$e->subject->items = $data;
	}

	public function delete( $id = null ) {
		$this->subTitle = __('Delete a Compliance Package.');

		return $this->Crud->execute();
	}

	public function trash() {
		$this->set('title_for_layout', __('Compliance Packages (Trash)'));

		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function add($tp_id = null) {
		$this->title = __('Create a Compliance Package');

		$this->set('selected', $tp_id);

		$this->Crud->on('beforeRender', array($this, '_beforeAddEditRender'));
		$this->Crud->on('beforeRender', array($this, '_beforeAddRender'));
		$this->Crud->on('afterSave', [$this, '_syncFilter']);

		return $this->Crud->execute();
	}

	public function _syncFilter(CakeEvent $e)
	{
		// $subject = $e->subject;
		// if ($subject->success && $subject->created) {
		// 	$subject->model->syncComplianceIndex($subject->id);
		// }
	}

	public function _beforeAddRender(CakeEvent $e)
	{
		if ($this->_FieldDataCollection->has('third_party_id')) {
			$this->_FieldDataCollection->get('third_party_id')->toggleEditable(true);
		}
	}

	public function edit($id = null) {
		$this->title = __('Edit a Compliance Package');

		$this->Crud->on('beforeRender', array($this, '_beforeAddEditRender'));

		return $this->Crud->execute();
	}

	public function _beforeAddEditRender(CakeEvent $e)
	{
		if ($this->_FieldDataCollection->has('package_id')) {
			$this->_FieldDataCollection->get('package_id')->toggleEditable(true);
		}
	}

	/**
	 * Initialize options for join elements.
	 */
	private function initOptions() {
		$third_parties = $this->CompliancePackage->ThirdParty->find('list', array(
			'conditions' => CompliancePackage::thirdPartyListingConditions(),
			'order' => array('ThirdParty.name' => 'ASC'),
			'recursive' => -1
		));

		$this->set( 'third_parties', $third_parties );
	}

	private function initAddEditSubtitle() {
		$this->subTitle = false;
	}

	public function import() {
		$this->title = __('Upload a Complete Compliance Package');
		$this->subTitle = __('You can upload a complete compliance package from your computer using a Comma Separated File (CSV).');


		$this->Crud->action()->saveMethod('importSave');
		$this->Crud->on('beforeHandle', [$this, '_beforeImportHandle']);
		$this->Crud->on('beforeRender', [$this, '_beforeImportRender']);

		return $this->Crud->execute();
	}

	public function _beforeImportHandle(CakeEvent $e)
	{
		$model = $e->subject->model;
		$CompliancePackageCollection = $model->getFieldCollection();

		$cprId = $CompliancePackageCollection->get('compliance_package_regulator_id')->config();
		// $cprId['hidden'] = false;
		$cprId['editable'] = true;
		$cprId['empty'] = __('Choose one');
		$cprId['description'] = __('Select one compliance package from the drop down above, only those empty compliance packages will be shown. Updates to existing compliance packages must be done by editing, deleting and adding rows at the "Compliance Package Item" tab on the top.');
		$cprId['options'] = [$model->CompliancePackageRegulator, 'getEmptyRegulators'];
		// ddd($cprId);

		$_Collection = new FieldDataCollection([], $e->subject->model);
		$_Collection->add('compliance_package_regulator_id', $cprId);
		$_Collection->add('CsvFile', [
			'type' => 'file',
			'label' => __('File Upload'),
			'description' => __('Upload the Compliance Package in CSV format. Remember we hold a database of pre-compiled packages for most well known frameworks (PCI, ISO, Etc) on our documentation.'),
			'editable' => true
		]);

		$this->_FieldDataCollection = $_Collection;
	}

	public function _beforeImportRender(CakeEvent $e)
	{
		$this->Modals->setHeaderHeading($this->title);
		$this->Modals->changeConfig('footer.buttons.saveBtn.text', __('Import'));
	}

	/**
	* Make a full copy from one of your existing Third Parties with Compliance Package keeping all settings.
	*/
	public function duplicate() {
		$this->title = __('Duplicate a Compliance Package');
		$this->set('subtitle_for_layout', __('Make a full copy of a Compliance Package, keeping it\'s original settings'));

		$this->set(array(
			'compliance_package_regulators' => $this->CompliancePackage->CompliancePackageRegulator->getEmptyRegulators()
		));

		$this->Crud->useModel('CompliancePackageRegulator');
		$this->Crud->enable('duplicate');

		$this->Crud->action()->saveOptions([
			'deep' => true
		]);

		$this->Crud->on('beforeHandle', [$this, '_beforeDuplicateHandle']);
		$this->Crud->on('beforeSave', [$this, '_beforeDuplicateSave']);
		$this->Crud->on('beforeRender', [$this, '_beforeDuplicateRender']);
		$this->Crud->on('afterSave', [$this, '_afterDuplicateSave']);

		return $this->Crud->execute();
	}

	public function _afterDuplicateSave(CakeEvent $e)
	{
		$subject = $e->subject;
		if ($subject->success && $subject->created) {
			$subject->model->syncComplianceIndex($subject->id);

			$data = $subject->model->find('first', [
				'conditions' => [
					'CompliancePackageRegulator.id' => $subject->id
				],
				'contain' => [
					'CompliancePackage' => [
						'CompliancePackageItem' => [
							'ComplianceManagement'
						]
					]
				]
			]);

			$ids = Hash::extract($data, 'CompliancePackage.{n}.CompliancePackageItem.{n}.ComplianceManagement.id');
			$ComplianceManagement = ClassRegistry::init('ComplianceManagement');
			$ComplianceManagement->Behaviors->ObjectStatus->triggerObjectStatus($ComplianceManagement, null, $ids);

			$cacheKeys = [
				'readable',
				$this->logged['id'],
				'ComplianceManagement'
			];

			$cacheKey = implode('_', $cacheKeys);
			Cache::delete($cacheKey, 'visualisation');
		}
	}

	public function _beforeDuplicateHandle(CakeEvent $e)
	{
		$model = $e->subject->model;
		$CompliancePackageCollection = $model->CompliancePackage->getFieldCollection();
		$CompliancePackageRegulatorCollection = $model->getFieldCollection();

		$cprId = $CompliancePackageCollection->get('compliance_package_regulator_id')->config();
		$cprId['editable'] = true;
		$cprId['empty'] = __('Choose one');
		$cprId['options'] = [$model, 'getNotEmptyRegulators'];

		$_Collection = new FieldDataCollection([], $e->subject->model);
		$_Collection->add('compliance_package_regulator_id', $cprId);
		$_Collection->add($CompliancePackageRegulatorCollection->get('name'));

		$this->_FieldDataCollection = $_Collection;
	}

	public function _beforeDuplicateSave(CakeEvent $e)
	{
		//first validate the duplication form before actually duplicating the objects
		$this->CompliancePackage->CompliancePackageRegulator->validator()->add('compliance_package_regulator_id', 'notEmpty', array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'message' => __('This field is required')
		));
		$this->CompliancePackage->CompliancePackageRegulator->validator()->remove('Owner');
		$this->CompliancePackage->CompliancePackageRegulator->validator()->remove('url');
		$this->CompliancePackage->CompliancePackageRegulator->validator()->remove('language');
		$this->CompliancePackage->CompliancePackageRegulator->validator()->remove('restriction');
		$this->CompliancePackage->CompliancePackageRegulator->validator()->remove('version');
		$this->CompliancePackage->CompliancePackageRegulator->validator()->remove('publisher_name');
		$this->CompliancePackage->CompliancePackageRegulator->validator()->remove('Legal');

		$this->CompliancePackage->CompliancePackageItem->ComplianceManagement->validator()->remove('Owner');

		$this->CompliancePackage->CompliancePackageRegulator->set($this->request->data);
		$preFormValidate = $this->CompliancePackage->CompliancePackageRegulator->validates(array(
			'fieldList' => array('compliance_package_regulator_id', 'name')
		));

		if ($preFormValidate) {

			// remove the temporary validation rule for duplication form
			$this->CompliancePackage->CompliancePackageRegulator->validator()->remove('compliance_package_regulator_id');

			// find and prepare all data associations for a duplicated save
			$data = $this->CompliancePackage->CompliancePackageRegulator->find('first', array(
				'conditions' => array(
					'CompliancePackageRegulator.id' => $this->request->data['CompliancePackageRegulator']['compliance_package_regulator_id']
				),
				'contain' => array(

					//@todo try to optimize this with upcoming functionality migration
					'CompliancePackage' => array(
						'CompliancePackageItem' => array(
							'ComplianceManagement' => array(
								// 'Comment',
								'Owner' => array(
									'fields' => array('id')
								),
								'OwnerGroup' => array(
									'fields' => array('id')
								),
								'SecurityService' => array(
									'fields' => array('id')
								),
								'SecurityPolicy' => array(
									'fields' => array('id')
								),
								'Risk' => array(
									'fields' => array('id')
								),
								'ThirdPartyRisk' => array(
									'fields' => array('id')
								),
								'BusinessContinuity' => array(
									'fields' => array('id')
								),
								'Project' => array(
									'fields' => array('id')
								),
								'Asset' => array(
									'fields' => array('id')
								),
								'ComplianceException' => array(
									'fields' => array('id')
								),
								'ComplianceAnalysisFinding' => array(
									'fields' => array('id')
								),
							)
						)
					)
				)
			));

			if (empty($data)) {
				throw new NotFoundException();
			}

			//@todo perform a more dynamic merge of data from duplicate form
			// $data = Hash::merge($data, $this->request->data);
			$data['CompliancePackageRegulator']['name'] = $this->request->data['CompliancePackageRegulator']['name'];

			//remove data we dont want to save
			unset($data['CompliancePackageRegulator']['id']);
			unset($data['CompliancePackageRegulator']['created']);
			unset($data['CompliancePackageRegulator']['modified']);

			$data = Hash::remove($data, 'CompliancePackage.{n}.id');
			$data = Hash::remove($data, 'CompliancePackage.{n}.created');
			$data = Hash::remove($data, 'CompliancePackage.{n}.modified');

			$data = Hash::remove($data, 'CompliancePackage.{n}.CompliancePackageItem.{n}.id');
			$data = Hash::remove($data, 'CompliancePackage.{n}.CompliancePackageItem.{n}.created');
			$data = Hash::remove($data, 'CompliancePackage.{n}.CompliancePackageItem.{n}.modified');

			$data = Hash::remove(
				$data, 'CompliancePackage.{n}.CompliancePackageItem.{n}.ComplianceManagement.id'
			);
			$data = Hash::remove(
				$data, 'CompliancePackage.{n}.CompliancePackageItem.{n}.ComplianceManagement.compliance_package_item_id'
			);
			$data = Hash::remove(
				$data, 'CompliancePackage.{n}.CompliancePackageItem.{n}.ComplianceManagement.created'
			);
			$data = Hash::remove(
				$data, 'CompliancePackage.{n}.CompliancePackageItem.{n}.ComplianceManagement.modified'
			);

			// $data = Hash::remove(
			// 	$data, 'CompliancePackage.{n}.CompliancePackageItem.{n}.ComplianceManagement.Comment.{n}.id'
			// );
			// $data = Hash::remove(
			// 	$data, 'CompliancePackage.{n}.CompliancePackageItem.{n}.ComplianceManagement.Comment.{n}.created'
			// );
			// $data = Hash::remove(
			// 	$data, 'CompliancePackage.{n}.CompliancePackageItem.{n}.ComplianceManagement.Comment.{n}.modified'
			// );

			// handle HABTM relation saving in the Cake way
			foreach ($data['CompliancePackage'] as $packageKey => &$package) {
				foreach ($package['CompliancePackageItem'] as $key => &$item) {
					if (!isset($item['ComplianceManagement'])) {
						continue;
					}

					$item['ComplianceManagement']['Project'] = $this->parseComplianceHabtmJoins($item['ComplianceManagement'], 'Project');
					$item['ComplianceManagement']['SecurityService'] = $this->parseComplianceHabtmJoins($item['ComplianceManagement'], 'SecurityService');
					$item['ComplianceManagement']['SecurityPolicy'] = $this->parseComplianceHabtmJoins($item['ComplianceManagement'], 'SecurityPolicy');
					$item['ComplianceManagement']['Risk'] = $this->parseComplianceHabtmJoins($item['ComplianceManagement'], 'Risk');
					$item['ComplianceManagement']['ThirdPartyRisk'] = $this->parseComplianceHabtmJoins($item['ComplianceManagement'], 'ThirdPartyRisk');
					$item['ComplianceManagement']['BusinessContinuity'] = $this->parseComplianceHabtmJoins($item['ComplianceManagement'], 'BusinessContinuity');
					$item['ComplianceManagement']['Asset'] = $this->parseComplianceHabtmJoins($item['ComplianceManagement'], 'Asset');
					$item['ComplianceManagement']['ComplianceException'] = $this->parseComplianceHabtmJoins($item['ComplianceManagement'], 'ComplianceException');
					$item['ComplianceManagement']['ComplianceAnalysisFinding'] = $this->parseComplianceHabtmJoins($item['ComplianceManagement'], 'ComplianceAnalysisFinding');

					$ownerUsers = $this->parseComplianceHabtmJoins($item['ComplianceManagement'], 'Owner');
					$ownerGroups = $this->parseComplianceHabtmJoins($item['ComplianceManagement'], 'OwnerGroup');

					$owners = [];

					foreach ($ownerUsers as $userId) {
						$owners[] = UserFieldsBehavior::getUserIdPrefix() . $userId;
					}

					foreach ($ownerGroups as $groupId) {
						$owners[] = UserFieldsBehavior::getGroupIdPrefix() . $groupId;
					}

					unset($item['ComplianceManagement']['Owner']);
					unset($item['ComplianceManagement']['OwnerGroup']);

					$item['ComplianceManagement']['Owner'] = $owners;
				}
			}

			$this->request->data = $data;
		}
	}

	public function _beforeDuplicateRender(CakeEvent $e)
	{
		$this->Modals->setHeaderHeading($this->title);
		$this->Modals->changeConfig('footer.buttons.saveBtn.text', __('Duplicate'));
	}

	/**
	 * Helper method to parse whats is joined with a ComplianceManagement for duplication.
	 */
	protected function parseComplianceHabtmJoins($complianceManagementData, $modelName) {
		return Hash::extract($complianceManagementData, $modelName . '.{n}.id');
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
