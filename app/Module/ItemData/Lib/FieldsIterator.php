<?php
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('CellOutput', 'AdvancedFilters.View/Renderer/Output');

class FieldsIterator implements Iterator
{
	const VALUE_RAW = 1;
	const VALUE_NICE = 2;
	const VALUE_ITEM = 3;
	const VALUE_ALL = 4;

	protected $_Item = null;
	protected $_Field = null;

	protected $_valueType = self::VALUE_ALL;

	protected $_data = [];

	public function __construct($Item, $Field, $valueType = self::VALUE_ALL)
	{
		$this->_Item = $Item;
		$this->_Field = $Field;
		$this->_valueType = $valueType;

		$this->_init();
	}

	public function _init()
	{
		$Items = ($this->_Item instanceof ItemDataCollection) ? $this->_Item : [$this->_Item];

		foreach ($Items as $Item) {
			if ($this->_Field->isAssociated()) {
				$AssocItems = $this->_getAssocItems($Item, $this->_Field);

				if (empty($AssocItems) || empty($AssocItems[0])) {
					continue;
				}

				foreach ($AssocItems as $AssocItem) {
					if ($AssocItem->getModel()->displayField !== null
						&& $AssocItem->getModel()->Behaviors->enabled('FieldData.FieldData')
						&& $AssocItem->getModel()->hasFieldDataEntity($AssocItem->getModel()->displayField)
					) {
						$Field = $AssocItem->getModel()->getFieldDataEntity($AssocItem->getModel()->displayField);

						$rawValue = $AssocItem->Properties->FieldData->value($AssocItem, $Field);
						$niceValue = $AssocItem->Properties->FieldData->value($AssocItem, $Field, false);
					}
					else {
						$rawValue = $AssocItem->getPrimary();
						$niceValue = $AssocItem->{$AssocItem->getModel()->displayField};
					}

					$this->_data[CellOutput::getKey($AssocItem, $this->_Field)] = [
						'raw' => $rawValue,
						'nice' => $niceValue,
						'item' => $Item
					];
				}
			}
			else {
				$rawValue = $Item->Properties->FieldData->value($Item, $this->_Field);
				$niceValue = $Item->Properties->FieldData->value($Item, $this->_Field, false);

				$this->_data[CellOutput::getKey($Item, $this->_Field)] = [
					'raw' => $rawValue,
					'nice' => $niceValue,
					'item' => $Item
				];
			}
		}
	}

	public function _getAssocItems($Item, $Field)
	{
		$AssocItems = [];

		if ($Item->getModel()->Behaviors->enabled('UserFields.UserFields')
			&& in_array($Field->getFieldName(), $Item->getModel()->getAllAssociations())
		) {
			$assocs = $Item->getModel()->getAssociationsByField($Field->getFieldName());

			foreach ($assocs as $assoc) {
				$UserFieldItems = $Item->{$assoc};

				if (!empty($UserFieldItems)) {
					foreach ($UserFieldItems as $UserFieldItem) {
						$AssocItems[] = $UserFieldItem;
					}
				}
			}
		}
		else {
			$AssocItems = $Item->{$this->_Field->getAssociationModel()};

			if (!($AssocItems instanceof ItemDataCollection)) {
				$AssocItems = [$AssocItems];
			}
		}

		return $AssocItems;
	}

	public function rewind()
	{
		$item = reset($this->_data);

		return $this->_getValue($item);
	}

	public function current()
	{
		$item = current($this->_data);

		return $this->_getValue($item);
	}

	public function key()
	{
		return key($this->_data);
	}

	public function next()
	{
		$item = next($this->_data);

		return $this->_getValue($item);
	}

	public function valid()
	{
		return key($this->_data) !== null;
	}

	protected function _getValue($value)
	{
		if ($this->_valueType == self::VALUE_RAW) {
			return $value['raw'];
		}
		elseif ($this->_valueType == self::VALUE_NICE) {
			return $value['nice'];
		}
		elseif ($this->_valueType == self::VALUE_ITEM) {
			return $value['item'];
		}

		return $value;
	}
}
