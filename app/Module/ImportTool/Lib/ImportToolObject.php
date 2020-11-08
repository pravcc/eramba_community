<?php
App::uses('ImportToolArgument', 'ImportTool.Lib');
App::uses('ImportToolRow', 'ImportTool.Lib');
App::uses('UserFieldsBehavior', 'UserFields');

/**
 * Importable object that can be defined by given human readable value.
 */
class ImportToolObject
{
	/**
	 * Matching statuses.
	 */
	const STATUS_NO_MATCH = 0;
	const STATUS_PARTIAL_MATCH = 1;
	const STATUS_MATCH = 2;

	/**
	 * @var ImportToolArgument
	 */
	private $_argument = null;

	/**
	 * @var ImportToolRow
	 */
	private $_row = null;

	/**
	 * Object data.
	 * 
	 * @var mixed
	 */
	protected $_object = null;

	/**
	 * Find value.
	 * 
	 * @var mixed
	 */
	protected $_findValue = null;

	/**
	 * Matching status.
	 * 
	 * @var mixed
	 */
	protected $_status = null;

	/**
	 * Construct and assign input vars.
	 * 
	 * @param ImportToolArgument $argument
	 * @return void
	 */
	public function __construct(ImportToolArgument $argument, ImportToolRow $row)
	{
		$this->_argument = $argument;
		$this->_row = $row;
	}

	/**
	 * Find object by given human readable value.
	 * 
	 * @param string $value By this value we are searching for the object.
	 * @return mixed Object data.
	 */
	public function find($value)
	{
		$this->_setFindValue($value);

		$Model = $this->_getFindModel();

		$matchingField = $this->_getFindField();

		if ($Model instanceof User && strpos($value, UserFieldsBehavior::getGroupIdPrefix()) !== false) {
			$Model = $this->getArgument()->getModel()->{$this->getArgument()->getAssocationModelName() . 'Group'};
			$matchingField = "name";
			$value = str_replace(UserFieldsBehavior::getGroupIdPrefix(), '', $value);
		}
		elseif ($Model instanceof User) {
			$matchingField = "login";
			$value = str_replace(UserFieldsBehavior::getUserIdPrefix(), '', $value);
		}
		elseif ($Model instanceof Group) {
			$matchingField = "name";
		}

		$sanitizedValue = $this->_sanitizeValue($value);

		// try to find record by input value
		$data = $Model->find('first', [
			'conditions' => [
				"{$Model->alias}.{$matchingField}" => $value
			],
			'recursive' => -1
		]);

		// if data is empty try to find record by fulltext
		if (empty($data)) {
			$data = $Model->find('first', [
				'conditions' => [
					"MATCH({$Model->alias}.{$matchingField}) AGAINST(? IN BOOLEAN MODE)" => $sanitizedValue
				],
				'recursive' => -1
			]);
		}

		if (!empty($data)) {
			$ItemData = $Model->getItemDataEntity($data);

			$this->_setObject($ItemData);

			if ($ItemData->{$matchingField} == $value) {
				$this->_setStatus(self::STATUS_MATCH);
			}
			else {
				$this->_setStatus(self::STATUS_PARTIAL_MATCH);
			}
		}
		else {
			$this->_setStatus(self::STATUS_NO_MATCH);
		}

		return $this->getObject();
	}

	/**
	 * Get field against which we want to find.
	 * 
	 * @return string
	 */
	public function _getFindField()
	{
		return $this->_getFindModel()->displayField;
	}

	/**
	 * Get model where we want to find object.
	 * 
	 * @return Model
	 */
	protected function _getFindModel()
	{
		return $this->getArgument()->getModel()->{$this->getArgument()->getAssocationModelName()};
	}

	/**
	 * Get ImportToolArgument.
	 * 
	 * @return ImportToolArgument
	 */
	public function getArgument()
	{
		return $this->_argument;
	}

	/**
	 * Get ImportToolRow.
	 * 
	 * @return ImportToolRow
	 */
	public function getRow()
	{
		return $this->_row;
	}

	/**
	 * Set subject ImportToolArgument.
	 *
	 * @param mixed $object Object data.
	 * @return void
	 */
	protected function _setObject($object)
	{
		$this->_object = $object;
	}

	/**
	 * Get object data.
	 * 
	 * @return mixed
	 */
	public function getObject()
	{
		return $this->_object;
	}

	/**
	 * Get import value of object.
	 * 
	 * @return string|int
	 */
	public function getImportValue()
	{
		if ($this->getObject() === null) {
			return '';
		}
		elseif ($this->getObject()->getModel()->alias == 'Group') {
			return $this->getObject()->getPrimary();
		}
		elseif ($this->getObject()->getModel() instanceof User) {
			return UserFieldsBehavior::getUserIdPrefix() . $this->getObject()->getPrimary();
		}
		elseif ($this->getObject()->getModel() instanceof Group) {
			return UserFieldsBehavior::getGroupIdPrefix() . $this->getObject()->getPrimary();
		}

		return $this->getObject()->getPrimary();
	}

	/**
	 * Set find value.
	 *
	 * @param mixed $value Find value.
	 * @return void
	 */
	protected function _setFindValue($value)
	{
		$this->_findValue = $value;
	}

	/**
	 * Get find value.
	 * 
	 * @return mixed
	 */
	public function getFindValue()
	{
		return $this->_findValue;
	}

	/**
	 * Set matching status.
	 *
	 * @param int $status Status.
	 * @return void
	 */
	protected function _setStatus($status)
	{
		$this->_status = $status;
	}

	/**
	 * Get matching status.
	 * 
	 * @return int
	 */
	public function getStatus()
	{
		return $this->_status;
	}

	/**
	 * Sanitize search value for matching.
	 *
	 * @param string $value
	 * @return string
	 */
	protected function _sanitizeValue($value)
	{
		return preg_replace('/[+\-><\(\)~*\"@]+/', ' ', $value);
	}
}