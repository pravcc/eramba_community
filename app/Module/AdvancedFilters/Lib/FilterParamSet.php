<?php
App::uses('FilterParam', 'AdvancedFilters.Lib');
App::uses('AdvancedFilterUserParam', 'AdvancedFilters.Model');

class FilterParamSet implements ArrayAccess, Iterator
{

	/**
	 * Array of FilterParam object instances.
	 * 
	 * @var array
	 */
	protected $_params = [];

	/**
	 * Instance of a model.
	 * 
	 * @var Model
	 */
	protected $_model = null;

	public function __construct(Model $Model = null)
	{
		$this->_model = $Model;
	}

	protected function _buildKey($type, $name)
	{
		return $type . '_' . $name;
	}

	protected function _explodeKey($key)
	{
		return explode('_', $key, 2);
	}

	/**
	 * Add a new filter field into this set of fields.
	 * 
	 * @param array $params Array of parameters
	 * @return FilterField
	 */
	public function add($type = AdvancedFilterUserParam::TYPE_GENERAL, $name, $value = null)
	{
		$this->_params[$this->_buildKey($type, $name)] = new FilterParam($type, $name, $value);
	}

	// get filter params assigned to a specific type
	public function getGroup($type)
	{
		$group = [];
		foreach ($this->_params as $key => $FilterParam) {
			if ($FilterParam->getType() == $type) {
				$exploded = $this->_explodeKey($key);

				$group[$exploded[1]] = $FilterParam->getValue();
			}
		}

		return $group;
	}

	public function get($type, $name)
	{
		$key = $this->_buildKey($type, $name);
		if ($name !== null) {
			if (!isset($this->_params[$key])) {
				return null;
			}
			
			return $this->_params[$key];
		}

		return $this->_params;
	}

	//
	// Methods to make implemented Interfaces work correctly
	//
	public function offsetSet($offset, $value)
	{
        if (is_null($offset)) {
            $this->_params[] = $value;
        } else {
            $this->_params[$offset] = $value;
        }
    }
    public function offsetExists($offset)
    {
        return isset($this->_params[$offset]);
    }
    public function offsetUnset($offset)
    {
        unset($this->_params[$offset]);
    }
    public function offsetGet($offset)
    {
        return isset($this->_params[$offset]) ? $this->_params[$offset] : null;
    }

	public function rewind()
	{
		return reset($this->_params);
	}
	public function current()
	{
		return current($this->_params);
	}
	public function key()
	{
		return key($this->_params);
	}
	public function next()
	{
		return next($this->_params);
	}
	public function valid()
	{
		return key($this->_params) !== null;
	}

}
