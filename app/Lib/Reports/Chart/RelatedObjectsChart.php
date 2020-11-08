<?php
App::uses('TreeChart', 'Reports.Lib/Chart');
App::uses('CakeText', 'Utility');

class RelatedObjectsChart extends TreeChart
{
	/**
	 * Update chart configuration according to subject data.
	 * 
	 * @param $subject Subject.
	 * @return void
	 */
	public function setData($subject)
	{
		$assocData = [];

		foreach ($subject->assoc as $key => $config) {
			$assocName = (is_string($config)) ? $config : $key;

			$AssocCollection = $subject->item->{$assocName};

			$items = [];

			if (!empty($AssocCollection)) {
				foreach ($AssocCollection as $Item) {
					$items[] = [
						'name' => CakeText::truncate($Item->{$Item->getModel()->displayField}, 50)
					];
				}
			}

			$assocLabel = (!empty($config['label'])) ? $config['label'] : $AssocCollection->getModel()->label(['singular' => true]);

			if (empty($items)) {
				$assocLabel .= ' ' . __('(Empty)');
			}

			$assocData[] = [
				'name' => $assocLabel,
				'children' => $items
			];
		}

		$data = [
			[
				'name' => $subject->item->{$subject->item->getModel()->displayField},
				'children' => $assocData
			]
		];

		$this->addSeries(['data' => $data]);
	}
}
