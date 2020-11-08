<?php
App::uses('SectionInterface', 'View/Helper');
App::uses('AppHelper', 'View/Helper');

abstract class SectionBaseHelper extends AppHelper /*implements SectionInterface */{
	public $helpers = ['Html', 'Ux', 'Taggable', 'Users', 'Ajax'];

	/**
	 * Model a current section helper maps to.
	 * 
	 * @var mixed
	 */
	protected $_mapModel = null;

	public function __construct(View $view, $settings = array()) {
		if ($this->_mapModel === null) {
			$name = substr(get_class($this), 0, -6);
			$this->_mapModel = Inflector::singularize($name);
		}

		parent::__construct($view, $settings);
	}

	/**
	 * Action list specific for a certain section.
	 * 
	 * @param  array  $item    Item data array.
	 * @param  array  $options Options.
	 */
	public function actionList($item, $options = []) {
		$_model = $this->mappedModel();
		$Model = ClassRegistry::init($_model);
		$_primaryKey = $Model->primaryKey;

		$options['item'] = $item;
		$options['model'] = $_model;

		return $this->Ajax->getActionList($item[$_model][$_primaryKey], $options);
	}

	/**
	 * @deprecated use Helper::actionList() instead.
	 */
	public function getActionList() {
		return call_user_func_array([$this, 'actionList'], func_get_args());
	}

	/**
	 * Model currently mapped for this helper class.
	 * 
	 * @return string Model name.
	 */
	public function mappedModel() {
		return $this->_mapModel;
	}


/*********************************************************************************************************/
/** Below is soon to be removed deprecated code, here only to make helpers compatible with new features **/
/*********************************************************************************************************/


	public function getStatusList($item) {
		// return $this->getStatuses($item);
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

	protected function getStatusKey($config, $model = null) {
		$key[] = $this->_View->name;
		$key[] = get_class($this);
		
		if (!empty($model)) {
			$key[] = $model;
		}

		$key[] = $config;
		return implode('_', $key);
	}

	public function getHeaderClass($item, $modelName, $allow = true) {
		$statuses = $this->getStatusArr($item, $allow, $modelName);
		$type = $this->getColorType($statuses);
		$class = $this->processHeaderType($type);

		return $class;
	}

}
