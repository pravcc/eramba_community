<?php
App::uses('CrudView', 'Controller/Crud');
App::uses('FieldDataCollection', 'FieldData.Model/FieldData');
App::uses('ItemDataEntity', 'FieldData.Model/FieldData');

class InlineEditView extends CrudView {

	protected $_Item;
	protected $_Collection;
	protected $_Field;
	protected $_uuid;
	protected $_success;

	/**
	 * Initialize callback logic that sets all variables.
	 * 
	 * @return void
	 */
	public function initialize()
	{
		parent::initialize();

		$this->_setField();
		$this->_setCollection();
		$this->_setItem();
		$this->_setArgs();
		$this->_setSuccess();
	}

	public function getItem()
	{
		return $this->_Item;
	}

	public function getField()
	{
		return $this->_Field;
	}

	public function getCollection()
	{
		return $this->_Collection;
	}

	public function getUuid()
	{
		return $this->_uuid;
	}

	public function getSuccess()
	{
		return $this->_success;
	}

	protected function _setField()
	{
		$this->_Field = $this->_listener('InlineEdit')->getField();
	}

	protected function _setCollection()
	{
		$Collection = new FieldDataCollection([], $this->_model());
		$Collection->add($this->_Field->getFieldName(), $this->_Field);
		$Collection->add('inlineEdit', [
			'type' => 'hidden',
			'default' => '1',
			'editable' => true
		]);

		$this->_Collection = $Collection;
	}

	protected function _setItem()
	{
		$this->_Item = ItemDataEntity::newInstance($this->_model(), $this->getSubject()->request->data);
	}

	protected function _setArgs()
	{
		$args = $this->_listener('InlineEdit')->listArgs();

		$this->_uuid = $args->uuid;
	}

	protected function _setSuccess()
	{
		if (isset($this->getSubject()->success)) {
			$this->_success = $this->getSubject()->success;
		}
	}

}
