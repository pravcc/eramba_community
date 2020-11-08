<?php
namespace Suggestion\Package\Asset;
use Suggestion\Package;
use Suggestion\Package\BusinessUnit\DefaultPackage;

class BasePackage extends Package {
	public $model = 'Asset';
	public $requestAction = '/assets/add';
}
