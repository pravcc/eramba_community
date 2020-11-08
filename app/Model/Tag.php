<?php
App::uses('AuthComponent', 'Controller/Component');

class Tag extends AppModel {
	const VALUE_SEPARATOR = ',';

	public $displayField = 'title';

	public $actsAs = array(
		'Containable',
		'AuditLog.Auditable',
		'HtmlPurifier.HtmlPurifier' => array(
			'config' => 'Strict',
			'fields' => array(
				'title', 'model', 'foreign_key', 'user_id'
			)
		)
	);

	public $validate = array(
		// 'title' => array(
		// 	'rule' => 'notBlank',
		// 	'required' => true,
		// 	'allowEmpty' => false,
		// 	'message' => 'Please enter a title for this tag'
		// )
	);

	public $belongsTo = array(
		'User'
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->label = __('Tags');

		$this->fieldGroupData = [
			'default' => [
				'label' => __('General')
			],
		];

		$this->fieldData = [
			'title' => [
				'label' => __('Tags'),
				'editable' => true,
				'inlineEdit' => true,
				'description' => __('Name of the asset')
			],
		];
		
		parent::__construct($id, $table, $ds);
	}

	/**
	 * Saves an array of tags for certain section.
	 *
	 * @param  array  $data       Array of data.
	 * @param  string $model      Model which this tag applies to.
	 * @param  int    $foreignKey Record which this tag applies to.
	 * @return boolean            True on success, false otherwise.
	 */
	public function saveTags($data, $model, $foreignKey = null, $conventionSave = false) {
		if (!$conventionSave && empty($data['Tag']['tags'])) {
			return true;
		}
		else {
			$pullFromModel = 'Tag';
		}

		if ($conventionSave && empty($data[$model]['tags'])) {
			return true;
		}
		elseif ($conventionSave) {
			$pullFromModel = $model;
		}
		
		$tags = explode(self::VALUE_SEPARATOR, $data[$pullFromModel]['tags']);

		$user = $this->currentUser();
		foreach ($tags as $title) {
			$tmp = array(
				'model' => $model,
				'foreign_key' => $foreignKey,
				'title' => $title,
				'user_id' => $user['id']
			);

			$this->create();
			if (!$this->save($tmp)) {
				return false;
			}
		}

		return true;
	}

	public function saveTagsArr($data = array(), $model, $foreignKey = null) {
		$tags['Tag']['tags'] = implode(self::VALUE_SEPARATOR, $data);
		return $this->saveTags($tags, $model, $foreignKey);
	}

	/**
	 * Delete tags according to params.
	 */
	public function deleteTags($model, $foreignKey = null) {
		$options = array(
			'Tag.model' => $model
		);

		if (!empty($foreignKey)) {
			$options['Tag.foreign_key'] = $foreignKey;
		}

		return $this->deleteAll($options);
	}

	/**
	 * Get all available tags for a section.
	 * 
	 * @param  string $model      Model name.
	 * @param  string $foreignKey Foreign key.
	 * @param  bool   $indexedArr True to return indexed array of values.
	 */
	public function getTags($model = null, $foreignKey = null, $indexedArr = true) {
		$conds = array();
		if (!empty($model)) {
			$conds['model'] = $model;
		}

		if (!empty($foreignKey)) {
			$conds['foreign_key'] = $foreignKey;
		}

		$data = $this->find('list', array(
			'conditions' => $conds,
			'order' => array('Tag.title' => 'ASC'),
			'fields' => array('Tag.id', 'Tag.title'),
			'group' => array('Tag.title'),
			'recursive' => -1
		));

		if ($indexedArr) {
			$data = array_values($data);
		}

		return $data;
	}

	public function convertTagsImport($value) {
		if (!empty($value)) {
			return implode(self::VALUE_SEPARATOR, $value);
		}

		return false;
	}

	public static function transforTagListSaveData($tags, $Model)
	{
		$data = [];

		foreach ($tags as $tag) {
			$data[] = [
				'model' => $Model->modelFullAlias(),
				'title' => $tag,
				'user_id' => AuthComponent::user('id'),
			];
		}

		return $data;
	}
}
