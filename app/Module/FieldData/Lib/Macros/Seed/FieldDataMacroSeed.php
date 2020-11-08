<?php
App::uses('MacroSeed', 'Macros.Lib/Seed');
App::uses('FieldDataMacro', 'FieldData.Lib/Macros');
App::uses('FieldDataCollection', 'FieldData.Model/FieldData');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('Inflector', 'Utility');
App::uses('FieldDataRenderer', 'FieldData.Lib');
App::uses('View', 'View');

/**
 * FieldData macro seeder.
 */
class FieldDataMacroSeed implements MacroSeed
{
/**
 * Working FieldDataCollection from which we want to create macros and add to collection.
 * 
 * @var FieldDataCollection
 */
	protected $_fieldDataCollection = null;

/**
 * Optionable modelPath for associated FieldDataCollection.
 * 
 * @var string
 */
	protected $_modelPath = null;

/**
 * @var View
 */
	protected $_View = null;

/**
 * Construct.
 * 
 * @param FieldDataCollection $collection
 */
	public function __construct(FieldDataCollection $collection, $modelPath = null) {
		$this->_fieldDataCollection = $collection;
		$this->_modelPath = $modelPath;

		$this->_View = new View();
		$this->_View->loadHelper('ObjectRenderer.ObjectRenderer');
		$this->_View->loadHelper('LimitlessTheme.Labels');
		$this->_View->loadHelper('LimitlessTheme.Labels');
		$this->_View->loadHelper('LimitlessTheme.Popovers');
		$this->_View->loadHelper('LimitlessTheme.Icons');

		if (AppModule::loaded('Reports')) {
			$this->_View->loadHelper('Reports.ReportChart');
			$this->_View->loadHelper('Reports.ReportBlockSetting');
		}
		
		$this->_View->loadHelper('AdvancedFilters.AdvancedFilters');
	}

/**
 * Loop through FieldDataCollection and create and add field macros to collection.
 * 
 * @param FieldDataCollection $collection
 * @return void
 */
	public function seed(MacroCollection $collection) {
		$groupSettings = $this->_fieldDataCollection->getModel()->getMacroGroupModelSettings();

		$collection->addGroup($groupSettings['name'], $groupSettings['slug']);

		foreach ($this->_fieldDataCollection as $field) {
			if ($field->isHidden() || $field->config('usable') === false) {
				continue;
			}

			$alias = $this->_getAlias($field);
			$label = $this->_getLabel($field);

			$applyCallback = [$this->_View->ObjectRenderer, 'getOutput'];

			$subject = new stdClass();
			$subject->field = $field;
			$subject->modelPath = $this->_modelPath;

			$macro = new FieldDataMacro($alias, $label, $subject, $applyCallback);

			$collection->add($macro, $groupSettings['slug']);
		}
	}

/**
 * Get macro alias.
 * 
 * @param FieldDataEntity $field
 * @return string Macro alias.
 */
	protected function _getAlias(FieldDataEntity $field) {
		$config = $field->config('macro');

		if (!empty($field->config('CustomFields'))) {
			$name = $field->config('CustomFields')['slug'];
		}
		else {
			$name = (isset($config['name'])) ? $config['name'] : $field->getFieldName();
		}

		$name = Inflector::underscore($name);

		return ClassRegistry::init($field->getModelName())->getMacroAlias($name);
	}

/**
 * Get macro label.
 * 
 * @param FieldDataEntity $field
 * @return string Macro label.
 */
	protected function _getLabel(FieldDataEntity $field) {
		return $field->getLabel();
	}
}