<?php
App::uses('Component', 'Controller');
App::uses('AdvancedFilter', 'Model');
App::uses('AdvancedFiltersData', 'Lib');
App::uses('AbstractQuery', 'Lib/AdvancedFilters/Query');
App::uses('AdvancedFilterCron', 'Model');
App::uses('AdvancedFilterUserSetting', 'Model');
App::uses('Inflector', 'Utility');

class AdvancedFiltersComponent extends Component {
	const FILTER_TYPE_INDEX = 'index';
	const FILTER_TYPE_TRASH = 'trash';
	const FILTER_TYPE_CSV = 'csv';
	const FILTER_TYPE_PDF = 'pdf';
	const FILTER_TYPE_CRON_DATA = 'cron_data';
	const FILTER_TYPE_CRON_COUNT = 'cron_count';

	protected $_defaults = array();

	private $model;

	private $pageLimit = ADVANCED_FILTER_DEFAULT_PAGE_LIMIT;
	private $maxSelectionSize = 10; 

	private $error = false;
	private $errorMessage = '';

	private $activeFilter = null;

	public $components = array('Search.Prg', 'Paginator', 'Crud.Crud');

	public function initialize(Controller $controller) {
		$this->controller = $controller;
	}

	public function setSettings($model = null) {
		if ($model === null) {
			$model = $this->model;
		}

		if (empty($this->settings)) {
			$this->controller->loadModel($model);

			$Model = ClassRegistry::init($model);

			if ($Model->Behaviors->enabled('AdvancedFilters.AdvancedFilters')) {
				$Model->buildAdvancedFilterArgs();
			}

			$this->settings = array(
				'model' => $model,
				'advancedFilter' => $this->controller->{$model}->advancedFilter,
				'advancedFilterSettings' => $this->controller->{$model}->advancedFilterSettings
			);
		}

		// $this->settings = $this->AdvancedFiltersExtented->processFilterSettings($this->settings);
	}

	/**
	 * getting fitered data
	 * 
	 * @param  $type
	 * @return mixed $data
	 */
	protected function _getData($type) {
		//conditions
		$conditions = $this->getConditions();
		if ($type == self::FILTER_TYPE_TRASH) {
			$conditions[$this->model . '.deleted'] = true;
		}

		//fields
		$getDefautFields = ($this->getSelectionSize() < 1) ? true : false;
		$fields = $this->getFields($getDefautFields);

		//limit
		$limit = $this->_getPageLimit($type);

		$options = $this->_getFindOptions($conditions, $fields, $limit);

		if ($type == self::FILTER_TYPE_CRON_DATA) {
			$data = $this->controller->{$this->model}->find('all', $options);

			//SecurityService, SecurityPolicy attach compliance data
			if (in_array($this->model, ['SecurityService', 'SecurityPolicy'])) {
				$this->controller->{$this->model}->ComplianceManagement->attachCompliancePackageData($data);
			}
		}
		elseif ($type == self::FILTER_TYPE_CRON_COUNT) {
			$data = $this->controller->{$this->model}->find('count', $options);
			//NOTE: Count query above can return false on empty result, its caused by group in query.
			if ($data === false) {
				$data = 0;
			}
		}
		elseif (in_array($type, array(self::FILTER_TYPE_INDEX, self::FILTER_TYPE_TRASH))) {
 			$this->Paginator->settings = $options;

			if ($this->_Collection->enabled('Crud')) {
				$subject = $this->Crud->trigger('beforePaginate', array(
					'paginator' => $this->Paginator,
					'success' => true,
					'viewVar' => 'data'
				));
			}

			$data = $this->Paginator->paginate($this->model);

			if ($this->_Collection->enabled('Crud')) {
				$subject = $this->Crud->trigger('afterPaginate', array(
					'success' => $subject->success,
					'items' => $data,
					'viewVar' => 'data'
				));
				$data = $subject->items;
			}

			$resultCount = $this->controller->request['paging'][$this->model]['count'];

			$this->setPagingCount($resultCount);
		}
		else {
			unset($options['limit']);
			$data = $this->controller->{$this->model}->find('all', $options);

			//SecurityService, SecurityPolicy attach compliance data
			if (in_array($this->model, ['SecurityService', 'SecurityPolicy'])) {
				$this->controller->{$this->model}->ComplianceManagement->attachCompliancePackageData($data);
			}

			$this->controller->set('pagingCount', count($data));

			$resultCount = count($data);

			$this->setPagingCount($resultCount);
		}

		return $data;
	}

