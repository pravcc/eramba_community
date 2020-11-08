<?php
App::uses('ItemDataEntity', 'FieldData.Model/FieldData');
App::uses('CakeText', 'Utility');

class LegalItemData extends ItemDataEntity
{
	public function __construct(Model $Model, $data)
	{
		parent::__construct($Model, $data);
	}
}