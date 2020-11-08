<?php
App::uses('ClassRegistry', 'Utility');
App::uses('Inflector', 'Utility');
App::uses('AppModel', 'Model');
App::uses('CakeEvent', 'Event');
App::uses('CakeEventManager', 'Event');
App::uses('CakeEventListener', 'Event');
App::uses('FieldDataExtensionCollection', 'FieldData.Model/FieldData');

class FieldDataEntity implements CakeEventListener {
	/**
	 * Field type that acts as checkbox having 2 values - ON/OFF or 1/0.
	 */
	const FIELD_TYPE_TOGGLE = 'toggle';

	/**
	 * Field type that acts as tags with Taggable support.
	 */
	const FIELD_TYPE_TAGS = 'tags';

	/**
	 * Other standard input types.
	 */
	const FIELD_TYPE_SELECT = 'select';
	const FIELD_TYPE_MULTIPLE = 'multiple';
	const FIELD_TYPE_DATE = 'date';
	const FIELD_TYPE_TEXT = 'text';
	const FIELD_TYPE_TEXTAREA = 'textarea';
	const FIELD_TYPE_NUMBER = 'number';
	const FIELD_TYPE_EDITOR = 'editor';
	const FIELD_TYPE_FILE = 'file';
	const FIELD_TYPE_HIDDEN = 'hidden';
	const FIELD_TYPE_OBJECT_STATUS = 'ObjectStatus';

	public $alias = null;

	protected $_field = null;
	protected $_config = array();
	protected $_modelName = null;
	protected $_association = [];

	/**
	 * Readable "human-friendly" label for the field shown to the end-user.
	 * 
	 * @var string
	 */
	protected $_label = null;

	/**
	 * Field detailed description on what the field does, for end-user. Usually written in a "help-box" under the field.
	 * 
	 * @var null|string
	 */
	protected $_description = null;

	/**
	 * Field help for additional field info. Usually written in a info icon under the field.
	 * 
	 * @var null|string
	 */
	protected $_help = null;

	/**
	 * Group key name to which this FieldDataEntity belongs. NULL to skip group.
	 * 
	 * @var null|string
	 */
	protected $_group = null;

	/**
	 * Options for a select/multiselect fields. Should be valid for `FormHelper::input()` config.
	 * NOTE: Options for associated fields (i.e BelongsTo, HABTM, ..) are filled in automatically.
	 * NOTE2: Options for HABTM association doesnt respect association's defined 'conditions' parameter.
	 * 
	 * Possible to define here:
	 *    - Array of data
	 *    - Callback, should return array of data, called before View rendering
	 *    	@see http://php.net/manual/en/language.types.callable.php
	 *    	Example: 'options' => array($this, 'modelMetohdName')
	 *    	
	 *    	Example with custom arguments:
	 *    	'options' => [
	 *		   'callable' => [$this, 'methodName'],
	 *		   'passParams' => true
	 *		]
	 *		
	 * 		Where method will be called with additional arguments:
	 * 		
	 * 		public function methodName(FieldDataEntity $Field, $arg1, ...) {...}
	 * 
	 * @var null|string|array
	 */
	protected $_options = null;

	/**
	 * Variable name with processed view options, that will be set for into the view.
	 * Leave null to generate the variable using default cake convention.
	 * 
	 * @var null|string
	 */
	protected $_viewVar = null;

	/**
	 * TBD.
	 * 
	 * @var boolean
	 */
	protected $_hidden = null;

	/**
	 * Type for a form input. Should be valid for `FormHelper::input()` config.
	 * Leave this value NULL if you dont want to override the FormHelper defaults.
	 * 
	 * @var boolean
	 */
	protected $_type = null;

	/**
	 * Field when is editable, then shows inside forms and is generally able to be changed by the user doing edits.
	 * Applies to for example Bulk Action editing or any ordinary form built using FieldData Layer.
	 * 
	 * @var bool
	 */
	protected $_editable = null;

	/**
	 * Applies for a single select field, where possible to choose 'empty' value.
	 * Should be valid for `FormHelper::input()` config.
	 * 
	 * @var bool|string
	 */
	protected $_empty = false;

	/**
	 * Default value for this field.
	 * 
	 * @var mixed
	 */
	protected $_default = null;