	protected function setPagingCount($count) {
		return $this->controller->set('pagingCount', $count);
	}

	/**
	 * returns formated find options data
	 * 
	 * @param  array $conditions
	 * @param  array $fields
	 * @param  mixed $limit
	 * @return array $options
	 */
	private function _getFindOptions($conditions, $fields, $limit) {
		$options = array(
			'conditions' => $conditions,
			'fields' => $fields['fields'],
			'contain' => $fields['contain'],
			'joins' => $fields['joins'],
			'order' => (!empty($this->Paginator->settings['order'])) ? $this->Paginator->settings['order'] : [],
			'group' => array($this->model . '.id'),
		);

		if (!empty($limit)) {
			$options['limit'] = $limit;
		}

		return $options;
	}

	/**
	 * returns find limit acording to type
	 *
	 * @param  string $type
	 * @return int|false $limit
	 */
	protected function _getPageLimit($type) {
		$limit = ADVANCED_FILTER_DEFAULT_PAGE_LIMIT;

		if ($type == self::FILTER_TYPE_CRON_COUNT) {
			$limit = false;
		}
		elseif ($type == self::FILTER_TYPE_CRON_DATA) {
			$limit = AdvancedFilterCron::EXPORT_ROWS_LIMIT;
		}
		elseif (!empty($this->controller->request->query['_limit']) 
			&& in_array($this->controller->request->query['_limit'], getFilterPageLimits())
			&& !in_array($type, array(self::FILTER_TYPE_CSV, self::FILTER_TYPE_PDF))
		) {
			$limit = $this->controller->request->query['_limit'];
		}

		return $limit;
	}

	/**
	 * common init filter calls
	 * 
	 * @param  string $model
	 */
	public function commonProcess($model) {
		$this->model = $model;

		$this->setSettings();
		$this->setFilterData();
		$this->setFilterSettings();
		$this->setComparisonTypes($this->controller->request->query);
	}

	/**
	 * return count or data of filter results for cron logs
	 * 
	 * @param  array $filter
	 * @param  $cronType - cron result type (count/data)
	 * @return mixed - result from DB
	 */
	public function filterCron($filter, $cronType) {
		$this->commonProcess($filter['AdvancedFilter']['model']);

		$type = ($cronType == AdvancedFilterCron::TYPE_DATA) ? self::FILTER_TYPE_CRON_DATA : self::FILTER_TYPE_CRON_COUNT;

		$result = $this->_getData($type);

		unset($this->controller->request->data[$this->model]);

		return $result;
	}

	public function filter($model, $view = 'html', $type = self::FILTER_TYPE_INDEX) {
		$this->commonProcess($model);

		$this->presetHandler($type);

		$this->setSavedFilters();

		$defaultIndex = $this->controller->AdvancedFilter->getDefault($model, $this->controller->logged['id']);

		// if (!empty($this->controller->request->query['force_default_index']) 
		// 	|| ($defaultIndex === false && empty($this->controller->request->query['advanced_filter']))
		// ) {
		// 	$this->Prg->commonProcess($this->model, array(
		// 		'filterEmpty' => true,
		// 		'excludedParams' => $this->cleanDefaultsBeforeProcess()
		// 	));

		// 	return false;
		// }

		// $data = $this->_getData($type);
		$this->controller->set('data', $data);

		$modelConfig = array();
		if (isset($this->controller->{$this->model}->config['actionList'])) {
			$modelConfig = $this->controller->{$this->model}->config['actionList'];
		}
		$this->controller->set('modelConfig', $modelConfig);

		// debug($data);

		unset($this->controller->viewVars['showHeader']);

		return $this->_setRender($type);;
	}

