<?php
namespace Suggestion\Package\TeamRole;
use Suggestion\Package;

class BasePackage extends Package {
	public $model = 'TeamRole';
	public $requestAction = '/teamRoles/add';
}
