<?php
App::uses('FieldDataExtension', 'FieldData.Model/FieldData');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');

class RiskClassificationExtension extends FieldDataExtension {
	protected $_classificationData = null;

	public function setup(FieldDataEntity $field, $config = []) {
	}

	public function initialize(FieldDataEntity $Field) {
		$this->Field = $Field;

		$model = $Field->getModelName();
		$Model = ClassRegistry::init($model);

		// lets modify the field for a compatibility with classic form and submit
		if ($Model->hasMethod('getMethod') && $Model->getMethod() == 'eramba') {
			$Field->toggleEditable(true);
			// $Field->config('type', FieldDataEntity::FIELD_TYPE_SELECT);

			$calculationValues = $Model->getClassificationTypeValues($Model->getSectionValues());
			$classifications = $Model->RiskClassification->RiskClassificationType->find('all', array(
				'conditions' => array(
					'RiskClassificationType.id' => $calculationValues
				),
				'order' => array('RiskClassificationType.name' => 'ASC'),
				'recursive' => 1
			));

			$this->_classificationData = $classifications;
		}
	}

	public function getClassficationsData() {
		return $this->_classificationData;
	}

}