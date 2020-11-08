<?php
namespace Suggestion\Package\RiskClassification;
use Suggestion\Package\RiskClassificationType\Impact;

class ImpactHigh extends BasePackage {
	public $alias = 'ImpactHigh';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('High');
		$this->description = __('Impact classification type');

		$this->data = array(
			'name' => $this->name,
			'risk_classification_type_id' => new Impact(),
			'criteria' => __('Legal consequences'),
			'value' => '3'
		);
	}

}
