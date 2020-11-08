<?php
App::uses('FilterFieldSet', 'AdvancedFilters.Lib');
App::uses('FilterParamSet', 'AdvancedFilters.Lib');
App::uses('CakeEvent', 'Event');
App::uses('CakeEventListener', 'Event');
App::uses('CakeEventManager', 'Event');
App::uses('AdvancedFiltersQuery', 'Lib/AdvancedFilters');
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');
App::uses('CakeText', 'Utility');
App::uses('Router', 'Routing');
App::uses('DebugTimer', 'DebugKit.Lib');
App::uses('CakeSession', 'Model/Datasource');
App::uses('Router', 'Routing');
App::uses('AdvancedFilterValue', 'AdvancedFilters.Model');

class AdvancedFiltersObject extends CakeObject implements CakeEventListener
{
	/**
	 * Instance of an AdvancedFilter model.
	 * 
	 * @var AdvancedFilter
	 */
	protected $_Instance = null;

	/**
	 * Relation to a Advanced Filter database object.
	 * 
	 * @var string
	 */
	protected $Model, $id, $userId = null;

	/**
	 * Instance of a FilterFieldSet class.
	 * 
	 * @var FilterFieldSet
	 */
	protected $_FilterFieldSet = null;

	/**
	 * Instance of a FilterParamSet class.
	 * 
	 * @var FilterParamSet
	 */
	protected $_FilterParamSet = null;

	/**
	 * Name.
	 * 
	 * @var string
	 */
	protected $_name = null;

	/**
	 * Description.
	 * 
	 * @var string
	 */
	protected $_description = null;

	/**
	 * Data.
	 * 
	 * @var array
	 */
	protected $_data = [];

	protected $_count, $_pageCount, $_currentPage, $_limit = null;

	/**
	 * Instance of the CakeEventManager this filter instance is using
	 * to dispatch inner events.
	 *
	 * @var CakeEventManager
	 */
	protected $_eventManager = null;

	/**
	 * Toggle displaying for Manage button.
	 * 
	 * @var boolean
	 */
	protected $_manageButton = false;

	/**
	 * Toggle that says if current filter is really a filter stored in DB.
	 * 
	 * @var boolean
	 */
	public $isDbFilter = false;

	// is current filter able to be deleted
	public $possibleToDelete = false;

	public $csvCountExport = false;
	public $csvDataExport = false;

	/**
	 * Filter parameters.
	 * 
	 * @var array
	 */
	protected $_filterValues = [];

	protected $_fieldData = [];

	// converted values as associated array of fields for filtering
	protected $_convertedValues = null;

	// Whether or not was filter method called already
	protected $_executed = false;

	// Session key for just saved functionality
	const JUST_SAVED_SESSION_KEY = 'AdvancedFiltersObject.justSaved';

	/**
	 * Constructor for a Filter.
	 * 
	 * @param null|int $id ID of the filter or null if the filter is running without a table saved row.
	 */
	public function __construct($id = null, $userId = null, $setFilterValues = true)
	{
		$this->_Instance = ClassRegistry::init('AdvancedFilters.AdvancedFilter');
		$this->id = $id;
		$this->userId = $userId;

		// process all parameters required for the actual filter query execution
		$this->_setFilterParams($setFilterValues);
	}

	/**
	 * Returns a list of all events that will fire in the model during it's lifecycle.
	 * You can override this function to add your own listener callbacks
	 *
	 * @return array
	 */
	public function implementedEvents() {
		return array(
			'AdvancedFilter.beforeFind' => array('callable' => 'beforeFind', 'passParams' => true),
			'AdvancedFilter.afterFind' => array('callable' => 'afterFind', 'passParams' => true)
		);
	}

	public function beforeFind($query) {
		return true;
	}

	public function afterFind($results) {
		if (is_array($results)) {
			$Collection = $this->Model->getItemDataCollection();
			foreach ($results as $item) {
				$Collection->add($item);
			}

			$results = $Collection;
		}

		return $results;
	}

	public function getEventManager() {
		if (empty($this->_eventManager)) {
			$this->_eventManager = new CakeEventManager();
			$this->_eventManager->attach($this);
		}

		return $this->_eventManager;
	}

	public function setModel(Model $Model)
	{
		$this->Model = $Model;

		$this->_initModelConfiguration();
	}

	public function getModel()
	{
		return $this->Model;
	}

	public function setId($id, $setFilterValues = true)
	{
		$this->id = $id;
		$this->_configureId($setFilterValues);
	}

	protected function _configureId($setFilterValues = true)
	{
		$filter = $this->_Instance->get($this->id);
		if (empty($filter)) {
			return false;
			// throw new NotFoundException(sprintf("Advanced Filter with ID '%d' does not exist.", $this->id), 1);
		}
		
		$this->setModel(ClassRegistry::init($filter['AdvancedFilter']['model']));
		$this->setName($filter['AdvancedFilter']['name']);
		$this->setDescription($filter['AdvancedFilter']['description']);

		$this->setUserFilterParams();

		if ($setFilterValues === true) {
			$formattedValues = $this->_Instance->getFormattedValues($this->id);
			$this->setFilterValues($formattedValues);
		}

		$this->manageFilter(true);
		$this->isDbFilter = true;
		
		if (!$filter['AdvancedFilter']['system_filter']) {
			$this->possibleToDelete = true;	
		}
		
		if ($filter['AdvancedFilter']['log_result_count']) {
			$this->csvCountExport = true;	
		}

		if ($filter['AdvancedFilter']['log_result_data']) {
			$this->csvDataExport = true;	
		}
	}

	public function setUserFilterParams()
	{
		if ($this->id) {
			// get owner user if none is provided
			if ($this->userId == null) {
				$filter = $this->_Instance->find('first', [
					'conditions' => [
						'id' => $this->id
					],
					'fields' => [
						'user_id'
					],
					'recursive' => -1
				]);

				$this->userId = $filter['AdvancedFilter']['user_id'];
			}

			$params = $this->_Instance->AdvancedFilterUserParam->find('all', [
				'conditions' => [
					'AdvancedFilterUserParam.advanced_filter_id' => $this->id,
					'AdvancedFilterUserParam.user_id' => $this->userId
				],
				'fields' => [
					'AdvancedFilterUserParam.type',
					'AdvancedFilterUserParam.param',
					'AdvancedFilterUserParam.value'
				],
				'recursive' => -1
			]);

			$this->_FilterParamSet = new FilterParamSet();
			foreach ($params as $param) {
				$this->_FilterParamSet->add($param['AdvancedFilterUserParam']['type'], $param['AdvancedFilterUserParam']['param'], $param['AdvancedFilterUserParam']['value']);
			}
		}
	}

