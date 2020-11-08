<?php
namespace Suggestion\Package\AssetClassification;
use Suggestion\Package\AssetClassificationType\Confidentiality;

class ConfidentialityHigh extends BasePackage {
	public $alias = 'ConfidentialityHigh';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('High');
		$this->description = __('Confidentiality classification type');

		$this->data = array(
			'name' => $this->name,
			'asset_classification_type_id' => new Confidentiality(),
			'criteria' => __('Legal consequences')
		);
	}

}
