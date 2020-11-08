<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
// App::uses('TaggableView', 'Taggable.Controller/Crud/View');

/**
 * Taggable Listener
 */
class TaggableListener extends CrudListener
{
	protected $_settings = [
		'fields' => []
	];

	public function implementedEvents() {
		return array(
			'Crud.initialize' => 'beforeHandle',
			'Crud.beforeSave' => 'beforeSave',
			'Crud.beforeRender' => 'beforeRender'
		);
	}
	
	public function beforeHandle(CakeEvent $event) {
		$request = $this->_request();
		$model = $this->_model();

		$this->_ensureBehavior($model);
		$this->_ensureAssociation($model);
	}

	protected function _ensureAssociation(Model $model)
	{
		$fields = $this->config('fields');
		$fields = array_unique($fields);

		foreach ($fields as $field) {
			$assoc = $model->getAssociated($field);

			// for already existing association, skip creating a new association
			if ($assoc !== null) {
				continue;
			}

			$model->bindModel(array(
				'hasMany' => array(
					$field => array(
						'className' => 'Tag',
						'foreignKey' => 'foreign_key',
						'conditions' => array(
							$field . '.model' => $model->alias
						),
						'dependent' => ''
					)
				)
			), false);

			$model->getFieldDataEntity($field)->preloadModelInfo($model);
		}
	}

/**
 * Ensure that the taggable behavior is loaded
 *
 * @param Model $model
 * @return void
 */
	protected function _ensureBehavior(Model $model) {
		if ($model->Behaviors->loaded('Taggable.Taggable')) {
			return;
		}

		$model->Behaviors->load('Taggable.Taggable');
		$model->Behaviors->Taggable->setup($model);
	}

	protected function _isInFieldList(CakeEvent $e, $field)
	{
		$subject = $e->subject;
		$model = $subject->model;
		$saveOptions = $subject->crud->action()->saveOptions();

		// if field list is configured, we check if the given $field is actually a part of the field list
		// there are 2 options what to check in the field list, either:
		// 1. within a $model key in the array
		// 2. or witout the $model key in the array
		if (isset($saveOptions['fieldList'])) {
			$fieldList = $saveOptions['fieldList'];
// ddd($fieldList);
			// we check the 1st option
			if (isset($fieldList[$model->alias]) && in_array($field, $fieldList[$model->alias])) {
				return true;
			}

			// we check the 2nd option
			if (in_array($field, $fieldList)) {
				return true;
			}

			return false;
		}

		// otherwise if field list is not set it means to process all fields so we return true
		return true;
	}

	/**
	 * Before save handles transformation of the request data to the conventional hasMany format.
	 * 
	 * @param  CakeEvent $e
	 */
	public function beforeSave(CakeEvent $e)
	{
		$subject = $e->subject;
		$controller = $subject->controller;
		$model = $subject->model;
		$request = $subject->request;

		// we get the fields list that needs to be transformed
		$fields = $this->config('fields');
		$fields = array_unique($fields);

		foreach ($fields as $field) {
			if (!$this->_isInFieldList($e, $field)) {
				unset($request->data[$model->alias][$field]);
				continue;
			}

			// we do the process only if the specific Tag key is set
			if (isset($request->data[$model->alias][$field])) {
				if (isset($subject->id)) {
					if ($this->_isCustomAssoc($field)) {
						$assoc = $model->getAssociated($field);
						$conditions = $assoc['conditions'];
						$assocModel = $assoc['className'];
						$foreignKey = $assoc['foreignKey'];
						list($plugin, $name) = pluginSplit($assocModel);
						if (!is_array($conditions)) {
							$conditions = [];
						}

						$conditions[$field . '.' . $foreignKey] = $subject->id;
						$model->$field->deleteAll($conditions, true, true);
					}
					else {
						$model->deleteTags($subject->id);
					}
				}

				$newTags = [];

				// we only transform it if its an array
				if (is_array($request->data[$model->alias][$field])) {
					if ($this->_isCustomAssoc($field)) {
						$assoc = $model->getAssociated($field);
						$conditions = $assoc['conditions'];
						$assocModel = $assoc['className'];
						list($plugin, $name) = pluginSplit($assocModel);
						$assocDisplayField = $model->$field->displayField;

						foreach ($request->data[$model->alias][$field] as $tag) {
							$_newTag = [
								$assocDisplayField => $tag,
								// 'user_id' => $controller->logged['id']
							];

							if (!empty($conditions)) {
								foreach ($conditions as $condition => $value) {
									$exploded = explode('.', $condition);
									$count = count($exploded);
									$_newTag[$exploded[$count-1]] = $value;
								}
							}

							$newTags[] = $_newTag;
						}
					}
					else {
						foreach ($request->data[$model->alias][$field] as $tag) {
							$newTags[] = [
								'model' => $model->alias,
								'title' => $tag,
								'user_id' => $controller->logged['id']
							];
						}
					}
				}

				$request->data[$model->alias][$field] = $newTags;
			}
		}
	}

	protected function _isCustomAssoc($field)
	{
		$model = $this->_model();
		$assoc = $model->getAssociated($field);
		$assocModel = $assoc['className'];
		if ($assocModel == 'Tag') {
			return false;
		}

		return true;
	}

	/**
	 * Before render callback that sets all required data into the view.
	 * 
	 * @param  CakeEvent $e
	 * @return void
	 */
	public function beforeRender(CakeEvent $e)
	{
		// $this->_controller()->set('Taggable', new TaggableView($e->subject));
	}

}