	public function FilterParamSet()
	{
		return $this->_FilterParamSet;
	}

	public function FilterFieldSet()
	{
		return $this->_FilterFieldSet;
	}

	protected function _setFilterFields()
	{
		$FilterFieldSet = new FilterFieldSet($this->Model);
		$this->_FilterFieldSet = $FilterFieldSet;
	}

	/**
	 * Configure all filter parameters to prepare it for filter process.
	 */
	protected function _setFilterParams($setFilterValues = true)
	{ 
		if ($this->id) {
			$this->_configureId($setFilterValues);
		}
		else {
			$this->setName(__('Not Saved Filter'));
			$this->setDescription(__('This is a temporal filter, you can adjust this filter settings or save it by clicking on Manage'));
			$this->id = CakeText::uuid();
		}
	}

	public function manageFilter($toggle = null)
	{
		if ($toggle === null) {
			return $this->_manageButton;
		}

		$this->_manageButton = (bool) $toggle;
	}

	public function setConvertedValues($values)
	{
		$this->_convertedValues = $values;
	}


	public function getConvertedValues()
	{
		return $this->_convertedValues;
	}

	protected function _initModelConfiguration()
	{
		if (!$this->Model->Behaviors->enabled('AdvancedFilters.AdvancedFilters')) {
			return;
		}

		$this->Model->buildAdvancedFilterArgs();
	}

	protected function _configureConditions()
	{
		$this->_setFilterFields();

		// only convert if its not provided already
		if ($this->_convertedValues === null) {
			$this->_convertedValues = $this->_convertFilterValues();
		}

		$this->_buildConfig($this->_convertedValues);

		if (!$this->Model->Behaviors->enabled('Search.Searchable')) {
			$this->Model->Behaviors->load('Search.Searchable');
		}
	}

	public function configureConditions()
	{
		$this->_configureConditions();
	}

	public function getConditions()
	{
		$this->_configureConditions();

		// for query
		// $query = $this->buildQueryParams();
		
		$params = $this->buildParams();

		$conditions = $this->Model->parseCriteria($params);

		$this->resetFilterArgs();

		return $conditions;
	}

