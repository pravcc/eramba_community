<?php
namespace Suggestion\Package\RiskClassification;
use Suggestion\Package;

class BasePackage extends Package {
	public $model = 'RiskClassification';
	public $requestAction = '/riskClassifications/add';
}
