<?php
namespace Suggestion\Package\RiskClassification;
use Suggestion\Package\RiskClassificationType\Impact;

class ImpactMedium extends BasePackage {
	public $alias = 'ImpactMedium';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Medium');
		$this->description = __('Impact classification type');

		$this->data = array(
			'name' => $this->name,
			'risk_classification_type_id' => new Impact(),
			'criteria' => __('Reputation consequences'),
			'value' => '2'
		);
	}

}
