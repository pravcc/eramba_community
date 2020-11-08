<?php
App::uses('AppModel', 'Model');

class Threat extends AppModel
{
	public $displayField = 'name';

	public $name = 'Threat';

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];
	
	public $actsAs = array(
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name'
			)
		),
		'AuditLog.Auditable',
		'Comments.Comments',
		'Attachments.Attachments',
		'Widget.Widget',
		'AdvancedFilters.AdvancedFilters'
	);

	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notBlank'
			),
			'unique' => array(
				'rule' => 'isUnique'
			)
		),
	);

	public $hasMany = array(
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Threats');

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
		];

		$this->fieldData = [
			'name' => [
				'label' => __('Name'),
				'editable' => true,
			],
		];

		$this->advancedFilterSettings = array(
			'pdf_title' => __('Threats'),
			'pdf_file_name' => __('threats'),
			'csv_file_name' => __('threats'),
			'bulk_actions' => true,
			'history' => true,
			'trash' => true,
			'use_new_filters' => true
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
				]);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}
}
