<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');
App::uses('AdvancedFiltersQuery', 'Lib/AdvancedFilters');
App::uses('Validation', 'Utility');
App::uses('AppValidation', 'Utility');
App::uses('CakeTime', 'Utility');
App::uses('AuditsTrait', 'Model/Trait');
App::uses('Hash', 'Utility');
App::uses('FilterAdapter', 'AdvancedFilters.Lib/QueryAdapter');
App::uses('UserFields', 'UserFields.Lib');
App::uses('NotificationSystem', 'NotificationSystem.Model');
App::uses('CustomField', 'CustomFields.Model');
/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model
{
	use AuditsTrait;

	// Use $_appModelConfigDefaults['behaviors'] instead
	public $actsAs = [
    ];

    // Custom error message for delete action (Used by CRUD for example)
    public $customDeleteMessage = '';
	
	/**
	 * Section (model) label name used throughout the application.
	 * 
	 * @var mixed String for a model label name, false for $name.
	 */
	public $label = null;

	/**
	 * Section (model) description used throughout the application
	 * @var mixed String for a model description
	 */
	protected $_description = null;

	public $filterArgs = [];

	/**
	 * Group label for this section.
	 * 
	 * @var null|string
	 */
	protected $_group = null;

	/**
	 * Use this property for editing default configuration of AppModel. Child models can overwride these settings with $_appModelConfig property
	 */
	protected $_appModelConfigDefaults = [
		'behaviors' => [
			'Containable',
			'EventManager.EventManager',
			'AdvancedQuery.AdvancedFinder',
			'ItemData.ItemData',
			'Macros.Macro'
		],
		'elements' => [
			'useCache' => false
		]
	];
	
	/**
	 * Use this property in child model for editing configuration of AppModel
	 *
	 * Turn off any behavior by behaviors => [behaviorName => false];
	 * Example 1: You want to preconfigure some behavior, but don't want to use it in all child models. Just put behaviors => [behaviorName => false] into this property within child model
	 * Example 2: You want to add configuration of any preconfigured behavior. You can do it like this: behaviors => [behaviorName => newConfigurationArray]
	 * Example 3: You want to change configuration of any preconfigured behavior. You need to disable preconfigured behavior first (Example 1) and then use actsAs property to add newly configured behavior to your model
	 *
	 * Turn off or on any feature, which is presented in elements array in $_appModelConfigDefaults property
	 * Example 1: You want to turn on/off auto-set viewOptions of FieldDataEntity into viewVars of view. You can do it like this: elements => [FieldDataEntity => [viewOptions => false/true (Default is false)]]
	 */
	protected $_appModelConfig = [
	];

	public $mapping = [
		'logRecords' => true,
	];

	public $_configDefaults = array(
		'titleColumn' => false,
		'indexController' => false,
		'logRecords' => false,
		'notificationSystem' => false,
		'statusReview' => false,
	);

	public $notificationSystem = array(
		/*
			formatted like 'MACRO' => array(
				'field' => 'Model.field',
				'name' => 'Field Name'
			)
		*/
		'macros' => array(),
		'customEmail' => false
	);

	/**
	 * Map a controller name to a model for features that requires it.
	 * 
	 * @var null|string
	 */
	public $mapController = null;

	/**
	 * Data for fields for a current model.
	 * Format:
	 * 'field_name' => array(
	 * 		'label' => __('My Field')
	 * )
	 * 
	 * @var array
	 */
	public $fieldData = array();

	// default group
	public $fieldGroupData = array('default' => array('label' => 'General'));

	public function __construct($id = false, $table = null, $ds = null)
	{
		//
		// Apply configuration for AppModel from its child classes
		$this->_appModelConfig = Hash::merge($this->_appModelConfigDefaults, $this->_appModelConfig);
		$this->_applyConfigOnBehaviors();
		$this->_applyConfigOnElements();
		//
		
		parent::__construct($id, $table, $ds);

		// Load FieldData behavior - needs to be loaded as the last behavior because it uses informations from model (associations etc.) which can be added/edited/configured during other behavior's initialization
		if (!empty($this->fieldData)) {
			$this->Behaviors->load('FieldData.FieldData');
		}

		// Backwards compatibility for removed obsolete classes
		$this->setupMapping();

		$this->initAdvancedFilter();
	}

	// generic situation with conventional HABTM fields
	public function afterValidate() {
		// transforms the data array to save the HABTM relation
    	$this->transformDataToHabtm();
    	$this->setHabtmConditionsToData();
	}

	/**
	 * Apply configuration for AppModel's behaviors (which are loaded automatically in AppModel) from its child class
	 * @return void
	 */
	protected function _applyConfigOnBehaviors()
	{
		$appModConfDefBehaviors = Hash::normalize($this->_appModelConfigDefaults['behaviors'], true);
		$appModConfBehaviors = Hash::normalize($this->_appModelConfig['behaviors'], true);
		$behaviors = Hash::normalize((array)$this->actsAs, true);
		foreach ($appModConfDefBehaviors as $behavior => $settings) {
			if (array_key_exists($behavior, $appModConfBehaviors) && $appModConfBehaviors[$behavior] === false) {
				unset($appModConfDefBehaviors[$behavior]);
				unset($appModConfBehaviors[$behavior]);
			}
		}

		$behaviors = Hash::merge($appModConfBehaviors, $behaviors);
		$this->actsAs = Hash::merge($appModConfDefBehaviors, $behaviors);
	}

	/**
	 * Apply configuration for AppModel's elements (settings for properties and methods) from its child class
	 * @return void
	 */
	protected function _applyConfigOnElements()
	{
		// Empty, use this function for any of element's (AppModel's properties and methods) initial configuration
	}

	/**
	 * Set configuration to $_appModelConfig property
	 * @param array $config Configuration in format: ['elements' => ['nameInDepth1' => ['nameInDepth2' => true]]]
	 */
	protected function _setAppModelConfig(Array $config, $rewrite = false)
	{
		$acceptableKeys = array_keys($this->_appModelConfigDefaults);
		foreach ($config as $key => $val) {
			if (!in_array($key, $acceptableKeys)) {
				unset($config[$key]);
			}
		}

		if ($rewrite) {
			foreach ($config as $key => $val) {
				$this->_appModelConfig[$key] = $val;
			}
		} else {
			$this->_appModelConfig = Hash::merge($this->_appModelConfig, $config);
		}
	}

	/**
	 * Get config from any of deep level of $_appModelConfig property
	 * Function accepts any number of arguments. Example: _getAppModelConfig('elements', 'nameInDepth1', 'nameInDepth2');
	 * @return mixed config
	 */
	protected function _getAppModelConfig()
	{
		$args = func_get_args();
		$tempConfig = $this->_appModelConfig;
		foreach ($args as $arg) {
			if (array_key_exists($arg, $tempConfig)) {
				$tempConfig = $tempConfig[$arg];
			} else {
				$tempConfig = false;
				break;
			}
		}

		return $tempConfig;
	}

	public function implementedEvents() {
		return parent::implementedEvents() + [
			'Model.afterAuditProperty' => array('callable' => 'afterAuditProperty', 'passParams' => true)
		];
	}
	 
	//@todo
	public function parentNode($type) {
		return 'visualisation/objects';
		// return [$this->alias => [$this->primaryKey => null]];

		// other possible solution is
		// return [$this->alias => [$this->primaryKey => $this->id]];
	}

    public function parentNodeId() {
    	if (!$this instanceof InheritanceInterface) {
    		return false;
    	}

    	$parent = $this->parentNode('Aco');
    	if ($parent !== null) {
	    	return $parent[$this->parentModel()][$this->{$this->parentModel()}->primaryKey];
	    }

	    return false;
    }

    /**
     * Validate if value is in list porovided by callable.
     */
    public function inCallableList($check, $func, $listKeys) {
		$callable = $func;

		if (method_exists($this, $func)) {
			$callable = [$this, $func];
		}

		$list = call_user_func($callable);

		if ($listKeys) {
			$list = array_keys($list);
		}

		$val = reset($check);

		return in_array($val, $list);
	}

     /**
     * Validate date in the future.
     */
    public function validateFutureDate($check) {
        $value = array_values($check);
        $date = $value[0];

        $today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));
        if ($date > $today) {
            return true;
        }

        return false;
    }

     /**
     * Validate date in the past.
     */
    public function validatePastDate($check) {
        $value = array_values($check);
        $date = $value[0];

        $today = CakeTime::format('Y-m-d', CakeTime::fromString('now'));
        if ($date <= $today) {
            return true;
        }

        return false;
    }

    /**
     * Validation for special fields with empty checkbox.
     */
    public function checkEmptyCheckbox($check, $emptyField) {
        reset($check);
        $mainField = key($check);

        $result = true;

        if (empty($this->data[$this->alias][$mainField]) && empty($this->data[$this->alias][$emptyField]) 
        ) {
            $result = false;
        }

        return $result;
    }

	/*
	 * static enum
	 * @access static
	 */
	 public static function sectionGroups($value = null) {
		$options = array(
			self::SECTION_GROUP_DEBUG => __('Debug'),
			self::SECTION_GROUP_ASSET_MGT => __('Asset Management'),
			self::SECTION_GROUP_RISK_MGT => __('Risk Management'),
			self::SECTION_GROUP_COMPLIANCE_MGT => __('Compliance Management'),
			self::SECTION_GROUP_CONTROL_CATALOGUE => __('Control Catalogue'),
			self::SECTION_GROUP_ORGANIZATION => __('Organization'),
			self::SECTION_GROUP_SECURITY_OPERATIONS => __('Security Operations'),
			self::SECTION_GROUP_PROGRAM => __('Program'),
			self::SECTION_GROUP_SYSTEM => __('System')
		);
		return self::enum($value, $options);
	}
	const SECTION_GROUP_DEBUG = 'debug';
	const SECTION_GROUP_ASSET_MGT = 'asset';
	const SECTION_GROUP_RISK_MGT = 'risk';
	const SECTION_GROUP_COMPLIANCE_MGT = 'compliance';
	const SECTION_GROUP_CONTROL_CATALOGUE = 'control';
	const SECTION_GROUP_ORGANIZATION = 'organization';
	const SECTION_GROUP_SECURITY_OPERATIONS = 'security-operations';
	const SECTION_GROUP_PROGRAM = 'program';
	const SECTION_GROUP_SYSTEM = 'system';


	/**
	 * Group label for this section.
	 * 
	 * @return string
	 */
	public function group() {
		return self::sectionGroups($this->_group);
	}

	/**
	 * Groupped breadcumbs label for this section.
	 * 
	 * @return string
	 */
	public function groupLabel()
	{
		$group = !is_array($this->group()) ? $this->group() : '-';
		return sprintf('%s / %s', $group, $this->label(['displayParents' => true]));
	}

	/**
	 * Get section (model) label name.
	 */
	public function label($options = array()) {
		$options = am(array(
			'singular' => false,
			'displayParents' => false
		), $options);

		$label = $this->_getLabel();
		
		if (!empty($options['singular'])) {
			$label = Inflector::singularize($label);
		}

		if ($options['displayParents'] === true && $this instanceof InheritanceInterface) {
			return sprintf('%s / %s', ClassRegistry::init($this->parentModel())->label($options), $label);
		}

		return $label;
	}

	/**
	 * Internal method to get a default label for this model.
	 * 
	 * @return string Label.
	 */
	protected function _getLabel() {
		if (!empty($this->label)) {
			$label = $this->label;
		}
		else {
			$modelName = get_class($this);

			// in case something went wrong
			if ($modelName == 'AppModel') {
				return getEmptyValue(false);
			}

			$label = Inflector::humanize(Inflector::tableize($modelName));
		}

		return $label;
	}

	public function description()
	{
		return $this->_description;
	}

	/**
	 * Get a mapped controller name the current model.
	 * 
	 * @return string Controller name.
	 */
	public function getMappedController() {
		if ($this->mapController !== null) {
			return $this->mapController;
		}

		return controllerFromModel($this->name);
	}

	/**
	 * Get mapped route to the current model. Used in front-end links.
	 * 
	 * @return array Route URL.
	 */
	public function getMappedRoute($params = [])
    {
        $url = [
            'plugin' => Inflector::underscore($this->plugin),
            'controller' => $this->getMappedController(),
            'action' => 'index'
        ];

        return array_merge($url, $params);
    }

	/**
	 * Get index url params for current model.
	 * 
	 * @return array Url params.
	 */
	public function getSectionIndexUrl($params = []) {
		$defaultParams = [
			'plugin' => null,
			'controller' => $this->getMappedController(),
			'action' => 'index'
		];

		$url = am($defaultParams, $params);

		return $url;
	}

	/**
	 * static enums
	 * @access static
	 */
	public static function enum($value, $options, $default = '') {
		if ($value !== null && (is_string($value) || is_numeric($value))) {
			if (array_key_exists($value, $options)) {
				return $options[$value];
			}
			return $default;
		}
		return $options;
	}

	/**
	 * Get the current user
	 *
	 * Necessary for logging the "owner" of a change set,
	 * when using the AuditLog behavior.
	 *
	 * @return mixed|null User record. or null if no user is logged in.
	 */
	public function currentUser($value = null) {
		App::uses('AuthComponent', 'Controller/Component');
		if ($value !== null) {
			return AuthComponent::user($value);
		}
		
		return array(
			'id' => AuthComponent::user('id'),
			'description' => AuthComponent::user('full_name'),
		);
	}

	// soft delete handler
	public function exists($id = null) {
		if ($this->Behaviors->loaded('SoftDelete')) {
			return $this->existsAndNotDeleted($id);
		} else {
			return parent::exists($id);
		}
	}

	// soft delete handler
	public function delete($id = null, $cascade = true) {
		$result = parent::delete($id, $cascade);

		if ($result === false && $this->Behaviors->enabled('SoftDelete')) {
			$this->afterDelete();

			//AssociativeDelete
			if ($this->Behaviors->enabled('AssociativeDelete')) {
				$this->associativeDelete();
			}
			
			return (bool) $this->field($this->alias . '.deleted', array($this->alias . '.deleted' => 1));
		}
		return $result;
	}

	/**
	 * Public API method to build a joins query based on app's general rules.
	 * 
	 * @param  mixed $assocModel  Model name as string for which to generate this $query,
	 *                            or array of Model names to merge the resulting $query array.
	 * @return array              Query params.
	 */
	public function buildJoinsQuery($assocModel, $type = 'INNER') {
		App::uses('ObjectStatusBehavior', 'ObjectStatus.Model/Behavior');
		$ObjectStatusBehavior = new ObjectStatusBehavior();

		if (!is_array($assocModel)) {
			$assocModel = (array) $assocModel;
		}
		$assocModel = array_unique($assocModel);
		
		$joins = $conditions = [];
		foreach ($assocModel as $modelName) {
			$joins = array_merge($joins, $ObjectStatusBehavior->joinModels($this, $modelName, $type));
	        $conditions = array_merge($conditions, $ObjectStatusBehavior->getAdditionalConditions($this, $modelName));
	    }

		return [
			'conditions' => $conditions,
			'joins' => $joins
		];
	}

	/**
	 * Compatibility reasons for deprecated mapping behavior, will be removed
	 * BEGIN.
	 */
	public static $logged_id, $modelClass, $currentAction;
	protected function setupMapping() {
		// AppSetting is loaded in bootstrap and needs to be excluded here
		if ($this->alias == 'AppSetting') {
			return;
		}

		if (!empty($this->mapping)) {
			$this->mapping = array_merge($this->_configDefaults, $this->mapping);
		}
		else {
			$this->mapping = $this->_configDefaults;
		}

		if ($this->mapping['logRecords']) {
			$this->Behaviors->load('SystemLog', array(
				'priority' => 9
			));
		}
	}

	/**
	 * Deprecated #removeAfterAppModelCleaning
	 */
	public function alterQueries($options = array()) {
		if (is_bool($options) && $options) {
			$options = array(
				'notificationSystem' => false
			);
		}
		else {
			$options = array_merge(
				array(
					'notificationSystem' => true
				),
				(array) $options
			);
		}

		if ($this->Behaviors->loaded('NotificationsSystem')) {
			$this->includeNotifications($options['notificationSystem']);
		}
	}

	public function initAdvancedFilter() {
		if (empty($this->advancedFilter)) {
			return;
		}

		if ($this->Behaviors->enabled('ObjectStatus.ObjectStatus') && !method_exists($this, 'getAdvancedFilterConfig')) {

			$config = $this->getObjectStatusFilterConfig();

			$this->advancedFilter[__('Status')] = $config;
		}

		if ($this->Behaviors->enabled('CustomFields.CustomFields') && !method_exists($this, 'getAdvancedFilterConfig')) {
			$config = $this->getCustomFieldsFilterConfig();

			if ($config !== false) {
				$this->advancedFilter[__('Custom Fields')] = $config;
			}
		}

		$this->advancedFilterOtherTab();

		$this->buildFilterArgs();
		$this->addOrderFields();

		$settingsDefault = array(
			'max_selection_size' => 30
		);

		$this->advancedFilterSettings = am($settingsDefault, $this->advancedFilterSettings);
	}

	private function advancedFilterOtherTab() {
		if (method_exists($this, 'getAdvancedFilterConfig')) {
			return;
		}

		$allowTimeStamps = true;
		if (isset($this->advancedFilterSettings['include_timestamps']) && $this->advancedFilterSettings['include_timestamps'] === false) {
			$allowTimeStamps = false;
		}

		if ($allowTimeStamps && $this->schema('created') !== null) {
			$this->advancedFilter[__('Other')]['created'] = [
				'type' => 'date',
				'name' => __('Created on'),
				'show_default' => false,
				'filter' => [
					'type' => 'subquery',
					'method' => 'findComplexType',
					'findField' => "{$this->alias}.created",
					'field' => "{$this->alias}.id",
				],
			];
		}

		if ($allowTimeStamps && $this->schema('modified') !== null) {
			$this->advancedFilter[__('Other')]['modified'] = [
				'type' => 'date',
				'name' => __('Last Updated'),
				'show_default' => false,
				'filter' => [
					'type' => 'subquery',
					'method' => 'findComplexType',
					'findField' => "{$this->alias}.edited",
					'field' => "{$this->alias}.id",
				],
			];
		}

		if ($this->Behaviors->enabled('Comments.Comments')) {
			$this->advancedFilter[__('Other')]['comment_message'] = [
				'type' => 'text',
				'name' => __('Comments'),
				'show_default' => false,
				'filter' => [
					'type' => 'subquery',
					'method' => 'findComplexType',
					'findField' => 'Comment.message',
					'field' => "{$this->alias}.id",
				],
				'fieldData' => 'Comment.message'
			];

			$this->advancedFilter[__('Other')]['last_comment'] = [
				'type' => 'date',
				'name' => __('Last Comment'),
				'show_default' => false,
				'filter' => [
					'type' => 'subquery',
					'method' => 'findComplexType',
					'findField' => 'LastComment.created',
					'field' => "{$this->alias}.id",
				],
				'fieldData' => 'LastComment.last_created'
			];
		}

		if ($this->Behaviors->enabled('Attachments.Attachments')) {
			$this->advancedFilter[__('Other')]['attachment_filename'] = [
				'type' => 'text',
				'name' => __('Attachments'),
				'show_default' => false,
				'filter' => [
					'type' => 'subquery',
					'method' => 'findComplexType',
					'findField' => 'Attachment.name',
					'field' => "{$this->alias}.id",
				],
				'fieldData' => 'Attachment.name'
			];

			$this->advancedFilter[__('Other')]['last_attachment'] = [
				'type' => 'date',
				'name' => __('Last Attachment'),
				'show_default' => false,
				'filter' => [
					'type' => 'subquery',
					'method' => 'findComplexType',
					'findField' => 'LastAttachment.created',
					'field' => "{$this->alias}.id",
				],
				'fieldData' => 'LastAttachment.last_created'
			];
		}
	}

	/**
	 * Seed other filters to new builder instance.
	 * 
	 * @param FilterConfigurationBuilder $advancedFilterConfig Filter config builder instance.
	 * @return void
	 */
	protected function otherFilters(FilterConfigurationBuilder $advancedFilterConfig)
	{
		$advancedFilterConfig->group('other', [
			'name' => __('Other')
		]);

		$allowTimeStamps = true;
		if (isset($this->advancedFilterSettings['include_timestamps']) && $this->advancedFilterSettings['include_timestamps'] === false) {
			$allowTimeStamps = false;
		}

		if ($allowTimeStamps && $this->schema('created') !== null) {
			$advancedFilterConfig->dateField('created', [
				'label' => __('Created on')
			]);
		}

		if ($allowTimeStamps && $this->schema('edited') !== null) {
			$advancedFilterConfig->dateField('modified', [
				'label' => __('Last Updated'),
				'findField' => "{$this->alias}.edited",
				'fieldData' => 'edited'
			]);
		}

		if ($this->Behaviors->enabled('Comments.Comments')) {
			$advancedFilterConfig->textField('comment_message', [
				'label' => __('Comments'),
				'findField' => 'Comment.message',
				'fieldData' => 'Comment.message'
			]);

			$advancedFilterConfig->dateField('last_comment', [
				'label' => __('Last Comment'),
				'findField' => 'LastComment.created',
				'fieldData' => 'LastComment.last_created'
			]);
		}

		if ($this->Behaviors->enabled('Attachments.Attachments')) {
			$advancedFilterConfig->textField('attachment_filename', [
				'label' => __('Attachments'),
				'findField' => 'Attachment.name',
				'fieldData' => 'Attachment.name'
			]);

			$advancedFilterConfig->dateField('last_attachment', [
				'label' => __('Last Attachment'),
				'findField' => 'LastAttachment.created',
				'fieldData' => 'LastAttachment.last_created'
			]);
		}
	}

	protected function mergeAdvancedFilterFields($advancedFilter) {
		$this->advancedFilter = array_replace_recursive($this->advancedFilter, $advancedFilter);
		foreach ($this->advancedFilter as $pageKey => $page) {
			foreach ($page as $itemKey => $item) {
				if ($this->advancedFilter[$pageKey][$itemKey] === null) {
					unset($this->advancedFilter[$pageKey][$itemKey]);
				}
			}
		}
		$this->advancedFilter = array_filter($this->advancedFilter, function($var){return !is_null($var);});
	}

	/**
	 * returns array of models used in additional_actions fiter setting
	 * 
	 * @return array
	 */
	public function getAdvancedFilterAdditionalModels() {
		$additionalModels = array();
		
		if (!empty($this->advancedFilterSettings['additional_actions'])) {
			$additionalModels = array_keys($this->advancedFilterSettings['additional_actions']);
		}

		return $additionalModels;
	}

	private function buildFilterArgs() {
		$filterArgs = array();

		$filterArgs['id'] = array(
			'type' => 'value',
			'field' => array($this->alias . '.id'),
			'_name' => __('ID')
		);

		if ($this->schema('deleted') !== null) {
			$filterArgs['deleted'] = array(
				'type' => 'subquery',
				'method' => 'findComplexType',
				'field' => $this->alias . '.id',
				'findField' => $this->alias . '.deleted',
				'_config' => [
					'type' => 'select'
				]
			);
			$filterArgs['deleted_date'] = array(
				'type' => 'subquery',
				'method' => 'findComplexType',
				'field' => $this->alias . '.id',
				'findField' => $this->alias . '.deleted_date',
				'_config' => [
					'type' => 'date'
				]
			);
		}
		
		foreach ($this->advancedFilter as $fieldSet) {
			foreach ($fieldSet as $field => $data) {
				$filterArgs[$field . '__show'] = array();

				if (!empty($data['filter'])) {
					$filterArgs[$field] = $data['filter'];
					$filterArgs[$field]['_name'] = (!empty($data['name'])) ? $data['name'] : '';
					// if (!empty($data['comparison'])) {
						$filterArgs[$field . '__comp_type'] = array();
					// }
					
					if ($data['type'] == 'date') {
						$filterArgs[$field . '__use_calendar'] = array();
					}

					if ($this->getFilterNoneConds($field, $data)) {
						$filterArgs[$field . '__none'] = array();
					}
				}

				$tmpData = $data;
				unset($tmpData['filter']);
				$filterArgs[$field]['_config'] = $tmpData;
			}
		}

		if (!isset($filterArgs['created'])) {
			$filterArgs['created'] = array(
				'type' => 'subquery',
	            'method' => 'findComplexType',
				'field' => $this->alias . '.id',
				'findField' => $this->alias . '.created',
				'_config' => [
					'type' => 'date'
				]
			);
		}

		if (!empty($filterArgs)) {
			$filterArgs['_limit'] = array();
			$this->filterArgs = $filterArgs;
		}
	}

	private function addOrderFields() {
		foreach ($this->advancedFilter as $cat => $fieldSet) {
			foreach ($fieldSet as $field => $data) {
				if ((!empty($data['contain']) || !empty($data['containable'])) && empty($data['many'])) {
					if (!empty($data['contain'])) {
						reset($data['contain']);
						$model = key($data['contain']);
						if (!empty($this->getAssociated($model)['association']) 
							&& ($this->getAssociated($model)['association'] == 'belongsTo' || $this->getAssociated($model)['association'] == 'hasOne')
						) {
							$this->advancedFilter[$cat][$field]['order'] = $model . '.' . $data['contain'][$model][0];
						}
					}
				}
			}
		}

		if (!empty($filterArgs)) {
			$filterArgs['_limit'] = array();
			$this->filterArgs = $filterArgs;
		}
	}

	public function getFilterNoneConds($field, $data, $condValueType = true, $condSubqueryType = true) {
		$schema = $this->schema($field);

		$conds = ($data['type'] == 'multiple_select');

		$valueConds = false;
		if ($condValueType) {
			$valueConds = $this->getFilterNoneValueConds($field, $data);
		}

		$subqueryConds = false;
		if ($condSubqueryType) {
			$subqueryConds = $this->getFilterNoneSubqueryConds($field, $data);
		}
		$conds &= $subqueryConds || $valueConds;
		
		return $conds;
	}

	public function getFilterNoneSubqueryConds($field, $data) {
		return $data['filter']['type'] == 'subquery' && $data['filter']['method'] == 'findByHabtm';
	}

	public function getFilterNoneValueConds($field, $data) {
		$schema = $this->schema($field);

		return $data['filter']['type'] == 'value' && (isset($schema) && (!empty($schema['null'])));
	}

	/**
	 * Wrapper function to save joined data using habtm join model.
	 *
	 * @param string $model Name of the model to join list of data with.
	 * @param mixed $list Array data of list of IDs to join with current item being saved or string field key to get from $this->data or NULL to search $this->data automatically.
	 * @param int $id Current item ID.
	 */
	public function joinHabtm($model, $list = null, $id = null, $skipCheck = false) {
		$assoc = $this->getAssociated($model);
		$with = $assoc['with'];
		$assocForeignKey = $assoc['associationForeignKey'];
		
		$list = $this->getJoinHabtmListData($model, $list);

		if (empty($list)) {
			return true;
		}

		$id = $this->getJoinHabtmId($id);

		$saveData = $this->getJoinHabtmSaveData($model, $list, $id);

		foreach ($saveData as $data) {
			if ($skipCheck === false) {
				// check if the related item we want to join exists, otherwise we would get foreign key constraint error
				$count = $this->{$model}->find('count', array(
					'conditions' => array(
						$model . '.' . $this->{$model}->primaryKey => $data[$assocForeignKey]
					),
					'recursive' => -1
				));

				if (empty($count)) {
					return false;
				}
			}

			$this->{$with}->create();
			if (!$this->{$with}->save($data)) {
				return false;
			}

		}

		return true;
	}
	protected function getJoinHabtmSaveData($model, $list, $id) {
		$assoc = $this->getAssociated($model);
		$assocForeignKey = $assoc['associationForeignKey'];

		$saveData = array();
		foreach ($list as $joinId) {
			$saveData[] = array(
				$assoc['foreignKey'] => $id,
				$assocForeignKey => $joinId
			);
		}

		return $saveData;
	}

	protected function getJoinHabtmListData($model, $list = null) {
		if (!empty($list) && is_array($list)) {
			return $list;
		}
		
		$assoc = $this->getAssociated($model);
		$assocForeignKey = $assoc['associationForeignKey'];

		//if field key is specified
		if (!empty($list) && is_string($list) && isset($this->data[$this->alias][$list])) {
			return $this->data[$this->alias][$list];
		}

		// search it automatically
		if ($list === null && isset($this->data[$this->alias][$assocForeignKey])) {
			return $this->data[$this->alias][$assocForeignKey];
		}

		return false;
	}

	protected function getJoinHabtmId($id = null) {
		if ($id === null) {
			$id = $this->id;
		}

		return $id;
	}

	/**
	 * Checks if related objects actually exists in database - used mainly for Import Tool feature.
	 * 
	 * @return boolean True if all exists or the check passed, False otherwise.
	 */
	public function checkRelatedExists($model, $list, $additionalConds = []) {
		$conds = is_array($list) && empty($list);
		$conds = $conds || (!is_array($list) && in_array($list, ['', null, false], true));
		if ($conds) {
			return true;
		}

		$conds = $this->_getRelatedExistsConds($model, $list, $additionalConds);
		$count = $this->{$model}->find('count', array(
			'conditions' => $conds,
			'recursive' => -1
		));

		// PHP 7.2 temporary fix for count() method
		// count() will now yield a warning on invalid countable types passed to the array_or_countable parameter.
		if (!is_array($list)) {
			$list = [$list];
		}

		return count($list) == $count;
	}

	/**
	 * Get conditions array parameter for checking existence of related objects.
	 * 
	 * @param  string $model           Model name.
	 * @param  array  $list            Array of provided IDs.
	 * @param  array  $additionalConds Additional conditions to consider.
	 * @return array                   Conditions.
	 */
	protected function _getRelatedExistsConds($model, $list, $additionalConds) {
		if (!is_array($list)) {
			$list = array($list);
		}

		$conds = [
			$model . '.' . $this->{$model}->primaryKey => $list
		];

		if (!empty($additionalConds)) {
			$conds = am($conds, $additionalConds);
		}

		return $conds;
	}

	/**
	 * Makes a field invalid for validation of related objects,
	 * letting user know what exactly is invalid in the list of data.
	 * 
	 * @param  string $model           Model name.
	 * @param  string $fieldName       Field name to invalidate with the proper message.
	 * @param  array  $list            List of items (foreign keys).
	 * @param  array  $additionalConds Additional conditions.
	 * @return void
	 */
	public function invalidateRelatedNotExist($model, $fieldName, $list, $additionalConds = []) {
		if (!$this->checkRelatedExists($model, $list, $additionalConds)) {
			$data = $this->{$model}->find('list', array(
				'conditions' => $this->_getRelatedExistsConds($model, $list, $additionalConds),
				'fields' => [
					$this->{$model}->escapeField($this->{$model}->primaryKey)
				],
				'recursive' => -1
			));

			// belongsTo relation has its own message
			if (!is_array($list)) {
				$message = __("Object which you are trying to use does not exist or is in a wrong format.\n\rThat is: <strong>%s</strong>", $list);
			}
			// hasAndBelongsToMany has also its own message
			else {
				$foreignKeys = array_keys($data);
				$nonExistent = array_diff($list, $foreignKeys);
				$message = __(
					"Some items you are trying to use does not exist.\n\rThese are: <strong>%s</strong>",
					implode(', ', $nonExistent)
				);
			}

			$this->invalidate($fieldName, $message);
		}
	}

	/**
	 * General method to fetch record's title.
	 */
	public function getRecordTitle($id) {
		$data = $this->find('first', array(
			'conditions' => array(
				$this->alias . '.' . $this->primaryKey => $id
			),
			'fields' => array(
				$this->alias . '.' . $this->displayField
			),
			'recursive' => -1
		));

		$value = "";
		if (isset($data[$this->alias][$this->displayField])) {
			$value = $data[$this->alias][$this->displayField];
		}

		return getEmptyValue($value);
	}

	public function statusExpired($conditions = null) {
		$data = $this->find('count', [
			'conditions' => [
				$this->alias . '.id' => $this->id,
			] + $conditions,
			'recursive' => -1
		]);

		return (boolean) $data;
	}

	public function _statusExpiredReviews() {
		$data = $this->Review->find('count', [
			'conditions' => [
				'Review.foreign_key' => $this->id,
				'Review.model' => $this->alias,
				'Review.completed' => REVIEW_NOT_COMPLETE,
				'DATE(Review.planned_date) < DATE(NOW())'
			],
			'recursive' => -1
		]);

		return (boolean) $data;
	}

	public function getExpiredReviews($id, $model) {
		$today = date('Y-m-d', strtotime('now'));

		$expiredReviews = $this->Review->find('list', array(
			'conditions' => array(
				'Review.foreign_key' => $id,
				'Review.model' => $model,
				'Review.completed' => REVIEW_NOT_COMPLETE,
				'DATE(Review.planned_date) <' => $today
			),
			'fields' => array('id', 'id'),
			'recursive' => -1
		));

		return $expiredReviews;
	}

	public function lastMissingReview($id, $model) {
		$today = date('Y-m-d', strtotime('now'));

		$expiredReviews = $this->Review->find('first', array(
			'conditions' => array(
				'Review.foreign_key' => $id,
				'Review.model' => $model,
				'Review.completed' => REVIEW_NOT_COMPLETE,
				'DATE(Review.planned_date) <' => $today
			),
			'fields' => array('id', 'planned_date'),
			'order' => array('Review.planned_date' => 'DESC'),
			'recursive' => -1
		));

		if (isset($expiredReviews['Review']['planned_date'])) {
			return $expiredReviews['Review']['planned_date'];
		}

		return false;
	}

	/**
	 * Pass-throught function for Setting::getVariable();
	 */
	public function getSettingVariable($var, $cache = true)
	{
		if ($cache && Configure::check('Eramba.Settings.' . $var)) {
			return Configure::read('Eramba.Settings.' . $var);
		}
		
		$setting = ClassRegistry::init('Setting');
		return $setting->getVariable($var);
	}

	public function getStatusFilterOption() {
		return getStatusFilterOption();
	}

	public function getStatusFilterOptionInverted() {
		$arr = getStatusFilterOption();

		$k = array_keys($arr);
		$v = array_values($arr);

		$rv = array_reverse($v);

		$options = array_combine($k, $rv);
		return $options;
	}

	public function setComparisonType($data = array(), $filter) {
		$query = array();
		$field = (!empty($filter['field'])) ? $filter['field'] : $this->alias . '.' . $filter['name'];
		if ($filter['_config']['type'] == 'date') {
			$field = 'DATE(' . $field . ')';
		}
		$query[$field . ' ' . getComparisonTypes()[$filter['comp_type']]] = $data[$filter['name']];
		return $query;
	}

	/**
	 * Filter wrapper method for inherited statuses.
	 * example:
		...
		'filter' => array(
			'type' => 'subquery',
			'method' => 'findByInheritedStatus',
			'field' => 'Risk.id',
			'status' => array(

				// model where to search the status
				'model' => 'SecurityService',

				// column for the search
				'field' => 'ongoing_corrective_actions',

				// 1. option is to make the provided searched value negative (for custom uses)
				// 'negativeValue' => true,

				// 2. option is to make the searched value custom
				// 'customValue' => SECURITY_SERVICE_DESIGN
			)
		)
		...
	 */
	public function findByInheritedStatus($data = array(), $filterParams = array()) {
		$inherit = $filterParams['status'];
		// debug($data);
		// debug($filterParams);

		$assoc = $this->getAssociated($inherit['model']);
		// debug($assoc);exit;

		if (empty($assoc)) {
			AppError('Filter status model association does not exist!');
			return false;
		}

		if ($assoc['association'] != 'hasAndBelongsToMany') {
			AppError('Filter for inherited statuses does not support any other association than HABTM');
			return false;
		}

		$model = $inherit['model'];
		$with = $assoc['with'];
		$findValue = $data[$filterParams['name']];

		if (!empty($inherit['negativeValue'])) {
			$findValue = !$findValue;
		}

		if (!empty($inherit['customValue'])) {
			$findValue = $inherit['customValue'];
		}

		$comparisonOperator = false;
		if (!empty($inherit['comparisonOperator'])) {
			$comparisonOperator = $inherit['comparisonOperator'];
		}

		$this->{$with}->bindModel(array(
			'belongsTo' => array(
				$model
			)
		));
		$this->{$with}->Behaviors->attach('Containable', array(
				'autoFields' => false
			)
		);
		$this->{$with}->Behaviors->attach('Search.Searchable');

		$conditionField = $model . '.' . $inherit['field'];
		if (!empty($comparisonOperator)) {
			$conditionField .= ' ' . $comparisonOperator;
		}

		$query = $this->{$with}->getQuery('all', array(
			'conditions' => array(
				$conditionField => $findValue
			),
			'fields' => array(
				$with . '.' . $assoc['foreignKey']
			)
		));

		return $query;
	}

	public function findByHabtm($data = array(), $filterParams = array()) {
		$findByModel = $filterParams['findByModel'];

		$assoc = $this->getAssociated($findByModel);

		if (empty($assoc)) {
			AppError('Filter status model association does not exist!');
			return false;
		}

		if ($assoc['association'] != 'hasAndBelongsToMany') {
			AppError('Filter for inherited statuses does not support any other association than HABTM');
			return false;
		}

		$with = $assoc['with'];

		$this->{$with}->Behaviors->attach('Containable', array(
				'autoFields' => false
			)
		);
		$this->{$with}->Behaviors->attach('Search.Searchable');

		$value = $data[$assoc['associationForeignKey']];
		if (!is_array($value) && $value === ADVANCED_FILTER_MULTISELECT_NONE) {

			$queryChild = $this->{$with}->getQuery('all', array(
				'fields' => array(
					$with . '.' . $assoc['foreignKey']
				)
			));
			// debug($queryChild);
			$query = $this->getQuery('all', array(
				'conditions' => array(
					$this->alias . '.' . $this->primaryKey . ' NOT IN (' . $queryChild . ')'
				),
				'fields' => array(
					$this->alias . '.' . $this->primaryKey
				),
				'group' => array($this->alias . '.' . $this->primaryKey),
				'recursive' => -1
			));
		}
		else {
			$foreignKeyParam = $with . '.' . $assoc['foreignKey'];
			$assocKeyParam = $with . '.' . $assoc['associationForeignKey'];

			$havingCount = (!empty($filterParams['orCondition'])) ? 1 : count($value);

			$queryChild = $this->{$with}->getQuery('all', array(
				'fields' => array(
					$foreignKeyParam
				),
				'group' => $foreignKeyParam . ' HAVING COUNT(' . $foreignKeyParam . ')  >= ' . $havingCount
			));

			$conditions = array(
				$assocKeyParam => $value,
				$foreignKeyParam . ' IN (' . $queryChild . ')'
			);
			if (!empty($assoc['conditions'])) {
				$conditions = am($assoc['conditions'], $conditions);
			}
			if (!empty($filterParams['conditions'])) {
				$conditions = am($filterParams['conditions'], $conditions);
			}

			$query = $this->{$with}->getQuery('all', array(
				'conditions' => $conditions,
				'fields' => array(
					$foreignKeyParam
				),
				'group' => $foreignKeyParam
			));
		}

		return $query;
	}

	public function findByHabtmComparison($data = array(), $filter = array()) {
		$model = $filter['findByModel'];
		$assoc = $this->getAssociated($model);

		if (empty($assoc)) {
			AppError('Filter status model association does not exist!');
			return false;
		}

		if ($assoc['association'] != 'hasAndBelongsToMany') {
			AppError('Filter for inherited statuses does not support any other association than HABTM');
			return false;
		}

		$with = $assoc['with'];

		$this->{$with}->Behaviors->attach('Containable', array('autoFields' => false));
		$this->{$with}->Behaviors->attach('Search.Searchable');

		$query = $this->{$with}->getQuery('all', array(
			'conditions' => array(
				$filter['comparisonField'] . ' ' . getComparisonTypes()[$filter['comp_type']] => $data[$filter['name']]
			),
			'joins' => array(
				array(
					'table' => $this->{$model}->table,
					'alias' => $model,
					'type' => 'INNER',
				)
			),
			'fields' => array(
				$with . '.' . $assoc['foreignKey']
			)
		));

		return $query;
	}

	public function findByCustomField($data = array(), $filterParams = array()) {
		$alias = 'CustomFieldValue';
		$this->bindCustomFieldValues();
		$this->{$alias}->Behaviors->attach('Containable', array(
				'autoFields' => false
			)
		);
		$this->{$alias}->Behaviors->attach('Search.Searchable');

		$assoc = $this->getAssociated($alias);

		$arg = sprintf('CustomFields_%s', $filterParams['customField']['slug']);
		$value = trim($data[$arg]);

		// default column condition extracted from associacion data
		$conds = $assoc['conditions'];

		// additional conditions
		$conds[$alias . '.custom_field_id'] = $filterParams['customField']['id'];
		
		if ($filterParams['customField']['type'] == CustomField::TYPE_DATE) {
			$conds[$alias . '.value ' . getComparisonTypes()[$filterParams['comp_type']]] =  $value;
		}

		if ($filterParams['customField']['type'] == CustomField::TYPE_DROPDOWN) {
			$conds[$alias . '.value'] = $value;
		}
		else {
			$conds[$alias . '.value LIKE'] = '%' . $value . '%';
		}

		$query = $this->{$alias}->getQuery('all', array(
			'conditions' => $conds,
			'fields' => array(
				$alias . '.' . $assoc['foreignKey']
			),
			'recursive' => -1
		));

		return $query;
	}

	public function findByComment($data = array(), $filter) {
		$this->Comment->Behaviors->attach('Containable', array('autoFields' => false));
		$this->Comment->Behaviors->attach('Search.Searchable');

		$query = $this->Comment->getQuery('all', array(
			'conditions' => array(
				'Comment.message LIKE' => '%' .  $data[$filter['name']] . '%',
				'Comment.model' => $this->alias
			),
			'fields' => array(
				'Comment.foreign_key'
			),
			'recursive' => -1
		));

		return $query;
	}

	public function findByAttachment($data = array(), $filter) {
		$this->Attachment->Behaviors->attach('Containable', array('autoFields' => false));
		$this->Attachment->Behaviors->attach('Search.Searchable');

		$query = $this->Attachment->getQuery('all', array(
			'conditions' => array(
				$filter['conditionFiled'] . ' LIKE' => '%' .  $data[$filter['name']] . '%',
				'Attachment.model' => $this->alias
			),
			'fields' => array(
				'Attachment.foreign_key'
			),
			'recursive' => -1
		));

		return $query;
	}

	public function getFilterRelatedData($fieldData = array()) {
		if (empty($fieldData['data']['findByModel'])) {
			AppError('findByModel parameter for data key is required.');
			return false;
		}

		$findByModel = $fieldData['data']['findByModel'];

		$assoc = $this->getAssociated($findByModel);
		// if ($assoc['association'] != 'belongsTo') {
		// 	AppError('Only belongsTo association is supported for this method');
		// 	return false;
		// }

		// debug($assoc);
		// debug($fieldData);exit;

		$list = $this->{$findByModel}->find('list', array(
			// 'fields' => array('ComplianceTreatmentStrategy.id', 'ComplianceTreatmentStrategy.name'),
			// 'order' => array('ComplianceTreatmentStrategy.name' => 'ASC'),
			'recursive' => -1
		));
		
		return $list;
	}

	public function getUsers()
	{
		$User = ClassRegistry::init('User');
		$User->virtualFields['full_name'] = 'CONCAT(User.name, " ", User.surname)';
		$users = $User->find('list', array(
			'conditions' => array(),
			'fields' => array('User.id', 'User.full_name'),
		));

		return $users;
	}

	protected function _findSelf($state, $query, $results = array()) {
		if ($state === 'before') {
			// debug($query['habtm']);exit;
			$habtm = $query['habtm'];
			if (is_string($habtm)) {
				$habtm = $this->getAssociated($habtm);
			}

			$habtm2 = $habtm;
			$habtm2['foreignKey'] = $habtm2['associationForeignKey'];
			$habtm2['associationForeignKey'] = $habtm['foreignKey'];

			$this->bindModel(array(
				'hasAndBelongsToMany' => array(
					'AssociatedNode1' => $habtm,
					'AssociatedNode2' => $habtm2
				)
			));
			// debug($habtm);debug($habtm2);

			$query['recursive'] = -1;
			
			if (!empty($query['habtmAssoc'])) {
				$query['contain']['AssociatedNode1'] = $query['habtmAssoc'];
				$query['contain']['AssociatedNode2'] = $query['habtmAssoc'];//debug($query);exit;
			}
			else {
				$query['contain'][] = 'AssociatedNode1';
				$query['contain'][] = 'AssociatedNode2';
			}

			return $query;
		}

		foreach($results as &$result) {
			if(isset($result['AssociatedNode1']) || isset($result['AssociatedNode2'])) {
				if (!empty($result['AssociatedNode1'])) {
					//debug($result);exit;
				}
				$associated_nodes = array();

				if(isset($result['AssociatedNode1'])) {
					foreach($result['AssociatedNode1'] as $associated_node) {
						$associated_nodes[] = $associated_node;
					}
				}

				if(isset($result['AssociatedNode2'])) {
					foreach($result['AssociatedNode2'] as $associated_node) {
						$associated_nodes[] = $associated_node;
					}
				}

				unset($result['AssociatedNode1']);
				unset($result['AssociatedNode2']);

				$result['SelfJoin'] = $associated_nodes;
			}
		}

		unset($result);

		return $results;
	}

	/**
	 * Get all HABTM related data and format it to optimize db queries for huge indexes.
	 */
	public function getAllHabtmData($joinModel, $query = array()) {
		$join = $this->getAssociated($joinModel);
		$assocForeignKey = $join['associationForeignKey'];
		$foreignKey = $join['foreignKey'];

		$this->{$joinModel}->bindModel(array(
			'hasMany' => array(
				$join['with']
			)
		));

		$query['contain'][$join['with']] = array('fields' => array($foreignKey));

		$data = $this->{$joinModel}->find('all', array(
			//'fields' => array('RiskClassification.name', 'RiskClassification.value', 'RiskClassification.criteria'),
			'contain' => $query['contain']
		));
		$formattedData = $joinIds = array();
		foreach ($data as $item) {
			$formattedData[$item[$joinModel]['id']] = $item;

			foreach ($item[$join['with']] as $assocData) {
				if (!isset($joinIds[$assocData[$foreignKey]])) {
					$joinIds[$assocData[$foreignKey]] = array();
				}

				$joinIds[$assocData[$foreignKey]][] = $assocData[$assocForeignKey];
			}
		}

		return array(
			'formattedData' => $formattedData,
			'joinIds' => $joinIds
		);
	}

	/**
	 * Adds a list validation for a field.
	 */
	protected function addListValidation($field, $list = array(), $message = null) {
		if (empty($message)) {
			$message = __('Selected option is not valid');
		}

		return $this->validator()->add($field, 'inList', array(
			'rule' => array('inList', $list),
			'message' => $message
		));
	}

	/**
	 * Custom validation method that checks and compare array_keys() from a callback method with the user input.
	 * Example:
	 * 'step_type' => array(
			'notBlank' => [
				'rule' => 'notBlank',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field is required'
			],
			'callable' => [
				'rule' => ['callbackValidation', ['WorkflowStagesNextStage', 'stepTypes']],
				'message' => 'Incorrect step type'
			]
		)
	 */
	public function callbackValidation($checkValue, $callback) {
		if (!is_callable($callback)) {
			trigger_error('Validation callback is not a callable type');
		}

        $value = array_values($checkValue);
        $value = $value[0];

        $list = array_keys(call_user_func($callback));

        return Validation::inList($value, $list);
    }

	public function findByTags($data = array(), $filterParams = array()) {
		$this->Tag->Behaviors->attach('Containable', array('autoFields' => false));

		$this->Tag->Behaviors->attach('Search.Searchable');

		$query = $this->Tag->getQuery('all', array(
			'conditions' => array(
				'Tag.title' => $data[$filterParams['name']],
				'model' => $filterParams['model']
			),
			'fields' => array(
				'Tag.foreign_key'
			)
		));

		return $query;
	}

	public function getTags($data = array()) {
		$model = (!empty($data['filter']['model'])) ? $data['filter']['model'] : $this->alias;

		$Tag = ClassRegistry::init('Tag');

		$tags = $Tag->find('list', array(
			'conditions' => array(
				'Tag.model' => $model,
			),
			'order' => array('Tag.title' => 'ASC'),
			'fields' => array('Tag.title'),
			'group' => array('Tag.title'),
			'recursive' => -1
		));

		$tags = array_combine($tags, $tags);
		return $tags;
	}

	/**
	 * Transforms the data array to save the HABTM relation.
	 *
	 * @param mixed null|array $keys Null to process all defined HABTM relations; or array of association keys to process.
	 */
	public function transformDataToHabtm($keys = null) {
		foreach (array_keys($this->hasAndBelongsToMany) as $model){
			if (is_array($keys) && !in_array($model, $keys)) {
				continue;
			}

			if(isset($this->data[$this->name][$model])){
				$this->data[$model][$model] = $this->data[$this->name][$model];
				unset($this->data[$this->name][$model]);
			}
		}
	}

	/**
	 * Initializes the model for writing a new record, loading the default values
	 * for those fields that are not defined in $data, and clearing previous validation errors.
	 * Especially helpful for saving data in loops.
	 *
	 * @param bool|array $data Optional data array to assign to the model after it is created. If null or false,
	 *   schema data defaults are not merged.
	 * @param bool $filterKey If true, overwrites any primary key input with an empty value
	 * @return array The current Model::data; after merging $data and/or defaults from database
	 * @link https://book.cakephp.org/2.0/en/models/saving-your-data.html#model-create-array-data-array
	 */
	public function create($data = array(), $filterKey = false)
	{
		$data = parent::create($data, $filterKey);

		$this->_storedHabtmOriginalData = [];

		return $data;
	}

	/**
	 * Storing the values for previous data that will be restored after successfull save because can cause conflicts
	 * on some other saves from other models.
	 * 
	 * @var array
	 */
	protected $_storedHabtmOriginalData = [];

	/**
	 * sets join conditions to data as join table extra fields
	 * 
	 * @param mixed null|array $keys Null to process all defined HABTM relations; or array of association keys to process.
	 */
	public function setHabtmConditionsToData($keys = null) {
		$this->restoreHabtmConditionalData();

		foreach (array_keys($this->hasAndBelongsToMany) as $model){
			if (is_array($keys) && !in_array($model, $keys)) {
				continue;
			}

			$assoc = $this->getAssociated($model);
			$with = $assoc['with'];

			// @todo temporary solution to skip user fields associations as they have their own
			// management of this situation (e.g "User-3" strings that needs to be exploded by "-"... etc)
			if (in_array($with, ['UserFields.UserFieldsUser', 'UserFields.UserFieldsGroup'])) {
				continue;
			}

			$data = array();
			if (isset($this->data[$model][$model])) {
				$data = $this->data[$model][$model];
			}
			elseif (isset($this->data[$model])) {
				$data = $this->data[$model];
			}

			if (!empty($data) && !empty($this->hasAndBelongsToMany[$model]['conditions'])) {
				$this->_storedHabtmOriginalData[$model] = $this->data[$model];
				
				$dataItems = array();
				foreach ((array) $data as $assocForeignKey) {
					$item = array();
					$item[$this->hasAndBelongsToMany[$model]['associationForeignKey']] = $assocForeignKey;
					foreach ($this->hasAndBelongsToMany[$model]['conditions'] as $field => $value) {
						$field = explode('.', $field);
						$item[end($field)] = $value;
					}
					$dataItems[] = $item;
				}
				
				if (!empty($this->data[$model][$model])) {
					$this->data[$model][$model] = $dataItems;
				}
				else {
					$this->data[$model] = $dataItems;
				}
			}
		}
	}

	/**
	 * When using conditional HABTM save, it might be the case of a additional save(s) on afterSave callback
	 * from within a Behavior for example, which then crashes the model.
	 */
	public function restoreHabtmConditionalData() {
		foreach ($this->_storedHabtmOriginalData as $model => $values) {
			$this->data[$model] = $values;
		}
	}
	
	public function findComplexType($data = array(), $filterParams = array()) {
		$filterAdapter = new FilterAdapter();
		$query = $filterAdapter->getQuery($this, $filterParams, $data);

		return $query;
	}

	public static $listSources = null;
	public static function listSources($Model)
	{
		if (!isset(self::$listSources[$Model->useDbConfig])) {
			self::$listSources[$Model->useDbConfig] = ConnectionManager::getDataSource($Model->useDbConfig)->listSources();
		}

		return self::$listSources[$Model->useDbConfig];
	}

	/**
     * default list finder
     * 
     * @return array
     */
    public function getList() {
    	$sources = self::listSources($this);
    	if (!in_array(strtolower($this->tablePrefix . $this->table), array_map('strtolower', $sources), true)) {
			return [];
		}

        $data = $this->find('list', [
            'order' => [
                $this->alias . '.' . $this->displayField => 'ASC'
            ]
        ]);

        return $data;
    }

    /**
	 * Validates if there is at least one value selected within multiple HABTM fields.
	 * 
	 * @param  array  $checkModels HABTM Model aliases to check in $this->data
	 * @param  string $errorMessage error message
	 * @param  boolean $invalidateAll invalidate all check models
	 * @return bool                True if one or more values are selected, False if no items selected.
	 */
	public function validateMultipleFields($checkModels = [], $errorMessage, $invalidateAll = false) {
		$ret = false;
		foreach ($checkModels as $check) {
			if (!isset($this->data[$this->alias][$check])) {
				continue;
			}

			$val = (array) $this->data[$this->alias][$check];
			$val = array_filter($val);
			
			$ret = $ret || count($val);
		}

		if (!$ret) {
			$invalidateModels = ($invalidateAll) ? $checkModels : [reset($checkModels)];
			foreach ($invalidateModels as $model) {
				$this->invalidate($model , $errorMessage);
			}
		}

		return $ret;
	}

	/**
	 * Custom URL rule to allow non qualified domains.
	 */
	public function urlCustom($check) {
        $value = array_values($check);
        $value = $value[0];
        
        return AppValidation::url($value);
    }

    /**
     * Catch property change and create notification.
     */
    public function propertyChangeNotification($propertyName, $oldValue, $newValue, $wantedProperty, $notification, $labels = null) {
    	if ($propertyName == $wantedProperty && $oldValue !== $newValue && $oldValue !== null) {
    		if ($labels !== null) {
    			$oldValue = $labels[$oldValue];
    			$newValue = $labels[$newValue];
    		}

    		if (AppModule::loaded('NotificationSystem')) {
				App::uses('NotificationSystemManager', 'NotificationSystem.Lib');
				App::uses('NotificationSystemSubject', 'NotificationSystem.Lib');

	    		$NotificationSubject = new NotificationSystemSubject(['force' => true]); 
				$NotificationSubject->old_value = $oldValue;
				$NotificationSubject->new_value = $newValue;

				$NotificationSystemManager = new NotificationSystemManager();
				return $NotificationSystemManager->triggerNotification(
					'status_change',
					$this->modelFullName(),
					$this->id,
					$NotificationSubject
				);
			}
			else {
				return true;
			}
		}

		return false;
    }

    const SYSTEM_LOG_COMMENT = 991;
    const SYSTEM_LOG_ATTACHMENT = 992;
    const SYSTEM_LOG_ATTACHMENT_DELETE = 993;
    const SYSTEM_LOG_WIDGET_VIEW = 994;
    
    public function getSystemLogsConfig() {
        return [
            'logs' => [
                self::SYSTEM_LOG_COMMENT => [
                    'action' => self::SYSTEM_LOG_COMMENT,
                    'label' => __('Comment add'),
                    'message' => __('Comment "%s" added to question "%s".')
                ],
                self::SYSTEM_LOG_ATTACHMENT => [
                    'action' => self::SYSTEM_LOG_ATTACHMENT,
                    'label' => __('Attachment add'),
                    'message' => __('Attachment "%s" added to question "%s".')
                ],
                self::SYSTEM_LOG_ATTACHMENT_DELETE => [
                    'action' => self::SYSTEM_LOG_ATTACHMENT_DELETE,
                    'label' => __('Attachment delete'),
                    'message' => __('Attachment "%s" deleted from question "%s".')
                ],
                self::SYSTEM_LOG_WIDGET_VIEW => [
                    'action' => self::SYSTEM_LOG_WIDGET_VIEW,
                    'label' => __('Widget show'),
                    'message' => __('Widget show.')
                ],
            ],
        ];
    }

    public function modelFullName() {
    	return (!empty($this->plugin)) ? "{$this->plugin}.{$this->name}" : $this->name;
    }
    
    public function modelFullAlias() {
    	return (!empty($this->plugin)) ? "{$this->plugin}.{$this->alias}" : $this->alias;
    }

    protected function _getModelObjectReminderNotification($className = null)
    {
    	if ($className === null) {
    		$plugin = null;
    		$className = $this->alias;
    	} else {
    		list($plugin, $className) = pluginSplit($className);
    	}
    	
    	return [
			'type' => NOTIFICATION_TYPE_AWARENESS,
			'className' => $plugin . '.' . $className . 'ObjectReminder',
			'label' => __('Recurrent Awareness Reminder')
		];
    }

    public function getNotificationSystemConfig()
	{
		return [
			'macros' => true,
			'notifications' => [
				'object_reminder' => [
					'type' => NOTIFICATION_TYPE_AWARENESS,
					'className' => '.ObjectReminder',
					'label' => __('Recurrent Awareness Reminder')
				],
				'comments' => [
					'type' => NOTIFICATION_TYPE_DEFAULT,
					'className' => 'Comments.Comments',
					'label' => __('Only New Comment')
				],
				'attachments' => [
					'type' => NOTIFICATION_TYPE_DEFAULT,
					'className' => 'Attachments.Attachments',
					'label' => __('Only New Attachment')
				],
				'widget_object' => [
					'type' => NOTIFICATION_TYPE_DEFAULT,
					'className' => 'Widget.WidgetObject',
					'label' => __('New Comment or Attachment')
				],
				'digest' => [
					'type' => NOTIFICATION_TYPE_DEFAULT,
					'className' => 'Widget.Digest',
					'key' => 'value',
					'label' => __('Digest of Comments & Attachments')
				],
				'advanced_filters' => [
					'type' => NOTIFICATION_TYPE_REPORT,
					'className' => 'AdvancedFilters.AdvancedFilters',
					'label' => __('Send Scheduled Filters')
				],
				'reports' => [
					'type' => NOTIFICATION_TYPE_REPORT,
					'className' => 'Reports.Reports',
					'label' => __('Send Scheduled Report')
				]
			]
		];
	}

	/**
	 * Wrapper for a notification trigger method also checks if notifications feature exists and is enabled.
	 * 
	 * @return boolean
	 */
	public function triggerNotification($notification, $id, $Subject)
	{
		if (AppModule::loaded('NotificationSystem')) {
			if (!$this->Behaviors->enabled('NotificationSystem.NotificationSystem')) {
				$this->Behaviors->load('NotificationSystem.NotificationSystem');
			}

			return $this->Behaviors->NotificationSystem->triggerNotification($this, $notification, $id, $Subject);
		}

		return true;
	}

	/**
	 * Simulation of save action for FormReload feature.
	 */
	public function formReloadSave()
	{
		return false;
	}

	/**
	 * Pass through function for parent::validateAssociated to avoid using data parameter as reference so it can be called by call_user_func function
	 */
	public function customValidateAssociated($data, $options = [])
	{
		return parent::validateAssociated($data, $options);
	}

	public function hasSectionIndex()
	{
		return false;
	}

	public function traverse($path)
	{
		$models = explode('.', $path);

		$Model = $this;

		foreach ($models as $alias) {
			if (empty($Model->{$alias})) {
				break;
			}

			$Model = $Model->{$alias};
		}

		return $Model;
	}

	public function getDisplayFilterFields()
	{
		$fields = [];

		if (!empty($this->displayField) && !empty($this->filterArgs[$this->displayField])) {
			$fields[] = $this->displayField;
		}

		return $fields;
	}

	protected function _readDataSource($type, $query)
	{
		if (!$this->_getAppModelConfig('elements', 'useCache')) {
			return parent::_readDataSource($type, $query);
		}

		$configName = $this->createCacheConfig();
		$cacheName = md5(json_encode($query) . json_encode($this->hasOne) . json_encode($this->belongsTo));
 		$cache = Cache::read($cacheName, $configName);
		if ($cache !== false) {
			$this->resetAssociations();
			return $cache;
		}

		$results = parent::_readDataSource($type, $query);
		Cache::write($cacheName, $results, $configName);
		return $results;
	}

	// protected function _doSave($data = null, $options = array())
	// {
	// 	Cache::clear(false, 'model_cache');
	// 	return parent::_doSave($data, $options);
	// }

	// public function updateAll($fields, $conditions = true)
	// {
	// 	Cache::clear(false, 'model_cache');
	// 	return parent::updateAll($fields, $conditions);
	// }

	// public function delete($id = null, $cascade = true)
	// {
	// 	Cache::clear(false, 'model_cache');
	// 	return parent::_doSave($data, $options);
	// }

	// public function deleteAll($conditions, $cascade = true, $callbacks = false)
	// {
	// 	Cache::clear(false, 'model_cache');
	// 	return parent::deleteAll($conditions, $cascade, $callbacks);
	// }
	
	public function afterSave($created, $options = array())
	{
		// if ($this->_getAppModelConfig('elements', 'useCache')) {
			$this->clearCacheGroups();
		// }

		return parent::afterSave($created, $options);
	}

	public function afterDelete()
	{
		// if ($this->_getAppModelConfig('elements', 'useCache')) {
			$this->clearCacheGroups();
		// }

		return parent::afterDelete();
	}

	protected function clearCacheGroups()
	{
		$configName = $this->createCacheConfig();
		$settings = Cache::settings($configName);
		foreach ($settings['groups'] as $group) {
			Cache::clearGroup($group, $configName);
		}
	}

	protected function createCacheConfig()
	{
		$groups = array_merge([$this->alias], array_keys($this->getAssociated()));
		$configName = 'model_cache-' . $this->alias . '-' . md5(json_encode($groups));
		if (!Cache::isInitialized($configName)) {
			$config = [
				'engine' => 'File',
				'duration' => '+1 day',
				'path' => CACHE . 'model_cache' . DS,
				'prefix' => 'model_cache_',
				'groups' => $groups
			];
			Cache::config($configName, $config);
		}

		return $configName;
	}

	protected function isNewEntity()
	{
		return empty($this->id) && empty($this->data[$this->name]['id']) ? true : false;
	}
}
