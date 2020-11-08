<?php

trait TablesTrait {
	/**
	 * Create new Table object or one of its child objects (TableRow, TableColumn)
	 * @param  string       $objectsArrayName          Name of array in the class where objects are stored
	 * @param  array        $options                   Set values to params of the class
	 * @return Table|TableRow|TableColumn  Returns Table object - existing or newly created
	 */
	protected function createObject(string $objectClassName, string $objectsArrayName, array $options = [])
	{
		if (!isset($this->{$objectsArrayName})) {
			$this->{$objectsArrayName} = [];
		}

		$objectName = $objectClassName . (count($this->{$objectsArrayName}) + 1);
		$object = $this->{$objectsArrayName}[$objectName] = new $objectClassName($objectName);
		$object->setBlockType(!empty($this->blockType) ? $this->blockType : 'table');
		$object->applyOptions($options);

		return $object;
	}
}