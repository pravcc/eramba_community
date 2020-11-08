<?php
class LegalsHelper extends AppHelper {
	public $helpers = array('Html', 'FieldData.FieldData', 'LimitlessTheme.Alerts');
	public $settings = array();

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function beforeLegalAdvisorValue()
	{
		ClassRegistry::init('User')->displayField = 'full_name_with_type';
	}

	public function riskMagnifierField(FieldDataEntity $Field)
	{
		$out = null;

		$isUsedInRisks = $this->_View->get('isUsedInRisks');
		if (!empty($isUsedInRisks)) {
			$out .= $this->Alerts->danger(__('This legal is used by %d risks, if you proceed we will update the risk score for this risks.', $isUsedInRisks));
		}

		$out .= $this->FieldData->input($Field);

		return $out;
	}
}