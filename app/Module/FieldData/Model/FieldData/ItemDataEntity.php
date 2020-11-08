<?php
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');
App::uses('ItemDataPropertyCollection', 'FieldData.Model/FieldData');

class ItemDataEntity implements JsonSerializable
{
	/**
	 * Model instance.
	 * 
	 * @var Model
	 */
	protected $_Model;

	/**
	 * Variable holds data for current item.
	 * 
	 * @var array
	 */
	protected $_data;

	/**
	 * List of properties to load when ItemDataEntity class is initialized.
	 * 
	 * @var array
	 */
	public $actsAs = [
		'AdvancedFilters.AdvancedFilters',
		'FieldData.FieldData',
		'ObjectStatus.ObjectStatus',
		'Utils.SoftDelete',
		'Widget.Widget',
	];

	/**
	 * List of Property objects currently loaded on the current ItemDataEntity.
	 * 
	 * @var array
	 */
	public $Properties = null;

	/**
	 * Variable holds on the fly created sub-objects.
	 * 
	 * @var array
	 */
	protected $_runtime = [];

	/**
	 * Constructor for current item class.
	 * 
	 * @param Model  $Model
	 * @param array  $data  Data array.
	 */
	public function __construct(Model $Model, $data)
	{
		$this->_Model = $Model;
		$this->_data = $data;

		$this->Properties = new ItemDataPropertyCollection();
		$this->Properties->init($this, $this->actsAs);
	}

	/**
	 * Handles custom method calls directed to properties.
	 *
	 * @param  string $method Name of method to call.
	 * @param  array  $params Parameters for the method.
	 * @return mixed          Whatever is returned by called method.
	 */
	public function __call($method, $params) {
		$result = $this->Properties->dispatchMethod($this, $method, $params);
		if ($result !== ['unhandled']) {
			return $result;
		}
	}

	/**
	 * Getter for associated model's data which builds ItemDataCollection/ItemDataEntity during runtime.
	 */
	public function __get($name)
	{
		// we return a reference to a sub-object
		if (isset($this->_runtime[$name])) {
			return $this->_runtime[$name];
		}

		// we return a simple variable
		if (isset($this->_data[$this->_Model->alias][$name])) {
			return $this->_data[$this->_Model->alias][$name];
		}

		if (isset($this->_data[$name])) {
			$multipleAssociations = $this->_getMultipleAssociations();

			// in case we are getting some association with multiple items
			// we create a collection class out of that data
			if (in_array($name, $multipleAssociations)) {
				$Collection = ItemDataCollection::newInstance(ClassRegistry::init($name));
				foreach ($this->_data[$name] as $subItem) {
					$Collection->add($subItem);
				}

				return $this->_runtime[$name] = $Collection;
			}

			$singleAssociations = $this->_getSingleAssociations();

			// in case we are getting a single association, we create a simple ItemDataEntity class instance
			if (in_array($name, $singleAssociations)) {
				$AssocModel = ClassRegistry::init($name);
				$newInstance = self::newInstance($AssocModel, $this->_data[$name]);

				// in case of belongsTo and hasOne associated data where all column's values are set to null
				// we handle it as non-existent object
				if ($newInstance->getPrimary() === null) {
					return null;
				}

				return $this->_runtime[$name] = $newInstance;
			}

			return $this->_data[$name];
		}
	}

	public function __isset($name)
	{
		$data = $this->{$name};

		return !empty($data);
	}

	public function jsonSerialize() {
		return $this->_data;
	}

	/**
	 * Add new data into current ItemDataEntity instance.
	 * 
	 * @param string $model Section name.
	 * @param array $data   Data.
	 */
	public function add($model, $data)
	{
		$this->_data[$model] = $data;
	}

	/**
	 * Shorthand method to initialize a new instance of this class.
	 * 
	 * @param  Model  $Model Model instance.
	 * @param  array  $item  Item data.
	 * @return ItemDataEntity
	 */
	public static function newInstance(Model $Model, $item)
	{
		$className = self::getClassName($Model);

		return new $className($Model, $item);
	}

	/**
	 * Get ItemDataEntity's class name related to a certain Model.
	 * 
	 * @return string        Class name.
	 */
	public static function getClassName(Model $Model)
	{
		$modelClassName = $Model->alias . 'ItemData';
		$plugin = $Model->plugin . '.';

		App::uses($modelClassName, $plugin . 'Model/FieldData/Item');

		// use customized class if exists to handle ItemDataEntity
		if (class_exists($modelClassName)) {
			$className = $modelClassName;
		}
		// otherwise use the default class for handling ItemDataEntity
		else {
			$className = 'ItemDataEntity';
		}

		return $className;
	}

	// get multiple associations for current model
	protected function _getMultipleAssociations()
	{
		$hasMany = $this->_Model->getAssociated('hasMany');
		$hasAndBelongsToMany = $this->_Model->getAssociated('hasAndBelongsToMany');

		return array_merge($hasMany, $hasAndBelongsToMany);
	}

	// get single associations for current model
	protected function _getSingleAssociations()
	{
		$hasMany = $this->_Model->getAssociated('hasOne');
		$hasAndBelongsToMany = $this->_Model->getAssociated('belongsTo');

		return array_merge($hasMany, $hasAndBelongsToMany);
	}

	/**
	 * Get Primary key value for the current ItemDataEntity
	 * 
	 * @param  FieldDataEntity $Field
	 */
	public function getPrimary()
	{
		$id = $this->id;
		if (is_numeric($this->id)) {
			$id = (int) $id;
		}

		return $id;
	}

	/**
	 * Get the model of current ItemDataEntity class instance.
	 * 
	 * @return Model
	 */
	public function getModel()
	{
		return $this->_Model;
	}

	/**
	 * Get raw data for the current ItemDataEntity class instance.
	 * 
	 * @return array
	 */
	public function getData()
	{
		return $this->_data;
	}

}