	/**
	 * Actual process that executes filter query having results set back into the class
	 * and returns back entire class instance.
	 *
	 * Converted Values are in the format:
	 * 
	 * 'id' => [
	 *    'value' => 'some_value',
	 *    'comparisonType' => 1',
	 *    'otherConfig' => '...'
	 * ],
	 * 'name' => [...]
	 * 
	 * @return AdvancedFiltersObject
	 */
	public function filter($type = 'all', $customOptions = [])
	{
		$customOptions = array_merge([
			'applyLimit' => false // apply filter's saved limit field to the query
		], $customOptions);

		// comments
		if ($this->Model->Behaviors->enabled('Comments.Comments')) {
			$this->Model->bindComments();
			$this->Model->bindLastComment();
		}

		// attachments
		if ($this->Model->Behaviors->enabled('Attachments.Attachments')) {
			$this->Model->bindAttachments();
			$this->Model->bindLastAttachment();
		}

		$conditions = $this->getConditions();

		$options = $this->_parseFindOptions();
		$options['conditions'] = $conditions;

		// Trigger beforeFind() event for this instance
		$event = new CakeEvent('AdvancedFilter.beforeFind', $this, array($options));
		list($event->break, $event->breakOn, $event->modParams) = array(true, array(false, null), 0);
		$this->getEventManager()->dispatch($event);

		if ($event->isStopped()) {
			return null;
		}

		$options = $event->result === true ? $event->data[0] : $event->result;

		if ($this->Model->alias == 'ComplianceManagement') {
			// $options['recursive'] = 2;
			$options['contain'] = [
				'CompliancePackageItem' => [
					'CompliancePackage' => [
						'CompliancePackageRegulator'
					]
				],
				'Risk',
				'ThirdPartyRisk',
				'BusinessContinuity',
				'ComplianceAnalysisFinding',
				'Legal',
				'ComplianceException',
				'SecurityPolicy',
				'SecurityService',
				'ComplianceTreatmentStrategy',
				'Asset',
				'Owner',
				'OwnerGroup',
				'Project' => [
					'ProjectAchievement'
				],
			];
		}
		if ($this->Model->alias == 'Goal') {
			// $options['recursive'] = 2;
			$options['contain'] = [
				'Owner',
				'SecurityService',
				'Risk',
				'ThirdPartyRisk',
				'BusinessContinuity',
				'Project',
				'SecurityPolicy',
				'ProgramIssue',
				'GoalAudit',
				'GoalAuditDate',
			];
		}
		elseif ($this->Model->alias == 'SecurityService') {
			// $options['recursive'] = 2;
			$options['contain'] = [
				'ServiceOwner',
				'ServiceOwnerGroup',
				'Collaborator',
				'CollaboratorGroup',
				'AuditOwner',
				'AuditOwnerGroup',
				'AuditEvidenceOwner',
				'AuditEvidenceOwnerGroup',
				'MaintenanceOwner',
				'MaintenanceOwnerGroup',
				'SecurityServiceAudit' => [
					'AuditOwner',
					'AuditOwnerGroup',
					'AuditEvidenceOwner',
					'AuditEvidenceOwnerGroup',
					'SecurityServiceAuditImprovement' => [
						'Project'
					]
				],
				'SecurityServiceMaintenance' => [
					'MaintenanceOwner',
					'MaintenanceOwnerGroup'
				],
				'SecurityServiceIssue',
				'SecurityServiceType',
				'Classification',
				'SecurityPolicy',
				'Risk',
				'ThirdPartyRisk',
				'BusinessContinuity',
				'SecurityIncident' => [
					'SecurityIncidentStatus'
				],
				'ServiceContract',
				'DataAsset' => [
					'DataAssetStatus',
					'DataAssetInstance' => [
						'Asset'
					]
				],
				'SecurityServiceAuditDate',
				'SecurityServiceMaintenanceDate',
				'Project' => [
					'ProjectAchievement'
				],
				'ComplianceManagement' => [
					'CompliancePackageItem' => [
						'CompliancePackage' => [
							'CompliancePackageRegulator'
						]
					]
				]
			];
		}
		elseif ($this->Model->alias == 'SecurityServiceAudit') {
			// $options['recursive'] = 2;
			$options['contain'] = [
				'AuditOwner',
				'AuditOwnerGroup',
				'AuditEvidenceOwner',
				'AuditEvidenceOwnerGroup',
				'SecurityServiceAuditImprovement',
				'SecurityService' => [
					'Classification',
					'Risk',
					'ThirdPartyRisk',
					'BusinessContinuity',
					'DataAsset' => [
						'DataAssetStatus',
						'DataAssetInstance' => [
							'Asset'
						]
					],
					'Project' => [
						'ProjectStatus',
						'ProjectAchievement'
					],
					'SecurityIncident' => [
						'SecurityIncidentStatus'
					],
					'ComplianceManagement' => [
						'CompliancePackageItem' => [
							'CompliancePackage' => [
								'CompliancePackageRegulator'
							]
						]
					]
				]
			];
		}
		elseif ($this->Model->alias == 'SecurityServiceMaintenance') {
			// $options['recursive'] = 2;
			$options['contain'] = [
				'MaintenanceOwner',
				'MaintenanceOwnerGroup',
				'SecurityService' => [
					'Classification',
					'Risk',
					'ThirdPartyRisk',
					'BusinessContinuity',
					'DataAsset' => [
						'DataAssetStatus',
						'DataAssetInstance' => [
							'Asset'
						]
					],
					'ComplianceManagement' => [
						'CompliancePackageItem' => [
							'CompliancePackage' => [
								'CompliancePackageRegulator'
							]
						]
					]
				]
			];
		}
		elseif ($this->Model->alias == 'SecurityPolicy') {
			$options['contain'] = [
				'SecurityPolicyDocumentType',
				'AssetLabel',
				'RelatedDocuments',
				'SecurityService',
				'PolicyException',
				'Project' => [
					'ProjectAchievement'
				],
				'AwarenessProgram',
				'ComplianceManagement' => [
					'CompliancePackageItem' => [
						'CompliancePackage' => [
							'CompliancePackageRegulator'
						]
					]
				],
				'RiskTreatment',
				'ThirdPartyRiskTreatment',
				'BusinessContinuityTreatment',
				'Owner',
				'OwnerGroup',
				'Collaborator',
				'CollaboratorGroup',
				'Tag',
				'SecurityPolicyReview',
				'LdapConnector',
				'DataAsset' => [
					'DataAssetStatus',
					'DataAssetInstance' => [
						'Asset'
					]
				],
			];
		}
		elseif ($this->Model->alias == 'ThirdParty') {
			$options['contain'] = [
				'Sponsor',
				'SponsorGroup',
				'ThirdPartyType',
				'Legal',
			];
		}
		elseif ($this->Model->alias == 'Risk') {
			$options['contain'] = [
				'RiskMitigationStrategy',
				'Asset' => [
					'BusinessUnit'
				],
				'Threat',
				'Vulnerability',
				'SecurityService',
				'RiskException',
				'Project' => [
					'ProjectAchievement'
				],
				'SecurityPolicyIncident' => [
					'SecurityPolicyDocumentType'
				],
				'SecurityPolicyTreatment' => [
					'SecurityPolicyDocumentType'
				],
				'SecurityIncident' => [
					'SecurityIncidentStatus'
				],
				'Owner',
				'OwnerGroup',
				'Stakeholder',
				'StakeholderGroup',
				'RiskClassification' => [
					'RiskClassificationType'
				],
				'RiskClassificationTreatment' => [
					'RiskClassificationType'
				],
				'RiskAppetiteThresholdTreatment',
				'RiskAppetiteThresholdAnalysis',
				'Tag',
				'RiskReview',
				'DataAsset' => [
					'DataAssetInstance' => [
						'Asset'
					],
					'DataAssetStatus'
				],
				'ComplianceManagement' => [
					'CompliancePackageItem' => [
						'CompliancePackage' => [
							'CompliancePackageRegulator'
						]
					]
				]
			];
		}
		elseif ($this->Model->alias == 'ThirdPartyRisk') {
			$options['contain'] = [
				'RiskMitigationStrategy',
				'Asset' => [
					'BusinessUnit'
				],
				'Threat',
				'Vulnerability',
				'SecurityService',
				'RiskException',
				'ThirdParty' => [
					'Legal'
				],
				'Project' => [
					'ProjectAchievement'
				],
				'SecurityPolicyIncident' => [
					'SecurityPolicyDocumentType'
				],
				'SecurityPolicyTreatment' => [
					'SecurityPolicyDocumentType'
				],
				'SecurityIncident' => [
					'SecurityIncidentStatus'
				],
				'Owner',
				'OwnerGroup',
				'Stakeholder',
				'StakeholderGroup',
				'RiskClassification' => [
					'RiskClassificationType'
				],
				'RiskClassificationTreatment' => [
					'RiskClassificationType'
				],
				'RiskAppetiteThresholdTreatment',
				'RiskAppetiteThresholdAnalysis',
				'Tag',
				'ThirdPartyRiskReview',
				'DataAsset' => [
					'DataAssetInstance' => [
						'Asset'
					],
					'DataAssetStatus'
				],
				'ComplianceManagement' => [
					'CompliancePackageItem' => [
						'CompliancePackage' => [
							'CompliancePackageRegulator'
						]
					]
				]
			];
		}
		elseif ($this->Model->alias == 'BusinessContinuity') {
			$options['contain'] = [
				'RiskMitigationStrategy',
				'BusinessUnit' => [
					'Process'
				],
				'Process' => [
					'BusinessUnit'
				],
				'Threat',
				'Vulnerability',
				'SecurityService',
				'RiskException',
				'Project' => [
					'ProjectAchievement'
				],
				'SecurityPolicyIncident' => [
					'SecurityPolicyDocumentType'
				],
				'SecurityPolicyTreatment' => [
					'SecurityPolicyDocumentType'
				],
				'SecurityIncident' => [
					'SecurityIncidentStatus'
				],
				'RiskClassification' => [
					'RiskClassificationType'
				],
				'RiskClassificationTreatment' => [
					'RiskClassificationType'
				],
				'Owner',
				'OwnerGroup',
				'Stakeholder',
				'StakeholderGroup',
				'BusinessContinuityPlan',
				'RiskAppetiteThresholdTreatment',
				'RiskAppetiteThresholdAnalysis',
				'Tag',
				'BusinessContinuityReview',
				'DataAsset' => [
					'DataAssetInstance' => [
						'Asset'
					],
					'DataAssetStatus'
				],
				'ComplianceManagement' => [
					'CompliancePackageItem' => [
						'CompliancePackage' => [
							'CompliancePackageRegulator'
						]
					]
				]
			];
		} else if ($this->Model->alias === 'BusinessUnit') {
			$options['contain'] = [
				'Process',
				'Legal',
				'BusinessUnitOwner',
				'BusinessUnitOwnerGroup'
			];
		} else if ($this->Model->alias === 'Process') {
			$options['contain'] = [
				'BusinessUnit'
			];
		} else if ($this->Model->alias === 'RiskException') {
			$options['contain'] = [
				'Risk',
				'ThirdPartyRisk',
				'BusinessContinuity',
				'Tag',
				'Requester',
				'RequesterGroup'
			];
		} else if ($this->Model->alias === 'PolicyException') {
			$options['contain'] = [
				'ThirdParty',
				'SecurityPolicy',
				'Classification',
				'Asset',
				'Requestor',
				'RequestorGroup'
			];
		} else if ($this->Model->alias === 'ComplianceException') {
			$options['contain'] = [
				'ComplianceManagement',
				'Tag',
				'ComplianceManagement' => [
					'CompliancePackageItem' => [
						'CompliancePackage' => [
							'CompliancePackageRegulator'
						]
					]
				],
				'Requestor',
				'RequestorGroup'
			];
		} elseif ($this->Model->alias === 'Project') {
			$options['contain'] = [
				'ProjectStatus',
				'ProjectExpense',
				'ProjectAchievement',
				'Owner',
				'OwnerGroup',
				'Risk',
				'ThirdPartyRisk',
				'BusinessContinuity',
				'SecurityService',
				'SecurityPolicy' => [
					'SecurityPolicyDocumentType'
				],
				'ComplianceManagement' => [
					'CompliancePackageItem' => [
						'CompliancePackage' => [
							'CompliancePackageRegulator'
						]
					]
				],
				'DataAsset',
				'SecurityServiceAuditImprovement' => [
					'SecurityServiceAudit' => [
						'SecurityService'
					]
				],
				'BusinessContinuityPlanAuditImprovement' => [
					'BusinessContinuityPlanAudit' => [
						'BusinessContinuityPlan'
					]
				],
				'Tag'
			];
		} elseif ($this->Model->alias === 'ProjectAchievement') {
			$options['contain'] = [
				'Project' => [
					'Owner',
					'OwnerGroup',
					'ProjectStatus'
				],
				'TaskOwner',
				'TaskOwnerGroup'
			];
		} elseif ($this->Model->alias === 'ProjectExpense') {
			$options['contain'] = [
				'Project' => [
					'Owner',
					'OwnerGroup',
					'ProjectStatus'
				],
			];
		} else if ($this->Model->alias === 'ProgramIssue') {
			$options['contain'] = [
				'ProgramIssueType'
			];
		} else if ($this->Model->alias === 'Asset') {
			$options['contain'] = [
				'BusinessUnit' => [
					'Legal',
				],
				'AssetMediaType',
				'AssetClassification' => [
					'AssetClassificationType'
				],
				'AssetLabel',
				'Legal',
				'AssetOwner',
				'AssetOwnerGroup',
				'AssetGuardian',
				'AssetGuardianGroup',
				'AssetUser',
				'AssetUserGroup',
				'SecurityIncident' => [
					'SecurityIncidentStatus'
				],
				'Legal',
				'Risk',
				'ThirdPartyRisk',
				'ComplianceManagement' => [
					'CompliancePackageItem' => [
						'CompliancePackage' => [
							'CompliancePackageRegulator'
						]
					]
				],
				'DataAssetInstance' => [
					'DataAssetSetting',
					'DataAsset' => [
						'DataAssetStatus'
					]
				],
				'RelatedAssets',
				'AssetReview'
			];

			if (AppModule::loaded('AccountReviews')) {
				$options['contain'][] = 'AccountReview';
			}
		} else if ($this->Model->alias === 'TeamRole') {
			$options['contain'] = [
				'User'
			];
		} elseif ($this->Model->alias === 'CompliancePackageRegulator') {
			$options['contain'] = [
				'CompliancePackage' => [
					'CompliancePackageItem'
				],
				'Owner',
				'OwnerGroup',
				'Legal'
			];
		} elseif ($this->Model->alias === 'CompliancePackage') {
			$options['contain'] = [
				'CompliancePackageRegulator',
				'CompliancePackageItem',
			];
		} else if ($this->Model->alias === 'Goal') {
			$options['contain'] = [
                'Owner',
                'SecurityService',
                'Risk' => [
                    'Asset'
                ],
                'ThirdPartyRisk' => [
                    'Asset'
                ],
                'BusinessContinuity',
                'Project',
                'SecurityPolicy',
                'ProgramIssue' => [
                    'ProgramIssueType'
                ]
            ];
		} else if ($this->Model->alias === 'CompliancePackageItem') {
			$options['contain'] = [
				'CompliancePackage' => [
					'CompliancePackageRegulator'
				],
			];
		} else if ($this->Model->alias === 'ComplianceAnalysisFinding') {
			$options['contain'] = [
				'ComplianceManagement' => [
					'CompliancePackageItem' => [
						'CompliancePackage' => [
							'CompliancePackageRegulator'
						]
					]
				],
				'Tag',
				'Owner',
				'OwnerGroup',
				'Collaborator',
				'CollaboratorGroup'
			];
		} else if ($this->Model->alias === 'SecurityIncident') {
			$options['contain'] = [
				'SecurityService',
				'ThirdParty' => [
					'Legal'
				],
				'Asset' => [
					'Legal'
				],
				'Owner',
				'OwnerGroup',
				'Classification',
				'SecurityIncidentStatus',
				'SecurityIncidentStage',
				'SecurityIncidentStagesSecurityIncident',
				'AssetRisk' => [
					'SecurityPolicy',
					'SecurityPolicyIncident'
				],
				'ThirdPartyRisk' => [
					'SecurityPolicy',
					'SecurityPolicyIncident'
				],
				'BusinessContinuity' => [
					'SecurityPolicy',
					'SecurityPolicyIncident'
				]
			];
		} else if ($this->Model->alias === 'BusinessContinuityPlan') {
			$options['contain'] = [
				'SecurityServiceType',
				'Owner',
				'OwnerGroup',
				'Sponsor',
				'SponsorGroup',
				'LaunchInitiator',
				'LaunchInitiatorGroup',
				'BusinessContinuityPlanAudit',
				'BusinessContinuityTask'
			];
		} else if ($this->Model->alias === 'Scope') {
			$options['contain'] = [
				'CisoRole',
				'CisoDeputy',
				'BoardRepresentative',
				'BoardRepresentativeDeputy'
			];
		} else if ($this->Model->alias === 'DataAssetInstance') {
			$options['contain'] = [
				'Asset' => [
					'AssetOwner',
					'AssetOwnerGroup',
				],
				'DataAssetSetting' => [
					'Dpo',
			        'Processor',
			        'Controller',
			        'ControllerRepresentative',
			        'SupervisoryAuthority',
				],
				'DataAsset' => [
					'Project' => [
						'ProjectAchievement'
					],
					'SecurityService',
					'SecurityPolicy',
					'Risk',
					'ThirdPartyRisk',
					'BusinessContinuity'
				]
			];
		} else if ($this->Model->alias === 'DataAsset') {
			$options['contain'] = [
				'DataAssetInstance' => [
					'Asset',
				],
				'DataAssetGdpr' => [
					'DataAssetGdprDataType',
					'DataAssetGdprCollectionMethod',
					'DataAssetGdprLawfulBase',
					'DataAssetGdprThirdPartyCountry',
					'DataAssetGdprArchivingDriver',
					'ThirdPartyInvolved',
				],
				'DataAssetStatus',
				'SecurityService',
				'BusinessUnit',
				'ThirdParty',
				'Project' => [
					'ProjectAchievement'
				],
				'SecurityPolicy',
				'Risk',
				'ThirdPartyRisk',
				'BusinessContinuity',
			];
		} elseif ($this->Model->alias === 'AwarenessProgram') {
			$options['contain'] = [
				'AwarenessProgramLdapGroup',
				'AwarenessProgramActiveUser',
				'AwarenessProgramIgnoredUser',
				'AwarenessProgramCompliantUser',
				'AwarenessProgramNotCompliantUser',
				'AwarenessReminder',
				'AwarenessTraining',
				'AwarenessProgramDemo',
				'AwarenessProgramRecurrence',
				'AwarenessProgramMissedRecurrence',
				'LdapConnector',
				'SecurityPolicy',
			];
		} elseif ($this->Model->alias === 'VendorAssessment') {
			$this->Model->bindSystemLog();

			$options['contain'] = [
				'Auditor',
				'AuditorGroup' => [
					'User'
				],
				'Auditee',
				'AuditeeGroup' => [
					'User'
				],
				'ThirdParty',
				'Tag',
				'VendorAssessmentFeedback',
				'VendorAssessmentQuestionnaire' => [
					'VendorAssessmentQuestion' => [
						'VendorAssessmentOption'
					]
				],
				'VendorAssessmentFinding',
				'VendorAssessmentSystemLog' => [
					'fields' => [
						'VendorAssessmentSystemLog.id', 'VendorAssessmentSystemLog.user_id', 'VendorAssessmentSystemLog.action',
						'VendorAssessmentSystemLog.model', 'VendorAssessmentSystemLog.foreign_key', 'VendorAssessmentSystemLog.created',
					],
					'order' => [
						'VendorAssessmentSystemLog.created' => 'ASC'
					]
				]
			];
		} elseif ($this->Model->alias === 'VendorAssessmentSystemLog') {
			$options['contain'] = [
				'User',
				'VendorAssessment',
			];
		} elseif ($this->Model->alias === 'VendorAssessmentFeedback') {
			$options['contain'] = [
				'VendorAssessmentQuestion',
				'VendorAssessment' => [
					'VendorAssessmentFinding' => [
						'VendorAssessmentQuestion'
					]
				],
				'VendorAssessmentOption',
			];
		} elseif ($this->Model->alias === 'VendorAssessmentFinding') {
			$options['contain'] = [
				'Auditor',
				'AuditorGroup',
				'Auditee',
				'AuditeeGroup',
				'VendorAssessment' => [
					'Auditor',
					'AuditorGroup',
					'Auditee',
					'AuditeeGroup',
				],
				'Tag',
				'VendorAssessmentQuestion'
			];
		} elseif ($this->Model->alias === 'AccountReview') {
			$this->Model->bindSystemLog();

			$options['contain'] = [
				'Owner',
				'OwnerGroup',
				'Reviewer',
				'ReviewerGroup',
				'Asset',
				'Tag',
				'AccountReviewFeed',
				'ComparisonAccountReviewFeed',
				'AccountReviewPull'
			];
		} elseif ($this->Model->alias === 'AccountReviewFinding') {
			$options['contain'] = [
				'AccountReviewPull' => [
					'AccountReview'
				],
				'AccountReviewFeedback' => [
					'AccountReviewFeedRow'
				],
				'Owner',
				'OwnerGroup',
				'Reviewer',
				'ReviewerGroup',
				'Tag'
			];
		} elseif ($this->Model->alias === 'AccountReviewPull') {
			$this->Model->bindSystemLog();
			
			$options['contain'] = [
				'AccountReview' => [
					'Asset',
					'AccountReviewFeed',
					'ComparisonAccountReviewFeed'
				],
				'AccountReviewFeedback',
				'AccountReviewFinding'
			];
		} elseif ($this->Model->alias === 'AccountReviewPullSystemLog') {
			$options['contain'] = [
				'User',
				'AccountReview',
				'AccountReviewPull',
			];
		} elseif ($this->Model->alias === 'AccountReviewFeedback') {
			$options['contain'] = [
				'AccountReviewPull' => [
					'AccountReview' => [
						'Asset',
						'AccountReviewFeed',
						'ComparisonAccountReviewFeed'
					]
				],
				'User',
				'AccountReviewFeedbackRole',
				'AccountReviewFeedRow',
				'Tag'
			];
		} elseif ($this->Model->alias === 'Legal') {
			$options['contain'] = [
				'LegalAdvisor',
				'LegalAdvisorGroup'
			];
		} elseif ($this->Model->alias === 'ServiceContract') {
			$options['contain'] = [
				'Owner',
				'OwnerGroup',
				'SecurityService',
				'ThirdParty'
			];
		} elseif ($this->Model->alias === 'GoalAudit') {
			$options['contain'] = [
				'User',
				'Goal',
				'GoalAuditImprovement'
			];
		} elseif ($this->Model->alias === 'BusinessContinuityPlanAudit') {
			$options['contain'] = [
				'User',
				'BusinessContinuityPlan',
				'BusinessContinuityPlanAuditImprovement'
			];
		} elseif ($this->Model->alias === 'BusinessContinuityTask') {
			$options['contain'] = [
				'AwarenessRole',
				'BusinessContinuityPlan',
			];
		} elseif ($this->Model->alias === 'AssetReview') {
			$options['contain'] = [
				'Reviewer',
				'ReviewerGroup',
				'Asset',
			];
		} elseif ($this->Model->alias === 'RiskReview') {
			$options['contain'] = [
				'Reviewer',
				'ReviewerGroup',
				'Risk',
			];
		} elseif ($this->Model->alias === 'ThirdPartyRiskReview') {
			$options['contain'] = [
				'Reviewer',
				'ReviewerGroup',
				'ThirdPartyRisk',
			];
		} elseif ($this->Model->alias === 'BusinessContinuityReview') {
			$options['contain'] = [
				'Reviewer',
				'ReviewerGroup',
				'BusinessContinuity',
			];
		} elseif ($this->Model->alias === 'SecurityPolicyReview') {
			$options['contain'] = [
				'Reviewer',
				'ReviewerGroup',
				'SecurityPolicy' => [
					'SecurityPolicyDocumentType'
				],
				'Attachment' => [
					'User'
				]
			];
		} elseif ($this->Model->alias === 'LdapSynchronizationSystemLog') {
			$options['contain'] = [
				'User',
				'LdapSynchronization',
			];
		} elseif ($this->Model->alias === 'User') {
			$options['contain'] = [
				'Portal',
				'Group',
				'LdapSynchronization' => [
					'LdapGroupConnector',
					'LdapAuthConnector'
				],
			];
		} elseif ($this->Model->alias === 'UserSystemLog') {
			$options['contain'] = [
				'User',
				'Portal'
			];
		} elseif ($this->Model->alias === 'ComplianceManagementMappingRelation') {
			$options['contain'] = [
				'LeftObject' => [
					'CompliancePackageItem' => [
						'CompliancePackage' => [
							'CompliancePackageRegulator'
						]
					]
				],
				'RightObject'=> [
					'CompliancePackageItem' => [
						'CompliancePackage' => [
							'CompliancePackageRegulator'
						]
					]
				]
			];
		}

		// temporary handler for custom field values
		if ($this->Model->getAssociated('CustomFieldValue') !== null) {
			$options['contain'][] = 'CustomFieldValue';
		}

		// comments
		if ($this->Model->Behaviors->enabled('Comments.Comments')) {
			if ($this->FilterFieldSet()->get('comment_message') && $this->FilterFieldSet()->get('comment_message')->getShow()) {
				$options['contain']['Comment'] = [
					'fields' => [
						'Comment.id', 'Comment.message', 'Comment.user_id', 'Comment.model', 'Comment.foreign_key', 'Comment.created'
					],
					'User' => [
						'fields' => [
							'User.id', 'User.full_name'
						],
					]
				];
			}

			if ($this->FilterFieldSet()->get('last_comment') && $this->FilterFieldSet()->get('last_comment')->getShow()) {
				$options['contain']['LastComment'] = [
					'fields' => [
						'LastComment.id', 'LastComment.model', 'LastComment.foreign_key', 'LastComment.created', 'LastComment.last_created'
					],
				];
				ClassRegistry::init('Comments.LastComment')->setVirtualField();
			}
		}

		// attachments
		if ($this->Model->Behaviors->enabled('Attachments.Attachments')) {
			if ($this->FilterFieldSet()->get('attachment_filename') && $this->FilterFieldSet()->get('attachment_filename')->getShow()) {
				$options['contain']['Attachment'] = [
					'fields' => [
						'Attachment.id', 'Attachment.name', 'Attachment.user_id', 'Attachment.model', 'Attachment.foreign_key', 'Attachment.created'
					],
					'User' => [
						'fields' => [
							'User.id', 'User.full_name'
						],
					]
				];
			}

			if ($this->FilterFieldSet()->get('last_attachment') && $this->FilterFieldSet()->get('last_attachment')->getShow()) {
				$options['contain']['LastAttachment'] = [
					'fields' => [
						'LastAttachment.id', 'LastAttachment.model', 'LastAttachment.foreign_key', 'LastAttachment.created', 'LastAttachment.last_created'
					],
				];
				ClassRegistry::init('Attachments.LastAttachment')->setVirtualField();
			}
		}

		// for pdfs, notifications or reports
		if ($customOptions['applyLimit']) {
			// by default unlimited
			$limit = AdvancedFilterValue::LIMIT_UNLIMITED;
			if (isset($this->_filterValues['_limit'])) {
				$limit = $this->_filterValues['_limit'];
			}

			unset($options['page']);

			if ($limit == AdvancedFilterValue::LIMIT_UNLIMITED) {
				unset($options['limit']);
			} else {
				$options['limit'] = $limit;
			}
		} else {
			if (!isset($options['limit'])) {
				$options['limit'] = 10;
			}

			if (!isset($options['page'])) {
				$options['page'] = 1;
				// $query['offset'] = ($query['page'] - 1) * $query['limit'];
			}
		}

		$paginatorOptions = $options;
		// only applicable if limit and page options are set
		// all filters displayed on index,
		// if (isset($paginatorOptions['limit'])) {
			// pagination
			// if (!$results) {
			// 	$count = 0;
			// } else {
				// $paginatorOptions = $options;

				$limit = AdvancedFilterValue::LIMIT_UNLIMITED;
				if (isset($this->_filterValues['_limit'])) {
					$limit = $this->_filterValues['_limit'];
				}

				unset($paginatorOptions['page']);

				if ($limit == AdvancedFilterValue::LIMIT_UNLIMITED) {
					unset($paginatorOptions['limit']);
				} else {
					$paginatorOptions['limit'] = $limit;
				}

				$count = $this->Model->advancedFind('count', $paginatorOptions)->get();
				if (isset($paginatorOptions['limit']) && $paginatorOptions['limit'] != AdvancedFilterValue::LIMIT_UNLIMITED) {
					if ($paginatorOptions['limit'] < $count) {
						$count = $paginatorOptions['limit'];
					}
				}
			// }
			
			if (isset($options['limit'])) {
				$pageCount = (int)ceil($count / $options['limit']);
				$this->_pageCount = $pageCount;
				$this->_count = $count;
				$this->_limit = $options['limit'];
				$this->_currentPage = $options['page'];
			}
		// }
// debug($count);

		if (!$customOptions['applyLimit']) {
		// if (isset($options['page']) && isset($options['limit'])) {

			$options['offset'] = ($options['page'] - 1) * $options['limit'];
			unset($options['page']);
			if ($count < $options['offset'] + $options['limit']) {
				// debug($options);
				$options['limit'] = $count - $options['offset'];// + $options['limit'] - $count;
			}
		}

// debug($options);
		$results = $this->Model->advancedFind($type, $options)->get();

		// unset virtual fields
		ClassRegistry::init('Comments.LastComment')->unsetVirtualField();
		ClassRegistry::init('Attachments.LastAttachment')->unsetVirtualField();

		DebugTimer::start('Event: AdvancedFilter.afterFind.' . $this->getId());
		$results = $this->_filterResults($results);
		DebugTimer::stop('Event: AdvancedFilter.afterFind.' . $this->getId());

		
		$this->_executed = true;
		
		return $this->_data = $results;
		
	}

