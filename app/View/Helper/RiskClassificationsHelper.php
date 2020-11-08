<?php
class RiskClassificationsHelper extends AppHelper {
	public $helpers = array('Html', 'LimitlessTheme.Alerts', 'FieldData.FieldData');
	public $settings = array();

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function riskClassificationTypeField(FieldDataEntity $Field)
	{
		$typeName = ClassRegistry::init('RiskClassificationType')->getFieldDataEntity('name');

		$out = $this->FieldData->input($Field);
		$out .= $this->FieldData->input($typeName);

		$out .= $this->Html->scriptBlock('
			$(function() {
				var $type_ele = $("#RiskClassificationRiskClassificationTypeId");
				var $new_class_ele = $("#RiskClassificationTypeName");

				$type_ele.on("change", function() {
					if ( $(this).val() == "" ) {
						$new_class_ele.prop( "disabled", false );
					} else {
						$new_class_ele.prop( "disabled", true );
					}
				}).trigger("change");
			});
		');

		return $out;
	}

	public function valueField(FieldDataEntity $Field)
	{
		$out = null;

		$isUsed = $this->_View->get('isUsed');
		if (!empty($isUsed)) {
			$out .= $this->Alerts->danger(__('This classification is used by %d risks, if you proceed we will update the risk score for this risks.', $isUsed));
		}

		$out .= $this->FieldData->input($Field);

		return $out;
	}

}