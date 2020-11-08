<?php
App::uses('DashboardAppModel', 'Dashboard.Model');

class DashboardLog extends DashboardAppModel {
	public $useTable = 'logs';

	public $validate = [
		'type' => array(
			'inList' => [
				'rule' => ['inList', [
					self::TYPE_RECALCULATION,
					self::TYPE_STORED_VALUES
				]],
				'message' => 'This type is not supported'
			]
		)
	];

	/*
	 * Available types for appetite classifications.
	 */
	 public static function types($value = null) {
		$options = array(
			self::TYPE_RECALCULATION => __('Recalculation'),
			self::TYPE_STORED_VALUES => __('Stored Values')
		);
		return parent::enum($value, $options);
	}
	const TYPE_RECALCULATION = 0;
	const TYPE_STORED_VALUES = 1;

	/**
	 * Check if there is some record of event with a specific type.
	 * 
	 * @return boolean       True if there is some record, False otherwise.
	 */
	public function hasEvent($type)
	{
		$event = $this->find('count', [
			'conditions' => [
				'DashboardLog.type' => $type
			],
			'recursive' => -1
		]);

		return (bool) $event;
	}

	/**
	 * Retrieve the last event date by type.
	 */
	public function getLastEvent($type)
	{
		$event = $this->find('first', [
			'conditions' => [
				'DashboardLog.type' => $type
			],
			'order' => [
				'DashboardLog.created' => 'DESC'
			],
			'recursive' => -1
		]);

		if (!empty($event)) {
			return $event['DashboardLog']['created'];
		}

		return false;
	}

}
