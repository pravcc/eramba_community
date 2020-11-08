<?php
App::uses('AppHelper', 'View/Helper');

// define('INHERIT_CONFIG_KEY', '_inherit');
class StatusHelper extends AppHelper {
	const INHERIT_CONFIG_KEY = '_inherit';

	public $helpers = array('Html');

	protected function getLabel($text, $type = 'success') {
		return $this->Html->tag('span', $text, array(
			'class' => 'label label-' . $type
		));
	}

	public function styleStatuses($statuses = array(), $options = array()) {
		$styled = array();
		foreach ($statuses as $status) {
			$styled[] = $this->getLabel($status['label'], $status['type']);
		}

		return $this->processStatuses($styled, $options);
	}

	/**
	 * Wrapper function to process all statuses for an item.
	 */
	public function processStatuses($statuses = array(), $options = array()) {
		if (!empty($options['raw'])) {
			return $statuses;
		}

		$options = array_merge(
			array(
				'inline' => true
			),
			(array) $options
		);

		if (empty($statuses)) {
			$statuses[] = $this->getLabel(__('Ok'), 'success');
		}

		return implode(($options['inline'] ? '<br />' : '<br />'), $statuses);
	}

	public function processStatusOptions($options) {
		$allow = '*';
		if (is_bool($options) && $options === true) {
			$allow = true;
		}
		else {
			if (isset($options['allow'])) {
				$allow = $options['allow'];
			}
		}

		return array(
			'allow' => $allow
		);
	}

	public function getAllowCond($allow, $config) {
		if (is_bool($allow) && $allow === true) {
			return true;
		}

		if (is_string($allow) && $allow === '*' && $config !== self::INHERIT_CONFIG_KEY) {
			return true;
		}

		if (is_array($allow) && in_array($config, $allow)) {
			return true;
		}

		return false;
	}

	public function getStatusKey($config, $model = null) {
		$key[] = $this->_View->name;
		$key[] = get_class($this);
		
		if (!empty($model)) {
			$key[] = $model;
		}

		$key[] = $config;
		return implode('_', $key);
	}

	public function getInheritedStatuses($item, $inheritOptions = array()) {
		$statuses = array();
		foreach ($inheritOptions as $helperName => $option) {
			if (isset($option[0])) {
				foreach ($option as $opt) {
					$statuses = am($statuses, $this->getInheritedSingleStatus($item, $helperName, $opt));
				}
			}
			else {
				$statuses = am($statuses, $this->getInheritedSingleStatus($item, $helperName, $option));
			}
		}

		return $statuses;
	}

	private function getInheritedSingleStatus($item, $helperName, $option) {
		if (empty($this->{$helperName})) {
			$this->{$helperName} = $this->_View->loadHelper($helperName);
		}

		if (!isset($option['config'])) {
			$option['config'] = '*';
		}

		return $this->{$helperName}->inheritItemStatus($item, $option['model'], $option['config']);
	}

	

	/**
	 * Get worst color type from array of statuses.
	 */
	public function getColorType($statuses = array()) {
		$types = array();
		foreach ($statuses as $status) {
			$types[] = $status['type'];
		}

		return getWorstColorType($types);
	}

	public function processHeaderType($type) {
		return 'widget-status ' . $type;
	}

	/**
	 * Wrapper function to process group of statuses from a single helper.
	 */
	public function processStatusesGroup($statuses) {
		return $statuses;
	}

	public function processItemArray($item, $model) {
		if (!isset($item[$model])) {
			$item = $this->normalizeItemArray($item, $model);
		}

		return $item;
	}

	/**
	 * Make joined item array data normalized.
	 */
	public function normalizeItemArray($item, $model) {
		$arr = array($model => array());
		$joins = array();
		foreach ($item as $key => $val) {
			if (is_array($val)) {
				$joins[$key] = $val;
			}
			else {
				$arr[$model][$key] = $val;
			}
		}

		$arr = array_merge($arr, $joins);

		return $arr;
	}
}