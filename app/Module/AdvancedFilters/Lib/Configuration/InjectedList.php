<?php
/**
 * Associative list with possibility to insert items before or after certain item. 
 */
final class InjectedList implements Countable, Iterator
{
	/**
	 * Associative array of data [key => value].
	 * 
	 * @var array
	 */
	private $_list = [];

	/**
	 * Array of key positions.
	 * 
	 * @var array
	 */
	private $_keyList = [];

	/**
	 * Iterator position for Iterator interface.
	 * 
	 * @var int
	 */
	private $_iterator = 0;

	/**
	 * Insert value on end of the list.
	 * 
	 * @param  string $key Key under which the value is stored.
	 * @param  mixed $value Inserted value.
	 * @return void
	 */
	public function insert(string $key, $value)
	{
		$this->_list[$key] = $value;

		if (!in_array($key, $this->_keyList)) {
			$this->_insertKey($key);
		}
	}

	/**
	 * Insert value before another value stored under given key.
	 *
	 * @param  string $beforeKey Key of the value before which input value will be added.
	 * @param  string $key Key under which the value is stored.
	 * @param  mixed $value Inserted value.
	 * @return void
	 */
	public function insertBefore(string $beforeKey, string $key, $value)
	{
		$this->_list[$key] = $value;

		$this->_insertKey($key, $beforeKey);
	}

	/**
	 * Insert value after another value stored under given key.
	 *
	 * @param  string $afterKey Key of the value after which input value will be added.
	 * @param  string $key Key under which the value is stored.
	 * @param  mixed $value Inserted value.
	 * @return void
	 */
	public function insertAfter(string $afterKey, string $key, $value)
	{
		$this->_list[$key] = $value;

		$this->_insertKey($key, $afterKey, true);
	}

	/**
	 * Get value stored under given key.
	 *
	 * @param  string $key Key under which the value is stored.
	 * @return mixed Value.
	 */
	public function get(string $key)
	{
		return $this->exists($key) ? $this->_list[$key] : null;
	}

	/**
	 * Remove value stored under given key.
	 *
	 * @param  string $key Key under which the value is stored.
	 * @return mixed Value.
	 */
	public function remove(string $key)
	{
		unset($this->_list[$key]);

		$this->_removeKey($key);
	}

	/**
	 * Check if value under given key exists.
	 *
	 * @param  string $key Key under which the value should be stored.
	 * @return bool
	 */
	public function exists(string $key)
	{
		return isset($this->_list[$key]);
	}

	/**
	 * Remove key from _keyList.
	 *
	 * @param  string $key Key which we want to remove.
	 * @return void
	 */
	private function _removeKey(string $key)
	{
		$keyIndex = array_search($key, $this->_keyList);

		if ($keyIndex !== false) {
			unset($this->_keyList[$keyIndex]);
		}

		$this->_keyList = array_values($this->_keyList);
	}

	/**
	 * Insert key into _keyList on the end of the list or before or after given $positionKey.
	 *
	 * @param  string $key Key which we want to remove.
	 * @return void
	 */
	private function _insertKey(string $key, string $positionKey = null, bool $after = false)
	{
		// remove key to prevent duplicit entries
		$this->_removeKey($key);

		if ($positionKey === null || !in_array($positionKey, $this->_keyList)) {
			$this->_keyList[] = $key;

			// reset _keyList keys
			$this->_keyList = array_values($this->_keyList);
			return;
		}

		$workingKeyList = [];

		foreach ($this->_keyList as $value) {
			if ($after === false && $positionKey === $value) {
				$workingKeyList[] = $key;
			}

			$workingKeyList[] = $value;

			if ($after === true && $positionKey === $value) {
				$workingKeyList[] = $key;
			}
		}

		$this->_keyList = $workingKeyList;
	}

	/**
	 * Countable interface methods.
	 */

	public function count()
	{
		return count($_list);
	}

	/**
	 * Iterator interface methods.
	 */

	public function current()
	{
		return $this->_list[$this->key()];
	}

	public function key()
	{
		return $this->_keyList[$this->_iterator];
	}

	public function next()
	{
		$this->_iterator++;
	}

	public function rewind()
	{
		$this->_iterator = 0;
	}

	public function valid()
	{
		return isset($this->_keyList[$this->_iterator]);
	}
}