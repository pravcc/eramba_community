<?php
App::uses('ItemDataProperty', 'FieldData.Model/FieldData/Item');
App::uses('ItemDataEntity', 'FieldData.Model/FieldData');
App::uses('AuthComponent', 'Controller/Component');

class WidgetProperty extends ItemDataProperty
{
	protected $_data = [];

	public function setWidgetData(ItemDataEntity $Item, $data)
	{
		$this->_data = $data;
	}

	public function getWidgetData(ItemDataEntity $Item)
	{
		return $this->_data;
	}

	public function commentsCount()
	{
		$count = 0;

		if (isset($this->_data['comments']['count_total'])) {
			$count = $this->_data['comments']['count_total'];
		}

		return $count;
	}

	public function unseenCommentsCount()
	{
		$count = 0;

		$userId = AuthComponent::user('id');

		if (isset($this->_data['comments']['user_data'][$userId]['count_unseen'])) {
			$count = $this->_data['comments']['user_data'][$userId]['count_unseen'];
		}

		return $count;
	}

	public function attachmentsCount()
	{
		$count = 0;

		if (isset($this->_data['attachments']['count_total'])) {
			$count = $this->_data['attachments']['count_total'];
		}

		return $count;
	}

	public function unseenAttachmentsCount()
	{
		$count = 0;

		$userId = AuthComponent::user('id');

		if (isset($this->_data['attachments']['user_data'][$userId]['count_unseen'])) {
			$count = $this->_data['attachments']['user_data'][$userId]['count_unseen'];
		}

		return $count;
	}
}