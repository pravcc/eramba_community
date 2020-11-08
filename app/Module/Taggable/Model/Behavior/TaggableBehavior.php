<?php
App::uses('ModelBehavior', 'Model');

/**
 * Taggable Behavior
 */
class TaggableBehavior extends ModelBehavior {

/**
 * Settings array
 *
 * @var array
 */
	public $settings = array();

/**
 * Default settings
 *
 * field                  - the fieldname that contains the raw tags as string
 *
 * @var array
 */
	protected $_defaults = array(
		'field' => 'Tag'
	);

/**
 * Setup
 *
 * @param Model $model Model instance that behavior is attached to
 * @param array $config Configuration settings from model
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		if (!isset($this->settings[$model->alias])) {
			$this->settings[$model->alias] = $this->_defaults;
		}

		$this->settings[$model->alias] = array_merge($this->settings[$model->alias], $config);
		$this->bindTagAssociations($model);
	}

/**
 * Bind tag assocations
 *
 * @param Model $model Model instance that behavior is attached to
 * @return void
 */
	public function bindTagAssociations(Model $model) {
		// $model->bindModel(array(
		// 	'hasMany' => array(
		// 		'Tag' => array(
		// 			'className' => 'Tag',
		// 			'foreignKey' => 'foreign_key',
		// 			'conditions' => array(
		// 				'Tag.model' => $model->alias
		// 			),
		// 			'dependent' => ''
		// 		)
		// 	)
		// ), false);

		
	}


/**
 * Saves a string of tags
 *
 * @param Model $model Model instance that behavior is attached to
 * @param string $tags Array of tags.
 *     Tags can contain special tokens called `identifiers´ to namespace tags or classify them into catageories.
 *     A valid string is "foo, bar, cakephp:special". The token `cakephp´ will end up as the identifier or category for the tag `special´
 * @param mixed $foreignKey the identifier for the record to associate the tags with
 * @param bool $update True will remove tags that are not in the $string, false won't
 *     do this and just add new tags without removing existing tags associated to
 *     the current set foreign key
 * @return array
 */
	// public function saveTags(Model $model, $tags = [], $foreignKey = null, $update = true) {
	// 	$source = array();
	// 	if ($model->hasMethod('currentUser')) {
	// 		$source = $model->currentUser();
	// 	}

	// 	$tmp = array();
	// 	foreach ($tags as $key => $title) {
	// 		$tmp[] = array(
	// 			'model' => $model->alias,
	// 			'foreign_key' => $foreignKey,
	// 			'title' => $title,
	// 			'user_id' => $source['id']
	// 		);
	// 	}

	// 	$result = $model->Tag->saveMany($tmp, array(
	// 		'validate' => false,
	// 		'atomic' => false
	// 	));

	// 	return (bool) $result;
	// }

	/**
	 * Get all available tags for a section.
	 * 
	 * @param  string $model      Model name.
	 * @param  string $foreignKey Foreign key.
	 * @param  bool   $indexedArr True to return indexed array of values.
	 */
	public function getTagged(Model $model, $foreignKey = null, $indexedArr = true) {
		$conds = array();
		if (!empty($model)) {
			$conds['model'] = $model->alias;
		}

		if (!empty($foreignKey)) {
			$conds['foreign_key'] = $foreignKey;
		}

		$data = $model->Tag->find('list', array(
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

	// public function afterSave(Model $model, $created, $options = array()) {
	// 	if (!isset($model->data[$model->alias][$this->settings[$model->alias]['field']])) {
	// 		return;
	// 	}
		
	// 	$this->deleteTags($model, $model->id);

	// 	$field = $model->data[$model->alias][$this->settings[$model->alias]['field']];
	// 	$hasTags = !empty($field);
	// 	if ($hasTags) {
	// 		$this->saveTags($model, $field, $model->id);
	// 	}
	// }

/**
 * Delete associated Tags if record has no tags and deleteTagsOnEmptyField is true
 *
 * @param Model $model Model instance that behavior is attached to
 * @param mixed $id Foreign key of the model, string for UUID or integer
 * @return void
 */
	public function deleteTags(Model $model, $id = null) {
		$options = array(
			'Tag.model' => $model->alias
		);

		if (!empty($id)) {
			$options['Tag.foreign_key'] = $id;
		}

		return $model->Tag->deleteAll($options, true, true);
	}

}