	/**
	 * Return the current limit.
	 * 
	 * @return int
	 */
	public function getLimit()
	{
		return $this->_limit;
	}

	/**
	 * Return the number of pages for filtered results.
	 * 
	 * @return int
	 */
	public function getPageCount()
	{
		return $this->_pageCount;
	}

	/**
	 * Return the count of total results.
	 * 
	 * @return int
	 */
	public function getCount()
	{
		return $this->_count;
	}

	/**
	 * Return the count of total results.
	 * 
	 * @return int
	 */
	public function getCurrentPage()
	{
		return $this->_currentPage;
	}

	public function isExecuted()
	{
		return $this->_executed;
	}

	public function getShowableFields()
	{
		$showable = [];
		foreach ($this->FilterFieldSet() as $field => $FilterField) {
			if ($FilterField->getShow()) {
				$showable[$field] = $FilterField;
			}
		}

		// uasort($showable, [$this, 'sortShowable']);

		return $showable;
	}

	public function sortShowable($a, $b) {
		if ($a->getOrder() == $b->getOrder()) {
			return 0;
		}
		return ($a->getOrder() < $b->getOrder()) ? -1 : 1;
	}

	/**
	 * Passes query results through afterFind() methods.
	 *
	 * @param array $results Results to filter
	 * @return array Set of filtered results
	 * @triggers AdvancedFilter.afterFind $this, array($results)
	 */
	protected function _filterResults($results) {
		$event = new CakeEvent('AdvancedFilter.afterFind', $this, array($results));
		$event->modParams = 0;
		$this->getEventManager()->dispatch($event);

		return $event->result !== null ? $event->result : $event->data[0];
	}

