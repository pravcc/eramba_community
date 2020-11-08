<?php
namespace Suggestion\Package\RiskClassification;
use Suggestion\Package\RiskClassificationType\Likelihood;

class LikelihoodHigh extends BasePackage {
	public $alias = 'LikelihoodHigh';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('High');
		$this->description = __('Likelihood classification type');

		$this->data = array(
			'name' => $this->name,
			'risk_classification_type_id' => new Likelihood(),
			'criteria' => __('Legal consequences'),
			'value' => '3'
		);
	}

}
