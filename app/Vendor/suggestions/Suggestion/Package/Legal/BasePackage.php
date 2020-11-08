<?php
namespace Suggestion\Package\Legal;
use Suggestion\Package;

class BasePackage extends Package {
	public $model = 'Legal';
	public $requestAction = '/legals/add';
}
