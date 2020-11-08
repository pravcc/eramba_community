<?php
namespace Suggestion\Package\Asset;
use Suggestion\Package\BusinessUnit\Finance;
use Suggestion\Package\Legal\SOX;

use Suggestion\Package\AssetLabel\Confidentiality;

use Suggestion\Package\AssetClassification\AvailabilityHigh;
use Suggestion\Package\AssetClassification\IntegrityHigh;
use Suggestion\Package\AssetClassification\ConfidentialityHigh;

class FinancialData extends BasePackage {
	public $alias = 'FinancialData';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Financial Data');

		$this->data = array(
			'name' => $this->name,
			'business_unit_id' => array(
				new Finance()
			),
			'description' => __('Financia information, payments, bank statements, etc.'),
			'asset_media_type_id' => ASSET_MEDIA_TYPE_DATA,
			'asset_label_id' => new Confidentiality(),
			'legal_id' => array( new SOX() ),
			'review' => date('Y-m-d'),

                        'asset_owner_id' => new Finance(), 
                        'asset_guardian_id' => new Finance(), 
                        'asset_user_id' => new Finance(), 

                        'asset_classification_id' => array(
                                new AvailabilityHigh(),
                                new IntegrityHigh(),
				new ConfidentialityHigh()
                        )

		);
	}
}
