<?php
App::uses('ItemDataProperty', 'FieldData.Model/FieldData/Item');

/**
 * AdvancedFiltersProperty class to manage FilterField class instances.
 */
class AdvancedFiltersProperty extends ItemDataProperty
{
	public function setup(ItemDataEntity $Item, $config = [])
	{
	}

	public function test(ItemDataEntity $Item, FilterField $FilterField)
	{
		$fieldName = $FilterField->getFieldName();
		$fieldData = $FilterField->getFieldDataConfig();

		if ($fieldData !== null) {
			$traverse = explode('.', $fieldData);
			$fieldName = array_pop($traverse);

			if (!empty($traverse)) {
				foreach ($traverse as $where) {
					if (!$Item->{$where}) {
						trigger_error(sprintf('Traverser pointer "%s" does not exist!', $where));
						return false;
					}

					$Item = $Item->{$where};
				}
			}

			if ($Item instanceof ItemDataCollection) {
				foreach ($Item as $I) {
					$val[] = $this->_parseFieldData($I, $fieldName);
				}

				return $val;
			}

		}

		return $this->_parseFieldData($Item, $fieldName);
	}

	protected function _parseFieldData(ItemDataEntity $Item, $fieldName)
	{
		if ($Item->Properties->FieldData === null) {
			trigger_error('FieldDataProperty on ItemDataProperty doesnt exist!');
			return false;
		}

		return $Item->Properties->FieldData->value($Item, $fieldName);
	}
}