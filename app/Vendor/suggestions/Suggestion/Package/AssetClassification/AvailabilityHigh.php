<?php
namespace Suggestion\Package\AssetClassification;
use Suggestion\Package\AssetClassificationType\Availability;

class AvailabilityHigh extends BasePackage {
	public $alias = 'AvailabilityHigh';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('High');
		$this->description = __('Availability classification type');

		$this->data = array(
			'name' => $this->name,
			'asset_classification_type_id' => new Availability(),
			'criteria' => __('Legal consequences')
		);
	}

}
