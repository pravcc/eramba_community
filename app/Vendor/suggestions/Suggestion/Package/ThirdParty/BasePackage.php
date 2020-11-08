<?php
namespace Suggestion\Package\ThirdParty;
use Suggestion\Package;

class BasePackage extends Package {
	public $model = 'ThirdParty';
	public $requestAction = '/thirdParties/add';
}
