<?php

namespace Suggestion\Package\Asset;

use Suggestion\Package\BusinessUnit\It;
use Suggestion\Package\BusinessUnit\CallCenter;

use Suggestion\Package\Legal\ISO27001;
use Suggestion\Package\Legal\SOX;
use Suggestion\Package\Legal\PCIDSS;

use Suggestion\Package\AssetLabel\Confidentiality;
use Suggestion\Package\AssetLabel\Privates;

use Suggestion\Package\AssetClassification\AvailabilityHigh;
use Suggestion\Package\AssetClassification\AvailabilityMedium;
use Suggestion\Package\AssetClassification\AvailabilityLow;
use Suggestion\Package\AssetClassification\IntegrityHigh;
use Suggestion\Package\AssetClassification\IntegrityMedium;
use Suggestion\Package\AssetClassification\IntegrityLow;
use Suggestion\Package\AssetClassification\ConfidentialityHigh;
use Suggestion\Package\AssetClassification\ConfidentialityMedium;
use Suggestion\Package\AssetClassification\ConfidentialityLow;

class PaymentData extends BasePackage {
	public $alias = 'PaymentData';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Credit Card Information');

		$this->data = array(
			'name' => $this->name,
			'business_unit_id' => array(
				new It(), new CallCenter(),
			),
			'description' => __('Credit card information provided by our customers to our call center.'),
			'asset_media_type_id' => ASSET_MEDIA_TYPE_DATA,
			'legal_id' => array( new ISO27001(), new PCIDSS(), ),
			'asset_label_id' =>  new Confidentiality(),
			'review' => date('Y-m-d'),

                        'asset_owner_id' => new It(),
                        'asset_guardian_id' => new It(),
                        'asset_user_id' => new CallCenter(),

                        'asset_classification_id' => array(
                                new AvailabilityHigh(),
                                new IntegrityHigh(),
                                new ConfidentialityHigh()
                        )

		);
	}
}
