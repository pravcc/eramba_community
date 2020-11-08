<?php
namespace Suggestion\Package\RiskException;
use Suggestion\Package;

class BasePackage extends Package {
	public $model = 'RiskException';
	public $requestAction = '/riskExceptions/add';
}
