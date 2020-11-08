<?php
namespace Suggestion\Package\Asset;

use Suggestion\Package\BusinessUnit\It;
use Suggestion\Package\BusinessUnit\DefaultPackage;
use Suggestion\Package\BusinessUnit\Finance;
use Suggestion\Package\BusinessUnit\CallCenter;

use Suggestion\Package\Legal\ISO27001;
use Suggestion\Package\Legal\SOX;
use Suggestion\Package\Legal\PersonalDataAct;
use Suggestion\Package\Legal\PCIDSS;
use Suggestion\Package\Legal\ContractualAgreements;
use Suggestion\Package\Legal\ConfidentialityAgreements;

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

class Payment extends BasePackage {
	public $alias = 'Payment';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Payment Application');

		$this->data = array(
			'name' => $this->name,
			'business_unit_id' => array(
				new CallCenter(), new It(),
			),
			'description' => __('The application used by the call center and customers to process payments.'),
			'asset_media_type_id' => ASSET_MEDIA_TYPE_SOFTWARE,
			'legal_id' => array( new PCIDSS(), new ISO27001(), ),
			'review' => date('Y-m-d'),

                        'asset_label_id' =>  new Confidentiality(),
                        'asset_owner_id' => new It(),
                        'asset_guardian_id' => new It(),
                        'asset_user_id' => new CallCenter(),

                        'asset_classification_id' => array(
                                new AvailabilityHigh(),
                                new IntegrityHigh(),
                                New ConfidentialityHigh()
                        )


		);
	}
}
