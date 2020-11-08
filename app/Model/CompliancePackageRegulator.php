<?php
App::uses('AppModel', 'Model');
App::uses('ImportToolModule', 'ImportTool.Lib');
App::uses('UserFields', 'UserFields.Lib');
App::uses('L10n', 'I18n');
App::uses('Hash', 'Utility');

class CompliancePackageRegulator extends AppModel
{
	public $displayField = 'name';

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'description'
			)
		),
		'ModuleDispatcher' => [
			'behaviors' => [
				'CustomFields.CustomFields',
				'Reports.Report',
			]
		],
		'AuditLog.Auditable',
		// 'Utils.SoftDelete',
		'Visualisation.Visualisation',
		'UserFields.UserFields' => [
			'fields' => ['Owner']
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
		'SubSection' => [
			'childModels' => true
		],
		'AdvancedFilters.AdvancedFilters',
		'CustomLabels.CustomLabels'
	);

	public $validate = [
		'name' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		],
		'url' => [
			'url' => [
				'rule' => ['url', true],
				'allowEmpty' => true,
				'message' => 'Please enter a valid URL'
			]
		],
		'publisher_name' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		],
		'version' => [
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		],
	];

	public $hasMany = [
		'CompliancePackage'
	];

	public $hasAndBelongsToMany = array(
		'Legal'
	);

	public function __construct($id = false, $table = null, $ds = null)
	{
		//
		// Init helper Lib for UserFields Module
		$UserFields = new UserFields();
		//
		
		$this->label = __('Compliance Packages');
		$this->_group = parent::SECTION_GROUP_COMPLIANCE_MGT;

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
			'package-details' => [
				'label' => __('Package Details')
			]
		];

		$L10n = new L10n();
		$defaultLanguage = $L10n->get();

		$this->fieldData = [
			'name' => [
				'label' => __('Name'),
				'editable' => true,
				'description' => __('Provide a name for this compliance package, for example: ISO 27001 - UK, PCI-DSS 3.2.1, Etc'),
				'inlineEdit' => true,
			],
			'description' => [
				'label' => __('Description'),
				'editable' => true,
				'description' => __('Describe the Compliance Package'),
				'inlineEdit' => true,
			],
			'Owner' => $UserFields->getFieldDataEntityData($this, 'Owner', [
				'label' => __('Owner'),
				'description' => __('Select one or more users or groups that will manage this compliance package. This owners will be propagated to Compliance Management / Compliance Analysis.'),
				'quickAdd' => true,
				'inlineEdit' => true
			]),
			'Legal' => [
				'label' => __('Liabilities'),
				'editable' => true,
				'description' => __('OPTIONAL: Select on or more Liabilities defined at Organisation / Liabilities.'),
				'inlineEdit' => true,
				'quickAdd' => true
			],
			'publisher_name' => [
				'label' => __('Publisher Name'),
				'editable' => true,
				'description' => __('The name of the publiher, copyright owner for this compliance package.'),
				'inlineEdit' => true,
				'group' => 'package-details'
			],
			'version' => [
				'label' => __('Version'),
				'editable' => true,
				'description' => __('The version of this compliance package.'),
				'inlineEdit' => true,
				'group' => 'package-details'
			],
			'language' => [
				'label' => __('Language'),
				'editable' => true,
				'description' => __('OPTIONAL: Select the language used for this compliance package.'),
				'inlineEdit' => true,
				'options' => [$this, 'languages'],
				'group' => 'package-details',
				'default' => $defaultLanguage
			],
			'url' => [
				'label' => __('URL'),
				'editable' => true,
				'description' => __('OPTIONAL: Provide a URL for this Compliance Package'),
				'inlineEdit' => true,
				'group' => 'package-details'
			],
			'restriction' => [
				'label' => __('Restriction'),
				'editable' => true,
				'description' => __('OPTIONAL: Describe if this compliance package is public or paid.'),
				'inlineEdit' => true,
				'options' => [$this, 'restrictions'],
				'empty' => __('Choose one...'),
				'group' => 'package-details'
			],
		];

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Compliance Package Regulators'),
			'pdf_file_name' => __('compliance_package_regulators'),
			'csv_file_name' => __('compliance_package_regulators'),
			// 'additional_actions' => array(
			// 	'Process' => __('Processes')
			// ),
			'bulk_actions' => true,
			'history' => true,
            'trash' => true,
            'use_new_filters' => true,
			'add' => true,
		);

		parent::__construct($id, $table, $ds);
	}

	public function getAdvancedFilterConfig()
	{
		$advancedFilterConfig = $this->createAdvancedFilterConfig()
			->group('general', [
				'name' => __('General')
			])
				->nonFilterableField('id')
				->textField('name', [
					'showDefault' => true
				])
				->textField('description', [
					'showDefault' => true
				])
				->userField('Owner', 'Owner', [
					'showDefault' => true
				])
				->multipleSelectField('Legal', [ClassRegistry::init('Legal'), 'getList'], [
					'showDefault' => true
				])
			->group('package-details', [
				'name' => __('Package Details')
			])
				->textField('publisher_name', [
					'showDefault' => true
				])
				->textField('version', [
					'showDefault' => true
				])
				->multipleSelectField('language', [$this, 'languages'])
				->textField('url')
				->multipleSelectField('restriction', [$this, 'restrictions']);

		if (AppModule::loaded('CustomFields')) {
			$this->customFieldsFilters($advancedFilterConfig);
		}

		$this->otherFilters($advancedFilterConfig);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getMacrosConfig()
	{
		return [
			'assoc' => [],
			'seed' => [],
		];
	}

	public function getSectionInfoConfig()
	{
		return [
			'map' => [
				'CompliancePackageInstance',
			]
		];
	}

	public static function restrictions($value = null) {
		$options = array(
			self::RESTRICTION_PAID => __('Paid'),
			self::RESTRICTION_FREE => __('Free')
		);
		return parent::enum($value, $options);
	}
	const RESTRICTION_PAID = 0;
	const RESTRICTION_FREE = 1;

	/**
	 * List of all languages.
	 */
	public static function languages($value = null) {
		$L10n = new L10n();
		$catalog = $L10n->catalog();
		$options = array_combine(array_keys($catalog), Hash::extract($catalog, '{s}.language'));

		return parent::enum($value, $options);
	}

	public function deleteComplianceIndex($id)
	{
		$ret = ClassRegistry::init('AdvancedFilters.AdvancedFilter')->deleteAll([
			'OR' => [
				[
					'AdvancedFilter.model' => 'ComplianceManagement',
					'AdvancedFilter.slug' => 'third-party-' . $id,
				],
				[
					'AdvancedFilter.model' => 'CompliancePackage',
					'AdvancedFilter.slug' => 'third-party-' . $id,
				],
				[
					'AdvancedFilter.model' => 'CompliancePackageItem',
					'AdvancedFilter.slug' => 'third-party-' . $id,
				]
			]
		]);

		return $ret;
	}

	/**
	 * Method creates a system filters for compliance section related to specific Third Party.
	 * Usually this should execute always when new Third Party is created.
	 * 
	 * @param  integer $id        Third Party ID.
	 * @param  array   $listUsers Array of users or NULL value to sync for all users.
	 * @return boolean            True on success, False otherwise.
	 */
	public function syncComplianceIndex($id, $listUsers = null)
	{
		$ret = true;

		$ret &= $this->syncComplianceManagementIndex($id, $listUsers);
		$ret &= $this->syncCompliancePackagesIndex($id, $listUsers);

		return $ret;
	}

	public function syncComplianceManagementIndex($id, $listUsers = null)
	{
		$AdvancedFilter = ClassRegistry::init('AdvancedFilters.AdvancedFilter');
		if ($listUsers === null) {
			$listUsers = $AdvancedFilter->getUsersToSync();
		}

		$data = $this->find('first', [
			'conditions' => [
				'CompliancePackageRegulator.id' => $id
			],
			'fields' => [
				'CompliancePackageRegulator.id',
				'CompliancePackageRegulator.name'
			],
			'recursive' => -1
		]);

		$AdvancedFilterValue = $AdvancedFilter->buildShowDefaultFields('ComplianceManagement');
		$AdvancedFilterValue[] = [
			'field' => 'CompliancePackage-compliance_package_regulator_id',
			'value' => $id,
			'many' => 1
		];

		$ret = true;
		foreach ($listUsers as $userId) {
			if ($AdvancedFilter->filterExists('ComplianceManagement', 'third-party-' . $data['CompliancePackageRegulator']['id'], $userId)) {
				continue;
			}

			$AdvancedFilter->create();
			$ret &= $AdvancedFilter->saveAssociated([
				'AdvancedFilter' => [
					'user_id' => $userId,
					'name' => $data['CompliancePackageRegulator']['name'],
					'slug' => 'third-party-' . $data['CompliancePackageRegulator']['id'],
					'description' => __('Filter shows specific Compliance Package'),
					'model' => 'ComplianceManagement',
					'private' => 1,
					'log_result_data' => 0,
					'log_result_count' => 0,
					'system_filter' => 1
				],
				'AdvancedFilterUserSetting' => [
					'model' => 'ComplianceManagement',
					'default_index' => '1',
					'user_id' => $userId
				],
				'AdvancedFilterValue' => $AdvancedFilterValue
			]);
		}

		return $ret;
	}

	public function syncCompliancePackagesIndex($id, $listUsers = null)
	{
		$ret = true;

		$ret &= $this->_syncCompliancePackagesIndex($id, $listUsers, 'CompliancePackage');
		$ret &= $this->_syncCompliancePackagesIndex($id, $listUsers, 'CompliancePackageItem');

		return $ret;
	}

	public function _syncCompliancePackagesIndex($id, $listUsers = null, $model)
	{
		$AdvancedFilter = ClassRegistry::init('AdvancedFilters.AdvancedFilter');
		if ($listUsers === null) {
			$listUsers = $AdvancedFilter->getUsersToSync();
		}

		$data = $this->find('first', [
			'conditions' => [
				'CompliancePackageRegulator.id' => $id
			],
			'fields' => [
				'CompliancePackageRegulator.id',
				'CompliancePackageRegulator.name'
			],
			'recursive' => -1
		]);

		$AdvancedFilterValue = $AdvancedFilter->buildShowDefaultFields($model);
		$AdvancedFilterValue[] = [
			'field' => 'CompliancePackage-compliance_package_regulator_id',
			'value' => $id,
			'many' => 1
		];

		$ret = true;
		foreach ($listUsers as $userId) {
			if ($AdvancedFilter->filterExists($model, 'third-party-' . $data['CompliancePackageRegulator']['id'], $userId)) {
				continue;
			}

			$AdvancedFilter->create();
			$ret &= $AdvancedFilter->saveAssociated([
				'AdvancedFilter' => [
					'user_id' => $userId,
					'name' => $data['CompliancePackageRegulator']['name'],
					'slug' => 'third-party-' . $data['CompliancePackageRegulator']['id'],
					'description' => __('Filter shows specific Third Party'),
					'model' => $model,
					'private' => 1,
					'log_result_data' => 0,
					'log_result_count' => 0,
					'system_filter' => 1
				],
				'AdvancedFilterUserSetting' => [
					'model' => $model,
					'default_index' => '1',
					'user_id' => $userId
				],
				'AdvancedFilterValue' => $AdvancedFilterValue
			]);
		}

		return $ret;
	}

	public function hasSectionIndex()
	{
		return true;
	}

	/**
	 * Get the list of Compliance Package Regulators that does not have ANY sub-items (are empty).
	 * 
	 * @return array List of items.
	 */
	public function getEmptyRegulators()
	{
		$usedPackageIds = $this->CompliancePackage->find('list', [
			'fields' => [
				'CompliancePackage.compliance_package_regulator_id'
			],
			'group' => [
				'CompliancePackage.compliance_package_regulator_id'
			],
			'recursive' => -1
		]);

		// get the list of un-used regulators
		$data = $this->find('list', [
			'conditions' => [
				'CompliancePackageRegulator.id !=' => $usedPackageIds
			],
			'fields' => [
				'id', 'name'
			],
			'recursive' => -1
		]);

		return $data;
	}

	/**
	 * Get the list of Compliance Package Regulators that does have one or more sub-items.
	 * 
	 * @return array List of items.
	 */
	public function getNotEmptyRegulators()
	{
		$usedPackageIds = $this->CompliancePackage->find('list', [
			'fields' => [
				'CompliancePackage.compliance_package_regulator_id'
			],
			'group' => [
				'CompliancePackage.compliance_package_regulator_id'
			],
			'recursive' => -1
		]);

		// get the list of un-used regulators
		$data = $this->find('list', [
			'conditions' => [
				'CompliancePackageRegulator.id' => $usedPackageIds
			],
			'fields' => [
				'id', 'name'
			],
			'recursive' => -1
		]);

		return $data;
	}

	/**
	 * Sync compliance filters names.
	 */
	public function syncFiltersName($regulatorId)
	{
		$regulator = $this->find('first', [
			'conditions' => [
				'CompliancePackageRegulator.id' => $regulatorId,
			],
			'fields' => [
				'CompliancePackageRegulator.id', 'CompliancePackageRegulator.name'
			],
			'recursive' => -1
		]);

		if (empty($regulator)) {
			return false;
		}

		return ClassRegistry::init('AdvancedFilters.AdvancedFilter')->renameFilter(
			['CompliancePackage', 'CompliancePackageItem', 'ComplianceManagement'],
			'third-party-' . $regulator['CompliancePackageRegulator']['id'],
			$regulator['CompliancePackageRegulator']['name']
		);
	}

}
