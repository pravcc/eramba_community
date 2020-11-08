<?php
App::uses('AppController', 'Controller');
App::uses('DataAssetInstance', 'Model');
App::uses('Hash', 'Utility');
App::uses('DataAssetGdpr', 'Model');

/**
 * @section
 */
class DataAssetsController extends AppController
{
	public $uses = ['DataAsset', 'DataAssetInstance'];
	public $helpers = [];
	public $components = [
		'Search.Prg', 'Paginator', 'AdvancedFilters', 'Pdf', 'ObjectStatus.ObjectStatus',
		'Ajax',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true,
				],
				'add' => [
                    'saveOptions' => [
                        'deep' => false
                    ]
                ],
                'edit' => [
                    'saveOptions' => [
                        'deep' => false
                    ]
                ]
			],
			'listeners' => [
				'Api', 'ApiPagination', '.SubSection', 'Widget.Widget',
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
		$this->Ajax->settings['modules'] = ['comments', 'records', 'attachments'];
		
		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'trash', 'history', 'restore']);

		parent::beforeFilter();

		$this->title = __('Data Flows');
		$this->subTitle = __('For the data assets previously identified, describe the process of how it is created, processed, stored, tansmitted and disposed of in order to ensure that the correct controls are in place for each phase of the lifecycle of the data.');
	}

	public function index($id = null) {
		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');
		$this->Crud->addListener('ObjectStatus', 'ObjectStatus.ObjectStatus');

		return $this->Crud->execute();
	}

	public function delete($id = null)
	{
		$this->subTitle = __('Delete a Data asset.');

		return $this->Crud->execute();
	}

	public function trash()
	{
		return $this->Crud->execute();
	}

	public function add($dataAssetInstanceId = null)
	{
		$this->title = __('Analyse a Data Asset');
		$this->initAddEditSubtitle();

		// if (isset($this->request->data['DataAsset']['data_asset_instance_id'])) {
			// $dataAssetInstanceId = $this->request->data['DataAsset']['data_asset_instance_id'];

			$this->Crud->on('beforeSave', function() {
				if (!empty($this->request->data['DataAsset']['data_asset_instance_id'])) {
					$this->_commonUpdateProcess($this->request->data['DataAsset']['data_asset_instance_id']);
				}
			});
			
			$this->Crud->on('beforeRender', function() {
				if (!empty($this->request->data['DataAsset']['data_asset_instance_id'])) {
					$this->_setFormFields($this->request->data['DataAsset']['data_asset_instance_id']);
				}
			});
		// }

		return $this->Crud->execute();
	}

	public function _setFormFields($dataAssetInstanceId)
	{
		$DataAssetGdpr = ClassRegistry::init('DataAssetGdpr');

		$dataAssetStatusId = DataAsset::STATUS_COLLECTED;
		if (!empty($this->request->data['DataAsset']['data_asset_status_id'])) {
			$dataAssetStatusId = $this->request->data['DataAsset']['data_asset_status_id'];
		}

		$fieldList = DataAssetGdpr::$fieldGroups[$dataAssetStatusId];

		foreach ($fieldList as $field) {
			$Field = $DataAssetGdpr->getFieldDataEntity($field);

			$Field->config('group', 'gdpr');
			$Field->config('groupClass', $this->DataAsset->fieldGroupData['gdpr']);

			$this->_FieldDataCollection->remove($field);
			$this->_FieldDataCollection->add($Field);
		}

		// set order options based on $dataAssetInstanceId
		$this->_FieldDataCollection->get('order')->config('options', $this->DataAsset->getOrderOptions($dataAssetInstanceId));
	}

	private function _commonUpdateProcess($dataAssetInstanceId)
	{
		$dataAssetInstance = $this->DataAssetInstance->getItem($dataAssetInstanceId);

		$this->request->data['DataAsset']['data_asset_instance_id'] = $dataAssetInstanceId;

		if ($dataAssetInstance['DataAssetSetting']['gdpr_enabled']) {
			$this->DataAsset->enableGdprValidation();
			if ($this->request->is('post') || $this->request->is('put')) {
				if (!isset($this->request->data['DataAsset']['data_asset_status_id'])) {
					$this->request->data['DataAsset']['data_asset_status_id'] = DataAsset::STATUS_COLLECTED;
				}

				$this->DataAsset->DataAssetGdpr->setValidation($this->request->data['DataAsset']['data_asset_status_id']);
			}
		}

		$this->set('dataAssetInstance', $dataAssetInstance);
	}

	public function edit($id) {
		$this->title = __('Analyse a Data Asset');
		$this->initAddEditSubtitle();

		$dataAssetInstanceId = $this->DataAsset->field('data_asset_instance_id', ['id' => $id]);

		$this->Crud->on('beforeSave', function() use ($dataAssetInstanceId) {
			$this->_commonUpdateProcess($dataAssetInstanceId);
		});
		
		$this->Crud->on('beforeFind', [$this, '_beforeFind']);
		$this->Crud->on('afterFind', [$this, '_afterFind']);
		$this->Crud->on('beforeRender', function() use ($dataAssetInstanceId) {
			$this->_setFormFields($dataAssetInstanceId);
		});

		return $this->Crud->execute();
	}

	public function _beforeFind(CakeEvent $event) {
		$event->subject->query['recursive'] = 2;
	}

	public function _afterFind(CakeEvent $event) {
		$data = $event->subject->item;
		if (!empty($data['DataAssetGdpr']['DataAssetGdprDataType'])) {
			$data['DataAssetGdpr']['DataAssetGdprDataType'] = Hash::extract($data['DataAssetGdpr']['DataAssetGdprDataType'], '{n}.data_type');
		}
		if (!empty($data['DataAssetGdpr']['DataAssetGdprCollectionMethod'])) {
			$data['DataAssetGdpr']['DataAssetGdprCollectionMethod'] = Hash::extract($data['DataAssetGdpr']['DataAssetGdprCollectionMethod'], '{n}.collection_method');
		}
		if (!empty($data['DataAssetGdpr']['DataAssetGdprLawfulBase'])) {
			$data['DataAssetGdpr']['DataAssetGdprLawfulBase'] = Hash::extract($data['DataAssetGdpr']['DataAssetGdprLawfulBase'], '{n}.lawful_base');
		}
		if (!empty($data['DataAssetGdpr']['DataAssetGdprThirdPartyCountry'])) {
			$data['DataAssetGdpr']['DataAssetGdprThirdPartyCountry'] = Hash::extract($data['DataAssetGdpr']['DataAssetGdprThirdPartyCountry'], '{n}.third_party_country');
		}
		if (!empty($data['DataAssetGdpr']['DataAssetGdprArchivingDriver'])) {
			$data['DataAssetGdpr']['DataAssetGdprArchivingDriver'] = Hash::extract($data['DataAssetGdpr']['DataAssetGdprArchivingDriver'], '{n}.archiving_driver');
		}
        if (!empty($data['DataAssetGdpr']['ThirdPartyInvolved'])) {
            $data['DataAssetGdpr']['ThirdPartyInvolved'] = Hash::extract($data['DataAssetGdpr']['ThirdPartyInvolved'], '{n}.country_id');
        }

		$event->subject->item = $data;
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('In the end, is your core data assets that you struggle to protect every day, isnt it?. It\'s important you identify for each data asset status (creation, modification, storage, transit and deletion) how those assets are protected.');
	}

	public function getAssociatedRiskData() {
		$this->YoonityJSConnector->deny();

		$this->allowOnlyAjax();
		$this->autoRender = false;

		$riskIds = json_decode($this->request->query['riskIds']);
		$data = [
			'securityServices' => array_values($this->DataAsset->Risk->RisksSecurityService->find('list', [
				'conditions' => [
					'RisksSecurityService.risk_id' => $riskIds
				],
				'fields' => [
					'RisksSecurityService.security_service_id'
				]
			])),
			'projects' => array_values($this->DataAsset->Risk->ProjectsRisk->find('list', [
				'conditions' => [
					'ProjectsRisk.risk_id' => $riskIds
				],
				'fields' => [
					'ProjectsRisk.project_id'
				]
			])),
			'securityPolicies' => array_values($this->DataAsset->Risk->RisksSecurityPolicy->find('list', [
				'conditions' => [
					'RisksSecurityPolicy.risk_id' => $riskIds,
					'RisksSecurityPolicy.risk_type' => 'asset-risk',
				],
				'fields' => [
					'RisksSecurityPolicy.security_policy_id'
				]
			])),
		];

		echo json_encode($data);
	}

	public function getAssociatedThirdPartyRiskData() {
		$this->YoonityJSConnector->deny();

		$this->allowOnlyAjax();
		$this->autoRender = false;

		$riskIds = json_decode($this->request->query['riskIds']);
		$data = [
			'securityServices' => array_values($this->DataAsset->ThirdPartyRisk->SecurityServicesThirdPartyRisk->find('list', [
				'conditions' => [
					'SecurityServicesThirdPartyRisk.third_party_risk_id' => $riskIds
				],
				'fields' => [
					'SecurityServicesThirdPartyRisk.security_service_id'
				]
			])),
			'projects' => array_values($this->DataAsset->ThirdPartyRisk->ProjectsThirdPartyRisk->find('list', [
				'conditions' => [
					'ProjectsThirdPartyRisk.third_party_risk_id' => $riskIds
				],
				'fields' => [
					'ProjectsThirdPartyRisk.project_id'
				]
			])),
			'securityPolicies' => array_values($this->DataAsset->ThirdPartyRisk->RisksSecurityPolicy->find('list', [
				'conditions' => [
					'RisksSecurityPolicy.risk_id' => $riskIds,
					'RisksSecurityPolicy.risk_type' => 'third-party-risk',
				],
				'fields' => [
					'RisksSecurityPolicy.security_policy_id'
				]
			])),
		];

		echo json_encode($data);
	}

	public function getAssociatedBusinessContinuityData() {
		$this->YoonityJSConnector->deny();

		$this->allowOnlyAjax();
		$this->autoRender = false;

		$riskIds = json_decode($this->request->query['riskIds']);
		$data = [
			'securityServices' => array_values($this->DataAsset->BusinessContinuity->BusinessContinuitiesSecurityService->find('list', [
				'conditions' => [
					'BusinessContinuitiesSecurityService.business_continuity_id' => $riskIds
				],
				'fields' => [
					'BusinessContinuitiesSecurityService.security_service_id'
				]
			])),
			'projects' => array_values($this->DataAsset->BusinessContinuity->BusinessContinuitiesProjects->find('list', [
				'conditions' => [
					'BusinessContinuitiesProjects.business_continuity_id' => $riskIds
				],
				'fields' => [
					'BusinessContinuitiesProjects.project_id'
				]
			])),
			'securityPolicies' => array_values($this->DataAsset->BusinessContinuity->RisksSecurityPolicy->find('list', [
				'conditions' => [
					'RisksSecurityPolicy.risk_id' => $riskIds,
					'RisksSecurityPolicy.risk_type' => 'business-risk',
				],
				'fields' => [
					'RisksSecurityPolicy.security_policy_id'
				]
			])),
		];

		echo json_encode($data);
	}

	public function getAssociatedSecurityServices() {
		$this->YoonityJSConnector->deny();

		$this->allowOnlyAjax();
		$this->autoRender = false;

		$serviceIds = json_decode($this->request->query['serviceIds']);
		$data = array_values($this->DataAsset->SecurityService->SecurityPoliciesSecurityService->find('list', [
			'conditions' => [
				'SecurityPoliciesSecurityService.security_service_id' => $serviceIds
			],
			'fields' => [
				'SecurityPoliciesSecurityService.security_policy_id'
			]
		]));

		echo json_encode($data);
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
