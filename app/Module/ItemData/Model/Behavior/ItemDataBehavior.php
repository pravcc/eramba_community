<?php
App::uses('ModelBehavior', 'Model');
App::uses('ItemDataEntity', 'FieldData.Model/FieldData');
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');

/**
 * ItemDataBehavior
 */
class ItemDataBehavior extends ModelBehavior {
	/**
	 * Build ItemDataEntity instance from input data.
	 * 
	 * @param  Model $Model
	 * @param  array $data
	 * @return ItemDataEntity
	 */
	public function getItemDataEntity(Model $Model, $data)
	{
		return ItemDataEntity::newInstance($Model, $data);
	}

	/**
	 * Build ItemDataCollection instance from current model.
	 * 
	 * @param  Model $Model
	 * @return ItemDataCollection
	 */
	public function getItemDataCollection(Model $Model)
	{
		return ItemDataCollection::newInstance($Model);
	}
}