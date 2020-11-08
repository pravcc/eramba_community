<?php
namespace Suggestion\Package\AssetClassification;
use Suggestion\Package\AssetClassificationType\Confidentiality;

class ConfidentialityLow extends BasePackage {
	public $alias = 'ConfidentialityLow';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Low');
		$this->description = __('Confidentiality classification type');

		$this->data = array(
			'name' => $this->name,
			'asset_classification_type_id' => new Confidentiality(),
			'criteria' => __('Minor consequences')
		);
	}

}
