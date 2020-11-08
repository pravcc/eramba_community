<?php
App::uses('Model', 'Model');
App::uses('ItemDataEntity', 'FieldData.Model/FieldData');

/**
 * Class that makes possible to work with ItemDataEntity instances in a bulk.
 */
class ItemDataCollection implements ArrayAccess, Iterator
{

	/**
	 * Collection of ItemDataEntity class instances.
	 * 
	 * @var array
	 */
	protected $_items = [];

	/**
	 * Primary key values of added ItemDataEntity instances for this Collection
	 * 
	 * @var array
	 */
	protected $_primaryKeys = [];

	/**
	 * Instance of a model.
	 * 
	 * @var Model
	 */
	protected $_Model = null;

	/**
	 * Variable holds on the fly created sub-objects.
	 * 
	 * @var array
	 */
	protected $_runtime = [];

	/**
	 * Construction for the Collection
	 * 
	 * @param Model $Model
	 */
	public function __construct($Model)
	{
		$this->_Model = $Model;
	}

	/**
	 * Getter which generates a merged collection out of set of collections of entities.
	 * 
	 * @param  string $name
	 * @return null|ItemDataCollection
	 */
	public function __get($name)
	{
		$SubCollection = null;
		foreach ($this->_items as $Item) {
			// in case specified sub-object doesn't exist we skip this cycle
			if ($Item->{$name} === null) {
				continue;
			}

			$subObject = $Item->{$name};

			// we create a SubCollection class only in case there are some required objects
			// to add to the resulting collection
			// that's to make the __get() method return something only if SubCollection is not empty
			if ($SubCollection === null) {
				$SubCollection = ClassRegistry::init($name)->getItemDataCollection();
			}

			// in case sub-object is a Collection we merge all items together in the new Collection
			if ($subObject instanceof ItemDataCollection) {
				foreach ($subObject as $subItem) {
					$SubCollection->add($subItem);
				}
			}

			// in case sub-object is only a single entity we include it into the new Collection
			if ($subObject instanceof ItemDataEntity) {
				$SubCollection->add($subObject);
			}
		}

		// we return something only if there is a collection existing
		if ($SubCollection instanceof ItemDataCollection) {
			return $SubCollection;
		}
	}

	/**
	 * Shorthand method to initialize a new instance of this class.
	 * 
	 * @param  Model  $Model Model instance.
	 * @return ItemDataCollection
	 */
	public static function newInstance(Model $Model)
	{
		$className = self::getClassName($Model);

		return new $className($Model);
	}

	/**
	 * Get ItemDataCollection's class name related to a certain Model.
	 * 
	 * @return string        Class name.
	 */
	public static function getClassName(Model $Model)
	{
		$modelClassName = $Model->alias . 'ItemCollection';
		$plugin = $Model->plugin . '.';

		App::uses($modelClassName, $plugin . 'Model/FieldData/Collection');

		// use customized class if exists to handle ItemDataCollection
		if (class_exists($modelClassName)) {
			$className = $modelClassName;
		}
		// otherwise use the default class for handling ItemDataCollection
		else {
			$className = 'ItemDataCollection';
		}

		return $className;
	}

	/**
	 * Get model associated with current Collection
	 * 
	 * @return Model
	 */
	public function getModel()
	{
		return $this->_Model;
	}

	/**
	 * Adds a primary key value for added ItemDataEntity into this Collection for easier access.
	 * 
	 * @param int $value Primary key value.
	 */
	protected function _addPrimaryKey($value, $key)
	{
		$this->_primaryKeys[$key] = $value;
	}

	/**
	 * Get the list of primary keys for all added ItemDataEntity class instances.
	 * 
	 * @return array
	 */
	public function getPrimaryKeys()
	{
		return $this->_primaryKeys;
	}

	/**
	 * Get the count of items for this ItemDataCollection class instance.
	 * 
	 * @return int
	 */
	public function count()
	{
		return count($this->getPrimaryKeys());
	}

	/**
	 * Adds a new ItemDataEntity object into this collection.
	 * 
	 * @param $Item array|ItemDataEntity
	 * @return false|ItemDataEntity      False in case the same object already exist in this Collection,
	 *                                   Instance of newly added ItemDataEntity class otherwise.
	 */
	public function add($Item)
	{
		if (!$Item instanceof ItemDataEntity) {
			$Item = ItemDataEntity::newInstance($this->getModel(), $Item);
		}

		$primaryKey = $Item->getPrimary();

		// return false if the same entity already exist in the collection
		if (in_array($primaryKey, $this->getPrimaryKeys()) || $primaryKey === null) {
			return false;
		}

		$this->_items[] = $Item;
		$keys = array_keys($this->_items);
		$lastKey = $keys[count($keys)-1];
		$this->_addPrimaryKey($primaryKey, $lastKey);

		return $Item;
	}

	/**
	 * Search this collection's ItemDataEntity object by it's primary key.
	 * 
	 * @param  mixed $primaryKey  Primary key.
	 * @return ItemDataEntity
	 */
	public function getByPrimaryKey($primaryKey)
	{
		$key = array_search($primaryKey, $this->_primaryKeys);

		return $this->_items[$key];
	}


	//
	// Methods to make implemented Interfaces work correctly
	//
	public function offsetSet($offset, $value)
	{
        if (is_null($offset)) {
            $this->_items[] = $value;
        } else {
            $this->_items[$offset] = $value;
        }
    }
    public function offsetExists($offset)
    {
        return isset($this->_items[$offset]);
    }
    public function offsetUnset($offset)
    {
        unset($this->_items[$offset]);
    }
    public function offsetGet($offset)
    {
        return isset($this->_items[$offset]) ? $this->_items[$offset] : null;
    }

	public function rewind()
	{
		return reset($this->_items);
	}
	public function current()
	{
		return current($this->_items);
	}
	public function key()
	{
		return key($this->_items);
	}
	public function next()
	{
		return next($this->_items);
	}
	public function valid()
	{
		return key($this->_items) !== null;
	}

}