<?php

trait TooltipsTrait {
	/**
	 * Create new Tooltip object or one of its child objects (TooltipRow, TooltipColumn)
	 * @param  string       $objectsArrayName          Name of array in the class where objects are stored
	 * @param  array        $options                   Set values to params of the class
	 * @return Tooltip|TooltipRow|TooltipColumn  Returns Tooltip object - existing or newly created
	 */
	protected function createObject(string $objectClassName, string $objectsArrayName, array $options = [])
	{
		$objectName = $objectClassName . (count($this->{$objectsArrayName}) + 1);
		$object = $this->{$objectsArrayName}[$objectName] = new $objectClassName($objectName);
		$object->applyOptions($options);

		return $object;
	}
}