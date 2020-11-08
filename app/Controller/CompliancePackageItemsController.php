<?php
App::uses('AppController', 'Controller');

class CompliancePackageItemsController extends AppController
{
	public $helpers = [];
	public $components = [
		'Search.Prg',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				],
			],
			'listeners' => [
				'Api', 'ApiPagination', 'Widget.Widget', 'Visualisation.Visualisation'
			]
		],
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
		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'trash', 'history', 'restore']);

		parent::beforeFilter();

		$this->title = __('Compliance Packages');
		$this->subTitle = __('Manage the library of compliance requirements in the scope of your GRC program');
	}

	public function index() {
		$this->title = __('Compliance Package Items');

		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->title = __('Delete a Compliance Package Item');

		return $this->Crud->execute();
	}

	public function _afterSave(CakeEvent $event) {
		if (!empty($event->subject->success)) {
			if (!empty($event->subject->created)) {
				$ComplianceManagement = ClassRegistry::init('ComplianceManagement');
				$ComplianceManagement->syncObjects();
			}
		}
	}

	public function add() {
		$this->Crud->on('beforeSave', [$this, '_beforeSave']);
		$this->Crud->on('afterSave', [$this, '_afterSave']);
		$this->Crud->on('beforeHandle', [$this, '_configureFields']);

		return $this->Crud->execute();
	}

	public function loadPackages()
	{
		// we reset the complinace_package_id field
		$this->request->data['CompliancePackageItem']['compliance_package_id'] = null;

		$this->_configureFields();
	}

	public function loadPackageFormFields()
	{
		$this->_configureFields();
	}

	public function edit($id = null)
	{
		$this->title = __('Edit a Compliance Package Item');

		$this->set('id', $id);

		$this->Crud->on('beforeSave', [$this, '_beforeSave']);
		$this->Crud->on('afterSave', [$this, '_afterSave']);
		$this->Crud->on('beforeHandle', [$this, '_configureFields']);

		return $this->Crud->execute();
	}

	public function _configureFields()
	{
		$ComplianceCollection = $this->_FieldDataCollection;

		$CompliancePackage = ClassRegistry::init('CompliancePackage');
		$CompliancePackageCollection = $CompliancePackage->getFieldCollection();
		$this->set($CompliancePackageCollection->getViewOptions('CompliancePackageCollection'));

		$_Collection = new FieldDataCollection([], $this->CompliancePackageItem);

		$c = $CompliancePackageCollection->compliance_package_regulator_id->config();
		$c['renderHelper'] = ['CompliancePackageItems', 'compliancePackageRegulatorField'];
		$c['empty'] = __('Choose one');
		$c['editable'] = true;

		$_Collection->add(new FieldDataEntity($c, $CompliancePackage));
		$_Collection->add($ComplianceCollection->compliance_package_id);
		$_Collection->add($ComplianceCollection->name);
		$_Collection->add($ComplianceCollection->item_id);
		$_Collection->add($ComplianceCollection->description);
		$_Collection->add($ComplianceCollection->audit_questionaire);

		// for loadPackages() action
		if (isset($this->request->data['CompliancePackage']['compliance_package_regulator_id'])) {
			$cprId = $this->request->data['CompliancePackage']['compliance_package_regulator_id'];
		}

		// when editing or loading part of the form via ajax
		if (!empty($cprId)) {
			$options = $_Collection->compliance_package_id->config('options');
			$options['args'] = [$cprId];

			$_Collection->compliance_package_id->config('options', $options);
		}

		$this->_FieldDataCollection = $_Collection;
	}

	public function _beforeSave(CakeEvent $event)
	{
		if (!empty($this->request->data['CompliancePackageItem']['compliance_package_id'])) {
			$this->request->data['CompliancePackage']['id'] = $this->request->data['CompliancePackageItem']['compliance_package_id'];
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

	public function trash()
	{
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

}
