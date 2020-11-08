<?php
namespace Suggestion\Package\RiskClassification;
use Suggestion\Package\RiskClassificationType\Likelihood;

class LikelihoodLow extends BasePackage {
	public $alias = 'LikelihoodLow';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Low');
		$this->description = __('Likelihood classification type');

		$this->data = array(
			'name' => $this->name,
			'risk_classification_type_id' => new Likelihood(),
			'criteria' => __('Minor consequences'),
			'value' => '1'
		);
	}

}