	public function buildParams()
	{
		$params = [];
		foreach ($this->_FilterFieldSet->get() as $name => $FilterField) {
			$params[$name] = $FilterField->getValue();
		}

		return $params;
	}

	public function buildQueryParams()
	{
		$query = [];
		foreach ($this->_FilterFieldSet->get() as $name => $FilterField) {
			$query = array_merge($query, $FilterField->buildQueryParams());
		}

		return $query;
	}

	protected function _buildConfig($params) {
		unset($params['_limit']);
		foreach ($params as $k => $v) {
			if (!isset($v['value'])) {
				$params[$k]['value'] = null;
			}
		}

		// $config = array_combine(array_keys($params), Hash::extract($params, '{s}.value'));
		// $fieldConfig = Hash::remove($params, '{s}.value');

		$this->_setFieldConfig();

		// initialize fields configuration just before executing a filter
		$this->_prepareFields();

		// foreach ($config as $key => &$value) {
		// 	if (is_bool($value)) {
		// 		$value = (int) $value;
		// 		$value = (string) $value;
		// 	}
		// }

		// return $config;
	}

	protected function _setFieldConfig() {
		foreach ($this->_convertedValues as $field => $fieldOptions) {
			$this->_FilterFieldSet->add($field, $fieldOptions);
		}
	}

