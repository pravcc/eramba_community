<?php
App::uses('ItemDataProperty', 'FieldData.Model/FieldData/Item');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');

/**
 * FieldDataProperty class to manage FieldData features within ItemDataEntity.
 */
class FieldDataProperty extends ItemDataProperty
{
	public function setup(ItemDataEntity $Item, $config = [])
	{
	}

	/**
	 * Read provided data and extract the value for a given $Field.
	 * 
	 * @param  ItemDataEntity         $Item
	 * @param  FieldDataEntity|string $Field
	 * @return string                 Field raw value
	 */
	public function value(ItemDataEntity $Item, $Field, $raw = true)
	{
		$item = $Item->getData();

		if (!$Field instanceof FieldDataEntity) {
			// $hasFieldData = $Item->getModel()->hasFieldDataEntity($Field);
			$Field = $Item->getModel()->getFieldDataEntity($Field);
		}

		if (!$Field instanceof FieldDataEntity) {
			trigger_error('Field is not instance of FieldDataEntity:');
			ddd($Field);
		}

		$Model = $this->_model($Field);
		$modelName = $Model->alias;
		$fieldName = $Field->getFieldName();

		if ($Field->isType(FieldDataEntity::FIELD_TYPE_OBJECT_STATUS)) {
			$statusName = str_replace('ObjectStatus_', '', $Field->getFieldName());
			$value = $Item->Properties->ObjectStatus->getStatusValue($Item, $statusName);
		}
		elseif (!$Field->isAssociated()) {
			$value = $Item->{$fieldName};

			if ($Field->hasOptionsCallable() && !$Field->isType(FieldDataEntity::FIELD_TYPE_TOGGLE)) {
				$fieldOptions = $Field->getFieldOptions();

				if (isset($fieldOptions[$value])) {
					$value = $fieldOptions[$value];
				}
			}
		}
		else {
			$value = $Item->{$Item->getModel()->displayField};

			// $associationModel = $Field->getAssociationModel();
			// $associationKey = $Field->getAssociationKey();
			// $className = $Field->getAssociationConfig('className');
			// $classNameInstance = ClassRegistry::init($className);

			// if ($associationKey === 'belongsTo') {
			// 	$value = $item[$associationModel][$classNameInstance->displayField];
			// }

			// if ($associationKey === 'hasAndBelongsToMany') {
			// 	$value = $this->_extractMany($item, $associationModel, $classNameInstance->displayField);
			// }

			// if ($associationKey === 'hasOne') {
			// 	ddd(123);
			// }

			// if ($associationKey === 'hasMany') {
			// 	$value = $this->_extractMany($item, $associationModel, $classNameInstance->displayField);
			// }
		}

		if (!$raw) {
			$value = $this->_humanValue($value, $Field);
		}

		return $value;
	}

	protected function _humanValue($value, $Field)
	{
		// for toggle switch with default on/off options - no customizations via options
		if ($Field->isType(FieldDataEntity::FIELD_TYPE_TOGGLE)) {
			if (!$Field->hasOptionsCallable()) {
				if ($value) {
					$value = __('Yes');
				}

				if (!$value) {
					$value = __('No');
				}
			}
			else {
				$fieldOptions = $Field->getFieldOptions();

				if (isset($fieldOptions[$value])) {
					$value = $fieldOptions[$value];
				}
				else {
					$value = __('Undefined');
				}
			}
		}
		elseif ($Field->isType(FieldDataEntity::FIELD_TYPE_OBJECT_STATUS) && $value) {
			$value = __('Yes');
		}
		elseif ($Field->isType(FieldDataEntity::FIELD_TYPE_OBJECT_STATUS) && !$value) {
			$value = __('No');
		}

		return $value;
	}

	/**
	 * Extract many objects from the data array.
	 *
	 * @return array
	 */
	protected function _extractMany($item, $associationModel, $field)
	{
		$value = [];
		foreach ($item[$associationModel] as $subItem) {
			$value[$subItem['id']] = $subItem[$field];
		}

		return $value;
	}

	protected function _model(FieldDataEntity $Field)
	{
		$modelName = $Field->getModelName();
		$Model = ClassRegistry::init($modelName);

		return $Model;
	}
}