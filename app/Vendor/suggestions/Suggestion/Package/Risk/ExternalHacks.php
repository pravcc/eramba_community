<?php
namespace Suggestion\Package\Risk;

use Suggestion\Package\RiskException\DefaultPackage;
// use Suggestion\Package\RiskException\BasePackage;
use Suggestion\Package\RiskException\BusinessInsurancePolicy;
use Suggestion\Package\RiskException\NoBudget;
use Suggestion\Package\RiskException\NoRiskForOwner;

use Suggestion\Package\SecurityService\EndPointHardwareInventory;

use Suggestion\Package\Asset\Databases;
use Suggestion\Package\Asset\Payment;
use Suggestion\Package\Asset\PBX;
use Suggestion\Package\Asset\EmployeePersonalInformation;
use Suggestion\Package\Asset\Employees;
use Suggestion\Package\Asset\EmployeeLaptop;
use Suggestion\Package\Asset\FinancialData;
use Suggestion\Package\Asset\PaymentData;
use Suggestion\Package\Asset\Os;
use Suggestion\Package\Asset\SapSystems;
use Suggestion\Package\Asset\ShareDrives;

use Suggestion\Package\SecurityPolicy\RiskManagement;;

use Suggestion\Package\RiskClassification\ImpactHigh;
use Suggestion\Package\RiskClassification\ImpactLow;
use Suggestion\Package\RiskClassification\ImpactMedium;
use Suggestion\Package\RiskClassification\LikelihoodHigh;
use Suggestion\Package\RiskClassification\LikelihoodLow;
use Suggestion\Package\RiskClassification\LikelihoodMedium;

use Suggestion\Package\SecurityService\ActiveDirectoryGroupReviews;
use Suggestion\Package\SecurityService\ActiveDirectoryUserReviews;
use Suggestion\Package\SecurityService\AlternativePowerSources;
use Suggestion\Package\SecurityService\BadgeReviews;
use Suggestion\Package\SecurityService\CCTV;
use Suggestion\Package\SecurityService\CentralisedLogging;
use Suggestion\Package\SecurityService\ContractorReviews;
use Suggestion\Package\SecurityService\DatacenterSecurity;
use Suggestion\Package\SecurityService\DMZReviews;
use Suggestion\Package\SecurityService\FireMotionDetectors;
use Suggestion\Package\SecurityService\IDS;
use Suggestion\Package\SecurityService\NDAReviews;
use Suggestion\Package\SecurityService\NetworkAdministratorReviews;
use Suggestion\Package\SecurityService\PublicSharedDrivesScanner;
use Suggestion\Package\SecurityService\RegularVulnerabilityScanningExternal;
use Suggestion\Package\SecurityService\SAPApplicationAccountReviews;
use Suggestion\Package\SecurityService\SecureApplicationDevelopment;
use Suggestion\Package\SecurityService\SecureDisposalOfDataAndEquipment;
use Suggestion\Package\SecurityService\SecurityAwarenessTrainings;
use Suggestion\Package\SecurityService\ServiceAccountsReviews;
use Suggestion\Package\SecurityService\SystemPatching;

class ExternalHacks extends BasePackage {
	public $alias = 'ExternalHacks';

	public function __construct($options = array()) {
		parent::__construct($options);
		$this->name = __('Loss of data due bugs on public apps');

		$this->data = array(
			$this->model => array(
				/**
				 * General Tab
				 */

				'title' => $this->name,
				// owner
				'user_id' => ADMIN_ID,
				// stakeholder
				'guardian_id' => ADMIN_ID,
				'review' => date('Y-m-d', strtotime("+10 days")),

				/**
				 * Analysis tab
				 */

				'asset_id' => array(
					new Os(), new Payment(), new PBX(),
				),
				// threat tags (array of IDs from `threats` table)
				'threat_id' => array('11', '22','23','8', '11'),
				// threat description
				'threats' => __('Telephony, Paymnet Applications and operating systems  are internet facing and could be hacked'),
				// vulnerability tags (array of IDs from `vulnerabilities` table)
				'vulnerability_id' => array('4','9','10','14','16','34'),
				// threat description
				'vulnerabilities' => __('Vulnerabilities asociated with the operating systems or applications'),
				// risk classifications
				'risk_classification_id' => array(
					new ImpactHigh(),
					new LikelihoodLow()
				),

				/**
				 * Mitigation tab
				 */

				// lookup shared_constants
/*
define('RISK_MITIGATION_ACCEPT', 1);
define('RISK_MITIGATION_AVOID', 2);
define('RISK_MITIGATION_MITIGATE', 3);
define('RISK_MITIGATION_TRANSFER', 4);
*/
				'risk_mitigation_strategy_id' => RISK_MITIGATION_MITIGATE,
				'security_service_id' => array(
					new SystemPatching(), new IDS(), new SecureApplicationDevelopment(), new RegularVulnerabilityScanningExternal(),
				),
				'risk_exception_id' => array(
				
				),
				// no projects suggestions available
				'project_id' => '',
				// options - 0,10,20,30,40,50,60,70,80,90,100
				'residual_score' => '20',
				
				/**
				 * Incident containment tab
				 */

				// Procedure documents - we dont have any procedure suggestions now
				'procedure_id' => '',
				// Policy Documents - we have risk managements policy suggestions
				'policy_id' => array(),
				// Standard Documents - we dont have any standard suggestions now
				'standard_id' => ''
			),
			// Classifications
			'Tag' => array(
				'tags' => 'PCI-DSS',
			)
		);

	}
}
