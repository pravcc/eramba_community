<?php
App::uses('ItemDataProperty', 'FieldData.Model/FieldData/Item');
App::uses('ItemDataEntity', 'FieldData.Model/FieldData');

class SoftDeleteProperty extends ItemDataProperty
{
	/**
	 * Check if item is deleted.
	 */
	public function isDeleted(ItemDataEntity $Item)
	{
		$ret = false;

		$deleted = $Item->deleted;

		if ($Item->getModel()->Behaviors->enabled('Utils.SoftDelete') && !empty($deleted)) {
			$ret = true;
		}

		return $ret;
	}
}