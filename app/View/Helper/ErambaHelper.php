<?php
App::uses('AppHelper', 'View/Helper');

class ErambaHelper extends AppHelper {
	public $helpers = array('Html', 'Text');
	public $settings = array();
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
		$this->settings = $settings;
	}

	public function getNotificationBox($text, $options = array()) {
		$class = 'alert alert-info ';
		if (isset($options['class'])) {
			$class .= (is_array($options['class'])) ? implode(' ', $options['class']) : $options['class'];
		}

		return $this->Html->div(
			$class, 
			'<i class="icon-exclamation"></i> ' . $this->getEmptyValue($text),
			$options
		);
	}

	/**
	 * SQL debug helper function.
	 */
	public function getQueryLogs() {
		return getQueryLogs();
	}

	/**
	 * Generates custom logo if provided, otherwise given default.
	 */
	public function getLogo($defaultUrl = DEFAULT_LOGO_WHITE_URL, $fullPath = false, $attrs = array()) {
		$attrs = am($attrs, array(
			'alt' => 'Eramba'
		));

		$url = $defaultUrl;
		if (!empty(Configure::read('Eramba.Settings.CUSTOM_LOGO'))) {
			$url = $this->getCustomLogoUrl();
		}

		if ($fullPath) {
			$url = Router::url($url, true);
		}

		return $this->Html->image($url, $attrs);
	}

	/**
	 * Get the controller action url that shows logo to aviod direct ruting.
	 */
	public function getCustomLogoUrl($fullPath = false) {
		$url = array(
			'controller' => 'settings',
			'action' => 'getLogo',
			'plugin' => null,
			'admin' => false
		);

		if ($fullPath) {
			return Router::url($url, true);
		}

		return $url;
	}

	/**
	 * Sets empty cell value if empty value provided.
	 */
	public function getEmptyValue($value = null, $forceNum = false) {
		return getEmptyValue($value, $forceNum);
	}

	/**
	 * Get truncated text with a tooltip containing a real text.
	 */
	public function getTruncatedTooltip($text, $options = array()) {
		$options = am(array(
			'title' => __('Help'),
			'content' => $text,
			'truncate' => true,
			'truncateLength' => 30,
			'placement' => 'top',
			'container' => false,
			'inline' => false,
			'class' => '',
			'icon' => true
		), $options);

		$icon = '';
		if ($options['icon']) {
			$icon = $this->Html->tag('i', false, array(
				'class' => 'icon-info-sign'
			));
		}

		if (!empty($options['truncate'])) {
			$text = $this->Text->truncate($text, $options['truncateLength']);
		}

		$div = $this->Html->div('bs-popover ' . $options['class'], $text . ' ' . $icon, array(
			'data-trigger' => 'hover',
			'data-placement' => $options['placement'],
			'data-html' => 'true',
			'data-container' => $options['container'],
			'data-original-title' => $options['title'],
			'data-content' => $this->getEmptyValue($options['content']),
		));

		return $div;
	}

	/**
	 * encodes data to json, escapes single quote marks 
	 * 
	 * @param  mixed $data
	 * @return string
	 */
	public function jsonEncode($data) {
		return str_replace("'", "\'", json_encode($data));
	}

	/**
	 * @deprecated  Use LabelHelper instead.
	 */
	public function getLabel($text, $type = 'success') {
		if ($type == 'error') {
			$type = 'danger';
		}
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

		if (is_string($allow) && $allow === '*' && $config !== INHERIT_CONFIG_KEY) {
			return true;
		}
		
		if (is_array($allow) && in_array($config, $allow)) {
			return true;
		}

		return false;

		/*$cond = $allow === '*' && $config !== INHERIT_CONFIG_KEY;
		$cond = $cond || (is_array($allow) && in_array($config, $allow));
		$cond = $cond || (is_array($allow) && in_array($config, $allow));*/

		// return ($allow === '*' || (is_array($allow) && in_array($config, $allow)));
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

	protected function getInheritedStatuses($item, $inheritOptions = array()) {
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

	protected function inheritItemStatus($item, $inheritModel, $configs = '*') {
		if (!isset($item[$inheritModel])) {
			appError("This item is missing status array data for: " . $inheritModel);
		}

		if (!empty($item[$inheritModel])) {
			if (isset($item[$inheritModel][0])) {
				$statuses = array();
				foreach ($item[$inheritModel] as $i) {
					$statuses = array_merge($statuses, $this->getStatusArr($i, $configs, $inheritModel));
				}

				return $statuses;
			}
			else {
				return $this->getStatusArr($item, $configs, $inheritModel);
			}
		}

		return array();
	}

	public function getHeaderClass($item, $modelName, $allow = true) {
		$statuses = $this->getStatusArr($item, $allow, $modelName);
		$type = $this->getColorType($statuses);
		$class = $this->processHeaderType($type);

		return $class;
	}

	public function getStatusClass($item, $modelName = null, $allow = '*') {
		$statuses = $this->getStatusArr($item, $allow, $modelName);
		$type = $this->getColorType($statuses);

		return $type;
	}

	/**
	 * Get worst color type from array of statuses.
	 */
	protected function getColorType($statuses = array()) {
		$types = array();
		foreach ($statuses as $status) {
			$types[] = $status['type'];
		}

		return getWorstColorType($types);
	}

	protected function processHeaderType($type) {
		return 'widget-status ' . $type;
	}

	/**
	 * Wrapper function to process group of statuses from a single helper.
	 */
	protected function processStatusesGroup($statuses) {
		return $statuses;
	}

	/**
	 * Returns expired label based on expired status.
	 */
	protected function getExpiredByStatusLabel($expired, $options = array()) {
		$defaults = array(
			'showNotExpiredLabel' => false
		);

		$options = array_merge($defaults, $options);

		if ($expired) {
			return $this->Html->tag('span', __('Expired'), array('class' => 'label label-danger'));
		}
		else {
			if ($options['showNotExpiredLabel'] === true) {
				return $this->Html->tag('span', __('Not Expired'), array('class' => 'label label-success'));
			}
		}

		return false;
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
	protected function normalizeItemArray($item, $model) {
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