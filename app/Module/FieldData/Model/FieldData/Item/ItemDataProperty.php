<?php
App::uses('CakeObject', 'Core');

class ItemDataProperty extends CakeObject
{
	
	/**
	 * Contains configuration settings for use with individual ItemDataEntity objects. This
	 * is used because if multiple ItemDataEntities use this Property, each will use the same
	 * object instance.
	 *
	 * @var array
	 */
	public $settings = [];

	/**
	 * Setup this property with the specified configuration settings.
	 *
	 * @param ItemDataEntity
	 * @param array $config Configuration settings.
	 * @return void
	 */
	public function setup(ItemDataEntity $Item, $config = [])
	{
	}

}