<?php
namespace Suggestion\Package\AssetClassification;
use Suggestion\Package\AssetClassificationType\Confidentiality;

class ConfidentialityMedium extends BasePackage {
	public $alias = 'ConfidentialityMedium';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Medium');
		$this->description = __('Confidentiality classification type');

		$this->data = array(
			'name' => $this->name,
			'asset_classification_type_id' => new Confidentiality(),
			'criteria' => __('Reputation consequences')
		);
	}

}
