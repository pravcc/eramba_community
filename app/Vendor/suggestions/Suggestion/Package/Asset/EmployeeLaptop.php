<?php
namespace Suggestion\Package\Asset;

use Suggestion\Package\BusinessUnit\It;
use Suggestion\Package\BusinessUnit\DefaultPackage;

use Suggestion\Package\Legal\ISO27001;
use Suggestion\Package\Legal\SOX;
use Suggestion\Package\Legal\PersonalDataAct;
use Suggestion\Package\Legal\PCIDSS;
use Suggestion\Package\Legal\ContractualAgreements;

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


class EmployeeLaptop extends BasePackage {
	public $alias = 'EmployeeLaptop';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Employee Laptop');

		$this->data = array(
			'name' => $this->name,
			'business_unit_id' => array(
				new It()
			),
			'description' => __('Employee laptops and desktop computers'),
			'asset_media_type_id' => ASSET_MEDIA_TYPE_HARDWARE,
			'Legal' => array( new Sox(), new ContractualAgreements() ),
			'review' => date('Y-m-d'),

                        'asset_label_id' =>  new Privates(),
                        'asset_owner_id' => new It(),
                        'asset_guardian_id' => new It(),
                        'asset_user_id' => BU_EVERYONE,

                        'asset_classification_id' => array(
                                new AvailabilityMedium(),
                                new IntegrityHigh(),
                                New ConfidentialityHigh()
                        )

		);
	}
}
