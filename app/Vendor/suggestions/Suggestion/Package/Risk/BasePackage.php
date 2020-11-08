<?php
namespace Suggestion\Package\Risk;
use Suggestion\Package;

class BasePackage extends Package {
	public $model = 'Risk';
	public $requestAction = '/risks/add';
}
