<?php
App::uses('FilterField', 'AdvancedFilters.Lib');

class FilterFieldSet implements ArrayAccess, Iterator
{

	/**
	 * Array of FilterField object instances.
	 * 
	 * @var array
	 */
	protected $_fields = [];

	/**
	 * Instance of a model.
	 * 
	 * @var Model
	 */
	protected $_model = null;

	protected $_limit = 15;

	public function __construct(Model $Model)
	{
		$this->_model = $Model;
	}

	/**
	 * Add a new filter field into this set of fields.
	 * 
	 * @param array $params Array of parameters
	 * @return FilterField
	 */
	public function add($name, $params = [])
	{
		$this->_fields[$name] = new FilterField($this->_model, $name, $params);
	}

	public function get($name = null)
	{
		if ($name !== null) {
			if (!isset($this->_fields[$name])) {
				return null;
			}
			
			return $this->_fields[$name];
		}

		return $this->_fields;
	}

	//
	// Methods to make implemented Interfaces work correctly
	//
	public function offsetSet($offset, $value)
	{
        if (is_null($offset)) {
            $this->_fields[] = $value;
        } else {
            $this->_fields[$offset] = $value;
        }
    }
    public function offsetExists($offset)
    {
        return isset($this->_fields[$offset]);
    }
    public function offsetUnset($offset)
    {
        unset($this->_fields[$offset]);
    }
    public function offsetGet($offset)
    {
        return isset($this->_fields[$offset]) ? $this->_fields[$offset] : null;
    }

	public function rewind()
	{
		return reset($this->_fields);
	}
	public function current()
	{
		return current($this->_fields);
	}
	public function key()
	{
		return key($this->_fields);
	}
	public function next()
	{
		return next($this->_fields);
	}
	public function valid()
	{
		return key($this->_fields) !== null;
	}

}
