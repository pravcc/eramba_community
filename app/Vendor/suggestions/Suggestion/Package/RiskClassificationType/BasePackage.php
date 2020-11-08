<?php
namespace Suggestion\Package\RiskClassificationType;
use Suggestion\Package;

class BasePackage extends Package {
	public $model = 'RiskClassificationType';
	public $requestAction = '/riskClassifications/addClassificationType';
	public $forceExistingItem = true;
}
