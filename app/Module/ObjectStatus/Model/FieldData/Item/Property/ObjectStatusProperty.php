<?php
App::uses('ClassRegistry', 'Utility');
App::uses('ItemDataProperty', 'FieldData.Model/FieldData/Item');
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');

/**
 * ObjectStatusProperty
 */
class ObjectStatusProperty extends ItemDataProperty
{
	public function setup(ItemDataEntity $Item, $config = [])
	{
	}

	public function getStatusesData(ItemDataEntity $Item)
	{
		$Model = $Item->getModel();

		$data = [];

		if (!$Model->Behaviors->enabled('ObjectStatus.ObjectStatus')) {
			return $data;
		}

		$data = $Item->ObjectStatus;

		if ($data === null) {
			$data = $this->_getStatuses($Item);
		}

		if (is_array($data) && !empty($data)) {
			$Collection = ItemDataCollection::newInstance(ClassRegistry::init('ObjectStatus.ObjectStatus'));

			foreach ($data as $item) {
				$Collection->add($item);
			}

			$data = $Collection;
		}

		return $data;
	}

	public function getStatusesConfig(ItemDataEntity $Item)
	{
		$Model = $Item->getModel();

		$data = [];

		if (!$Model->Behaviors->enabled('ObjectStatus.ObjectStatus')) {
			return $data;
		}

		return $Model->Behaviors->ObjectStatus->field($Model);
	}

	public function getStatusValue(ItemDataEntity $Item, $status)
	{
		$data = $this->getStatusesData($Item);

		$value = false;

		if (empty($data)) {
			return $value;
		}

		foreach ($data as $item) {
			if ($item->name == $status) {
				$value = $item->status;
				continue;
			}
		}

		return $value;
	}

	public function getItemStatus(ItemDataEntity $Item)
	{
		$priorities = [
			'success' => 1,
			'improvement' => 2,
			'info' => 3,
			'warning' => 4,
			'danger' => 5,
		];

		$priority = 1;

		$data = $this->getStatusesData($Item);
		$config = $this->getStatusesConfig($Item);

		if (!empty($data)) {
			foreach ($data as $item) {
				$statusConfig = (isset($config[$item->name])) ? $config[$item->name] : null;

				if ($statusConfig !== null 
					&& $item->status 
					&& $priorities[$statusConfig['type']] > $priority 
					&& empty($statusConfig['hidden'])
				) {
					$priority = $priorities[$statusConfig['type']];
				}
			}
		}

		return array_flip($priorities)[$priority];
	}

	protected function _getStatuses(ItemDataEntity $Item)
	{
		$data = $Item->getModel()->getItemObjectStatuses($Item->getPrimary());

		$Collection = ItemDataCollection::newInstance(ClassRegistry::init('ObjectStatus.ObjectStatus'));

		foreach ($data as $item) {
			$Collection->add($item);
		}

		return $Collection;
	}

}