	/**
	 * Validation definition.
	 * 
	 * @var null|array
	 */
	protected $_validate = null;

	/**
	 * Allows to customize CakeSchema returned config for this field.
	 * This is experimental now.
	 * 
	 * @var null|array
	 */
	protected $_schema = null;

	/**
	 * Uses a custom Helper class for rendering a field instead of FieldDataHelper by default.
	 * Your custom class must extend FieldDataHelper class.
	 * 
	 * @var null|string
	 */
	protected $_renderHelper = null;

	/**
	 * Default config for a FieldData class.
	 * @var array
	 */
	protected $_defaults = array(
		'label' => null,
		'description' => null,
		'help' => null,
		'group' => 'default',
		'options' => false,
		'viewVar' => null,
		'hidden' => false,
		'type' => null,
		'editable' => false,
		'empty' => null,
		'validate' => null,
		'schema' => array(), //@deprecated This configuration will be read-only
		'renderHelper' => null,
		'Extensions' => [],
		'dependency' => null,
		'inlineEdit' => false
	);

	/**
	 * Reference to a Field Group Class where this field belongs, NULL for uncategorized fields.
	 * 
	 * @var FieldGroupEntity|null
	 */
	protected $_groupClass = null;

	/**
	 * Instance of the CakeEventManager this helper is using
	 * to dispatch inner events.
	 *
	 * @var CakeEventManager
	 */
	protected $_eventManager = null;

	/**
	 * List of extension objects attached to this FieldDataEntity class.
	 *
	 * @var array
	 */
	public $Extensions = array();

	/**
	 * Dependencies for this field.
	 * 
	 * @var null|boolean
	 */
	protected $_dependency = null;

	/**
	 * Dependencies for this field.
	 * 
	 * @var null|boolean
	 */
	protected $_inlineEdit = false;


	/**
	 * @todo attach field entities to group class.
	 */
	public function __construct($config, &$Model) {
		$this->_field = $config['_field'];

		$this->preloadModelInfo($Model);
		
		$this->_config = $this->parseConfig($config, $Model);
		
		if (!$this->isHidden() && $this->_group) {
			if (!isset($Model->fieldGroupData[$this->_group])) {
				throw new Exception('Group for a fieldData config not found', 1);
			}

			$this->_groupClass = $Model->fieldGroupData[$this->_group];
		}

		if ($this->alias === null) {
			$this->alias = sprintf('%s.%s.%s', get_class($this), $Model->alias, $this->_field);
		}

		ClassRegistry::addObject($this->alias, $this);

		// start up Extensions
		$this->Extensions = new FieldDataExtensionCollection();
		$this->Extensions->init($this->alias, $this->config('Extensions'));

		// trigger initialize
		$this->getEventManager()->dispatch(new CakeEvent('FieldData.initialize', $this));
	}

	public function implementedEvents() {
		return array(
			'FieldData.initialize' => array('callable' => 'initialize'),
			'FieldData.beforeFind' => array('callable' => 'beforeFind', 'passParams' => true),
			'FieldData.afterFind' => array('callable' => 'afterFind', 'passParams' => true),
			'FieldData.parseConfig' => array('callable' => 'parseConfig', 'passParams' => true),
			'FieldData.parseModel' => array('callable' => 'parseModel', 'passParams' => true),
			'FieldData.viewVar' => array('callable' => 'viewVar', 'passParams' => true),
			'FieldData.fieldOptions' => array('callable' => 'fieldOptions', 'passParams' => true),
		);
	}

	// trigger an event
	public function trigger($event, $args = [], $defaultArg = 0) {
		if (!$event instanceof CakeEvent) {
			$event = new CakeEvent('FieldData.' . $event, $this, $args);
			list($event->break, $event->breakOn, $event->modParams) = array(true, array(false, null), 0);
		}

		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}

