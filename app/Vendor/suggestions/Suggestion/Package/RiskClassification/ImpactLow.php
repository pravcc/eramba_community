<?php
namespace Suggestion\Package\RiskClassification;
use Suggestion\Package\RiskClassificationType\Impact;

class ImpactLow extends BasePackage {
	public $alias = 'ImpactLow';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Low');
		$this->description = __('Impact classification type');

		$this->data = array(
			'name' => $this->name,
			'risk_classification_type_id' => new Impact(),
			'criteria' => __('Minor consequences'),
			'value' => '1'
		);
	}

}
