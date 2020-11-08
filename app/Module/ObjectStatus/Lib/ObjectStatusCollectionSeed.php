<?php
App::uses('ObjectStatusAppModel', 'ObjectStatus.Model');
App::uses('ItemDataEntity', 'FieldData.Model/FieldData');
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');

class ObjectStatusCollectionSeed
{
	public function seed($data)
	{
		$modelIds = [];

		$workingData = $data;

		if ($workingData instanceof ItemDataEntity) {
			$workingData = [$workingData];
		}

		$modelIds = $this->_getCollectionItemIds($workingData, $modelIds);

		if (empty($modelIds)) {
			return;
		}

		$statuses = $this->_getObjectStatuses($modelIds);

		$this->_seedStatusData($workingData, $statuses);
	}

	protected function _getCollectionItemIds($data, &$modelIds = [])
	{
		foreach ($data as $Item) {
			$modelIds[$Item->getModel()->name][$Item->getPrimary()] = $Item->getPrimary();

			$fields = array_keys($Item->getData());

			foreach ($fields as $field) {
				$FieldItem = $Item->{$field};

				if (!($FieldItem instanceof ItemDataCollection) && !($FieldItem instanceof ItemDataEntity)) {
					continue;
				}

				$FieldItem = ($FieldItem instanceof ItemDataCollection) ? $FieldItem : [$FieldItem];

				$this->_getCollectionItemIds($FieldItem, $modelIds);
			}
		}

		return $modelIds;
	}

	protected function _getObjectStatuses($modelIds)
	{
		$ObjectStatus = ClassRegistry::init('ObjectStatus.ObjectStatus');

		$conditions = [];

		foreach ($modelIds as $model => $ids) {
			$conditions['OR'][] = [
				'ObjectStatus.model' => $model,
				'ObjectStatus.foreign_key' => $ids
			];
		}

		$statuses = $ObjectStatus->find('all', [
			'conditions' => $conditions,
			'contain' => []
		]);

		$indexedStatuses = [];

		foreach ($statuses as $status) {
			$indexedStatuses[$status['ObjectStatus']['model'] . $status['ObjectStatus']['foreign_key']][] = $status['ObjectStatus'];
		}

		return $indexedStatuses;
	}

	protected function _seedStatusData($data, $statuses)
	{
		foreach ($data as $Item) {
			$key = $Item->getModel()->name . $Item->getPrimary();

			$fields = array_keys($Item->getData());

			$itemStatuses = isset($statuses[$key]) ? $statuses[$key] : [];
			$Item->add('ObjectStatus', $itemStatuses);

			foreach ($fields as $field) {
				$FieldItem = $Item->{$field};

				if (!($FieldItem instanceof ItemDataCollection) && !($FieldItem instanceof ItemDataEntity)) {
					continue;
				}

				$FieldItem = ($FieldItem instanceof ItemDataCollection) ? $FieldItem : [$FieldItem];

				$this->_seedStatusData($FieldItem, $statuses);
			}
		}
	}
}
