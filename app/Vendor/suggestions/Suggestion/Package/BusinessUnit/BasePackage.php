<?php
namespace Suggestion\Package\BusinessUnit;
use Suggestion\Package;

class BasePackage extends Package {
	public $model = 'BusinessUnit';
	public $requestAction = '/businessUnits/add';
}
