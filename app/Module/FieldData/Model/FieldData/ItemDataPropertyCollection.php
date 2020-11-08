<?php
/**
 * ItemDataPropertyCollection
 *
 */

App::uses('ObjectCollection', 'Utility');
App::uses('CakeEventListener', 'Event');

/**
 * ItemDataEntity Property collection class.
 */
class ItemDataPropertyCollection extends ObjectCollection
{

	/**
	 * Reference to the parent ItemDataEntity class.
	 * 
	 * @var ItemDataEntity
	 */
	public $ItemDataEntity = null;

	/**
	 * Keeps a list of all methods of attached properties.
	 *
	 * @var array
	 */
	protected $_methods = [];

	/**
	 * Attaches an object and loads extensions.
	 */
	public function init(ItemDataEntity $ItemDataEntity, array $properties = [])
	{
		$this->ItemDataEntity = $ItemDataEntity;

		if (!empty($properties)) {
			foreach (ItemDataPropertyCollection::normalizeObjectArray($properties) as $name => $config) {
				$this->load($name, $config);
			}
		}
	}

	/**
	 * Load a Property for ItemDataEntity into this collection.
	 * 
	 * @param  string $object  Object name.
	 * @param  array  $config  Configuration.
	 * @return ItemDataProperty
	 */
	public function load($object, $config = [])
	{
		$property = $config['class'];

		list($plugin, $class) = pluginSplit($property, true);
		if (isset($this->_loaded[$class])) {
			return $this->_loaded[$class];
		}
		$object = $class;
		$class .= 'Property';
		App::uses($class, $plugin . 'Model/FieldData/Item/Property');

		if (!class_exists($class)) {
			throw new CakeException(sprintf('Class property %s doesnt exist.', $class));
		}

		$this->_loaded[$object] = new $class($this, $config);
		if (!$this->_loaded[$object] instanceof ItemDataProperty) {
			throw new CakeException(sprintf('Class property %s must extend ItemDataProperty class.', $class));
		}

		// always initialize setup() method for constructed property
		$this->_loaded[$object]->setup($this->ItemDataEntity, $config);

		// cache property's public methods
		$blacklist = ['setup', '__construct', 'toString', 'requestAction', 'dispatchMethod', 'log'];
		$methods = get_class_methods($this->_loaded[$object]);
		$this->_methods[$object] = array_values(array_diff($methods, $blacklist));

		// configure its default state
		$enable = isset($settings['enabled']) ? $settings['enabled'] : true;
		if ($enable === true) {
			$this->enable($object);
		}

		// return the loaded object
		return $this->_loaded[$object];
	}

	/**
	 * Dispatches a property method. Will call normal public methods.
	 *
	 * @param ItemDataEntity $Item   The ItemDataEntity class instance the method was originally called on.
	 * @param string         $method The method called.
	 * @param array          $params Parameters for the called method.
	 * @return array                 Result.
	 */
	public function dispatchMethod(ItemDataEntity $Item, $method, $params = [])
	{
		$object = $this->hasMethod($method);
		if ($object === false) {
			return ['unhandled'];
		}

		return call_user_func_array(
			[$this->_loaded[$object], $method],
			array_merge([&$Item], $params)
		);
	}

	/**
	 * Check to see if a method exists in one of the properties.
	 * 
	 * @param  string  $method Method name.
	 * @return mixed           Name of the property where method is found, False otherwise.
	 */
	public function hasMethod($method)
	{
		foreach ($this->_methods as $object => $methods) {
			if (in_array($method, $methods)) {
				return $object;
			}
		}

		return false;
	}
}