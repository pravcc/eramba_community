<?php
class AssetClassificationsHelper extends AppHelper {
	public $helpers = array('Html', 'LimitlessTheme.Alerts', 'FieldData.FieldData');
	public $settings = array();

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function assetClassificationTypeField(FieldDataEntity $Field)
	{
		$typeName = ClassRegistry::init('AssetClassificationType')->getFieldDataEntity('name');

		$out = $this->FieldData->input($Field);
		$out .= $this->FieldData->input($typeName);

		$out .= $this->Html->scriptBlock('
			$(function() {
				var $type_ele = $("#AssetClassificationAssetClassificationTypeId");
				var $new_class_ele = $("#AssetClassificationTypeName");

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
			$out .= $this->Alerts->danger(__('This Classification is in use and changing it will recalculate all Risks!'));
		}

		$out .= $this->FieldData->input($Field);

		return $out;
	}

}