	/**
	 * sets view to render
	 * 
	 * @param $type
	 */
	protected function _setRender($type) {
		$elem = 'index';

		if ($type == self::FILTER_TYPE_TRASH) {
			$elem = 'trash';
		}

		if ($this->_Collection->enabled('Crud')) {
			$this->Crud->trigger('beforeRender', array('success' => true, 'advancedFilters' => true));
		}

		return $this->controller->render(DS . 'Elements' . DS . ADVANCED_FILTERS_ELEMENT_PATH . $elem);
	}

	/**
	 * handling of active filter or default index, setting of required active data
	 * 
	 * @param  int $type
	 * @return boolean
	 */
	public function presetHandler($type) {
		if ($type == self::FILTER_TYPE_TRASH) {
			return true;
		}

		if (!empty($this->controller->request->query['force_default_index'])) {
			return false;
		}

		$activeFilterId = $this->_getActiveFilterId();

		$activeFilter = $this->controller->AdvancedFilter->getFilter($activeFilterId);
		if (empty($activeFilter)) {
			return false;
		}

		$this->setActiveFilterData($activeFilter);

		if ($this->controller->AdvancedFilter->isDefault($activeFilterId)) {
			$this->buildRequest($activeFilterId);
		}

		return true;
	}

	/**
	 * returns AdvancedFilter.id of active filter
	 * 
	 * @return int AdvancedFilter.id | false
	 */
	protected function _getActiveFilterId() {
		$id = false;

		if (!empty($this->controller->request->query['advanced_filter_id'])) {
			$id = $this->controller->request->query['advanced_filter_id'];
		}
		else {
			$id = $this->controller->AdvancedFilter->getDefault(
				$this->model,
				$this->controller->logged['id']
			);
		}

		return $id;
	}

	/**
     * build request query/data from stored filter values
     * 
     * @param  int $filterId
     * @param  string $type query|data
     * @param  null|string $model
     */
    public function buildRequest($filterId, $type = 'query', $model = null) {
    	$values = $this->controller->AdvancedFilter->getFilterValues($filterId);

    	if (empty($values)) {
    		return false;
    	}

    	$requestData = AdvancedFilter::buildValues($values);

    	if ($type == 'data') {
    		$requestData = array($model => $requestData);
    	}

    	$this->controller->request->$type += $requestData;
    }

	/**
	 * Skip parameters having default value for shorter querystring.
	 *
	 * @see http://httpd.apache.org/docs/2.2/mod/core.html#limitrequestline
	 */
	protected function cleanDefaultsBeforeProcess() {
		$exclude = array();

		$data = isset($this->controller->request->data[$this->model]) ? $this->controller->request->data[$this->model] : array();
		foreach ($this->settings['advancedFilter'] as $fieldSet) {
			foreach ($fieldSet as $field => $fieldData) {
				$showKey = $field . '__show';
				$noneKey = $field . '__none';
				$compKey = $field . '__comp_type';

				if (isset($data[$showKey])) {
					if ($data[$showKey] === '0') {
						unset($this->controller->request->data[$this->model][$showKey]);
						$exclude[] = $showKey;
					}
				}

				if (isset($data[$noneKey])) {
					if ($data[$noneKey] === '0') {
						unset($this->controller->request->data[$this->model][$noneKey]);
						$exclude[] = $noneKey;
					}
				}

				if ($fieldData['filter']['method'] = 'findComplexType' && isset($data[$compKey])) {
			        $queryClass = Inflector::classify($fieldData['type']) . 'Query';
					App::uses($queryClass, 'Lib/AdvancedFilters/Query');

					$defaultComp = $queryClass::$defaultComparison;

					if ($data[$compKey] == $defaultComp) {
						unset($this->controller->request->data[$this->model][$compKey]);
						$exclude[] = $compKey;
					}
				}
			}
		}

		return $exclude;
	}

	public function pdf($model) {
		$this->commonProcess($model);

		$data = $this->_getData(self::FILTER_TYPE_PDF);

		$this->controller->set('data', $data);
	}

	public function csv($model) {
		$this->commonProcess($model);

		$data = $this->_getData(self::FILTER_TYPE_CSV);

		$this->setCsvData($data);

		return true;
	}

