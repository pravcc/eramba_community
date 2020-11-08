<?php
namespace Suggestion\Package\RiskClassification;
use Suggestion\Package\RiskClassificationType\Likelihood;

class LikelihoodMedium extends BasePackage {
	public $alias = 'LikelihoodMedium';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Medium');
		$this->description = __('Likelihood classification type');

		$this->data = array(
			'name' => $this->name,
			'risk_classification_type_id' => new Likelihood(),
			'criteria' => __('Reputation consequences'),
			'value' => '2'
		);
	}

}
