<?php
App::uses('AdvancedFiltersAppModel', 'AdvancedFilters.Model');

class AdvancedFilterValue extends AdvancedFiltersAppModel
{
	const LIMIT_UNLIMITED = -1;

	public $belongsTo = [
		'AdvancedFilter' => [
			'className' => 'AdvancedFilters.AdvancedFilter'
		]
	];

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Advanced Filters');

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			]
		];

		$this->fieldData = [
			'_limit' => [
				'label' => __('Limit'),
				'editable' => true,
				'description' => __('Limit the number of results of the filter to whatever value you select'),
				'options' => [$this, 'getLimitOptions'],
				'default' => self::LIMIT_UNLIMITED,
				'renderHelper' => ['AdvancedFilters.AdvancedFilterValues', 'limitField']
			],
			'_order_column' => [
				'label' => __('Order Column'),
				'editable' => true,
				'description' => __('Order the filter by a specific column'),
				'options' => [
					'callable' => [$this, 'getOrderColumn'],
					'passParams' => true
				],
			],
			'_order_direction' => [
				'label' => __('Order Direction'),
				'editable' => true,
				'description' => __('Sort using ascending or descending numerical / alphabetic order'),
				'options' => [$this, 'getOrderDirections'],
			],
		];

		parent::__construct($id, $table, $ds);
	}

	public function getLimitOptions()
	{
		return [
			10 => '10',
			15 => '15',
			20 => '20',
			25 => '25',
			30 => '30',
			35 => '35',
			40 => '40',
			45 => '45',
			50 => '50',
			100 => '100',
			500 => '500',
			self::LIMIT_UNLIMITED => __('Unlimited')
		];
	}

	public function getOrderDirections()
	{
		return [
			'ASC' => __('Ascending'),
			'DESC' => __('Descending')
		];
	}

	public function getOrderColumn(FieldDataEntity $Field, $model)
	{
		$Model = ClassRegistry::init($model);
		$FieldCollection = $Model->getFieldCollection();

		$options = [];
		foreach ($FieldCollection as $field) {
			if (!$field->isAssociated() && $Model->schema($field->getFieldName()) !== null) {
				$options[$field->getFieldName()] = $field->getLabel();
			}
		}

		return $options;
	}

}