	public function cronDataCsv($model, $data) {
		$this->commonProcess($model);

		$this->setCsvData($data);
	}

	private function setCsvData($rawData) {
		$cronDate = (!empty($rawData[0]['__cron_date'])) ? true : false;
		$_header = $cronDate ? array('Date') : array();
		$_extract = $cronDate ? array('__cron_date') : array();
		$data = array();
		$_serialize = 'data';

		$filter = array(
			'model' => $this->model,
			'fields' => $this->settings['advancedFilter'],
		);

		$AdvancedFiltersData = new AdvancedFiltersData($this->controller->viewVars, AdvancedFiltersData::VIEW_TYPE_CSV);

		foreach ($rawData as $key => $item) {
			if ($cronDate) {
				$data[$key]['__cron_date'] = $item['__cron_date'];
			}
			foreach ($filter['fields'] as $fieldSet) {
				foreach ($fieldSet as $field => $fieldData) {
					if (!empty($this->controller->request->data[$filter['model']][$field . '__show'])) {
						$_header[$fieldData['name']] = $fieldData['name'];
						$_extract[$field] = $field;
						// $data[$key][$field] = $this->csvField($filter, $field, $fieldData, $item);
						$data[$key][$field] = $AdvancedFiltersData->getFieldValue($filter, $field, $fieldData, $item);
					}
				}
			}
		}

		// $this->controller->set('_newline', "\n");
		// $this->controller->set('_eol', $_eol);
		// exit;
		$this->controller->set(compact('_header', '_extract', 'data', '_serialize'));
	}

	/**
	 * @deprecated In favor of AdvancedFiltersData::getFieldValue()
	 */
	private function csvField($filter, $field, $fieldData, $item) {
		if (!empty($fieldData['filter']['status'])) {
			$baseHelper = Inflector::pluralize($filter['model']);
			$baseHelperClass = $baseHelper . 'Helper';

			App::import('Helper', $baseHelper);
			$baseClass = new $baseHelperClass(new View());
			
			$value = $baseClass->getFilterSingleStatus(
				$item,
				$fieldData['filter']['status']['model'],
				array($fieldData['filter']['status']['field'])
			);
		}
		elseif (!empty($fieldData['filter']['customField'])) {
			$customFieldsHelper = 'CustomFields';
			$customFieldsHelperClass = 'CustomFieldsHelper';

			App::import('Helper', $customFieldsHelper);
			$customFieldsClass = new $customFieldsHelperClass(new View());

			$value = $customFieldsClass->getItemValue($item, $fieldData['filter']['customField']);
		}
		elseif (!empty($fieldData['field'])) {
			$value = array_unique(Hash::extract($item, $fieldData['field']));
			if (empty($fieldData['many'])) {
				if (!empty($value[0])) {
					$value = $value[0];
				}
				else {
					$value = '-';
				}
			}
		}
		elseif (empty($fieldData['contain'])) {
			$value = $item[$filter['model']][$field];
		}
		elseif (!empty($fieldData['many'])) {
			$value = array();
			foreach ($fieldData['contain'] as $alias => $aliasFields) {
				foreach ($item[$alias] as $subItem) {
					$valueItem = '';
					foreach ($aliasFields as $key => $aliasField) {
						if ($key > 0) {
							$valueItem .= ' ';
						}
						$valueItem .= $subItem[$aliasField];
					}
					$value[] = $valueItem;
				}
			}
		}
		else {
			$value = '';
			foreach ($fieldData['contain'] as $alias => $aliasFields) {
				foreach ($aliasFields as $key => $aliasField) {
					if ($key > 0) {
						$value .= ' ';
					}
					$value .= $item[$alias][$aliasField];
				}
			}
		}

		if ($fieldData['type'] == 'date') {
			// for now we wont format dates
			// return date('d.m.Y', strtotime($value));
			return $value;
		}
		elseif (!empty($fieldData['many'])) {
			return implode(', ', $value);
		}
		else {
			if (!empty($fieldData['data']['result_key'])) {
				$options = $this->controller->viewVars[$field . '_data'];

				if (isset($options[$value])) {
					return $options[$value];
				}
				else {
					return '-';
				}
			}
			else {
				return $value;
			}
		}
	}

