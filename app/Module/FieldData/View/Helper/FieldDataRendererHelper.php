<?php
App::uses('AppHelper', 'View/Helper');
App::uses('FormHelper', 'View/Helper');

class FieldDataRendererHelper extends AppHelper
{
	public $settings = [];
	public $helpers = ['FieldData.FieldData', 'FieldData.FieldDataCollection', 'SectionDispatch'];

	protected function _model(FieldDataEntity $Field)
	{
		$modelName = $Field->getModelName();
		$Model = ClassRegistry::init($modelName);

		return $Model;
	}

	public function render(FieldDataEntity $Field, ItemDataEntity $Item)
	{
		// debug($Item->getValue($Field));
		return $Item->getValue($Field);
	}

	/*public function render(FieldDataEntity $Field, $item)
	{	
		$Model = $this->_model($Field);
		
		$value = $this->getValue($Field, $item);
		// $primaryKey = $this->getPrimaryKey($Field, $item);
		// $value = $this->_formatValue($Field, $value, $primaryKey);

		return $value;
	}*/

	/**
	 * Default style for inline edit form of a single $Field.
	 * 
	 * @param  FieldDataEntity $Field
	 * @param  array           $item  Item data.
	 * @return string                 Rendered form.
	 */
	public function inlineEditForm(FieldDataEntity $Field, $item)
	{
		$Collection = $this->_getInlineEditCollection($Field, $item);
		$options = $this->getInlineEditOptions($Field, $item);
		$form = $this->FieldDataCollection->form($Collection, $options);

		return $form;
	}

	/**
	 * Default options for inline edit form.
	 * 
	 * @param  FieldDataEntity $Field
	 * @param  array           $item  Item data.
	 * @return array                  Array of options accepted by FieldDataCollectionHelper::form() method.
	 */
	public function getInlineEditOptions(FieldDataEntity $Field, ItemDataEntity $Item)
	{
		$Model = $this->_model($Field);
		$modelName = $Model->alias;
		$fieldName = $Field->getFieldName();

		$value = $Item->Properties->FieldData->value($Item, $Field);
		if ($Field->isAssociated()) {
			$associationKey = $Field->getAssociationKey();
			if ($associationKey === 'hasAndBelongsToMany') {
				$value = array_keys($value);
			}
		}

		return [
			'raw' => true,
			'tabs' => false,
			'url' => [
				'action' => 'inline_edit',
				$fieldName,
				$Item->getData()[$modelName]['id']
			],
			'input' => [
				'label' => false,
				'default' => $value
			]
		];
	}

	public function getInlineEditCollection(FieldDataEntity $Field, $item)
	{
		$Model = $this->_model($Field);
		$modelName = $Model->alias;
		$fieldName = $Field->getFieldName();

		$Collection = new FieldDataCollection([], $Model);
		$Collection->add($fieldName, $Field);

		return $Collection;
	}

	/**
	 * Get render* method name for a specific column.
	 * 
	 * @param  string $fieldName Field name
	 * @return string            Method name with a render* prefix
	 */
	/*protected function _getMethodName($fieldName, $state = 'before')
	{
		$fieldNameCamelCase = Inflector::camelize($fieldName);
		$methodName = $state . ucfirst($fieldNameCamelCase) . 'Value';

		return $methodName;
	}

	protected function _methodExists($model, $fieldName)
	{
		$methodName = $this->_getMethodName($fieldName);

		return $this->SectionDispatch->methodExists($model, $methodName);
	}

	protected function _triggerMethod($model, $fieldName)
	{
		$methodName = $this->_getMethodName($fieldName);
		if ($this->_methodExists($model, $fieldName)) {
			return $this->SectionDispatch->{$methodName}($model);
		}
	}*/

}