	/**
	 * Before filter process, this should be run for each field.
	 */
	protected function _prepareFields() {
		foreach ($this->_FilterFieldSet->get() as $name => $FilterField) {
			$FilterField->setPreferences();
		}
	}

	// reset $filterArgs to the default state ready for next find operation
	// removes comp_type from $filterArgs,... etc
	public function resetFilterArgs() {
		foreach ($this->_FilterFieldSet->get() as $name => $FilterField) {
			$FilterField->resetPreferences();
		}
	}

	public function field($field)
	{
		return $this->Model->Behaviors->AdvancedFilters->filterField($this->Model, $field);
	}

	public function getId()
	{
		return $this->id;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function setName($value)
	{
		$this->_name = $value;
	}

	public function setDescription($value)
	{
		$this->_description = $value;
	}

	public function getDescription()
	{
		return $this->_description;
	}

	public function setFilterValues($value)
	{
		$Model = $this->getModel();
		foreach ($Model->advancedFilter as $fieldSet) {
            foreach ($fieldSet as $field => $fieldData) {
            	$showField = $field . '__show';
            	if (empty($fieldData['show_default'])) {
            		continue;
            	}

            	if (isset($this->_filterValues[$showField])) {
            		continue;
            	}

            	if (!isset($value[$showField])) {
            		$value[$showField] = 1;
            	}
            }
        }

		$this->_filterValues = array_merge($this->_filterValues, $value);
	}

	public function getFilterValues($key = null)
	{
		if ($key !== null) {
			if (isset($this->_filterValues[$key])) {
				return $this->_filterValues[$key];
			}

			return null;
		}
		
		return $this->_filterValues;
	}

	public function getData()
	{
		return $this->_data;
	}

	public function parseFindOptions()
	{
		return $this->_parseFindOptions();
	}

	protected function _parseFindOptions()
	{
		$parseParams = [
			'limit',
			'order_column',
			'order_direction',
			'page',
			'pageLimit'
		];

		$parsed = [];
		foreach ($parseParams as $param) {
			$alias = '_' . $param;

			if (isset($this->_filterValues[$alias])) {
				$parsed[$param] = $this->_filterValues[$alias];
			}
			else {
				$parsed[$param] = null;
			}
		}

		extract($parsed);
		$options = [];
		if ($pageLimit !== null) {
			$options['limit'] = $pageLimit;
		}

		if ($page !== null) {
			$options['page'] = $page;
		}

		// if ordering values are set and the column actually exists on the model
		if ($order_column && $order_direction && $this->getModel()->schema($order_column) !== null) {
			$options['order'] = [$this->Model->alias . '.' . $order_column => $order_direction];
		}

		return $options;
	}

	/**
	 * Convert parameters accepted by `AdvancedFiltersBehavior::filter()` method from database filter data.
	 * 
	 * @param  array $data Data pulled from database
	 * @return array       Parameters accepted by default filter() method
	 */
	protected function _convertFilterValues()
	{
		$filterArgs = array_keys($this->Model->filterArgs);

		$args = [];
		foreach ($this->Model->advancedFilter as $k => $v) {
			$args = array_merge($args, array_keys($v));
		}

		if ($this->Model->schema('deleted') !== null) {
			$args[] = 'deleted';
		}

		$converted = [];
		foreach ($args as $arg) {
			if (isset($this->_filterValues[$arg])) {
				$converted[$arg]['value'] = $this->_filterValues[$arg];
			}

			if (isset($this->_filterValues[$arg . '__comp_type'])) {
				$converted[$arg]['comparisonType'] = (int) $this->_filterValues[$arg . '__comp_type'];
			}
			else {
				$fieldType = $this->Model->filterArgs[$arg]['_config']['type'];
				$className = AdvancedFiltersQuery::getTypeClass($fieldType);
				$converted[$arg]['comparisonType'] = (int) $className::$defaultComparison;
			}

			if (isset($this->_filterValues[$arg . '__show'])) {
				$converted[$arg]['show'] = (int) $this->_filterValues[$arg . '__show'];
			}
		}

		return $converted;
	}

	public function render($options = [])
	{
		$options = array_merge([
			'view' => 'AdvancedFilters./Elements/filter_object_light',
			'layout' => 'default',
			// 'helpers' => [
			// 	'ObjectRenderer.ObjectRenderer',
			// 	'ObjectStatus.ObjectStatus',
			// 	'LimitlessTheme.Icons',
			// 	'LimitlessTheme.Popovers',
			// 	'LimitlessTheme.Labels',
			// 	'AdvancedFilters.AdvancedFilters'
			// ]
		], $options);

		$View = new View();

		// set data
		// $this->filter();
		$View->set('AdvancedFiltersObject', $this);

		// load helapers
		// foreach ($options['helpers'] as $helper) {
		// 	$View->loadHelper($helper);
		// }

		return $View->render($options['view'], $options['layout']);
	}

	public function pdf()
	{
		$Pdf = new \Knp\Snappy\Pdf(Configure::read('Eramba.Settings.PDF_PATH_TO_BIN'));

		$Pdf->setOptions([
			'orientation' => 'Landscape',
			'margin-left' => 0,
			'margin-right' => 0,
			'margin-top' => 0,
			'margin-bottom' => 0,
			'javascript-delay' => 1000,
			'lowquality' => true,
			'dpi' => 90
		]);

		// $this->filter();
		$render = $this->render([
			'layout' => 'rich_pdf'
		]);

		return $Pdf->getOutputFromHtml($render);
	}

	public function csv()
	{
		$_filter = $this;
		// $_filter = $this->filter();
        // $_filter->filter();
        $showableFields = $_filter->getShowableFields();

        
        $CollectionsArr = [];
        // foreach ($filtersData as $item) {
        //     if (empty($item['data'])) {
        //         continue;
        //     }

            $Collection = $this->Model->getItemDataCollection();
            foreach ($this->_data as $dataItem) {
                $Collection->add($dataItem);
            }

            $CollectionsArr[] = $Collection;
        // }

        $_header = [];
        $_extract = [];
        foreach ($showableFields as $FilterField) {
            $_header[] = $FilterField->getLabel();
            $_extract[] = $FilterField->getFieldName();
        }

        App::uses('CsvView', 'CsvView.View');
        $View = new CsvView();
        $View->loadHelper('ObjectRenderer.ObjectRenderer');
        
        $data = [];
        $key = 0;
        foreach ($CollectionsArr as $Collection) {
            foreach ($Collection as $Item) {
                // $data[$key]['__cron_date'] = $Item->__cron_date;
                foreach ($showableFields as $field => $FilterField) {
                    $TraverserData = traverser($Item, $FilterField);

                    $content = '';
                    $searchContent = '';

                    if (!empty($TraverserData['ItemDataEntity'])) {
                        $processors = [
                            'Text',
                            'CustomFields.CustomFields'
                        ];

                        $params = [
                            'item' => $TraverserData['ItemDataEntity'],
                            'field' => $TraverserData['FieldDataEntity']
                        ];

                        $data[$key][$FilterField->getFieldName()] = $View->ObjectRenderer->render('AdvancedFilters.Cell', $params, $processors);
                    }
                }

                $key++;
            }
        }
        
        $_serialize = 'data';
        $_delimiter = Configure::read('Eramba.Settings.CSV_DELIMITER');
        $View->viewVars = array_merge($View->viewVars, compact('_header', '_extract', 'data', '_serialize', '_delimiter'));

        return $View->render();
	}

	public static function isJustSaved($id, $query)
	{
		$queryHash = self::generateQueryHash($query);
		if (($justSavedFilters = CakeSession::read(self::JUST_SAVED_SESSION_KEY)) !== null &&
			array_key_exists($id, $justSavedFilters) &&
			$justSavedFilters[$id] === $queryHash) {
				return true;
		}

		return false;
	}

	public static function setJustSavedFilter($id, $query)
	{
		$queryHash = self::generateQueryHash($query);
		$justSavedFilters = CakeSession::read(self::JUST_SAVED_SESSION_KEY);
		if ($justSavedFilters == null) {
			$justSavedFilters = [];
		}

		$justSavedFilters[$id] = $queryHash;
		CakeSession::write(self::JUST_SAVED_SESSION_KEY, $justSavedFilters);
	}

	public static function generateQueryHash($query)
	{
		$query = self::trimFilterQuery($query);

		return md5(json_encode($query));
	}

	public static function trimFilterQuery($query)
	{
		if (!is_array($query)) {
			return $query;
		}

		//
		// Select only keys between start and end keys (temporary disabled because start and end keys are not guaranteed)
		// $startKey = 'id_show';
		// $endKey = '_order_direction';
		// $between = array_key_exists($startKey, $query) && array_key_exists($endKey, $query) ? false : true;
		// foreach ($query as $key => $val) {
		// 	if ($key === $startKey) {
		// 		$between = true;
		// 	}

		// 	if (!$between) {
		// 		unset($query[$key]);
		// 	}

		// 	if ($key === $endKey) {
		// 		$between = false;
		// 	}
		// }
		//

		//
		// Remove these specific keys (in case if start or end keys are not presented in query)
		$trimKeys = [
			'_', 'advanced_filter', 'advanced_filter_id', 'reload_advanced_filter_only', 'modalId'
		];

		foreach ($trimKeys as $tk) {
			if (array_key_exists($tk, $query)) {
				unset($query[$tk]);
			}
		}
		//

		return $query;
	}
}
