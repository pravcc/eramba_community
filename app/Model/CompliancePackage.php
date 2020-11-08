<?php
App::uses('AppModel', 'Model');
App::uses('ThirdParty', 'Model');

class CompliancePackage extends AppModel
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
		'AuditLog.Auditable',
		'Utils.SoftDelete',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'package_id', 'name', 'description', 'third_party_id'
			)
		),
		'ModuleDispatcher' => [
			'behaviors' => [
				'Reports.Report',
			]
		],
        'Visualisation.Visualisation',
        'AssociativeDelete.AssociativeDelete' => [
			'associations' => ['CompliancePackageItem']
		],
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'Macros.Macro',
		'SubSection' => [
			'parentField' => 'compliance_package_regulator_id'
		],
	);

	public $mapping = array(
		'titleColumn' => 'name',
		'logRecords' => true,
		'notificationSystem' => array('index'),
		'workflow' => false
	);

	public $workflow = array(
		// 'pullWorkflowData' => array('CompliancePackageItem')
	);

	public $validate = array(
		'compliance_package_regulator_id' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false,
			'on' => 'create'
		),
		'name' => array(
			'rule' => 'notBlank',
			'required' => false,
			'allowEmpty' => false
		),
		'package_id' => array(
			'rule' => 'notBlank',
			'required' => false,
			'allowEmpty' => false
		)
	);

	public $belongsTo = array(
		'CompliancePackageRegulator'
	);

	public $hasMany = array(
		'CompliancePackageItem',
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Compliance Packages');
		$this->_group = parent::SECTION_GROUP_COMPLIANCE_MGT;

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General')
			),
		);

		$this->fieldData = [
			'compliance_package_regulator_id' => array(
				'label' => __('Compliance Package'),
				'editable' => false,
				'inlineEdit' => true,
				'macro' => [
					'name' => 'third_party'
				]
			),
			'package_id' => array(
				'label' => __('Chapter ID'),
				'editable' => false,
				'inlineEdit' => true
			),
			'name' => array(
				'label' => __('Chapter Name'),
				'editable' => true,
				'inlineEdit' => true
			),
			'description' => array(
				'label' => __('Chapter Description'),
				'editable' => true,
				'inlineEdit' => true
			),
			'CompliancePackageItem' => array(
				'label' => __('Compliance Package Item'),
				'editable' => false,
				'usable' => false,
				// 'hidden' => true,
			)
		];

		$this->filterArgs = array(
			'search' => array(
				'type' => 'like',
				'field' => array('CompliancePackage.name'),
				'_name' => __('Search')
			)
		);

		$this->advancedFilter = array(
			__('General') => array(
				'id' => array(
					'type' => 'text',
					'name' => __('ID'),
					'filter' => false
				),
				'third_party_id' => array(
					'type' => 'multiple_select',
					'name' => __('Third Party'),
					'show_default' => false,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'CompliancePackage.third_party_id',
						'field' => 'CompliancePackage.id',
					),
					'data' => array(
						'method' => 'getThirdParties',
					),
					'fieldData' => 'ThirdParty.name'
				),
				'package_id' => array(
					'type' => 'text',
					'name' => __('Chapter ID'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'CompliancePackage.package_id',
						'field' => 'CompliancePackage.id',
					),
				),
				'name' => array(
					'type' => 'text',
					'name' => __('Chapter Name'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'CompliancePackage.name',
						'field' => 'CompliancePackage.id',
					),
				),
				'description' => array(
					'type' => 'text',
					'name' => __('Chapter Description'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'CompliancePackage.description',
						'field' => 'CompliancePackage.id',
					),
				),
				'item_id' => array(
					'type' => 'text',
					'name' => __('Item ID'),
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'CompliancePackageItem.item_id',
						'field' => 'CompliancePackage.id',
					),
					'many' => true,
					'fieldData' => 'CompliancePackageItem.item_id'
				),
				'item_name' => array(
					'type' => 'text',
					'name' => __('Item Name'),
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'CompliancePackageItem.name',
						'field' => 'CompliancePackage.id',
					),
					'many' => true,
					'fieldData' => 'CompliancePackageItem.name'
				),
				'item_description' => array(
					'type' => 'text',
					'name' => __('Item Description'),
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'CompliancePackageItem.description',
						'field' => 'CompliancePackage.id',
					),
					'many' => true,
					'fieldData' => 'CompliancePackageItem.description'
				),
				'item_audit_questionaire' => array(
					'type' => 'text',
					'name' => __('Item Audit Questionaire'),
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'CompliancePackageItem.audit_questionaire',
						'field' => 'CompliancePackage.id',
					),
					'many' => true,
					'fieldData' => 'CompliancePackageItem.audit_questionaire'
				),
			),
		);

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Compliance Packages'),
			'pdf_file_name' => __('compliance_packages'),
			'csv_file_name' => __('compliance_packages'),
			'actions' => true,
			// 'trash' => true,
			'history' => true,
			'bulk_actions' => true,
			'use_new_filters' => true,
			'add' => true,
		);

		parent::__construct($id, $table, $ds);
	}

	public function getReportsConfig()
	{
		return [
			'table' => [
				'model' => [
					'CompliancePackageRegulator', 'CompliancePackageItem',
				]
			],
		];
	}

	public function getMacrosConfig()
	{
		return [
			'assoc' => [
				'CompliancePackageRegulator', 'CompliancePackageItem',
			],
		];
	}

	/**
	 * Get the default condition rules for a relation with other model that applies to the current section.
	 * 
	 * @return array Default conditions.
	 */
	public static function thirdPartyListingConditions() {
		return [
			'ThirdParty.third_party_type_id' => ThirdParty::TYPE_REGULATORS
		];
	}

	public function getThirdParties() {
		$data = $this->ThirdParty->find('list', [
			'conditions' => [
				'ThirdParty.third_party_type_id' => ThirdParty::TYPE_REGULATORS
			],
			'order' => ['ThirdParty.name']
		]);

		return $data;
	}

	public function importSave($data, $options = [])
	{
		$success = false;

		$this->validator()->add('CsvFile', 'extension', array(
			'required' => true,
			'rule' => ['extension', ['csv']],
			'message' => 'The file you uploaded is not a valid CSV file'
		));

		$this->set($data);
		$valid = $this->validates();

		$this->validator()->remove('CsvFile');

		if ($valid) {
			$compliance_package_regulator_id = $data['CompliancePackage']['compliance_package_regulator_id'];
			$tmp_name = $data['CompliancePackage']['CsvFile']['tmp_name'];
			if (($handle = fopen($tmp_name, 'r')) !== FALSE) {
				$last_cp = false;
				$cp_tmp = $cpi_tmp = array();
				$has_error = false;

				$dataSource = $this->getDataSource();
				$dataSource->begin();

				$ret = true;
				while (($data = fgetcsv($handle, 0, ',')) !== false) {
					if (count($data) != 7) {
						$has_error = true;
						continue;
					}

					if (!$last_cp || $last_cp != $data[1]) {
						$last_cp = $data[1];

						$ret &= $this->_importSave($cp_tmp, $cpi_tmp);

						$cp_tmp = $cpi_tmp = array();
						$cp_tmp = array(
							'package_id' => $data[0],
							'name' => $data[1],
							'description' => $data[2],
							'compliance_package_regulator_id' => $compliance_package_regulator_id
						);
					}

					$cpi_tmp[] = array(
						'item_id' => $data[3],
						'name' => $data[4],
						'description' => $data[5],
						'audit_questionaire' => $data[6],
					);
				}

				$ret &= $this->_importSave($cp_tmp, $cpi_tmp);
				fclose($handle);

				$Compliance = ClassRegistry::init('ComplianceManagement');
				$ret &= $Compliance->syncObjects();

				if ($ret) {
					$dataSource->commit();
					$success = true;
				}
				else {
					$dataSource->rollback();
				}
			}
		}

		return $success;
	}

	/**
	 * Save imported values for compliance package and compliance package items.
	 */
	protected function _importSave($cp_arr = [], $cpi_arr = []) {
		if (empty($cp_arr)) {
			return true;
		}

		$this->create();
		$ret = $this->save($cp_arr);
		if (empty($ret) || empty($this->id)) {
			return false;
		}

		$cp_id = $this->id;

		foreach ($cpi_arr as $key => $cpi) {
			$cpi_arr[ $key ]['compliance_package_id'] = $cp_id;
		}

		$ret &= $this->CompliancePackageItem->saveAll($cpi_arr, array('atomic' => false));

		return $ret;
	}

	public function afterSave($created, $options = array()) {
		parent::afterSave($created, $options);

		$this->syncPackageItems($this->id);
	}

	/**
	 * Delete all items of package if package is soft deleted.
	 * If $id is null we find all soft deleted packages and delete all theirs items.
	 */
	public function syncPackageItems($id = null) {
		if ($id !== null) {
			if ($this->exists($id)) {
				return true;
			}
		}
		else {
			$id = $this->find('list', [
				'conditions' => ['CompliancePackage.deleted' => true],
				'fields' => ['CompliancePackage.id']
			]);
		}

		return $this->deletePackageItems($id);
	}

	/**
	 * Delete all items of package.
	 * 
	 * @param  mixed $packageId Id or array of Ids.
	 * @return boolean Success.
	 */
	public function deletePackageItems($packageId) {
		if (empty($packageId)) {
			return true;
		}

		$packageId = (array) $packageId;

		$itemIds = $this->CompliancePackageItem->find('list', [
			'conditions' => ['CompliancePackageItem.compliance_package_id' => $packageId],
			'fields' => ['CompliancePackageItem.id']
		]);

		$ret = true; 

		if (!empty($itemIds)) {
			foreach ($itemIds as $id) {
				$ret &= $this->CompliancePackageItem->delete($id);
			}
		}

		return $ret;
	}

	public function hasSectionIndex()
	{
		return true;
	}
}
