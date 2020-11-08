<?php
namespace Suggestion\Package\Asset;

use Suggestion\Package\BusinessUnit\It;
use Suggestion\Package\BusinessUnit\DefaultPackage;

use Suggestion\Package\Legal\ISO27001;
use Suggestion\Package\Legal\SOX;
use Suggestion\Package\Legal\PersonalDataAct;
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


class EmployeePersonalInformation extends BasePackage {
	public $alias = 'EmployeePersonalInformation';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Employee Personal Information');

		$this->data = array(
			'name' => $this->name,
			'business_unit_id' => array(
				new It()
			),
			'description' => __('Employee personal records that include their personal information.'),
			'asset_media_type_id' => ASSET_MEDIA_TYPE_DATA,
			'legal_id' => array( new PersonalDataAct() ),
			'review' => date('Y-m-d'),
			'asset_label_id' =>  new Privates(),

                        'asset_owner_id' => new DefaultPackage(),
                        'asset_guardian_id' => new DefaultPackage(),
                        'asset_user_id' => new DefaultPackage(),

                        'asset_classification_id' => array(
                                new AvailabilityHigh(),
                                new IntegrityHigh(),
                                new ConfidentialityHigh()
                        )

		);
	}
}
