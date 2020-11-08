<?php
App::uses('AppModel', 'Model');

class AssetLabel extends AppModel
{
	public $displayField = 'name';

	protected $_appModelConfig = [
		'behaviors' => [
		],
		'elements' => [
		]
	];

	public $mapping = array(
		'titleColumn' => 'name',
		'logRecords' => true,
		'workflow' => false
	);

	public $actsAs = array(
		'Containable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'description'
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
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		)
	);

	public $hasMany = array(
		'Asset',
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Asset Labels');

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
			'description' => [
				'label' => __('Description'),
				'editable' => true,
			],
		];

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
				]);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}
}
