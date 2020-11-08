<?php
App::uses('AppModel', 'Model');
App::uses('AdvancedFilterUserSetting', 'Model');

class AdvancedFilter extends AppModel {
	const NOT_DELETED = 0;
	const DELETED = 1;

	public $recursive = 0;

	public $actsAs = array(
		'Containable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'name', 'model'
			)
		)
	);

	public $belongsTo = array(
		'User',
		'AdvancedFilterUserSetting' => array(
			'foreignKey' => false,
			'conditions' => array(
				'AdvancedFilterUserSetting.advanced_filter_id = AdvancedFilter.id',
				'AdvancedFilterUserSetting.user_id = AdvancedFilter.user_id'
			)
		)
	);

	public $hasOne = array(
		// 'AdvancedFilterUserSetting',
	);

	public $hasMany = array(
		'AdvancedFilterValue',
		'AdvancedFilterCron',
	);

	public $validate = array(
		'name' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		),
		'model' => array(
			'rule' => 'notBlank',
			'required' => true,
			'allowEmpty' => false
		)
	);

	public static function getTextComparisonTypes($labels = false) {
		$types = array(
			'LIKE' => __('Like'),
			'NOT LIKE' => __('Not Like')
		);

		return ($labels) ? $types : array_keys($types);
	}

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Advanced Filters');

		parent::__construct($id, $table, $ds);
	}

	public function beforeFind($queryData) {
		parent::beforeFind($queryData);
		$queryData['conditions']['AdvancedFilter.deleted'] = self::NOT_DELETED;
		return $queryData;
	}

	public function softDelete($id) {
		return $this->updateAll(array(
			'AdvancedFilter.deleted' => self::DELETED, 
			'AdvancedFilter.deleted_date' => '"' . date('Y-m-d H:i:s') . '"'
		), array(
			'AdvancedFilter.id' => $id
		));
	}

	/**
	 * returns all Advanced filters for given userId and model
	 * 
	 * @param  int $userId
	 * @param  array $model
	 * @param  array $options
	 * @return array $filters
	 */
	public function getAll($userId, $model = array(), $options = array()) {
		$options = am(array(
			'find' => 'all',
			'contain' => array()
		), $options);

		$filters = $this->find($options['find'], array(
			'conditions' => array(
				'AdvancedFilter.model' => $model,
				'OR' => array(
					array(
						'AdvancedFilter.private' => ADVANCED_FILTER_NOT_PRIVATE,
					),
					array(
						'AdvancedFilter.private' => ADVANCED_FILTER_PRIVATE,
						'AdvancedFilter.user_id' => $userId,
					),
				)
			),
			'contain' => am(array(
				'AdvancedFilterUserSetting'
			), $options['contain']),
			'order' => array(
				'AdvancedFilter.name' => 'ASC'
			)
		));

		return $filters;
	}

	/**
	 * returns Advanced Filter data
	 * 
	 * @param  int $id
	 * @return array $filter
	 */
	public function getFilter($id) {
		$filter = $this->find('first', array(
			'conditions' => array(
				'AdvancedFilter.id' => $id
			),
			'contain' => array(
				'AdvancedFilterUserSetting'
			)
		));

		return $filter;
	}

	/**
	 * find default index for given user and model
	 * 
	 * @param  string $model
	 * @param  int $userId
	 * @return int AdvancedFilter.id on sucess | false on fail 
	 */
	public function getDefault($model, $userId) {
		$filter = $this->find('first', array(
			'fields' => array(
				'AdvancedFilter.id', 'AdvancedFilterUserSetting.id', 'AdvancedFilterUserSetting.default_index'
			),
			'conditions' => array(
				'AdvancedFilter.model' => $model,
				'AdvancedFilter.user_id' => $userId,
				'AdvancedFilterUserSetting.default_index' => AdvancedFilterUserSetting::DEFAULT_INDEX
			),
			'contain' => array(
				'AdvancedFilterUserSetting' => array(
					'conditions' => array(
						'AdvancedFilterUserSetting.advanced_filter_id = AdvancedFilter.id',
						'AdvancedFilterUserSetting.user_id' => $userId,
					)
				),
			)
		));

		return (!empty($filter['AdvancedFilterUserSetting']['default_index'])) ? $filter['AdvancedFilter']['id'] : false;
	}

	/**
	 * checks if AdvancedFilter is default index for given user
	 * 
	 * @param  int $id
	 * @param  int $userId
	 * @return boolean $isDefault
	 */
	public function isDefault($id, $userId = null) {
		$conditions = array(
			'AdvancedFilter.id' => $id,
			'AdvancedFilterUserSetting.default_index' => AdvancedFilterUserSetting::DEFAULT_INDEX
		);

		if ($userId !== null) {
			$conditions['AdvancedFilterUserSetting.user_id'] = $userId;
		}

		$isDefault = $this->find('count', array(
			'conditions' => $conditions,
		));

		return (boolean) $isDefault;
	}

	/**
	 * returns filter values data 
	 * 
	 * @param  int $id advanced_fiter_id
	 * @return array
	 */
	public function getFilterValues($id) {
		$values = $this->AdvancedFilterValue->find('all', array(
			'conditions' => array(
				'AdvancedFilterValue.advanced_filter_id' => $id
			)
		));

		return $values;
	}

	/**
	 * returns well formated AdvancedFilterValue data for request
	 * 
	 * @param  array $data
	 * @return array $formatedData
	 */
	public static function buildValues($data) {
		$formatedData = array();

		foreach ($data as $item) {
			$value = $item['AdvancedFilterValue']['value'];
			if ($item['AdvancedFilterValue']['many']) {
				$value = explode(',', $value);
			}
			$formatedData[$item['AdvancedFilterValue']['field']] = $value;
		}

		return $formatedData;
	}
}