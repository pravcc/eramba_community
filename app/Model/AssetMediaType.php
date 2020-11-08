<?php
App::uses('AppModel', 'Model');

class AssetMediaType extends AppModel
{
	public $displayField = 'name';

	public $name = 'AssetMediaType';

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

	public $hasAndBelongsToMany = array(
		'Threat',
		'Vulnerability'
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Asset Media Types');

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
		];

		$this->fieldData = [
			'name' => [
				'label' => __('Name'),
				'editable' => false,
			],
			'editable' => [
				'label' => __('Is Editable'),
				'editable' => false,
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
				]);

		return $advancedFilterConfig->getConfiguration()->toArray();
	}

	public function getThreatsVulnerabilities($typeIds) {
		if (empty($typeIds)) {
			return array('threats' => array(), 'vulnerabilities' => array());
		}

		$typeIds = array_unique($typeIds);

		$threats = $this->AssetMediaTypesThreat->find('list', array(
			'conditions' => array(
				'AssetMediaTypesThreat.asset_media_type_id' => $typeIds
			),
			'fields' => array('threat_id'),
			'recursive' => -1
		));

		$vulnerabilities = $this->AssetMediaTypesVulnerability->find('list', array(
			'conditions' => array(
				'AssetMediaTypesVulnerability.asset_media_type_id' => $typeIds
			),
			'fields' => array('vulnerability_id'),
			'recursive' => -1
		));

		return array(
			'threats' => array_values($threats),
			'vulnerabilities' => array_values($vulnerabilities)
		);
	}
}