	private function getSelectionSize() {
		$selectionSize = 0;
		if (!empty($this->controller->request->data[$this->model])) {
			foreach ($this->controller->request->data[$this->model] as $field => $value) {
				if (strpos($field, '__show') !== false && !empty($value)) {
					$selectionSize++;
				}
			}
		}

		return $selectionSize;
	}

	private function getFields($setDefault = false) {
		$fields = array('fields' => array(), 'contain' => array(), 'joins' => array());

		foreach ($this->settings['advancedFilter'] as $fieldSet) {
			foreach ($fieldSet as $field => $fieldData) {
				if (!empty($fieldData['hidden'])) {
					continue;
				}

				if ($setDefault && !empty($fieldData['show_default'])) {
					$this->controller->request->data[$this->model][$field . '__show'] = 1;
				}

				// status functionality
				if (!empty($fieldData['filter']['status'])) {
					$inherit = $fieldData['filter']['status'];
					$fieldData['contain'][$inherit['model']][] = $inherit['field'];
				}

				if (!empty($fieldData['joins'])) {
					// $fields['joins'] = Hash::merge($fields['joins'], $fieldData['joins']);
					$fields['joins'] = am($fields['joins'], $fieldData['joins']);// $fields['joins'] + $fieldData['joins'];
				}

				if (isset($fieldData['field'])) {
					// put into fields only current model's fields
					$countExploded = count(explode('.', $fieldData['field']));

					if (!empty($fieldData['field'])) {
						$_model = $this->controller->{$this->model};

						if (method_exists($_model, $fieldData['field'])) {
							$fields['fields'][] =  '(' . $_model->{$fieldData['field']}() . ') as ' . $field;
						}
						else {
							// we check if the parameter is defined with a model inside . (dot)
							$addToFieldsConds = $countExploded == 2;

							// we check if a defined field (without model definition) actually exists in the schema
							$existInSchema = $_model->schema($fieldData['field']);
							$addToFieldsConds = $addToFieldsConds || ($countExploded == 1 && !empty($existInSchema));

							// if conditions passes we add the field to the fieldList
							if ($addToFieldsConds) {
								$fields['fields'][] = $fieldData['field'];
							}
						}

						unset($_model);
					}
				}
				elseif (empty($fieldData['contain'])) {
					$fields['fields'][] = $this->model . '.' . $field;
				}
				else {
					if (isset($this->controller->request->data[$this->model][$field]) 
						|| !empty($this->controller->request->data[$this->model][$field . '__show'])
					) {
						foreach ($fieldData['contain'] as $alias => $containFields) {
							if (!empty($fields['contain'][$alias]['fields'])) {
								$fields['contain'][$alias]['fields'] = am($fields['contain'][$alias]['fields'], $containFields);
							}
							else {
								$fields['contain'][$alias] = array('fields' => $containFields);
							}
						}
					}
				}

				if (!empty($fieldData['containable'])) {
					$fields['contain'] = array_merge_recursive($fields['contain'], $fieldData['containable']);
				}
			}
		}

		// additional customized contain values for finding data
		if (!(isset($this->settings['advancedFilterSettings']['actions']) && $this->settings['advancedFilterSettings']['actions'] == false)) {
			if (!(isset($fields['contain']['Comment']) || in_array('Comment', $fields['contain']))) {
				$fields['contain'][] = 'Comment';
			}
			if (!(isset($fields['contain']['Attachment']) || in_array('Attachment', $fields['contain']))) {
				$fields['contain'][] = 'Attachment';
			}
		}

		$conds = isset($this->controller->{$this->model});
		$conds = $conds && $this->controller->{$this->model}->Behaviors->enabled('CustomFields.CustomFields');
		if ($conds) {
			$this->controller->{$this->model}->bindCustomFieldValues();
			$fields['contain'][] = 'CustomFieldValue';
		}

		return $fields;
	}

