<?php
App::uses('AppHelper', 'View/Helper');
App::uses('FormHelper', 'View/Helper');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('Hash', 'Utility');
App::uses('CakeEvent', 'Event');
App::uses('CakeEventListener', 'Event');
App::uses('CakeEventManager', 'Event');
App::uses('CakeText', 'Utility');
App::uses('QuickAddListener', 'QuickAdd.Controller/Crud/Listener');
App::uses('BulkActionsListener', 'BulkActions.Controller/Crud/Listener');

class FieldDataHelper extends AppHelper implements CakeEventListener {
	public $settings = array();
	public $helpers = array('Form', 'Html', 'LimitlessTheme.Buttons', 'LimitlessTheme.Icons', 'LimitlessTheme.Popovers', 'FormReload');
	// public $isCustomRenderer = false;
	
	/**
	 * Instance of the CakeEventManager this helper is using
	 * to dispatch inner events.
	 *
	 * @var CakeEventManager
	 */
	protected $_eventManager = null;

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
		$this->settings = $settings;
	}

	public function implementedEvents() {
		return array(
			'FieldDataHelper.parseOptions' => array('callable' => 'parseOptions', 'passParams' => true),
			'FieldDataHelper.inputName' => array('callable' => 'inputName', 'passParams' => true),
			// 'FieldDataHelper.beforeInput' => array('callable' => 'beforeInput', 'passParams' => true),
		);
	}

	/**
	 * Returns the CakeEventManager manager instance that is handling any callbacks.
	 * You can use this instance to register any new listeners or callbacks to the
	 * model events, or create your own events and trigger them at will.
	 *
	 * @return CakeEventManager
	 */
	public function getEventManager() {
		if (empty($this->_eventManager)) {
			$this->_eventManager = new CakeEventManager();
			$this->_eventManager->attach($this);
		}

		return $this->_eventManager;
	}

	/**
	 * Checks viewVars for options array of data set for field provided as FieldDataEntity class.
	 * 
	 * @param  FieldDataEntity $Field
	 * @return bool            True if options data is set for this View, false otherwise.
	 */
	public function inputHasViewOptions(FieldDataEntity $Field) {
		return $this->getInputViewOptions($Field) !== null;
	}

	/**
	 * Get input field options which are set in the $this->_View.
	 * @param  FieldDataEntity $Field
	 * @return array|null      Array of options, or null if options are not set in the view.
	 */
	public function getInputViewOptions(FieldDataEntity $Field) {
		$varName = $Field->getVariableKey();

		return $this->_View->get($varName);
	}

	/**
	 * Provides an easier way to display many fields.
	 * 
	 * @param  array  $Fields  Array of FieldDataEntity classes.
	 * @param  array  $options Single set of options for all of the fields.
	 * @return string          Rendered inputs.
	 */
	public function inputs($Fields, $options = []) {
		$inputs = [];
		foreach ($Fields as $key => $FieldDataEntity) {
			if (!$FieldDataEntity->isEditable()) {
				continue;
			}
			
			$inputs[] = $this->input($FieldDataEntity, $options);
		}

		return implode('', $inputs);
	}

	// Initialize a new helper class for dispatch
	public function dispatchHelper($name)
	{
		$this->{$name} = $this->_View->loadHelper($name);
		return $this->{$name};
	}

	/**
	 * FieldData's own wrapper for a FormHelper::input() method that accepts FieldDataEntity class.
	 * 
	 * @param  FieldDataEntity|array $Field    Either a FieldDataEntity class instance, or array for hasMany association,
	 *                                         where first value is a FieldDataEntity instance and the second value is 
	 *                                         a index number in a field name, i.e 'HasMany.0.field_name.'
	 * @param  array                 $options
	 * @return string                
	 */
	public function input($Field, $options = array())
	{
		if (!$Field instanceof FieldDataEntity && is_array($Field) && isset($Field[0])) {
			$FieldDataEntity = $Field[0];
		}
		else {
			$FieldDataEntity = $Field;
		}

		$renderHelper = $FieldDataEntity->consumeRenderHelper();
		if ($renderHelper !== null) {
			$helperClassName = is_array($renderHelper) ? $renderHelper[0] : $renderHelper;
			if (get_class($this) !== $helperClassName . 'Helper') {
				$this->dispatchHelper($helperClassName);
				if (is_array($renderHelper)) {
					return $this->{$helperClassName}->{$renderHelper[1]}($FieldDataEntity);
				}

				return $this->{$helperClassName}->input($FieldDataEntity);
			}
		}

		$index = $suffix = null;
		if (is_array($Field)) {
			$tmpField = $Field;
			$Field = array_values($tmpField);
			$Field = $tmpField[0];
			$index = $tmpField[1];
		}

		$options = $this->_parseOptions($Field, $options);

		// trigger parseOptions event
		$event = new CakeEvent('FieldDataHelper.parseOptions', $this, array($Field, $options));
		list($event->break, $event->breakOn) = array(true, false);
		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}

		$options = $event->result === true ? $event->data[1] : $event->result;

		if ($index !== null) {
			$index .= '.';
		}
		$inputNameDefault = $Field->getModelName() . '.' . $index . $Field->getFieldName();

		// field name in the form
		$event = new CakeEvent('FieldDataHelper.inputName', $this, array($Field, $inputNameDefault, $index));
		list($event->break, $event->breakOn) = array(true, false);
		$this->getEventManager()->dispatch($event);
		if ($event->isStopped()) {
			return false;
		}

		$inputName = $event->result === true ? $event->data[1] : $event->result;

		//tmp - to force content before defined after
		if (!empty($options['beforeAfter'])) {
			$options['after'] = $options['beforeAfter'] . $options['after'];
		}

		// temporary solution to one bulk action problem
		// @todo refactor with new fielddata
		if (!isset($options['inputName'])) {
			$options['inputName'] = $inputName;
		}

		$options = $this->_bulkAction($Field, $options);

		if (isset($options['inputName'])) {
			$inputName = $options['inputName'];
			unset($options['inputName']);
		}

		// if ($Field->getAssociationKey() === 'hasMany' && !$Field->isType(FieldDataEntity::FIELD_TYPE_TAGS)) {
		// 	$AssocModel = ClassRegistry::init($Field->getAssociationModel());

		// 	$AssocFieldData = $AssocModel->getFieldCollection();

		// 	$input = [];
		// 	foreach ($AssocFieldData as $AssocField) {
		// 		if ($AssocField->isEditable()) {
		// 			$input[] = $this->input([$AssocField, 0]);
		// 		}

		// 	}
		// 	$input = implode('', $input);
		// }
		// else {
			$input = $this->Form->input($inputName, $options);
		// }
		
		// $afterInput = $Field->trigger('afterInput', [$Field, $index, $options]);

		return $input;
	}

	/**
	 * Called before echoing the input().
	 * 
	 * @param  array $options  Options for the input.
	 * @return mixed           False to stop rendering, True to continue as normal, array of customized $options.
	 */
	public function parseOptions(FieldDataEntity $Field, $options) {
		return true;
	}

	public function inputName($Field, $inputName, $index) {
		return true;
	}

	public function beforeInput($Field) {
		return true;
	}

	protected function _parseOptions(FieldDataEntity $Field, $options = []) {
		$fieldName = $Field->getFieldName();
		$modelName = $Field->getModelName();

		$defaultClass = ['form-control'];
		$defaultFormat = ['before', 'label', 'between', 'input', 'error', 'after'];
		$err = $this->error($Field);

		$options = Hash::merge(array(
			// classes works as an array()
			'class' => $defaultClass,
			'div' => [
				'class' => 'form-group',
				'errorClass' => 'has-error',
			],
			'label' => $this->_labelOptions($Field),
			// 'label' => ['class' => 'control-label', 'text' => $Field->getLabel() . ' ' . $this->help($Field)],
			'after' => $err . $this->description($Field),
			'data-field-name' => $fieldName,
			// we had to move error message rendering to 'after' parameter because
			// cakephp2's form helper was not enough to achieve html structure for it
			'error' => ''
		), $options);

		//
		// Handle error class for Custom Fields
		if ($modelName === 'CustomFieldValue' && $err && isset($options['div']['errorClass'])) {
			$options['div']['class'] .= ' ' . $options['div']['errorClass'];
			unset($options['div']['errorClass']);
		}
		//

		if ($Field->isSelectable() && !in_array('select2-manual-init', $options['class'])) {
			$select2Type = $Field->isType(FieldDataEntity::FIELD_TYPE_MULTIPLE);
			$select2Type = $select2Type || $Field->isType(FieldDataEntity::FIELD_TYPE_SELECT);
			$select2Type = $select2Type || $Field->isType(FieldDataEntity::FIELD_TYPE_TAGS);

			$selectClass = [];
			if ($select2Type) {
				$selectClass = ['select2'];

				if (!isset($options['type'])) {
	                $options['type'] = 'select';
	            }
			}
			
			// handle custom class for a select field
			if ($options['class'] !== $defaultClass) {
				array_shift($options['class']);
				$selectClass = Hash::merge($selectClass, $options['class']);
			}

			$options['class'] = $selectClass;

			if ($Field->isType(FieldDataEntity::FIELD_TYPE_MULTIPLE)) {
				$options['multiple'] = true;
			}

			// Applies to single select field and tags
			if ($Field->getEmptyOption() !== null) {
				$options['data-placeholder'] = $Field->getEmptyOption();
			}

			// Setup empty option value for a single select field
			if (!$Field->hasMultiple() && $Field->getEmptyOption() !== null) {
				if (!isset($options['options'])) {
					// put an empty array key => value at the beginning of the options array for a select2 placeholder
					$fieldOptions = $this->getInputViewOptions($Field);
					if (!is_array($fieldOptions)) {
						$fieldOptions = [];
					}
				}
				else {
					$fieldOptions = $options['options'];
				}

				$fieldOptions = ['' => ''] + $fieldOptions;

				// custom config for select2 with empty value
				$options['options'] = $fieldOptions;
				$options['data-minimum-results-for-search'] = '5';
				$options['data-allow-clear'] = true;
			}

			// Extra parameters required for a field for tags
			if ($Field->isType(FieldDataEntity::FIELD_TYPE_TAGS)) {
				$_model = ClassRegistry::init($modelName);
				$assoc = $_model->getAssociated($fieldName);
				$assocModel = $assoc['className'];
				list($assocPlugin, $assocName) = pluginSplit($assocModel);

				$selectOptions = $this->getInputViewOptions($Field);
				if ($selectOptions === null) {
					$selectOptions = [];
				}

				if (isset($this->data[$fieldName])) {
					$dataValues = $this->data[$fieldName];
					$options['selected'] = Hash::extract($dataValues, '{n}.' . $_model->$fieldName->displayField);
				}

				if (isset($this->request->data[$modelName][$fieldName])) {
					$requestValues = $this->request->data[$modelName][$fieldName];
					if (isset($requestValues[0])) {
						// default situation pulls from already formatted data array
						$extractPath = '{n}.' . $_model->$fieldName->displayField;

						// fallback situation for non-standard situations pulls directly the data
						if (!is_array($requestValues[0])) {
							$extractPath = '{n}';
						}

						$requestValues = Hash::extract($requestValues, $extractPath);
						$selectOptions = array_merge($selectOptions, $requestValues);
						$options['selected'] = $requestValues;
					}
				}

				$selectOptions = array_unique($selectOptions);
				$selectOptions = array_unique($selectOptions);
				foreach ($selectOptions as $sOptKey => $sOptVal) {
					if (!empty($sOptVal)) {
						$selectOptions[$sOptKey] = [
							'value' => $sOptVal,
							'name' => $sOptVal
						];
					} else {
						// Remove empty array value
						unset($selectOptions[$sOptKey]);
					}
				}

				$options['options'] = $selectOptions;

				$options['data-tags'] = true;
				$options['multiple'] = true;
			}
		}
		else {
			$options['class'][] = 'form-control';
		}

		if ($Field->isType(FieldDataEntity::FIELD_TYPE_TEXT)) {
			if (!isset($options['type'])) {
				$options['type'] = 'text';
			}
		}

		if ($Field->isType(FieldDataEntity::FIELD_TYPE_DATE)) {
			if (!isset($options['type'])) {
				$options['type'] = 'text';
				$options['class'][] = 'datepicker';
				$options['id'] = 'datepicker-' . CakeText::uuid();
				$options['data-date-format'] = 'ymd';
			}

		}
		if ($Field->isType(FieldDataEntity::FIELD_TYPE_TOGGLE)) {
			$defaultToggleType = 'checkbox';
			$defaultToggleId = $fieldName . '-' . CakeText::uuid();

			// default toggle labeled ON/OFF checkbox
			if (!$this->inputHasViewOptions($Field)) {

				// possible to adjust the ON display label
				if (!isset($options['toggleLabel'])) {
					$options['toggleLabel'] = __('Enable');
				}

				if (!isset($options['type'])) {
					$options['type'] = $defaultToggleType;
					$options['id'] = $defaultToggleId;

					$options['between'] = '<div class="checkbox checkbox-switchery switchery-sm"><label for="' . $options['id'] . '">' . $options['toggleLabel'] . '</label>';
					$options['after'] = '</div>' . $options['after'];
					$options['class'][] = 'switchery';
				}
			}

			// toggle having custom options (array with 2 values representing ON/OFF)
			else {
				$toggleOptions = $this->getInputViewOptions($Field);

				$onText = $toggleOptions[1];
				$offText = $toggleOptions[0];

				if (!isset($options['type'])) {
					$options['type'] = $defaultToggleType;
					$options['id'] = $defaultToggleId;	

					$options['between'] = '<div class="checkbox checkbox-switch"><label for="' . $options['id'] . '">';
					$options['after'] = sprintf('%s / %s', $onText, $offText) . '</label></div>';
					$options['class'][] = 'switch';

					$options['data-on-color'] = "success";
					$options['data-off-color'] = "danger";
					$options['data-on-text'] = $onText;
					$options['data-off-text'] = $offText;
				}
			}
		}

		if ($Field->isType(FieldDataEntity::FIELD_TYPE_TEXTAREA)) {
			if (!isset($options['type'])) {
				$options['type'] = 'textarea';
			}
		}

		if ($Field->isType(FieldDataEntity::FIELD_TYPE_EDITOR)) {
			if (!isset($options['type'])) {
				$options['type'] = 'textarea';
				$options['class'] = array_merge($options['class'], ['summernote-editor']);
			}

			$options['cols'] = 18;
			$options['rows'] = 18;
		}

		if ($Field->isType(FieldDataEntity::FIELD_TYPE_FILE)) {
			if (!isset($options['type'])) {
				$options['type'] = 'file';
				$options['class'] = array_merge($options['class'], ['file-styled']);
			}
		}

		if ($Field->isType(FieldDataEntity::FIELD_TYPE_HIDDEN)) {
			if (!isset($options['type'])) {
				$options['type'] = 'hidden';
				$options['class'] = [];
			}
		}

		if (!isset($options['format'])) {
			$options['format'] = $defaultFormat;
		}

		if ($Field->getDefaultValue() !== null) {
			$options['default'] = $Field->getDefaultValue();
		}

		$options = $this->_quickAdd($Field, $options);

		// trigger parseOptions event
		// $event = new CakeEvent('FieldData.parseOptions', $this, array($options));
		// list($event->break, $event->breakOn) = array(true, false);
		// $this->getEventManager()->dispatch($event);
		// if ($event->isStopped()) {
		// 	return false;
		// }
		// $options = $event->result === true ? $event->data[0] : $event->result;

		// $options = $Field->trigger('parseOptions', [$options]);

		$options['class'] = implode(' ', array_unique($options['class']));

		return $options;
	}

	const BULK_ACTION_FIELD_SETTING_LEAVE_AS_IS = 1;
	const BULK_ACTION_FIELD_SETTING_CLEAR = 2;
	const BULK_ACTION_FIELD_SETTING_REPLACE = 3;
	const BULK_ACTION_FIELD_SETTING_APPEND = 4;
	protected function _bulkAction(FieldDataEntity $Field, $options)
	{
		$BulkActions = $this->_View->get('BulkActions');
		if ($BulkActions !== null && $BulkActions->isBulkRequest()) {
			$selectOptions = [
				self::BULK_ACTION_FIELD_SETTING_LEAVE_AS_IS => __('Leave as it is'),
				self::BULK_ACTION_FIELD_SETTING_CLEAR => __('Clear'),
				self::BULK_ACTION_FIELD_SETTING_REPLACE => __('Replace'),
				self::BULK_ACTION_FIELD_SETTING_APPEND => __('Append')
			];

			if (!$Field->isHabtm()) {
				unset($selectOptions[self::BULK_ACTION_FIELD_SETTING_APPEND]);
			}

			if (!$Field->config('type') == FieldDataEntity::FIELD_TYPE_DATE) {
				unset($selectOptions[self::BULK_ACTION_FIELD_SETTING_CLEAR]);
			}

			$selectId = 'bulk-action-field-settings-' . $Field->getFieldName();
			$select = $this->Form->input('BulkAction.field_settings.' . $Field->getFieldName(), [
				'type' => 'select',
				'label' => false,
				'div' => false,
				'id' => $selectId,
				'class' => 'select2',
				'options' => $selectOptions,
				'data-minimum-results-for-search' => '-1',
				'data-width' =>  '140px'
			]);

			$options['label']['text'] .= '<div class="pull-right" style="position: relative;">' . $select . '</div><div style="clear: both;"></div>';
		}

		return $options;
	}

	protected function _quickAdd(FieldDataEntity $Field, $options)
	{
		$BulkActions = $this->_View->get('BulkActions');
		if ($BulkActions !== null && $BulkActions->isBulkRequest()) {
			return $options;
		}

		$formName = $this->_View->get('formName');
		$toggle = $Field->config('quickAdd');

		if ($toggle !== true) {
			return $options;
		}

		$Model = ClassRegistry::init($Field->getAssociationModel());

		$formReloadRequestId = CakeText::uuid();
		$formReloadRequest = $this->Html->div('', '', array_merge([
			'id' => $formReloadRequestId,
		], $this->FormReload->triggerOptions()));

		$datasource = $Model->getMappedRoute([
			'action' => 'add',
			'?' => [
				QuickAddListener::REQUEST_PARAM => 1,
				QuickAddListener::REQUEST_PARAM_ON_SUCCESS => '#' . $formReloadRequestId,
			]
		]);
	
		$quickAddBtn = $this->Buttons->primary(__('Add'), [
			'clss' => ['bg-teal'],
			'data' => [
				'yjs-request' => 'crud/showForm',
				'yjs-target' => 'modal',
				'yjs-datasource-url' => Router::url($datasource),
				'yjs-event-on' => 'click',
			]
		]) . $formReloadRequest;

		$quickAddBtn = $this->Html->tag('span', $quickAddBtn, [
			'class' => 'input-group-btn input-group-btn-separated'
		]);

		$between = (isset($options['between'])) ? $options['between'] : '';
		$options['between'] = $between . '<div class="input-group">';

		$after = (isset($options['after'])) ? $options['after'] : '';
		$options['after'] = $quickAddBtn . '</div>' . $options['after'];

		return $options;
	}

	/**
	 * Returns a html formatted tag with help of the Field.
	 */
	public function help(FieldDataEntity $Field) {
		if ($Field->getHelp() === null) {
			return false;
		}

		return $this->Popovers->right($this->Icons->render('help'), $Field->getHelp(), __('Help'), [
			'size' => 'lg',
		]);
	}

	/**
	 * Returns a html formatted tag with description of the Field.
	 */
	public function description(FieldDataEntity $Field) {
		if ($Field->getDescription() === null) {
			return false;
		}

		$prefix = '';

		$validation = $Field->getValidation();
		if (isset($validation['mandatory']) && $validation['mandatory'] === true) {
 			$prefix = __('MANDATORY: ');
		}
		elseif(isset($validation['mandatory']) && $validation['mandatory'] === false) {
			$prefix = __('OPTIONAL: ');
		}

		$prefix = $this->Html->tag('span', $prefix, ['class' => 'help-block-prefix']);

		return $this->Html->tag('span', $prefix . $Field->getDescription(), ['class' => 'help-block']);
	}

	public function label(FieldDataEntity $Field)
	{
		$fieldName = $Field->getFieldName();
		$modelName = $Field->getModelName();
		$labelOptions = $this->_labelOptions($Field);

		$text = $labelOptions['text'];
		unset($labelOptions['text']);

		return $this->Form->label($modelName . '.' . $fieldName, $text, $labelOptions);
	}

	/**
	 * Alias for _labelOptions() method.
	 */
	public function labelOptions(FieldDataEntity $Field)
	{
		return $this->_labelOptions($Field);
	}

	protected function _labelOptions(FieldDataEntity $Field)
	{
		$text = $Field->getLabel() . ' ' . $this->help($Field);
		$class = 'control-label';

		return compact('text', 'class');
	}

	/**
	 * Returns a html formatted error message
	 */
	public function error(FieldDataEntity $Field)
	{
		$fieldName = $Field->getFieldName();
		$modelName = $Field->getModelName();

		return $this->Form->error($modelName . '.' . $fieldName, null, [
			'wrap' => 'label',
			'class' => 'validation-error-label',
			'for' => $this->Form->domId($modelName . '.' . $fieldName),
			'escape' => false
		]);
	}

	/**
	 * encodes data to json, escapes single quote marks 
	 * 
	 * @param  mixed $data
	 * @return string
	 */
	public static function jsonEncode($data) {
		return str_replace("'", "\'", json_encode($data));
	}
}