		$defaultData = isset($event->data[$defaultArg]) ? $event->data[$defaultArg] : true;
		return $event->result === true ? $defaultData : $event->result;
	}

	public function initialize() {
	}

	public function beforeFind($query) {
		return true;
	}

	// Preloads some data directly from the model instance to save up memory later
	public function preloadModelInfo(&$Model) {
		$this->_modelName = $Model->name;

		$this->_association['key'] = $this->_setAssociationKey($Model);
		$this->_association['model'] = $this->_setAssociationModel($Model);
		$this->_association['config'] = $this->_setAssociationConfig($Model);
	}

	// set model association key if exists, e.g belongsTo
	protected function _setAssociationKey(&$Model) {
		$associations = $Model->associations();

		// check each association type (belongsTo, HasOne...)
		foreach ($associations as $name) {
			if ($name == 'belongsTo') {
				// for belongsTo we use the unique foreign key, i.e user_id
				$checkIn = Hash::extract($Model->{$name}, '{s}.foreignKey');
			}
			else {
				// all existing association model aliases
				$checkIn = array_keys($Model->{$name});
			}

			
			if (in_array($this->_field, $checkIn)) {
				return $name;
			}
		}

		return false;
	}

	// set the model associaton key alias, e.g BusinessUnit
	protected function _setAssociationModel(&$Model) {
		if (!$this->isAssociated()) {
			return false;
		}

		// for belongsTo we pull alias of the model
		if ($this->getAssociationKey() == 'belongsTo') {
			$associations = $Model->belongsTo;
			foreach ($associations as $m => $a) {
				if ($a['foreignKey'] == $this->_field) {
					return $m;
				}
			}
		}

		// for other types its the same as field name
		return $this->_field;
	}

	// set the association configuration options, e.g ['conditions' => [...], 'foreignKey' => ...]
	protected function _setAssociationConfig(&$Model) {
		if ($this->getAssociationModel()) {
			return $Model->getAssociated($this->getAssociationModel());
		}

		return false;
	}

	/**
	 * Check if current field is a model association.
	 * 
	 * @return boolean True if it is an association to other model.
	 */
	public function isAssociated() {
		return (bool) $this->getAssociationKey();
	}

	/**
	 * Get the association key (belongsTo, etc).
	 * 
	 * @return mixed Association key, or false if field is not an association.
	 */
	public function getAssociationKey() {
		return $this->_association['key'];
	}

	/**
	 * Returns a related associated model name for the current field if it is actually an association field.
	 * 
	 * @return mixed  Model or false if not an association field.
	 */
	public function getAssociationModel() {
		return $this->_association['model'];
	}

	/**
	 * Returns the config options for association defined in model.
	 *
	 * @return array|false
	 */
	public function getAssociationConfig($param = null) {
		if (is_null($param)) {
			return $this->_association['config'];
		}

		return $this->_association['config'][$param];
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
	 * Reads and updates the configuration and its missing parameters if possible.
	 */
	public function parseConfig(array $config, &$Model) {
		$config = am($this->_defaults, $config);

		$sources = self::listSources($Model);
		if (in_array(strtolower($Model->tablePrefix . $Model->table), array_map('strtolower', $sources), true)) {
			$schema = $Model->schema($this->getFieldName());
			$config['schema'] = $schema;

			$type = null;
			if ($schema !== null) {
				if (in_array($schema['type'], ['date', 'datetime'])) {
					$type = self::FIELD_TYPE_DATE;
				}

				if (in_array($schema['type'], ['integer', 'float', 'decimal'])) {
					$type = self::FIELD_TYPE_NUMBER;
				}

				if ($schema['type'] == 'string') {
					$type = self::FIELD_TYPE_TEXT;
				}

				if ($schema['type'] == 'text') {
					$type = self::FIELD_TYPE_TEXTAREA;
				}
			}

			if ($this->isSelectable() || !empty($config['options'])) {
				if ($this->hasMultiple()) {
					$type = self::FIELD_TYPE_MULTIPLE;
				}
				else {
					$type = self::FIELD_TYPE_SELECT;
				}
			}

			if ($config['type'] === null) {
				$config['type'] = $type;
			}
		}

		if ($config['hidden']) {
			$config['group'] = null;
		}

		foreach ($config['Extensions'] as $key => &$name) {
			if (is_string($name)) {
				$name = ['className' => $name];
			}

			if (is_numeric($key)) {
				$config['Extensions'][$name['className']] = $name;
				unset($config['Extensions'][$key]);
			}
		}

		foreach ($config as $key => $value) {
			$this->{'_' . $key} = $value;
		}

		return $config;
	}

	/**
	 * Returns the CakeEventManager manager instance that is handling any callbacks.
	 * You can use this instance to register any new listeners or callbacks to the
	 * model events, or create your own events and trigger them at will.
	 *
	 * @return CakeEventManager
	 */
	protected $_eventManagerConfigured = false;

	public function getEventManager() {
		if (empty($this->_eventManager)) {
			$this->_eventManager = new CakeEventManager();
			$this->_eventManager->attach($this->Extensions);
			$this->_eventManager->attach($this);
		}

		if (!$this->_eventManagerConfigured) {
			$this->_eventManagerConfigured = true;
		}

		return $this->_eventManager;
	}

	/**
	 * Load all extension classes.
	 */
	protected function _loadExtensions() {
		foreach ($this->config('Extensions') as $name => $config) {
			$this->Extensions->load($name, $this, $config);
			// $this->_loadExtension($name);
		}
	}

	// Get an extension object instance if exists.
	public function __get($name) {
		if ($this->Extensions->enabled($name)) {
			return $this->Extensions->{$name};
		}
	}

	public function __call($name, $args) {
		if (isset($this->_defaults[$name])) {
			if (empty($args)) {
				return $this->{'_' . $name};
			}
		}
	}

	public function config($key = null, $value = null) {
		if ($value !== null) {
			$this->_config = Hash::insert($this->_config, $key, $value);
			
			// this IF statement need to be refactored, because in case the _key has null value, the isset() returns false and
			// does not update the original _variable
			if (isset($this->{'_' . $key}) || $key == 'renderHelper') {
				$this->{'_' . $key} = $value;
			}

			return $this;
		}

		if ($key === null) {
			return $this->_config;
		}

		return Hash::get($this->_config, $key);
	}

	/**
	 * Checks the type of this field.
	 * 
	 * @return boolean
	 */
	public function isType($type) {
		return $this->config('type') == $type;
	}


	/**
	 * Types of fields available here.
	 * 
	 * @access static
	 */
	public static function types($value = null) {
		$options = array(
			self::FIELD_TYPE_TOGGLE => __('Toggle'),
			self::FIELD_TYPE_TAGS => __('Tags'),
			self::FIELD_TYPE_SELECT => __('Select'),
			self::FIELD_TYPE_MULTIPLE => __('Multi-select'),
			self::FIELD_TYPE_DATE => __('Date'),
			self::FIELD_TYPE_TEXT => __('Text'),
			self::FIELD_TYPE_TEXTAREA => __('Textarea'),
			self::FIELD_TYPE_NUMBER => __('Number'),
			self::FIELD_TYPE_EDITOR => __('Editor'),
			self::FIELD_TYPE_FILE => __('File')
		);
		return AppModel::enum($value, $options);
	}

	/**
	 * Method changes this field class to act as a different field from different model - for use in FieldDataHelper.
	 * 
	 * @param  string $model Use as field from defined $model.
	 * @param  string $field Use as field with $field name.
	 * @return $this
	 */
	public function actsAs(FieldDataEntity $actsAs) {
		$this->_modelName = $actsAs->getModelName();
		$this->_field = $actsAs->getFieldName();
	}

	/**
	 * Get Model::schema() information.
	 * 
	 * @param  mixed $key Null to retrieve the entire array, or $key to retrieve specific data from the array.
	 * @todo Schema class.
	 */
	public function schema($key = null) {
		if (!empty($key)) {
			if (isset($this->_schema[$key])) {
				return $this->_schema[$key];
			}

			trigger_error(__('Schema information "%s" for a field "%s" does not exist', $key, $this->getFieldName()));
			return false;
		}

		return $this->_schema;
	}

	/**
	 * Check if current field has some dependency.
	 * 
	 * @return boolean True if it has, False otherwise.
	 */
	public function hasDependency()
	{
		if (empty($this->_dependency)) {
			return false;
		}

		return true;
	}

	public function getDependency()
	{
		return $this->_dependency;
	}

	public function isInlineEditable()
	{
		return $this->_inlineEdit;
	}

	/**
	 * Is current field editable - shows up in the add/edit form.
	 */
	public function isEditable() {
		return $this->_editable;
	}

	// toggle editable parameter on the fly
	public function toggleEditable($set = true) {
		$this->_editable = $this->_config['editable'] = $set;

		return $this;
	}

	public function getRenderHelper() {
		return $this->_renderHelper;
	}

	public function consumeRenderHelper()
	{
		$renderHelper = $this->_renderHelper;
		$this->_renderHelper = null;
		return $renderHelper;
	}

	/**
	 * Is field selectable - with some options configuration or having some association.
	 * Either select or multiselect or tags field can possibly have options.
	 */
	public function isSelectable() {
		return $this->isAssociated() || $this->hasOptionsCallable() || !empty($this->_options) || $this->isTags() || $this->isType(self::FIELD_TYPE_SELECT);
	}

	public function isDate() {
		return $this->isType(self::FIELD_TYPE_DATE);
	}

	public function isInteger() {
		if (empty($this->_schema)) {
			return false;
		}

		return $this->schema('type') == 'integer';
	}

	public function isFloat() {
		if (empty($this->_schema)) {
			return false;
		}

		return $this->schema('type') == 'float';
	}

	public function isString() {
		if (empty($this->_schema)) {
			return false;
		}

		return $this->schema('type') == 'string';
	}

	/**
	 * Get label for this field.
	 * 
	 * @return string
	 * @todo 'humanize' of the label.
	 */
	public function getLabel() {
		return $this->_label;
	}

	/**
	 * @alias For FieldDataEntity::getLabel() method.
	 */
	public function label() {
		return $this->getLabel();
	}

	public function getDescription() {
		return $this->_description;
	}

	public function getHelp() {
		return $this->_help;
	}

	/**
	 * Returns empty option (placeholder) for this field. Possible for use with select field only.
	 * 
	 * @return string|null Empty (placeholder) value.
	 */
	public function getEmptyOption() {
		return $this->_empty;
	}

	/**
	 * Get the default value for this field.
	 * 
	 * @return mixed
	 */
	public function getDefaultValue() {
		return $this->_default;
	}

	/**
	 * Get the validation.
	 * 
	 * @return null|array
	 */
	public function getValidation() {
		return $this->_validate;
	}

	/**
	 * Add validation rule.
	 *
	 * return void
	 */
	public function addValidation($validation = []) {
		$this->_validate = array_merge((array) $this->_validate, $validation);
	}

	public function isHidden() {
		return $this->_hidden;
	}

	public function isToggle() {
		return $this->isType(self::FIELD_TYPE_TOGGLE);
	}

	public function isTags() {
		return $this->isType(self::FIELD_TYPE_TAGS);
	}

	/**
	 * Is current field a HABTM association.
	 * 
	 * @return boolean True if correct, false otherwise.
	 */
	public function isHabtm() {
		if ($this->isAssociated() && $this->getAssociationKey() == 'hasAndBelongsToMany') {
			return true;
		}

		return false;
	}

	/**
	 * Is current field a HABTM association.
	 * 
	 * @return boolean True if correct, false otherwise.
	 */
	public function isHasMany() {
		if ($this->isAssociated() && $this->getAssociationKey() == 'hasMany') {
			return true;
		}

		return false;
	}

	/**
	 * If current field can have multiple options associated - is HABTM or HasMany.
	 * 
	 * @return boolean True if correct, false otherwise.
	 */
	public function hasMultiple() {
		if ($this->isHabtm() || $this->isHasMany()) {
			return true;
		}

		return false;
	}

	/**
	 * Get options for this field in case its a select multiselect field or custom callable options.
	 *
	 * @param  mixed  Any parameters that should propagate to the final callable callback method.
	 * @return array  Array of options formatted for instant use in a view.
	 */
	public function getViewOptions() {
		$options = call_user_func_array([$this, 'getFieldOptions'], func_get_args());
		if ($options !== null) {
			return [
				$this->getVariableKey() => $options
			];
		}

		return [];
	}

	/**
	 * @deprecated for self::buildVariableKey()
	 */
	public static function getViewOptionsVarName($fieldName) {
		return self::buildVariableKey($fieldName);
	}

	/**
	 * Build variable key for a view to match cake's convention which is applied automatically.
	 */
	public static function buildVariableKey($fieldName) {
		return Inflector::variable(Inflector::pluralize(preg_replace('/_id$/', '', $fieldName)));
	}

	/**
	 * Get variable key for a view for current field.
	 */
	public function getVariableKey() {
		// custom provided variable name
		if ($this->_viewVar !== null) {
			return $this->_viewVar;
		}

		return self::buildVariableKey($this->_field);
	}

	/**
	 * Checks if 'options' configuration is truly callable.
	 * 
	 * @return boolean
	 */
	public function hasOptionsCallable() {
		if (!empty($this->_options)) {
			$callable = $this->_options;
			if (is_array($this->_options) && isset($this->_options['callable'])) {
				$callable = $this->_options['callable'];
			}

			return is_callable($callable);
		}

		return false;
	}

	// format options
	public function normalizeOptionsCallable($options) {
		if (!$this->hasOptionsCallable()) {
			return false;
		}

		$callable = $options;
		$passParams = false;
		$args = null;
		if (is_array($options) && isset($options['callable'])) {
			$callable = $options['callable'];
			$passParams = $options['passParams'];
			if (isset($options['args'])) {
				$args = $options['args'];
			}
		}

		return [
			'callable' => $callable,
			'passParams' => $passParams,
			'args' => $args
		];
	}
	/**
	 * Get options for a select field. Returns $config['options'] if it is defined,
	 * otherwise retrieves data from associated model.
	 *
	 * @param  mixed  Any parameters that should propagate to the final callable callback method.
	 * @return array Options.
	 */
	public function getFieldOptions() {
		$normalizedOptions = $this->normalizeOptionsCallable($this->_options);

		// means is callable and already normalized as well
		if ($normalizedOptions !== false) {
			$args = [];

			if ($normalizedOptions['passParams'] === true) {
				if ($normalizedOptions['args'] !== null) {
					if (is_callable($normalizedOptions['args'])) {
						$args = (array) call_user_func($normalizedOptions['args'], ...[$this]);
					}
					else {
						$args = $normalizedOptions['args'];
					}
				}
				else {
					$args = func_get_args();
				}

				// we pass $this FieldDataEntity instance as a first argument, the rest of arguments are custom
				array_unshift($args, $this);
			}

			return call_user_func($normalizedOptions['callable'], ...$args);
		}

		if (!empty($this->_options)) {
			return $this->_options;
		}

		return $this->findRelated();
	}

	/**
	 * Builds a query for find()-ing related data.
	 * 
	 * @return array Query.
	 */
	public function buildRelatedQuery() {
		if (!$this->isAssociated()) {
			return false;
		}

		// $associationModel = $this->getAssociationModel();
		// $instance = _getModelInstance($this->_modelName);
		$assoc = $this->getAssociationConfig();
		$assocConds = $assoc['conditions'];

		$conds = null;
		// conditions can be applied to anything but HABTM relation
		if (!$this->isHabtm() && is_array($assocConds) && !empty($assocConds)) {
			$conds = $assocConds;
		}

		$query = [
			'conditions' => $conds,
			'recursive' => -1
		];

		if ($this->_type == self::FIELD_TYPE_TAGS && $this->_field == 'Tag') {
			$query['group'] = ['Tag.title'];
			$query['fields'] = ['Tag.title', 'Tag.title'];
		}

		return $query;
	}

	/**
	 * Finds data from this field's associated model.
	 * 
	 * @return array List of data.
	 */
	public function findRelated($query = null) {
		if (!$this->isAssociated()) {
			return null;
		}

		// if customized query is not provided as argument, we use the default one
		if ($query === null) {
			$query = $this->buildRelatedQuery();
		}

		$instance = _getModelInstance($this->_modelName);
		$associationModel = $this->getAssociationModel();

		$query = $this->trigger('beforeFind', [$query]);

		$list = $instance->{$associationModel}->find('list', $query);
		
		return $list;
	}

	public function getModelName() {
		return $this->_modelName;
	}

	public function getFieldName() {
		return $this->_field;
	}

	public static function buildHabtmData($data) {
		return implode(',', $data);
	}

	public static function parseHabtmData($data) {
		if (!empty($data)) {
			return explode(',', $data);
		}

		return $data;
	}

	/**
	 * Get Group class for the current FieldData entity.
	 * 
	 * @return FieldGroupEntity
	 */
	public function getGroup() {
		return $this->_groupClass;
	}

	public function moveToGroup($group)
	{
		$this->_groupClass = $group;
	}

	public function getType()
	{
		return $this->_type;
	}
}