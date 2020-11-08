<?php
/**
 * @package       AppPreview.Model
 */
App::uses('AppModel', 'Model');
App::uses('UserFields', 'UserFields.Lib');

class SectionItem extends AppModel {
	public $useDbConfig = 'preview';

	public $displayField = 'varchar';

	// public $workflow = array(
	// 	'additionalField' => array(
	// 		'owner' => array(
	// 			'type' => 'multiple',
	// 			'model' => 'ComplianceAnalysisFindingsOwner',
	// 			'column' => 'owner_id',
	// 			'foreign_key' => 'compliance_analysis_finding_id',
	// 			'label' => 'Owner',
	// 		),
	// 		'collaborator' => array(
	// 			'type' => 'multiple',
	// 			'model' => 'SectionItemsCollaborator',
	// 			'column' => 'collaborator_id',
	// 			'foreign_key' => 'section_item_id',
	// 			'label' => 'Collaborator',
	// 		),
	// 	)
	// );

	public $actsAs = array(
		'FieldData.FieldData',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'varchar', 'text'
			)
		),
		// 'Search.Searchable',
		'AuditLog.Auditable',
		'Utils.SoftDelete',
		'CustomFields.CustomFields',
		'Taggable',
		'Workflows.Triggerable' => [
			'fieldList' => ['varchar', 'text', 'date']
		],
		'Visualisation.Visualisation',
		'UserFields.UserFields' => [
			'fields' => ['UserField']
		],
		'ImportTool.ImportTool'
	);

	public $validate = array(
		'varchar' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'Custom validation message'
			)
		),
		'text' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'Custom validation message'
			)
		),
		'date' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'Custom validation message'
			),
			'date' => array(
				'rule' => 'date',
				'required' => true,
				'message' => 'This date has incorrect format'
			)
		),
		'user_id' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'Custom validation message'
			)
		),
		'HasAndBelongsToMany' => array(
			'multiple' => array(
				'rule' => array('multiple', array('min' => 1)),
				'required' => true,
				'message' => 'Custom validation message'
			)
		),
		'Tag' => array(
			'multiple' => array(
				'rule' => array('multiple', array('min' => 1)),
				'required' => true,
				'message' => 'Custom validation message'
			)
		),
		'tinyint_status' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'Custom validation message'
			)
		),
		'toggle_status' => [
			'notBlank' => [
				'rule'     => ['comparison', '!=', 0],
				'required' => true,
				'message' => 'You have to accept our toggle'
			]
		]
	);

	public $belongsTo = array(
		'BelongsTo' => [
			'className' => 'User',
			'foreignKey' => 'user_id'
		]
	);

	public $hasMany = array(
		'Comment' => array(
			'className' => 'Comment',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'Comment.model' => 'SectionItem'
			)
		),
		'Attachment' => array(
			'className' => 'Attachment',
			'foreignKey' => 'foreign_key',
			'conditions' => array(
				'Attachment.model' => 'SectionItem'
			)
		),
		// 'SystemRecord' => array(
		// 	'className' => 'SystemRecord',
		// 	'foreignKey' => 'foreign_key',
		// 	'conditions' => array(
		// 		'SystemRecord.model' => 'SectionItem'
		// 	)
		// ),
		'HasMany' => [
			'className' => 'SubSectionItem'
		]
	);

	public $hasAndBelongsToMany = array(
		'HasAndBelongsToMany2' => array(
			'className' => 'AnotherSectionItem',
			'with' => 'AnotherSectionItemsSectionItem',
			'joinTable' => 'another_section_items_section_items'
		),
		'HasAndBelongsToMany' => array(
			'className' => 'User',
			'with' => 'SectionItemsCollaborator',
			'joinTable' => 'section_items_collaborators',
			'foreignKey' => 'section_item_id',
			'associationForeignKey' => 'collaborator_id',
		)
	);

	/**
	 * Description is in the AppModel
	 */
	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public function __construct($id = false, $table = null, $ds = null)
	{
		//
		// Init helper Lib for UserFields Module
		$UserFields = new UserFields();
		//
		
		$this->label = __('Section Items');
		$this->_group = 'debug';

		$this->fieldGroupData = array(
			'default' => array(
				'label' => __('General Fields')
			),
			'user-fields' => array(
				'label' => __('User Fields')
			),
			// 'extensions' => array(
			// 	'label' => __('Extensions')
			// )
		);

		$this->fieldData = array(
			'varchar' => array(
				'label' => __('Varchar'),
				'editable' => true,
				'description' => __('This is a test description for this field')
			),
			'text' => array(
				'label' => __('Text'),
				'editable' => true,
				'description' => __('Testing description'),
			),
			'tinyint_status' => array(
				'label' => __('Status'),
				'options' => 'SectionItem::statuses', // callable static method
				'editable' => true,
				'description' => __('Testing description'),
				'empty' => __('Choose a status'),
			),
			'date' => array(
				'label' => __('Date'),
				'editable' => true,
				'description' => __('Testing description'),
			),
			'user_id' => array(
				'label' => __('Select (BelongsTo)'),
				'editable' => true,
				'description' => __('Testing description'),
				'empty' => __('Select a user'),

				//custom role as extension
				'Extensions' => [
					'Preview'
					// class in /lib/fielddata/Extensions/CustomRoleExtension.php
					// access as $fieldCollection->user_id->Extensions->CustomRole->...
					// 'CustomRole'
				]
			),
			'Tag' => array(
				'label' => __('Tags'),
				'editable' => true,
				'type' => 'tags',
				'description' => __('Testing description'),
				'empty' => __('Add a tag')
			),
			'HasAndBelongsToMany' => array(
				'label' => __('Multiselect (HABTM)'),
				'editable' => true,
				'description' => __('Testing description'),
			),
			'HasAndBelongsToMany2' => array(
				'label' => __('HABTM - AnotherSectionItem'),
				'editable' => false
			),
			'toggle_status' => array(
				'label' => __('Toggle ON/OFF'),
				'editable' => true,
				'type' => 'toggle',
				'description' => __('Testing description')
			),
			'UserField' => $UserFields->getFieldDataEntityData($this, 'UserField', [
				'label' => __('UserField'), 
				'description' => __('Testing description. This field replaced CustomRole field'),
				'group' => 'user-fields'
			])
		);

		$this->notificationSystem = array(
			'macros' => array(
				'SECTION_ITEM_ID' => array(
					'field' => 'SectionItem.id',
					'name' => __('Section Item ID')
				),
				'SECTION_ITEM_VARCHAR' => array(
					'field' => 'SectionItem.varchar',
					'name' => __('Section Item Varchar')
				),
				'SECTION_ITEM_TEXT' => array(
					'field' => 'SectionItem.text',
					'name' => __('Section Item Text')
				),
				'SECTION_ITEM_DATE' => array(
					'field' => 'SectionItem.date',
					'name' => __('Section Item Date')
				),
				'SECTION_ITEM_BELONGSTO' => array(
					'field' => 'Belongsto.full_name',
					'name' => __('Section Item BelongsTo')
				),
				'SECTION_ITEM_HABTM' => array(
					'field' => 'HasAndBelongsToMany.{n}.full_name',
					'name' => __('Section Item HasAndBelongsToMany')
				),
				'SECTION_ITEM_USER_FIELD' => $UserFields->getNotificationSystemData('UserField', [
					'name' => __('UserField')
				]),
			),
			'customEmail' =>  true
		);

		$this->advancedFilter = array(
			__('General') => array(
				'id' => array(
					'type' => 'text',
					'name' => __('ID'),
					'filter' => false
				),
				'varchar' => array(
					'type' => 'text',
					'name' => __('Varchar'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'SectionItem.varchar',
						'field' => 'SectionItem.id',
					)
				),
				'text' => array(
					'type' => 'text',
					'name' => __('Text'),
					'show_default' => false,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'SectionItem.text',
						'field' => 'SectionItem.id',
					)
				),
				'date' => array(
					'type' => 'date',
					'comparison' => true,
					'name' => __('Due Date'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'SectionItem.date',
						'field' => 'SectionItem.id',
					)
				),
				'tinyint_status' => array(
					'type' => 'select',
					'name' => __('Status'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'SectionItem.tinyint_status',
						'field' => 'SectionItem.id',
					),
					'data' => array(
						'method' => 'getStatuses',
						'empty' => __('All'),
						'result_key' => true,
					),
				),
				'user_id' => array(
					'type' => 'select',
					'name' => __('BelongsTo'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'BelongsTo.id',
						'field' => 'SectionItem.user_id',
					),
					'data' => array(
						'method' => 'getBelongsTo',
						'result_key' => true
					),
				),
				'tag_title' => array(
					'type' => 'multiple_select',
					'name' => __('Tags'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'Tag.title',
						'field' => 'SectionItem.id',
					),
					'data' => array(
						'method' => 'getTags',
					),
					'many' => true,
					'contain' => array(
						'Tag' => array(
							'title'
						)
					),
				),
				'habtm2_id' => array(
					'type' => 'multiple_select',
					'name' => __('HABTM2'),
					'show_default' => true,
					'filter' => array(
						'type' => 'subquery',
						'method' => 'findComplexType',
						'findField' => 'HasAndBelongsToMany2.id',
						'field' => 'SectionItem.id',
					),
					'data' => array(
						'method' => 'getHasAndBelongsToMany2',
					),
					'many' => true,
					'contain' => array(
						'HasAndBelongsToMany2' => array(
							'title'
						)
					),
				),
				'user_field_id' => $UserFields->getAdvancedFilterFieldData('SectionItem', 'UserField', [
					'name' => __('UserField'),
				])
			),
		);

		$this->advancedFilterSettings = array(
			'pdf_title' => $this->label,
			'pdf_file_name' => __('section_preview'),
			'csv_file_name' => __('section_preview'),
			'max_selection_size' => 10
		);
		$this->advancedFilterSettings = array(
            'pdf_title' => $this->label,
            'pdf_file_name' => __('compliance_findings'),
            'csv_file_name' => __('compliance_findings'),
            // 'reset' => array(
            //     'controller' => 'complianceAudits',
            //     'action' => 'index',
            // ),
            'history' => true,
            'bulk_actions' => true,
            'trash' => true,
            'view_item' => false,
            'use_new_filters' => true
        );

		parent::__construct($id, $table, $ds);
	}

	public function getImportToolConfig()
	{
		return [
            'SectionItem.varchar' => [
                'name' => __('Varchar')
            ],
            'SectionItem.text' => [
                'name' => __('Text')
            ],
            'SectionItem.date' => [
            	'name' => __('Date'),
            ],
            'SectionItem.user_id' => [
                'name' => __('BelongsTo'),
                'model' => 'BelongsTo',
            ],
            'SectionItem.HasAndBelongsToMany' => [
                'name' => __('HasAndBelongsToMany'),
                'model' => 'HasAndBelongsToMany'
            ]
        ];
	}

	/*
	 * Type of statuses
	 */
	 public static function statuses($value = null) {
		$options = array(
			self::STATUS_TYPE_1 => __('Status Type 1'),
			self::STATUS_TYPE_2 => __('Status Type 2'),
			self::STATUS_TYPE_3 => __('Status Type 3')
		);
		return parent::enum($value, $options);
	}
	const STATUS_TYPE_1 = 0;
	const STATUS_TYPE_2 = 1;
	const STATUS_TYPE_3 = 2;

	public function getStatuses() {
		return self::statuses();
	}

	/*public function beforeValidate($options = array()) {
		if (!$this->checkRelatedExists('BusinessUnit', $this->data['Asset']['business_unit_id'])) {
			$this->invalidate('business_unit_id', __('At least one of the selected items does not exist.'));
		}
	}*/

	public function beforeSave($options = array()) {
        // $this->transformDataToHabtm(array('HasAndBelongsToMany', 'HasAndBelongsToMany2'));

        return true;
    }

	public function get($id) {
		return $this->SectionItem->find('first');
	}

	public function getBelongsTo() {
		$data = $this->BelongsTo->find('list', [
			'fields' => ['BelongsTo.id', 'BelongsTo.full_name'],
			'order' => ['BelongsTo.full_name']
		]);

		return $data;
	}

	public function getHasAndBelongsToMany2() {
		$data = $this->HasAndBelongsToMany2->find('list', [
			'fields' => ['HasAndBelongsToMany2.id', 'HasAndBelongsToMany2.title'],
			'order' => ['HasAndBelongsToMany2.title']
		]);

		return $data;
	}

	public function beforeFind($query) {
		// debug($query);exit;
		return true;
	}
}
