<?php
namespace Suggestion\Package\AssetClassification;
use Suggestion\Package\AssetClassificationType\Integrity;

class IntegrityLow extends BasePackage {
	public $alias = 'IntegrityLow';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Low');
		$this->description = __('Integrity classification type');

		$this->data = array(
			'name' => $this->name,
			'asset_classification_type_id' => new Integrity(),
			'criteria' => __('Minor consequences')
		);
	}

}