	/**
	 * purifies $data
	 * 
	 * @param  array &$data
	 */
	public function purifyData(&$data) {
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$this->purifyData($value);
			}
			else {
				$data[$key] = Purifier::clean($value, 'Strict');
			}
		}
	}

	/**
	 * independent build of conditions
	 * 
	 * @param  string $model
	 * @param  array $data
	 * @return array
	 */
	public function buildConditions($model, $data) {
		$this->setComparisonTypes($data, $model, true);
		$conditions = $this->controller->{$model}->parseCriteria($data);

		return $conditions;
	}

	private function getConditions() {
		// return [];
		$this->purifyData($this->controller->request->query);
		
		$this->Prg->commonProcess($this->model, array(
			'filterEmpty' => true,
			'excludedParams' => $this->cleanDefaultsBeforeProcess()
		));
		$queryData = $this->proccessFields($this->Prg->parsedParams());

		$conditions = $this->controller->{$this->model}->parseCriteria($queryData);
		return $conditions;
	}

	private function setComparisonTypes(&$data, $model = null, $unsetData = false) {
		if ($model === null) {
			$model = $this->model;
		}

		foreach ($data as $field => $value) {
			if (strpos($field, '__comp_type') !== false) {
				$fieldName = str_replace('__comp_type', '', $field);
				$this->controller->{$model}->filterArgs[$fieldName]['comp_type'] = $value;
				if ($unsetData) {
					unset($data[$field]);
				}
			}
		}
	}

	public function setFilterSettings($model = null, $initSettings = false) {
		if ($model === null) {
			$model = $this->model;
		}

		if ($initSettings) {
			$this->setSettings($model);
		}

		$filterSetting = array(
			'model' => $model,
			'fields' => $this->settings['advancedFilter'],
			'settings' => $this->settings['advancedFilterSettings']
		);
		
		$this->controller->set('filter', $filterSetting);
	}

	public function setFilterData($model = null) {
		if ($model === null) {
			$model = $this->model;
		}
		else {
			$this->model = $model;
		}

		$this->controller->set('filterNoneFields', $this->getNoneFields($model));

		foreach ($this->settings['advancedFilter'] as $fieldSet) {
			foreach ($fieldSet as $field => $fieldData) {

				$varName = Inflector::slug($field);

				if (!empty($fieldData['data']['method']) || !empty($fieldData['data']['callable'])) {
					//temporarily disable altering queries with workflow status to get all the results
					$this->alterBelongsToQueries(true);
					if (!empty($fieldData['data']['callable'])) {
						$data = call_user_func($fieldData['data']['callable']);
					}
					else {
						$data = $this->controller->{$model}->{$fieldData['data']['method']}($fieldData);
					}
					$this->alterBelongsToQueries();

					$this->controller->set($varName . '_data', $data);
				}
				elseif (!empty($fieldData['data']['options'])) {
					$data = $fieldData['data']['options'];
					$this->controller->set($varName . '_data', $data);
				}

			}
		}
	}

	// disable altering queries with workflow status to get all the results
	private function alterBelongsToQueries($type = array()) {
		$assoc = $this->controller->{$this->model}->getAssociated('belongsTo');
		if (!empty($assoc)) {
			foreach ($assoc as $_m) {
				$this->controller->{$this->model}->{$_m}->alterQueries($type);
			}
		}
	}

	public function getNoneFields($model = null, $valueType = true, $subqueryType = true) {
		if ($model === null) {
			$model = $this->model;
		}

		$noneFields = array();
		foreach ($this->settings['advancedFilter'] as $fieldSet) {
			foreach ($fieldSet as $field => $fieldData) {
				if ($this->controller->{$model}->getFilterNoneConds($field, $fieldData, $valueType, $subqueryType)) {
					$noneFields[] = $field;
				}
			}
		}

		return $noneFields;
	}

	/**
	 * sets list of saved filters
	 * 
	 * @param string $model
	 */
	public function setSavedFilters($model = null) {
		$filters = $this->_getSavedFilters($model);

		$this->controller->set('savedFilters', $filters);
	}

	/**
	 * gets list of saved filters (with additional model filters included)
	 * 
	 * @param string $model
	 * @return array list of filters
	 */
	protected function _getSavedFilters($model = null) {
		if ($model === null) {
			$model = $this->model;
		}

		$this->controller->loadModel($model);

		$additionalModels = array_merge(
			array($model),
			$this->controller->{$model}->getAdvancedFilterAdditionalModels()
		);

		$user = $this->controller->logged;
		$filters = $this->controller->AdvancedFilter->getAll($this->controller->logged['id'], $additionalModels, array(
			'contain' => array('User')
		));

		return $filters;
	}

	/**
	 * sets active filter data and title
	 * 
	 * @param array $filter
	 */
	public function setActiveFilterData($filter) {
		$titleSufix = '';
		if ($this->controller->AdvancedFilter->isDefault($filter['AdvancedFilter']['id'])) {
			$titleSufix = ' (' . __('Default Filter Index') . ')';
		}
		$this->controller->set('title_for_layout', $filter['AdvancedFilter']['name'] . $titleSufix);
		$this->controller->set('activeFilter', $filter);
	}

	/**
	 * check if there is default filter index for working model
	 *
	 * @param  int $type
	 * @return int filterId | false
	 */
	// private function isDefaultIndex($type) {
	// 	if ($type == self::FILTER_TYPE_TRASH) {
	// 		return false;
	// 	}

	// 	$q = $this->controller->request->query;
	// 	unset($q['advanced_filter']);

	// 	if (!empty($q)) {
	// 		return false;
	// 	}

	// 	$this->controller->loadModel('AdvancedFilterUserSetting');

	// 	$defaultFilterId = $this->controller->AdvancedFilterUserSetting->getModelDefaultIndex($this->model);

	// 	return $defaultFilterId;
	// }

	private function proccessFields($data) {
		$filteredData = array();
		$noneFields = $this->getNoneFields(null, true, false);

		foreach ($data as $field => $value) {
			if (strpos($field, '__comp_type') === false && strpos($field, '__show') === false && strpos($field, '__none') === false 
				&& strpos($field, '_limit') === false && strpos($field, '__use_calendar') === false
			) {
				// none value type for multiselect fields
				if (in_array($field, $noneFields) && $value === ADVANCED_FILTER_MULTISELECT_NONE) {
					$filteredData[$field] = '';
					$this->controller->{$this->model}->filterArgs[$field]['allowEmpty'] = true;
				}
				else {
					$filteredData[$field] = $value;
				}
			}
			elseif (strpos($field, '__comp_type') !== false) {
				$fieldName = str_replace('__comp_type', '', $field);
				if (in_array($value, [AbstractQuery::COMPARISON_IS_NULL, AbstractQuery::COMPARISON_IS_NOT_NULL])) {
					$this->controller->{$this->model}->filterArgs[$fieldName]['allowEmpty'] = true;
					$filteredData[$fieldName] = '';
				}
				else {
					$this->controller->{$this->model}->filterArgs[$fieldName]['allowEmpty'] = false;
				}
			}
		}

		return $filteredData;
	}

	public function validateSelection($data, $model) {
		$this->model = $model;

		$this->setSettings();

		if (empty($data)) {
			$this->controller->set('errorMessage', __('Something went wrong.'));
			return false;
		}

		$selectionSize = 0;

		foreach ($data as $field => $value) {
			if (strpos($field, '__show') !== false && !empty($value)) {
				$selectionSize++;
			}
			if ($selectionSize > $this->settings['advancedFilterSettings']['max_selection_size']) {
				$this->controller->set('errorMessage', __('We cant show more than %s fields - please uncheck fields before you select new ones.', $this->maxSelectionSize));
				return false;
			}
		}

		return true;
	}

	public function resetCustomFields($model) {
		if ($this->controller->{$model}->Behaviors->loaded('CustomFields.CustomFields') 
            && $this->controller->{$model}->Behaviors->enabled('CustomFields.CustomFields')
        ) {
            $this->controller->Components->unload('CustomFields.CustomFieldsMgt');
            $this->controller->CustomFieldsMgt = $this->controller->Components->load('CustomFields.CustomFieldsMgt', ['model' => $model]);
            $this->controller->CustomFieldsMgt->initialize($this->controller);
        }
	}
}