<?php
App::uses('AppController', 'Controller');
App::uses('Hash', 'Utility');

/**
 * @section
 */
class CompliancePackageRegulatorsController extends AppController
{
	public $helpers = ['UserFields.UserField'];
	public $components = [
		'Search.Prg', 'AdvancedFilters', 'Paginator', 
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
			'fields' => ['Owner']
		]
	];

	public $ownersSubmitted = false;

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
		$this->Crud->view('delete', 'delete');

		parent::beforeFilter();

		$this->title = __('Compliance Packages');
		$this->subTitle = __('Manage Compliance Packages and their requirements');
	}

	public function index()
	{
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		// $this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function delete($id = null) {
		$this->title = __('Compliance Packages');
		$this->subTitle = __('Delete a Compliance Package.');
		
		$this->Crud->on('afterDelete', function(CakeEvent $event) {
			$subject = $event->subject;
			$model = $subject->model;

			if ($event->subject->success) {
				// after successful deletion we remove unneeded compliance filters
				$model->deleteComplianceIndex($event->subject->id);
			}
		});

		return $this->Crud->execute();
	}

	public function trash()
	{
		// $this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');
		
		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Create a Compliance Package');

		$this->initAddEditSubtitle();

		$this->Crud->on('afterSave', [$this, '_syncFilter']);

		return $this->Crud->execute();
	}

	public function edit( $id = null ) {
		$this->title = __('Edit a Compliance Package');

		$this->initAddEditSubtitle();

		$this->Crud->on('afterSave', [$this, '_syncFilter']);
		$this->Crud->on('beforeSave', [$this, '_beforeEdit']);
		$this->Crud->on('afterSave', [$this, '_afterEdit']);

		return $this->Crud->execute();
	}

	/**
	 * Lets synchronize filters related to the added/edited Compliance Package.
	 * 
	 * @param  CakeEvent $e
	 */
	public function _syncFilter(CakeEvent $e)
	{
		$subject = $e->subject;
		$model = $subject->model;
		$request = $subject->request;

		if ($subject->success) {
			$model->syncComplianceIndex($subject->id);

			if ($subject->created === false) {
				ClassRegistry::init('CompliancePackageRegulator')->syncFiltersName($subject->id);
			}
		}
	}

	/**
	 * Store difference of Owner values for later use in _afterEdit() to sync Owners for compliance analysis.
	 * 
	 * @param  CakeEvent $e
	 */
	public function _beforeEdit(CakeEvent $e)
	{
		$subject = $e->subject;
		$model = $subject->model;
		$request = $subject->request;

		if (!empty($request->data['CompliancePackageRegulator']['Owner'])) {
			$requestOwners = $request->data['CompliancePackageRegulator']['Owner'];
			$data = $model->find('first', [
				'conditions' => [
					'CompliancePackageRegulator.id' => $subject->id
				],
				'contain' => [
					'Owner',
					'OwnerGroup'
				]
			]);

			$previousOwners = Hash::extract($data, 'Owner.{n}.id');

			// in case the save process goes through here, continue the rest of the process later in afterSave()
			$this->ownersSubmitted = true;

			// we have to save these values as a class variables for later use
			$this->addedOwners = array_values(array_diff($requestOwners, $previousOwners));
			$this->removedOwners = array_values(array_diff($previousOwners, $requestOwners));
		}
	}

	/**
	 * Lets synchronize owners for related Compliance Analysis items.
	 * 
	 * @param  CakeEvent $e
	 */
	public function _afterEdit(CakeEvent $e)
	{
		$subject = $e->subject;
		$model = $subject->model;
		$request = $subject->request;

		if ($subject->success && !empty($this->ownersSubmitted)) {
			$packages = $model->CompliancePackage->find('list', [
				'conditions' => [
					'CompliancePackage.compliance_package_regulator_id' => $subject->id
				],
				'fields' => ['id'],
				'recursive' => -1
			]);

			$items = $model->CompliancePackage->CompliancePackageItem->find('list', [
				'conditions' => [
					'CompliancePackageItem.compliance_package_id' => $packages
				],
				'fields' => ['id'],
				'recursive' => -1
			]);

			$CM = $model->CompliancePackage->CompliancePackageItem->ComplianceManagement;
			$managements = $CM->find('list', [
				'conditions' => [
					'ComplianceManagement.compliance_package_item_id' => $items
				],
				'fields' => ['id'],
				'recursive' => -1
			]);

			$currentOwners = Hash::extract($request->data, 'CompliancePackageRegulator.Owner.{n}');

			$ret = true;
			foreach ($managements as $management) {
				$data = $CM->find('first', [
					'conditions' => [
						'ComplianceManagement.id' => $management
					],
					'contain' => [
						'Owner',
						'OwnerGroup'
					]
				]);

				$managementOwners = Hash::extract($data, 'Owner.{n}.id');
				foreach ($this->addedOwners as $addedOwner) {
					$managementOwners[] = $addedOwner;
				}

				$managementOwners = array_unique($managementOwners);

				foreach ($this->removedOwners as $removedOwner) {
					$key = array_search($removedOwner, $managementOwners);
					if ($key !== false) {
						unset($managementOwners[$key]);
					}
				}

				$saveData = [
                    'ComplianceManagement' => [
                        'id' => $management,
                        'Owner' => $managementOwners
                    ]
                ];

                $ret &= $CM->saveAssociated($saveData, [
                    'validate' => 'first',
                    'atomic' => true,
                    'deep' => true,
                    'fieldList' => [
                    	'Owner'
                    ]
                ]);
			}
		}
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('TBD.');
	}

	public function history($id)
	{
		return $this->Crud->execute();
	}

	public function restore($autidId)
	{
		$this->Crud->on('afterRestore', function(CakeEvent $event) {
			if ($event->subject->success) {
				// after restoration we (again) recreate compliance filters
				ClassRegistry::init('CompliancePackageRegulator')->syncComplianceIndex($event->subject->id);
			}
		});

		return $this->Crud->execute();
	}
}
